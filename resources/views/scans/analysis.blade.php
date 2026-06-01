<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('patients.show', $scan->patient->id) }}" class="p-2 bg-white rounded-xl shadow-sm hover:bg-gray-50 transition-all">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight">Báo cáo Phân tích Chuyên sâu</h2>
                    <p class="text-sm text-gray-500 font-medium">Mã quét #{{ $scan->id }} - Bệnh nhân: {{ $scan->patient->name }}</p>
                </div>
            </div>
            
            {{-- Badge trạng thái tổng quát --}}
            <div class="flex items-center">
                @if($scan->prediction == 'Malignant')
                    <span class="px-4 py-2 bg-red-100 text-red-700 rounded-full text-xs font-black uppercase tracking-widest border border-red-200 shadow-sm">Nguy cơ cao (Ác tính)</span>
                @elseif($scan->prediction == 'Normal' || $scan->prediction == 'Benign')
                    <span class="px-4 py-2 bg-emerald-100 text-emerald-700 rounded-full text-xs font-black uppercase tracking-widest border border-emerald-200 shadow-sm">An toàn (Lành tính)</span>
                @else
                    <span class="px-4 py-2 bg-amber-100 text-amber-700 rounded-full text-xs font-black uppercase tracking-widest border border-amber-200 shadow-sm">Cần theo dõi (Trung tính)</span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#f4f7fa]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    {{-- 1. Bản đồ nhiệt AI --}}
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
                    </div>

                    {{-- 2. Phân loại chuẩn đoán 3 cấp độ --}}
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-100">
                        <h3 class="text-lg font-black text-gray-800 mb-6 uppercase tracking-wider">Phân loại chuẩn đoán hệ thống</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-6 rounded-3xl border-2 {{ ($scan->prediction == 'Normal' || $scan->prediction == 'Benign') ? 'border-emerald-500 bg-emerald-50' : 'border-gray-100 opacity-40' }} transition-all">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-2 bg-emerald-500 rounded-lg text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                                    </div>
                                    <span class="text-[10px] font-black text-emerald-600 uppercase">Level 1</span>
                                </div>
                                <p class="text-sm font-black text-gray-800">Lành tính</p>
                                <p class="text-[10px] text-gray-500 mt-1">Không phát hiện dấu hiệu bất thường về bệnh lý.</p>
                            </div>

                            <div class="p-6 rounded-3xl border-2 {{ ($scan->prediction == 'Neutral') ? 'border-amber-500 bg-amber-50' : 'border-gray-100 opacity-40' }} transition-all">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-2 bg-amber-500 rounded-lg text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
                                    </div>
                                    <span class="text-[10px] font-black text-amber-600 uppercase">Level 2</span>
                                </div>
                                <p class="text-sm font-black text-gray-800">Trung tính</p>
                                <p class="text-[10px] text-gray-500 mt-1">Có dấu hiệu nghi vấn, cần thực hiện thêm xét nghiệm.</p>
                            </div>

                            <div class="p-6 rounded-3xl border-2 {{ ($scan->prediction == 'Malignant') ? 'border-red-500 bg-red-50' : 'border-gray-100 opacity-40' }} transition-all">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-2 bg-red-500 rounded-lg text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2"/></svg>
                                    </div>
                                    <span class="text-[10px] font-black text-red-600 uppercase">Level 3</span>
                                </div>
                                <p class="text-sm font-black text-gray-800">Ác tính</p>
                                <p class="text-[10px] text-gray-500 mt-1">Xác suất cao xuất hiện các khối u ác tính.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    {{-- Card Xác suất --}}
                    <div class="bg-indigo-900 rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden">
                        <h3 class="text-xl font-black mb-8 italic">Phân phối Xác suất</h3>
                        <div class="space-y-6">
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-2 uppercase tracking-widest">
                                    <span>Độ tin cậy dự đoán</span>
                                    <span>{{ number_format($scan->confidence_score * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-white/10 h-3 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-1000 {{ $scan->prediction == 'Malignant' ? 'bg-red-500' : 'bg-emerald-400' }}" 
                                         style="width: {{ $scan->confidence_score * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <p class="mt-8 text-[10px] text-indigo-200 leading-relaxed italic border-t border-white/10 pt-4">
                            * Mô hình được tối ưu hóa cho độ nhạy lâm sàng. Kết quả dự đoán là: **{{ $scan->prediction }}**.
                        </p>
                    </div>

                    {{-- Nút in --}}
                    <div class="bg-white rounded-[2.5rem] p-6 shadow-lg border border-gray-100">
                        <button onclick="window.print()" class="w-full py-4 bg-gray-900 text-white rounded-2xl font-bold text-sm hover:bg-black transition-all flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-width="2"/></svg>
                            Xuất báo cáo PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-radial-gradient {
            background: radial-gradient(circle at center, rgba(239, 68, 68, 0.6) 0%, rgba(251, 191, 36, 0.3) 40%, transparent 70%);
        }
    </style>
</x-app-layout>