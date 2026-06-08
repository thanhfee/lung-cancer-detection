<x-app-layout>
    @php
        $hash = $item['hash'] ?? $item['news_hash'];
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-sky-700">Chi tiết tin tức</p>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-950">Bản tin y tế</h2>
            </div>
            <a href="{{ route('news.index') }}" class="inline-flex h-10 w-fit items-center rounded-lg bg-white px-4 text-sm font-extrabold text-[#06488f] shadow-sm ring-1 ring-sky-100 transition hover:bg-sky-50">
                Quay lại tin tức
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-[#eef8ff] pb-24">
        <main class="mx-auto max-w-5xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            <article class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-100">
                <div class="h-1.5 w-full bg-[#06488f]"></div>
                <div class="p-6 lg:p-8">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-lg bg-sky-50 px-3 py-2 text-xs font-black uppercase tracking-wide text-sky-700 ring-1 ring-sky-100">
                            {{ $item['topic'] ?? 'Tin tức' }}
                        </span>
                        <span class="text-sm font-bold text-slate-400">{{ $item['published_label'] ?? '' }}</span>
                        @if($isSaved)
                            <span class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-black uppercase tracking-wide text-amber-700 ring-1 ring-amber-100">Đã lưu</span>
                        @endif
                    </div>

                    <h1 class="mt-6 text-3xl font-black leading-tight tracking-tight text-slate-950 lg:text-4xl">
                        {{ $item['title'] }}
                    </h1>

                    <p class="mt-5 text-base font-medium leading-8 text-slate-600">
                        {{ $item['summary'] ?: 'Tin này chưa có mô tả ngắn từ RSS. Bạn có thể mở nguồn để đọc nội dung đầy đủ.' }}
                    </p>

                    <div id="detail-ai-summary" class="mt-6 hidden rounded-lg border border-sky-100 bg-white p-5 text-sm leading-7 text-slate-700 shadow-sm"></div>

                    <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-11 items-center justify-center rounded-lg bg-[#06488f] px-4 text-sm font-extrabold text-white transition hover:bg-[#053a73]">
                            Đọc nguồn gốc
                        </a>
                        <button type="button" onclick="summarizeDetailNews()" class="inline-flex h-11 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-4 text-sm font-extrabold text-emerald-700 transition hover:bg-emerald-100">
                            Tóm tắt AI
                        </button>
                        @unless($isSaved)
                            <form action="{{ route('news.save') }}" method="POST">
                                @csrf
                                <input type="hidden" name="news_hash" value="{{ $hash }}">
                                <input type="hidden" name="topic" value="{{ $item['topic'] ?? '' }}">
                                <input type="hidden" name="title" value="{{ $item['title'] }}">
                                <input type="hidden" name="url" value="{{ $item['url'] }}">
                                <input type="hidden" name="source" value="{{ $item['source'] ?? '' }}">
                                <input type="hidden" name="summary" value="{{ $item['summary'] ?? '' }}">
                                <input type="hidden" name="published_label" value="{{ $item['published_label'] ?? '' }}">
                                <button class="inline-flex h-11 w-full items-center justify-center rounded-lg border border-amber-200 bg-amber-50 px-4 text-sm font-extrabold text-amber-700 transition hover:bg-amber-100">
                                    Lưu tin
                                </button>
                            </form>
                        @else
                            <span class="inline-flex h-11 items-center justify-center rounded-lg border border-amber-200 bg-amber-50 px-4 text-sm font-extrabold text-amber-700">
                                Tin đã lưu
                            </span>
                        @endunless
                    </div>

                    <div class="mt-8 rounded-lg bg-slate-50 p-5 ring-1 ring-slate-100">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Nguồn</p>
                        <p class="mt-2 text-sm font-bold text-slate-700">{{ $item['source'] ?? 'RSS' }}</p>
                        <p class="mt-2 break-all text-sm font-medium text-slate-500">{{ $item['url'] }}</p>
                    </div>
                </div>
            </article>
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

        async function summarizeDetailNews() {
            const target = document.getElementById('detail-ai-summary');
            target.classList.remove('hidden');
            renderAiLoading(target);

            const prompt = [
                'Bạn là trợ lý y khoa cho bác sĩ.',
                'Không chào hỏi, không viết thành một đoạn dài.',
                'Tóm tắt bản tin sau bằng tiếng Việt theo 4-5 mục ngắn.',
                'Dùng đúng định dạng: **Ý chính:**, **Liên quan lâm sàng:**, **Lưu ý:**, **Khuyến nghị:**.',
                'Mỗi mục tối đa 2 câu, diễn đạt gọn gàng.',
                'Nêu ý nghĩa với tầm soát ung thư phổi, AI y tế hoặc chăm sóc hô hấp nếu có.',
                'Không đưa ra chẩn đoán y khoa.',
                'Tiêu đề: ' + @js($item['title']),
                'Mô tả: ' + @js($item['summary'] ?? '')
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
