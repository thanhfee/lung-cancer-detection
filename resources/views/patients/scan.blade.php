<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('patients.show', $patient->id) }}" class="p-2 bg-white rounded-xl shadow-sm hover:bg-gray-50 transition-all">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight">Chẩn đoán Hình ảnh AI</h2>
                    <p class="text-sm text-gray-500 font-medium italic">Bệnh nhân: {{ $patient->name }} (#{{ $patient->patient_code }})</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#f8fafc]">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
    <div style="background: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <strong>Lỗi Validation!</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

            <form action="{{ route('scans.store') }}" method="POST" enctype="multipart/form-data" id="scan-form">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                    <div class="lg:col-span-3 space-y-6">
                        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-100">
                            <label class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-6 block text-center">Tải lên ảnh X-Quang Phổi</label>
                            
                            <div id="drop-area" class="relative group cursor-pointer border-4 border-dashed border-gray-100 rounded-[2rem] p-12 flex flex-col items-center justify-center hover:border-blue-400 hover:bg-blue-50/50 transition-all duration-300">
                                <input type="file" name="xray_image" id="image-input" class="hidden" accept="image/*" required onchange="previewImage(event)">
                                
                                <div id="upload-placeholder" class="text-center">
                                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="text-lg font-bold text-gray-700">Kéo thả hoặc Click để chọn ảnh</p>
                                    <p class="text-sm text-gray-400 mt-1 italic">Hỗ trợ: JPG, PNG (Max 10MB)</p>
                                </div>

                                <div id="preview-container" class="hidden w-full relative">
                                    <img id="image-preview" class="w-full h-auto rounded-2xl shadow-2xl border-4 border-white" src="">
                                    <button type="button" onclick="resetUpload(event)" class="absolute -top-4 -right-4 bg-red-500 text-white p-2 rounded-full shadow-lg hover:bg-red-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-100 h-full flex flex-col">
                            <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center">
                                <span class="flex h-3 w-3 relative mr-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                                </span>
                                Trạng thái Phân tích
                            </h3>
                            
                            <div class="space-y-4 flex-grow">
                                <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100">
                                    <p class="text-xs text-gray-600 font-medium leading-relaxed">Hệ thống AI sẽ sử dụng mô hình Deep Learning để nhận diện dấu hiệu bệnh lý từ ảnh X-Quang.</p>
                                </div>
                                <div class="p-4 border border-gray-100 rounded-2xl bg-gray-50/30">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Tên file:</span>
                                        <span id="file-name" class="text-xs font-bold truncate ml-2 text-blue-600">Chưa chọn</span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" id="btn-scan" class="mt-8 w-full py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-[1.5rem] font-black text-lg shadow-xl hover:shadow-blue-200 hover:-translate-y-1 transition-all duration-300">
                                BẮT ĐẦU QUÉT 
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="loading-overlay" class="fixed inset-0 bg-gray-900/95 backdrop-blur-xl hidden items-center justify-center z-[100]">
        <div class="text-center">
            <div class="relative inline-block mb-8">
                <div class="w-32 h-32 border-8 border-blue-500/10 border-t-blue-500 rounded-full animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-12 h-12 text-blue-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
            <h2 class="text-3xl font-black text-white tracking-[0.3em] uppercase mb-2">Đang Phân Tích</h2>
            <p class="text-blue-400 font-medium italic animate-bounce">Chờ xử lý</p>
        </div>
    </div>

    <script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('upload-placeholder');
        const container = document.getElementById('preview-container');
        const fileNameSpan = document.getElementById('file-name');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                placeholder.classList.add('hidden');
                container.classList.remove('hidden');
                fileNameSpan.textContent = input.files[0].name;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resetUpload(e) {
        e.preventDefault();
        e.stopPropagation();
        document.getElementById('image-input').value = "";
        document.getElementById('upload-placeholder').classList.remove('hidden');
        document.getElementById('preview-container').classList.add('hidden');
        document.getElementById('file-name').textContent = "Chưa chọn";
    }

    // Xử lý khi nhấn nút Quét
    document.getElementById('scan-form').onsubmit = function(e) {
        const fileInput = document.getElementById('image-input');
        if (fileInput.files.length === 0) {
            alert("Hãy chọn ảnh X-Quang trước khi quét!");
            e.preventDefault();
            return false;
        }
        // Hiện overlay loading
        const overlay = document.getElementById('loading-overlay');
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    };

    // Kích hoạt chọn file khi click vào vùng drop-area
    document.getElementById('drop-area').onclick = function(e) {
        // Chỉ kích hoạt nếu không phải nhấn vào nút X xóa ảnh
        if (!e.target.closest('button')) {
            document.getElementById('image-input').click();
        }
    };
    </script>
</x-app-layout>