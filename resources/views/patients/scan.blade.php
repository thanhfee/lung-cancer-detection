<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('patients.index') }}" class="p-2 bg-white rounded-xl shadow-sm hover:bg-gray-50 transition-all">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight">Chẩn đoán Hình ảnh AI</h2>
                    <p class="text-sm text-gray-500 font-medium italic">Bệnh nhân: {{ $patient->name }} (#{{ $patient->patient_code }})</p>
                </div>
            </div>
            <div class="hidden md:block">
                <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-2xl text-xs font-black uppercase tracking-widest border border-blue-100">
                    AI Model: LungCancer-v2.0
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#f8fafc]">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl font-bold text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl font-bold text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('scans.store') }}" method="POST" enctype="multipart/form-data" id="scan-form">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                    
                    <div class="lg:col-span-3 space-y-6">
                        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-blue-100/50 border border-gray-100 relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-4 opacity-10">
                                <svg class="w-24 h-24 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                            </div>

                            <label class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-6 block">Tải lên ảnh X-Quang Phổi</label>
                            
                            <div id="drop-area" class="relative group cursor-pointer border-4 border-dashed border-gray-100 rounded-[2rem] p-12 flex flex-col items-center justify-center hover:border-blue-400 hover:bg-blue-50/50 transition-all duration-300">
                                <input type="file" name="image" id="image-input" class="hidden" accept="image/*" required onchange="previewImage(event)">
                                
                                <div id="upload-placeholder" class="text-center">
                                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="text-lg font-bold text-gray-700">Kéo thả hoặc Click để chọn ảnh</p>
                                    <p class="text-sm text-gray-400 mt-1 font-medium italic">Hỗ trợ: JPG, PNG, DICOM (Max 10MB)</p>
                                </div>

                                <div id="preview-container" class="hidden w-full relative">
                                    <img id="image-preview" class="w-full h-auto rounded-2xl shadow-2xl border-4 border-white" src="">
                                    <button type="button" onclick="resetUpload()" class="absolute -top-4 -right-4 bg-red-500 text-white p-2 rounded-full shadow-lg hover:bg-red-600 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-blue-100/50 border border-gray-100 h-full flex flex-col">
                            <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center">
                                <span class="mr-2">⚡</span> Phân tích Kết quả
                            </h3>
                            
                            <div class="space-y-4 flex-grow">
                                <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-50">
                                    <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Quy trình AI</p>
                                    <p class="text-sm text-gray-600 leading-relaxed font-medium">Hệ thống sẽ thực hiện tiền xử lý ảnh, trích xuất đặc trưng và đưa ra dự đoán về các dấu hiệu u ác tính hoặc lành tính.</p>
                                </div>

                                <div class="p-4 border border-gray-100 rounded-2xl space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest italic font-medium">Đã tải lên:</span>
                                        <span id="file-name" class="text-xs font-bold text-gray-700 truncate max-w-[150px]">Chưa có file</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest italic font-medium">Định dạng:</span>
                                        <span id="file-size" class="text-xs font-bold text-gray-700">-</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8">
                                <button type="submit" id="btn-scan" class="w-full py-5 bg-blue-600 text-white rounded-[1.5rem] font-black text-lg shadow-xl shadow-blue-200 hover:bg-blue-700 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center space-x-3 group">
                                    <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>BẮT ĐẦU QUÉT AI</span>
                                </button>
                                <p class="text-center text-[10px] text-gray-400 mt-4 uppercase font-bold tracking-tighter">Bằng việc bấm nút, bạn xác nhận hình ảnh hợp lệ để chẩn đoán</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="loading-overlay" class="fixed inset-0 bg-gray-900/80 backdrop-blur-lg hidden items-center justify-center z-50">
        <div class="text-center">
            <div class="relative w-32 h-32 mx-auto mb-6">
                <div class="absolute inset-0 border-4 border-blue-500/20 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                <div class="absolute inset-4 bg-blue-600 rounded-full flex items-center justify-center shadow-2xl">
                    <svg class="w-10 h-10 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <h2 class="text-2xl font-black text-white tracking-widest uppercase">Đang phân tích ảnh...</h2>
            <p class="text-blue-300 mt-2 font-medium">Hệ thống AI đang trích xuất đặc trưng mô phổi</p>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('upload-placeholder');
            const container = document.getElementById('preview-container');
            const fileNameSpan = document.getElementById('file-name');
            const fileSizeSpan = document.getElementById('file-size');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    placeholder.classList.add('hidden');
                    container.classList.remove('hidden');
                    fileNameSpan.textContent = input.files[0].name;
                    fileSizeSpan.textContent = (input.files[0].size / 1024 / 1024).toFixed(2) + ' MB';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function resetUpload() {
            document.getElementById('image-input').value = "";
            document.getElementById('upload-placeholder').classList.remove('hidden');
            document.getElementById('preview-container').classList.add('hidden');
            document.getElementById('file-name').textContent = "Chưa có file";
            document.getElementById('file-size').textContent = "-";
        }

        // Kích hoạt khi bấm nút "Bắt đầu quét"
        document.getElementById('scan-form').onsubmit = function() {
            document.getElementById('loading-overlay').classList.remove('hidden');
            document.getElementById('loading-overlay').classList.add('flex');
        };

        // Click vào vùng drop-area để chọn file
        document.getElementById('drop-area').onclick = function(e) {
            if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'svg') {
                document.getElementById('image-input').click();
            }
        };
    </script>
</x-app-layout>
