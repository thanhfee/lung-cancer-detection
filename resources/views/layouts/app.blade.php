<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Trợ lý AI Y tế</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.3s ease-out forwards; }
        
        #chat-content::-webkit-scrollbar { width: 4px; }
        #chat-content::-webkit-scrollbar-track { background: transparent; }
        #chat-content::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }

        /* Tùy chỉnh CSS cho Markdown để không bị vỡ layout */
        .ai-content ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 0.5rem; }
        .ai-content ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 0.5rem; }
        .ai-content p { margin-bottom: 0.5rem; }
        .ai-content strong { font-weight: 700; color: #1e1b4b; }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main>
            {{ $slot }}
        </main>
    </div>

    <div id="chat-wrapper" class="fixed bottom-6 right-6 z-50">
        <button id="chat-toggle" class="w-16 h-16 bg-indigo-600 rounded-full shadow-2xl flex items-center justify-center text-white hover:bg-indigo-700 transition-all transform hover:scale-110 border-4 border-white">
            <svg id="icon-open" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            <svg id="icon-close" class="w-8 h-8 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div id="chat-box" class="hidden absolute bottom-20 right-0 w-[22rem] md:w-[26rem] bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden flex flex-col transition-all duration-300 transform scale-95 origin-bottom-right">
            <div class="bg-indigo-600 p-6 text-white flex items-center space-x-4">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md relative">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-400 border-2 border-indigo-600 rounded-full animate-pulse"></span>
                </div>
                <div>
                    <h4 class="font-black text-base">Trợ lý Phân tích Y tế</h4>
                    <p class="text-[10px] opacity-80 uppercase tracking-widest font-bold italic">Powered by Gemini 3.1</p>
                </div>
            </div>
            
            <div id="chat-content" class="h-96 overflow-y-auto p-6 space-y-4 bg-gray-50/50">
                <div class="flex justify-start animate-fade-in-up">
                    <div class="bg-white border border-gray-100 p-4 rounded-2xl rounded-tl-none shadow-sm max-w-[85%] text-sm text-gray-700 leading-relaxed">
                        Chào bác sĩ Thành! Tôi đã sẵn sàng hỗ trợ bạn phân tích bệnh án này.
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white border-t border-gray-50 flex items-center space-x-2">
                <input id="chat-input" type="text" placeholder="Nhập câu hỏi về bệnh nhân..." class="flex-1 border-none bg-gray-100 rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                <button id="btn-send-global" class="w-12 h-12 bg-indigo-600 text-white rounded-2xl hover:bg-indigo-700 flex items-center justify-center transition-all active:scale-95 shadow-lg shadow-indigo-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        const chatToggle = document.getElementById('chat-toggle');
        const chatBox = document.getElementById('chat-box');
        const chatInput = document.getElementById('chat-input');
        const btnSend = document.getElementById('btn-send-global');
        const chatContent = document.getElementById('chat-content');

        // Cấu hình Marked.js để xử lý xuống dòng tốt hơn
        marked.setOptions({ gfm: true, breaks: true });

        // 1. Tối ưu việc lấy Patient ID từ URL bằng Regex
        function getPatientIdFromUrl() {
            const path = window.location.pathname;
            const match = path.match(/\/patients\/([0-9]+)/);
            return match ? match[1] : null;
        }

        // 2. Hàm chèn tin nhắn (Tích hợp Markdown cho AI)
        function appendMessage(role, text, id = '') {
            const isUser = role === 'user';
            const contentHTML = (!isUser) ? marked.parse(text) : text.replace(/\n/g, '<br>');
            
            const html = `
                <div class="flex ${isUser ? 'justify-end' : 'justify-start'} mb-4 animate-fade-in-up">
                    <div ${id ? `id="${id}"` : ''} class="${isUser ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white border border-gray-100 text-gray-700 rounded-tl-none ai-content'} p-4 rounded-2xl shadow-sm max-w-[85%] text-sm leading-relaxed overflow-x-auto">
                        ${contentHTML}
                    </div>
                </div>`;
            chatContent.insertAdjacentHTML('beforeend', html);
            chatContent.scrollTop = chatContent.scrollHeight;
        }

        // 3. Hàm gọi API lấy lịch sử chat cũ
        async function loadChatHistory(patientId) {
            try {
                chatContent.innerHTML = '<div class="text-center text-[10px] text-gray-400 animate-pulse py-4 italic">Đang truy xuất hồ sơ tư vấn...</div>';
                
                const response = await fetch(`{{ url('/api/chat-history') }}/${patientId}`);
                const history = await response.json();

                chatContent.innerHTML = ''; 

                if (history.length === 0) {
                    appendMessage('assistant', 'Chào bác sĩ! Tôi chưa tìm thấy lịch sử tư vấn cho ca bệnh này. Hãy đặt câu hỏi để bắt đầu.');
                } else {
                    history.forEach(item => {
                        appendMessage('user', item.user_message);
                        appendMessage('assistant', item.ai_response);
                    });
                }
            } catch (e) {
                console.error("Lỗi load history:", e);
                chatContent.innerHTML = '<div class="text-center text-[10px] text-red-400 py-4 italic">Lỗi kết nối dữ liệu y tế.</div>';
            }
        }

        // 4. Toggle Chatbox
        chatToggle.addEventListener('click', async () => {
            const isOpening = chatBox.classList.contains('hidden');
            chatBox.classList.toggle('hidden');
            document.getElementById('icon-open').classList.toggle('hidden');
            document.getElementById('icon-close').classList.toggle('hidden');
            
            if (isOpening) {
                chatInput.focus();
                const patientId = getPatientIdFromUrl();
                // Chỉ load nếu có ID bệnh nhân và khung chat chưa được đổ dữ liệu
                if (patientId && chatContent.children.length <= 1) {
                    await loadChatHistory(patientId);
                }
            }
        });

        // 5. Gửi tin nhắn
        async function sendGlobalMessage() {
            const message = chatInput.value.trim();
            const patientId = getPatientIdFromUrl();
            if (!message) return;

            appendMessage('user', message);
            chatInput.value = '';

            const loadingId = 'loading-' + Date.now();
            appendMessage('assistant', '<span class="flex space-x-1"><span class="animate-bounce">.</span><span class="animate-bounce" style="animation-delay:0.2s">.</span><span class="animate-bounce" style="animation-delay:0.4s">.</span></span>', loadingId);

            try {
                const response = await fetch('{{ route("ai.chat") }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                    },
                    body: JSON.stringify({ message: message, patient_id: patientId })
                });

                const data = await response.json();
                const targetBubble = document.getElementById(loadingId);
                
                if (response.ok) {
                    targetBubble.innerHTML = marked.parse(data.reply);
                } else {
                    targetBubble.innerHTML = '<span class="text-red-500 italic font-bold">Lỗi: ' + (data.reply || 'Vui lòng kiểm tra API Key.') + '</span>';
                }
            } catch (e) {
                document.getElementById(loadingId).innerHTML = '<span class="text-red-500 italic">Không thể kết nối đến máy chủ AI.</span>';
            }
            chatContent.scrollTop = chatContent.scrollHeight;
        }

        btnSend.addEventListener('click', sendGlobalMessage);
        chatInput.addEventListener('keypress', (e) => { 
            if (e.key === 'Enter') {
                e.preventDefault();
                sendGlobalMessage();
            }
        });
    </script>
</body>
</html>
