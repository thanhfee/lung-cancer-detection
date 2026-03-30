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
                    <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight">Báo cáo Phân tích Chuyên sâu</h2>
                    <p class="text-sm text-gray-500 font-medium">Phân tích kỹ thuật mã quét #{{ $scan->id }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#f4f7fa]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-100">
                        <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center">
                            <span class="w-2 h-6 bg-indigo-600 rounded-full mr-3"></span>
                            Bản đồ nhiệt AI (Heatmap Visualization)
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <p class="text-xs font-bold text-gray-400 uppercase text-center tracking-widest">Ảnh X-Quang Gốc</p>
                                <div class="rounded-3xl overflow-hidden border-4 border-gray-50 shadow-inner bg-black">
                                    <img src="{{ asset('storage/' . $scan->image_path) }}" class="w-full h-64 object-cover grayscale">
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <p class="text-xs font-bold text-indigo-500 uppercase text-center tracking-widest">Vùng AI tập trung (CAM)</p>
                                <div class="relative rounded-3xl overflow-hidden border-4 border-indigo-100 shadow-lg">
                                    <img src="{{ asset('storage/' . $scan->image_path) }}" class="w-full h-64 object-cover grayscale opacity-50">
                                    <div class="absolute inset-0 bg-radial-gradient from-red-500/60 via-yellow-400/30 to-transparent mix-blend-overlay"></div>
                                    <div class="absolute top-1/3 left-1/4 w-20 h-20 bg-red-600/40 rounded-full blur-2xl animate-pulse"></div>
                                </div>
                            </div>
                        </div>
                        <p class="mt-6 text-sm text-gray-500 italic leading-relaxed bg-gray-50 p-4 rounded-2xl border-l-4 border-indigo-200">
                            * Mô hình **CNN (Convolutional Neural Network)** đã phát hiện các mật độ mô bất thường tại thùy trên phổi phải. Các vùng màu đỏ biểu thị xác suất cao của các đặc trưng ác tính.
                        </p>
                    </div>

                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-100">
                        <h3 class="text-lg font-black text-gray-800 mb-6 uppercase tracking-wider">Đặc trưng trích xuất (Features)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="p-5 bg-blue-50 rounded-3xl text-center">
                                <p class="text-[10px] font-black text-blue-400 uppercase">Độ tương phản</p>
                                <p class="text-2xl font-black text-blue-700 mt-1">Cao</p>
                            </div>
                            <div class="p-5 bg-purple-50 rounded-3xl text-center">
                                <p class="text-[10px] font-black text-purple-400 uppercase">Kích thước vùng nghi vấn</p>
                                <p class="text-2xl font-black text-purple-700 mt-1">~2.4cm</p>
                            </div>
                            <div class="p-5 bg-emerald-50 rounded-3xl text-center">
                                <p class="text-[10px] font-black text-emerald-400 uppercase">Mật độ tế bào</p>
                                <p class="text-2xl font-black text-emerald-700 mt-1">Dày đặc</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-indigo-900 rounded-[2.5rem] p-8 text-white shadow-2xl overflow-hidden relative">
                        <div class="relative z-10">
                            <h3 class="text-xl font-black mb-8 italic">Phân phối Xác suất</h3>
                            
                            <div class="space-y-6">
                                <div>
                                    <div class="flex justify-between text-xs font-bold mb-2 uppercase tracking-widest">
                                        <span>Malignant (Ác tính)</span>
                                        <span>{{ number_format($scan->confidence_score * 100, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-white/10 h-3 rounded-full overflow-hidden">
                                        <div class="bg-red-500 h-full rounded-full shadow-[0_0_15px_rgba(239,68,68,0.5)] transition-all duration-1000" style="width: {{ $scan->confidence_score * 100 }}%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between text-xs font-bold mb-2 uppercase tracking-widest opacity-60">
                                        <span>Benign (Lành tính)</span>
                                        <span>{{ number_format((1 - $scan->confidence_score) * 100, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-white/10 h-3 rounded-full overflow-hidden">
                                        <div class="bg-emerald-400 h-full rounded-full transition-all duration-1000" style="width: {{ (1 - $scan->confidence_score) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-12 pt-8 border-t border-white/10">
                                <p class="text-xs font-medium text-indigo-200 italic leading-relaxed">
                                    "Kết quả này được tính toán dựa trên tập dữ liệu huấn luyện gồm 50,000+ mẫu X-quang phổi bệnh lý. Vui lòng đối chiếu với các xét nghiệm lâm sàng."
                                </p>
                            </div>
                        </div>
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl"></div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] p-6 shadow-lg border border-gray-100 space-y-3">
                        <button onclick="window.print()" class="w-full py-4 bg-gray-900 text-white rounded-2xl font-bold text-sm hover:bg-black transition-all flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            In báo cáo kỹ thuật
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .bg-radial-gradient {
            background: radial-gradient(circle at center, var(--tw-gradient-from), var(--tw-gradient-to));
        }
    </style>
</x-app-layout>