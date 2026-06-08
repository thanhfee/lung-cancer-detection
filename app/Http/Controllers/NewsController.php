<?php

namespace App\Http\Controllers;

use App\Models\NewsTopic;
use App\Models\SavedNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $topics = $this->activeTopics();
        $news = collect($this->allNews($topics));

        if ($request->filled('topic')) {
            $news = $news->where('topic', $request->query('topic'));
        }

        if ($request->filled('q')) {
            $keyword = Str::lower($request->query('q'));
            $news = $news->filter(function ($item) use ($keyword) {
                return Str::contains(Str::lower($item['title']), $keyword)
                    || Str::contains(Str::lower($item['summary'] ?? ''), $keyword)
                    || Str::contains(Str::lower($item['source'] ?? ''), $keyword);
            });
        }

        $savedHashes = SavedNews::where('user_id', auth()->id())
            ->pluck('news_hash')
            ->all();

        return view('news.index', [
            'news' => $news->values()->all(),
            'topics' => $topics,
            'savedHashes' => $savedHashes,
            'savedNews' => SavedNews::where('user_id', auth()->id())->latest()->take(6)->get(),
            'allTopics' => auth()->user()->role === 'admin' ? $this->allTopics() : collect(),
            'lastUpdated' => now()->timezone('Asia/Ho_Chi_Minh')->format('H:i - d/m/Y'),
            'activeTopic' => $request->query('topic'),
            'keyword' => $request->query('q'),
        ]);
    }

    public function show(string $hash)
    {
        $topics = $this->activeTopics();
        $item = collect($this->allNews($topics))->firstWhere('hash', $hash);

        if (!$item) {
            $item = SavedNews::where('news_hash', $hash)
                ->where('user_id', auth()->id())
                ->first()?->toArray();
        }

        abort_if(!$item, 404);

        return view('news.show', [
            'item' => $item,
            'isSaved' => SavedNews::where('user_id', auth()->id())->where('news_hash', $hash)->exists(),
        ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'news_hash' => ['required', 'string', 'max:64'],
            'topic' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string'],
            'url' => ['required', 'string'],
            'source' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'published_label' => ['nullable', 'string', 'max:255'],
        ]);

        SavedNews::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'news_hash' => $data['news_hash'],
            ],
            $data + ['user_id' => auth()->id()]
        );

        return back()->with('success', 'Đã lưu tin tức quan trọng.');
    }

    public function destroySaved(SavedNews $savedNews)
    {
        abort_if($savedNews->user_id !== auth()->id(), 403);

        $savedNews->delete();

        return back()->with('success', 'Đã bỏ lưu tin tức.');
    }

    public function storeTopic(Request $request)
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'query' => ['required', 'string', 'max:255'],
        ]);

        NewsTopic::create($data + ['is_active' => true]);
        Cache::forget('dashboard_news_trends_v2');

        return back()->with('success', 'Đã thêm chủ đề tin tức.');
    }

    public function updateTopic(Request $request, NewsTopic $topic)
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'query' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $topic->update($data + ['is_active' => $request->boolean('is_active')]);
        Cache::forget('dashboard_news_trends_v2');

        return back()->with('success', 'Đã cập nhật chủ đề tin tức.');
    }

    public function destroyTopic(NewsTopic $topic)
    {
        $topic->delete();
        Cache::forget('dashboard_news_trends_v2');

        return back()->with('success', 'Đã xóa chủ đề tin tức.');
    }

    private function activeTopics()
    {
        if (NewsTopic::count() === 0) {
            $this->seedDefaultTopics();
        }

        return NewsTopic::where('is_active', true)
            ->orderBy('label')
            ->get()
            ->map(fn ($topic) => [
                'id' => $topic->id,
                'label' => $topic->label,
                'query' => $topic->query,
            ])
            ->values()
            ->all();
    }

    private function allTopics()
    {
        if (NewsTopic::count() === 0) {
            $this->seedDefaultTopics();
        }

        return NewsTopic::orderBy('label')->get();
    }

    private function allNews(array $topics): array
    {
        $news = Cache::remember('dashboard_news_trends_v2', now()->addMinutes(30), function () use ($topics) {
            return collect($topics)
                ->flatMap(fn ($topic) => $this->fetchTopicNews($topic['query'], $topic['label']))
                ->sortByDesc('published_at')
                ->take(18)
                ->values()
                ->all();
        });

        return empty($news) ? $this->fallbackNews() : $news;
    }

    private function fetchTopicNews(string $query, string $topic): array
    {
        $url = 'https://news.google.com/rss/search?q=' . urlencode($query)
            . '&hl=vi&gl=VN&ceid=VN:vi';

        try {
            $response = Http::timeout(8)
                ->retry(1, 300)
                ->withHeaders(['User-Agent' => 'LungCareAI/1.0'])
                ->get($url);

            if (!$response->successful()) {
                return [];
            }

            $xml = @simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);

            if (!$xml || !isset($xml->channel->item)) {
                return [];
            }

            return collect($xml->channel->item)
                ->take(8)
                ->map(function ($item) use ($topic) {
                    $publishedAt = isset($item->pubDate) ? strtotime((string) $item->pubDate) : time();
                    $description = trim(strip_tags((string) ($item->description ?? '')));
                    $title = trim((string) $item->title);
                    $url = trim((string) $item->link);

                    return [
                        'hash' => sha1($url . $title),
                        'topic' => $topic,
                        'title' => $title,
                        'url' => $url,
                        'source' => trim((string) ($item->source ?? 'Google News')),
                        'summary' => Str::limit($description, 220),
                        'published_at' => $publishedAt,
                        'published_label' => date('H:i d/m/Y', $publishedAt),
                    ];
                })
                ->filter(fn ($item) => filled($item['title']) && filled($item['url']))
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function fallbackNews(): array
    {
        $items = [
            [
                'topic' => 'Cẩm nang',
                'title' => 'Tầm soát ung thư phổi định kỳ cho nhóm nguy cơ cao',
                'url' => route('dashboard') . '#health-guide',
                'source' => 'LungCare AI',
                'summary' => 'Người hút thuốc lâu năm, tiếp xúc khói bụi hoặc có triệu chứng hô hấp kéo dài nên được tư vấn tầm soát phù hợp.',
                'published_at' => time(),
                'published_label' => now()->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y'),
            ],
            [
                'topic' => 'AI y tế',
                'title' => 'AI hỗ trợ bác sĩ đọc nhanh dữ liệu chẩn đoán hình ảnh',
                'url' => route('dashboard') . '#ai-diagnosis',
                'source' => 'LungCare AI',
                'summary' => 'AI không thay thế bác sĩ, nhưng có thể giúp sàng lọc, ưu tiên hồ sơ và chuẩn hóa báo cáo lâm sàng.',
                'published_at' => time() - 3600,
                'published_label' => now()->subHour()->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y'),
            ],
        ];

        return collect($items)
            ->map(fn ($item) => $item + ['hash' => sha1($item['url'] . $item['title'])])
            ->all();
    }

    private function seedDefaultTopics(): void
    {
        collect([
            ['label' => 'Ung thư phổi', 'query' => 'ung thư phổi tầm soát chẩn đoán điều trị'],
            ['label' => 'AI y tế', 'query' => 'trí tuệ nhân tạo y tế chẩn đoán hình ảnh'],
            ['label' => 'Sức khỏe hô hấp', 'query' => 'sức khỏe hô hấp phổi bệnh viện'],
        ])->each(fn ($topic) => NewsTopic::firstOrCreate($topic, ['is_active' => true]));
    }
}
