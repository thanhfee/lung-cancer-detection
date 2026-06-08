<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-sky-700">LungCare AI News</p>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-950">Tin tức y tế</h2>
                <p class="mt-1 text-sm font-semibold text-slate-500">Tự cập nhật xu hướng mới về ung thư phổi, AI y tế và sức khỏe hô hấp.</p>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-lg border border-sky-100 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Cập nhật: {{ $lastUpdated }}
            </span>
        </div>
    </x-slot>

    <div class="min-h-screen bg-[#eef8ff] pb-24">
        <main class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-lg border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <section class="overflow-hidden rounded-lg bg-gradient-to-r from-white via-white to-sky-100 shadow-sm ring-1 ring-sky-100">
                <div class="grid gap-6 p-6 lg:grid-cols-[1fr_360px] lg:p-8">
                    <div>
                        <p class="inline-flex items-center gap-2 rounded-lg bg-sky-50 px-3 py-2 text-xs font-black uppercase tracking-[0.18em] text-sky-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Xu hướng hiện tại
                        </p>
                    
                        <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                            Theo dõi tin mới, lọc theo chủ đề, tìm kiếm nhanh, lưu tin quan trọng và nhờ AI tóm tắt điểm liên quan tới chẩn đoán phổi.
                        </p>
                    </div>

                    <form action="{{ route('news.index') }}" method="GET" class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-sky-100">
                        <label for="news-search" class="text-xs font-black uppercase tracking-widest text-slate-400">Tìm kiếm tin tức</label>
                        <div class="mt-3 flex gap-2">
                            <input id="news-search" name="q" value="{{ $keyword }}" class="h-11 min-w-0 flex-1 rounded-lg border-0 bg-slate-50 px-4 text-sm font-semibold text-slate-700 ring-1 ring-slate-100 focus:ring-2 focus:ring-sky-500" placeholder="Nhập từ khóa...">
                            @if($activeTopic)
                                <input type="hidden" name="topic" value="{{ $activeTopic }}">
                            @endif
                            <button class="h-11 rounded-lg bg-[#06488f] px-4 text-sm font-extrabold text-white transition hover:bg-[#053a73]">Tìm</button>
                        </div>
                        @if($keyword || $activeTopic)
                            <a href="{{ route('news.index') }}" class="mt-3 inline-flex text-sm font-bold text-sky-700 hover:text-[#06488f]">Xóa bộ lọc</a>
                        @endif
                    </form>
                </div>
            </section>

            <section class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-100">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('news.index', array_filter(['q' => $keyword])) }}" class="rounded-lg px-4 py-2 text-sm font-extrabold transition {{ !$activeTopic ? 'bg-[#06488f] text-white' : 'bg-slate-50 text-slate-600 ring-1 ring-slate-100 hover:bg-sky-50' }}">
                        Tất cả
                    </a>
                    @foreach($topics as $topic)
                        <a href="{{ route('news.index', array_filter(['topic' => $topic['label'], 'q' => $keyword])) }}" class="rounded-lg px-4 py-2 text-sm font-extrabold transition {{ $activeTopic === $topic['label'] ? 'bg-[#06488f] text-white' : 'bg-slate-50 text-slate-600 ring-1 ring-slate-100 hover:bg-sky-50' }}">
                            {{ $topic['label'] }}
                        </a>
                    @endforeach
                </div>
            </section>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_340px]">
                <section class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-100">
                    <div class="flex flex-col gap-4 border-b border-slate-100 bg-gradient-to-r from-white via-sky-50 to-cyan-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-sky-700">News feed</p>
                            <h3 class="mt-1 text-lg font-black text-slate-950">Tin mới cập nhật</h3>
                        </div>
                        <span class="inline-flex w-fit items-center gap-2 rounded-lg bg-white px-3 py-2 text-xs font-bold text-slate-500 ring-1 ring-slate-100">
                            <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                            {{ count($news) }} tin
                        </span>
                    </div>

                    <div class="bg-slate-50/60 p-4 sm:p-6">
                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                            @forelse($news as $item)
                                @php $isSaved = in_array($item['hash'], $savedHashes, true); @endphp
                                <article class="flex min-h-[320px] flex-col overflow-hidden rounded-lg bg-white ring-1 ring-slate-100 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-sky-100">
                                    <div class="h-1.5 w-full bg-[#06488f]"></div>
                                    <div class="flex flex-1 flex-col p-5">
                                        <div class="flex items-start justify-between gap-3">
                                            <span class="rounded-lg bg-sky-50 px-3 py-2 text-[11px] font-black uppercase tracking-wide text-sky-700 ring-1 ring-sky-100">
                                                {{ $item['topic'] }}
                                            </span>
                                            <span class="text-right text-xs font-bold text-slate-400">{{ $item['published_label'] }}</span>
                                        </div>

                                        <h4 class="mt-4 text-lg font-black leading-6 text-slate-950">
                                            {{ $item['title'] }}
                                        </h4>

                                        <p class="mt-3 text-sm font-medium leading-6 text-slate-600">
                                            {{ $item['summary'] ?: 'Bấm xem chi tiết để đọc đầy đủ nội dung từ nguồn tin.' }}
                                        </p>

                                        <div id="summary-{{ $item['hash'] }}" class="mt-4 hidden rounded-lg border border-sky-100 bg-white p-4 text-sm leading-6 text-slate-700 shadow-sm"></div>

                                        <div class="mt-auto pt-5">
                                            <p class="mb-3 text-xs font-black uppercase tracking-widest text-slate-400">
                                                Nguồn: {{ $item['source'] ?: 'RSS' }}
                                            </p>
                                            <div class="grid grid-cols-2 gap-2">
                                                <a href="{{ route('news.show', $item['hash']) }}" class="inline-flex h-10 items-center justify-center rounded-lg bg-[#06488f] px-4 text-sm font-extrabold text-white transition hover:bg-[#053a73]">
                                                    Chi tiết
                                                </a>
                                                <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-10 items-center justify-center rounded-lg border border-sky-200 bg-white px-4 text-sm font-extrabold text-[#06488f] transition hover:bg-sky-50">
                                                    Nguồn
                                                </a>
                                            </div>
                                            <div class="mt-2 grid grid-cols-2 gap-2">
                                                <button type="button" onclick="summarizeNews(@js($item['hash']), @js($item['title']), @js($item['summary']))" class="inline-flex h-10 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 text-sm font-extrabold text-emerald-700 transition hover:bg-emerald-100">
                                                    Tóm tắt AI
                                                </button>
                                                <form action="{{ route('news.save') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="news_hash" value="{{ $item['hash'] }}">
                                                    <input type="hidden" name="topic" value="{{ $item['topic'] }}">
                                                    <input type="hidden" name="title" value="{{ $item['title'] }}">
                                                    <input type="hidden" name="url" value="{{ $item['url'] }}">
                                                    <input type="hidden" name="source" value="{{ $item['source'] }}">
                                                    <input type="hidden" name="summary" value="{{ $item['summary'] }}">
                                                    <input type="hidden" name="published_label" value="{{ $item['published_label'] }}">
                                                    <button class="inline-flex h-10 w-full items-center justify-center rounded-lg border px-3 text-sm font-extrabold transition {{ $isSaved ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                                                        {{ $isSaved ? 'Đã lưu' : 'Lưu tin' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="col-span-full rounded-lg bg-white px-6 py-16 text-center ring-1 ring-slate-100">
                                    <p class="font-bold text-slate-400">Chưa lấy được tin tức mới. Vui lòng thử lại sau.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>

                <aside class="space-y-6">
                    <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-100">
                        <p class="text-xs font-black uppercase tracking-widest text-sky-700">Tin đã lưu</p>
                        <h3 class="mt-1 text-lg font-black text-slate-950">Quan trọng</h3>
                        <div class="mt-4 space-y-3">
                            @forelse($savedNews as $saved)
                                <div class="rounded-lg bg-slate-50 p-3 ring-1 ring-slate-100">
                                    <a href="{{ route('news.show', $saved->news_hash) }}" class="text-sm font-black leading-5 text-slate-900 hover:text-[#06488f]">{{ $saved->title }}</a>
                                    <form action="{{ route('news.saved.destroy', $saved->id) }}" method="POST" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs font-bold text-red-600 hover:text-red-700">Bỏ lưu</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-sm font-semibold text-slate-400">Chưa có tin đã lưu.</p>
                            @endforelse
                        </div>
                    </section>

                    @if(auth()->user()->role === 'admin')
                        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-100">
                            <p class="text-xs font-black uppercase tracking-widest text-sky-700">Admin</p>
                            <h3 class="mt-1 text-lg font-black text-slate-950">Quản lý chủ đề RSS</h3>

                            <form action="{{ route('news.topics.store') }}" method="POST" class="mt-4 space-y-3 rounded-lg bg-sky-50 p-3 ring-1 ring-sky-100">
                                @csrf
                                <input name="label" class="h-10 w-full rounded-lg border-0 bg-white px-3 text-sm font-semibold ring-1 ring-sky-100 focus:ring-2 focus:ring-sky-500" placeholder="Tên chủ đề">
                                <input name="query" class="h-10 w-full rounded-lg border-0 bg-white px-3 text-sm font-semibold ring-1 ring-sky-100 focus:ring-2 focus:ring-sky-500" placeholder="Từ khóa RSS">
                                <button class="h-10 w-full rounded-lg bg-[#06488f] text-sm font-extrabold text-white">Thêm chủ đề</button>
                            </form>

                            <div class="mt-4 space-y-3">
                                @foreach($allTopics as $topic)
                                    <form action="{{ route('news.topics.update', $topic->id) }}" method="POST" class="rounded-lg bg-slate-50 p-3 ring-1 ring-slate-100">
                                        @csrf
                                        @method('PUT')
                                        <input name="label" value="{{ $topic->label }}" class="h-9 w-full rounded-lg border-0 bg-white px-3 text-sm font-semibold ring-1 ring-slate-100 focus:ring-2 focus:ring-sky-500">
                                        <input name="query" value="{{ $topic->query }}" class="mt-2 h-9 w-full rounded-lg border-0 bg-white px-3 text-sm font-semibold ring-1 ring-slate-100 focus:ring-2 focus:ring-sky-500">
                                        <label class="mt-2 flex items-center gap-2 text-xs font-bold text-slate-500">
                                            <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked($topic->is_active)>
                                            Đang hoạt động
                                        </label>
                                        <div class="mt-2 grid grid-cols-2 gap-2">
                                            <button class="h-9 rounded-lg bg-[#06488f] text-xs font-extrabold text-white">Lưu</button>
                                            <button type="submit" form="delete-topic-{{ $topic->id }}" class="h-9 rounded-lg bg-red-50 text-xs font-extrabold text-red-600 ring-1 ring-red-100">Xóa</button>
                                        </div>
                                    </form>
                                    <form id="delete-topic-{{ $topic->id }}" action="{{ route('news.topics.destroy', $topic->id) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </aside>
            </div>
        </main>
    </div>

    <script>
        function escapeSummaryHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatAiSummary(reply) {
            let text = String(reply || '').replace(/\r/g, '').trim();

            if (!text) {
                return '<div class="rounded-lg bg-amber-50 p-4 font-bold text-amber-700 ring-1 ring-amber-100">AI chưa trả về nội dung tóm tắt.</div>';
            }

            text = text
                .replace(/^Chào.*?cung cấp:\s*/i, '')
                .replace(/\*\*([^*:\n]{2,80}):\*\*/g, '\n### $1\n')
                .replace(/\*\*([^*\n]{2,80})\*\*/g, '\n### $1\n')
                .replace(/(?:^|\n)\s*(\d+)[.)]\s+/g, '\n- ');

            const lines = text.split('\n').map((line) => line.trim()).filter(Boolean);
            let html = '<div class="space-y-3">';
            let sectionOpen = false;

            const closeSection = () => {
                if (sectionOpen) {
                    html += '</div>';
                    sectionOpen = false;
                }
            };

            lines.forEach((line) => {
                if (line.startsWith('### ')) {
                    closeSection();
                    html += '<div class="rounded-lg bg-sky-50 p-4 ring-1 ring-sky-100">';
                    html += '<p class="mb-2 text-xs font-black uppercase tracking-widest text-sky-700">' + escapeSummaryHtml(line.replace(/^###\s*/, '')) + '</p>';
                    sectionOpen = true;
                    return;
                }

                if (!sectionOpen) {
                    html += '<div class="rounded-lg bg-slate-50 p-4 ring-1 ring-slate-100">';
                    sectionOpen = true;
                }

                const bullet = line.replace(/^[-*•]\s*/, '');
                if (bullet !== line) {
                    html += '<div class="mt-2 flex gap-3"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[#06488f]"></span><p class="font-semibold leading-7 text-slate-700">' + escapeSummaryHtml(bullet) + '</p></div>';
                } else {
                    html += '<p class="mt-2 font-medium leading-7 text-slate-700">' + escapeSummaryHtml(line) + '</p>';
                }
            });

            closeSection();
            html += '</div>';

            return html;
        }

        function renderAiLoading(target) {
            target.innerHTML = '<div class="flex items-center gap-3 rounded-lg bg-sky-50 p-4 font-bold text-sky-700 ring-1 ring-sky-100"><span class="h-2.5 w-2.5 animate-pulse rounded-full bg-sky-500"></span>AI đang tóm tắt tin này...</div>';
        }

        async function summarizeNews(hash, title, summary) {
            const target = document.getElementById('summary-' + hash);
            target.classList.remove('hidden');
            renderAiLoading(target);

            const prompt = [
                'Bạn là trợ lý y khoa cho bác sĩ.',
                'Không chào hỏi, không viết thành một đoạn dài.',
                'Hãy tóm tắt tin sau bằng tiếng Việt theo 4 mục ngắn.',
                'Dùng đúng định dạng: **Ý chính:**, **Liên quan lâm sàng:**, **Lưu ý:**, **Khuyến nghị:**.',
                'Mỗi mục tối đa 2 câu, diễn đạt gọn gàng.',
                'Nêu rõ tin này liên quan gì đến ung thư phổi, AI y tế hoặc chăm sóc hô hấp.',
                'Không đưa ra chẩn đoán y khoa.',
                'Tiêu đề: ' + title,
                'Mô tả: ' + (summary || 'Không có mô tả.')
            ].join('\n');

            try {
                const response = await fetch('{{ route("ai.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message: prompt })
                });

                const data = await response.json();
                target.innerHTML = formatAiSummary(data.reply);
            } catch (error) {
                target.innerHTML = '<div class="rounded-lg bg-red-50 p-4 font-bold text-red-700 ring-1 ring-red-100">Không thể tóm tắt lúc này. Vui lòng thử lại sau.</div>';
            }
        }
    </script>
</x-app-layout>
