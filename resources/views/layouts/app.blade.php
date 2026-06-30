<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:5173 http://127.0.0.1:5173 https://cdn.jsdelivr.net https://fonts.bunny.net;
        connect-src 'self' http://localhost:5173 http://127.0.0.1:5173 ws://localhost:5173 ws://127.0.0.1:5173 http://127.0.0.1:8000 http://localhost:8000 http://127.0.0.1:5000 https://generativelanguage.googleapis.com;
        style-src 'self' 'unsafe-inline' http://localhost:5173 http://127.0.0.1:5173 https://fonts.bunny.net;
        font-src 'self' https://fonts.bunny.net;
        img-src 'self' data: blob: https: http://127.0.0.1:8000 http://localhost:8000;
        object-src 'none';">

    <title>{{ config('app.name', 'LungCare AI') }} - Trợ lý AI y tế</title>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in-up { animation: fadeInUp .28s ease-out forwards; }

        #chat-content::-webkit-scrollbar { width: 5px; }
        #chat-content::-webkit-scrollbar-track { background: transparent; }
        #chat-content::-webkit-scrollbar-thumb { background: #bae6fd; border-radius: 999px; }

        .ai-content ul { list-style-type: disc; margin-left: 1.25rem; margin-bottom: .5rem; }
        .ai-content ol { list-style-type: decimal; margin-left: 1.25rem; margin-bottom: .5rem; }
        .ai-content p { margin-bottom: .5rem; }
        .ai-content strong { font-weight: 800; color: #06488f; }
        .ai-content pre { background: #f1f5f9; padding: 1rem; border-radius: .5rem; overflow-x: auto; margin-bottom: .5rem; font-family: monospace; }

        .bg-indigo-600,
        .hover\:bg-indigo-700:hover,
        .focus\:bg-indigo-700:focus,
        .active\:bg-indigo-900:active { background-color: #06488f !important; }
        .text-indigo-600,
        .text-indigo-500,
        .hover\:text-indigo-600:hover,
        .hover\:text-indigo-800:hover { color: #06488f !important; }
        .bg-indigo-50 { background-color: #e0f2fe !important; }
        .bg-\[\#f8fafc\] { background-color: #eef8ff !important; }
        .border-indigo-100 { border-color: #bae6fd !important; }
        .shadow-indigo-100,
        .shadow-indigo-200 { --tw-shadow-color: rgba(14, 165, 233, .18) !important; }
        .focus\:ring-indigo-500:focus { --tw-ring-color: #0ea5e9 !important; }
        .rounded-\[2\.5rem\],
        .rounded-\[2rem\],
        .rounded-\[1\.5rem\] { border-radius: .5rem !important; }
    </style>
</head>
<body class="font-sans antialiased text-slate-800">
    <div class="min-h-screen bg-[#eef8ff]">
        @include('layouts.navigation')

        @isset($header)
            <header class="border-b border-sky-100 bg-white/85 shadow-sm backdrop-blur">
                <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main>
            {{ $slot }}
        </main>
    </div>

    <div id="chat-wrapper" class="fixed bottom-6 right-6 z-50">
        <button id="chat-toggle" class="flex h-16 w-16 items-center justify-center rounded-lg border-4 border-white bg-[#06488f] text-white shadow-2xl shadow-sky-200 transition hover:scale-105 hover:bg-[#053a73]">
            <svg id="icon-open" class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-5l-5 5v-5z"/></svg>
            <svg id="icon-close" class="hidden h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>

        <div id="chat-box" class="absolute bottom-20 right-0 hidden w-[22rem] origin-bottom-right overflow-hidden rounded-lg border border-sky-100 bg-white shadow-2xl shadow-sky-200 transition-all duration-300 md:w-[26rem]">
            <div class="flex items-center gap-4 bg-gradient-to-r from-[#06488f] to-sky-600 p-5 text-white">
                <div class="relative flex h-12 w-12 items-center justify-center rounded-lg bg-white/15 ring-1 ring-white/20">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17 9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/></svg>
                    <span class="absolute -right-1 -top-1 h-3 w-3 rounded-full border-2 border-[#06488f] bg-emerald-400"></span>
                </div>
                <div>
                    <h4 class="text-base font-black">Trợ lý phân tích y tế</h4>
                    <p id="chat-context-label" class="text-xs font-bold uppercase tracking-widest text-sky-100">Sẵn sàng hỗ trợ</p>
                </div>
            </div>

            <div id="chat-content" class="h-96 space-y-4 overflow-y-auto bg-slate-50/70 p-5">
                <div class="flex justify-start animate-fade-in-up">
                    <div class="max-w-[85%] rounded-lg rounded-tl-none border border-slate-100 bg-white p-4 text-sm leading-relaxed text-slate-700 shadow-sm">
                        Chào bác sĩ, tôi có thể hỗ trợ đọc nhanh dữ liệu, giải thích kết quả và gợi ý nội dung tư vấn.
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 border-t border-slate-100 bg-white p-4">
                <input id="chat-input" type="text" placeholder="Nhập câu hỏi..." class="h-12 min-w-0 flex-1 rounded-lg border-0 bg-slate-100 px-4 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-sky-500">
                <button id="btn-send-global" class="flex h-12 w-12 items-center justify-center rounded-lg bg-[#06488f] text-white shadow-sm transition hover:bg-[#053a73] active:scale-95">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19 9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        if (typeof marked !== 'undefined') {
            marked.setOptions({ gfm: true, breaks: true, mangle: false, headerIds: false });
        }

        const chatToggle = document.getElementById('chat-toggle');
        const chatBox = document.getElementById('chat-box');
        const chatInput = document.getElementById('chat-input');
        const btnSend = document.getElementById('btn-send-global');
        const chatContent = document.getElementById('chat-content');
        const chatContextLabel = document.getElementById('chat-context-label');
        let activeGlobalPatientId = null;

        function appendMessage(role, text, id = '') {
            const isUser = role === 'user';
            const safeText = text || '';
            const contentHTML = (!isUser && typeof marked !== 'undefined') ? marked.parse(safeText) : safeText.replace(/\n/g, '<br>');

            const html = `
                <div class="flex ${isUser ? 'justify-end' : 'justify-start'} animate-fade-in-up">
                    <div ${id ? `id="${id}"` : ''} class="${isUser ? 'bg-[#06488f] text-white rounded-tr-none' : 'bg-white border border-slate-100 text-slate-700 rounded-tl-none ai-content'} max-w-[85%] rounded-lg p-4 text-sm leading-relaxed shadow-sm">
                        ${contentHTML}
                    </div>
                </div>`;
            chatContent.insertAdjacentHTML('beforeend', html);
            chatContent.scrollTop = chatContent.scrollHeight;
        }

        function openGlobalChat(patientId = null) {
            activeGlobalPatientId = patientId;
            chatContextLabel.textContent = patientId ? `Đang hỗ trợ hồ sơ #${patientId}` : 'Sẵn sàng hỗ trợ';
            chatBox.classList.remove('hidden');
            document.getElementById('icon-open').classList.add('hidden');
            document.getElementById('icon-close').classList.remove('hidden');
            chatInput.focus();
        }

        window.openGlobalChat = openGlobalChat;

        chatToggle.addEventListener('click', () => {
            chatBox.classList.toggle('hidden');
            document.getElementById('icon-open').classList.toggle('hidden');
            document.getElementById('icon-close').classList.toggle('hidden');
            if (!chatBox.classList.contains('hidden')) chatInput.focus();
        });

        async function sendGlobalMessage() {
            const message = chatInput.value.trim();
            if (!message) return;

            appendMessage('user', message);
            chatInput.value = '';

            const loadingId = 'loading-' + Date.now();
            appendMessage('assistant', '<span class="animate-pulse">Đang xử lý...</span>', loadingId);

            try {
                const response = await fetch('{{ route("ai.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message: message,
                        patient_id: activeGlobalPatientId
                    })
                });

                const data = await response.json();
                const targetBubble = document.getElementById(loadingId);
                targetBubble.innerHTML = typeof marked !== 'undefined' ? marked.parse(data.reply || 'AI chưa trả về nội dung.') : (data.reply || 'AI chưa trả về nội dung.');
            } catch (e) {
                document.getElementById(loadingId).innerHTML = '<span class="font-bold text-red-600">Lỗi kết nối AI. Vui lòng thử lại.</span>';
            }

            chatContent.scrollTop = chatContent.scrollHeight;
        }

        btnSend.addEventListener('click', sendGlobalMessage);
        chatInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') sendGlobalMessage(); });
    </script>
</body>
</html>
