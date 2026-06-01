<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('patients.index') }}" class="p-2 bg-white rounded-xl shadow-sm hover:bg-gray-50 transition-all group">
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight">Hồ sơ chi tiết</h2>
                    <p class="text-sm text-gray-500 font-medium italic">Quản lý lịch sử chẩn đoán y khoa</p>
                </div>
            </div>
            
            <a href="{{ route('patients.scan', $patient->id) }}" 
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-[1.5rem] font-bold text-sm shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Thực hiện Quét AI mới
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-2xl font-bold text-sm flex items-center animate-fade-in-up">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-gray-200/50 border border-gray-100 mb-10 flex flex-wrap gap-8 items-center">
                <div class="w-20 h-20 rounded-[2rem] bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-3xl font-black shadow-lg">
                    {{ substr($patient->name, 0, 1) }}
                </div>
                <div class="flex-grow">
                    <h3 class="text-2xl font-black text-gray-900">{{ $patient->name }}</h3>
                    <div class="flex flex-wrap gap-4 mt-2">
                        <span class="px-3 py-1 bg-gray-100 rounded-lg text-xs font-bold text-gray-500 uppercase tracking-widest">#{{ $patient->patient_code }}</span>
                        <span class="px-3 py-1 bg-blue-50 rounded-lg text-xs font-bold text-blue-600 uppercase tracking-widest">{{ $patient->gender == 'Male' ? 'Nam' : 'Nữ' }} • {{ $patient->age }} tuổi</span>
                        <span class="px-3 py-1 bg-emerald-50 rounded-lg text-xs font-bold text-emerald-600 uppercase tracking-widest">Lượt quét: {{ $patient->scans->count() }}</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('patients.edit', $patient->id) }}" class="p-4 bg-gray-50 rounded-2xl hover:bg-indigo-50 hover:text-indigo-600 transition-all text-gray-400 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                </div>
            </div>

            <div class="relative">
                <h4 class="text-lg font-black text-gray-800 mb-8 flex items-center uppercase tracking-widest">
                    <span class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center mr-3 text-sm shadow-indigo-200 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    Timeline chẩn đoán
                </h4>

                @if($patient->scans->isEmpty())
                    <div class="bg-white p-20 text-center rounded-[3rem] border-2 border-dashed border-gray-200">
                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-gray-400 font-bold italic text-xl">Chưa có lịch sử quét hình ảnh.</p>
                        <a href="{{ route('patients.scan', $patient->id) }}" class="mt-4 inline-block text-indigo-600 font-bold hover:underline">Bắt đầu quét ngay →</a>
                    </div>
                @else
                    {{-- Đường kẻ Timeline dọc --}}
                    <div class="absolute left-4 md:left-8 top-16 bottom-0 w-1 bg-gradient-to-b from-gray-200 to-transparent rounded-full"></div>

                    <div class="space-y-12 relative">
                        @foreach($patient->scans->sortByDesc('created_at') as $scan)
                        @php
                            $isMalignant = str_contains(strtolower($scan->prediction), 'malignant');
                            $isUncertain = str_contains(strtolower($scan->prediction), 'uncertain');
                            
                            $color = $isMalignant ? 'red' : ($isUncertain ? 'orange' : 'emerald');
                            $statusText = $isMalignant ? 'PHÁT HIỆN BẤT THƯỜNG' : ($isUncertain ? 'CẦN KIỂM TRA THÊM' : 'BÌNH THƯỜNG');
                            
                            // Tailwind dynamic classes (bảo đảm Tailwind compile các class này)
                            $ringColor = "ring-$color-500";
                            $bgColor = "bg-$color-500";
                            $textColor = "text-$color-700";
                            $badgeBg = "bg-$color-50";
                            $badgeBorder = "border-$color-100";
                        @endphp

                        <div class="relative pl-12 md:pl-20 animate-fade-in-up">
                            {{-- Chấm Timeline --}}
                            <div class="absolute left-[11px] md:left-[27px] top-0 w-4 h-4 rounded-full border-4 border-white ring-4 {{ $ringColor }} {{ $bgColor }} z-10 shadow-md"></div>
                            
                            <div class="absolute left-0 md:left-24 -top-8 text-[11px] font-black text-gray-400 uppercase tracking-widest bg-[#f8fafc] pr-4">
                                {{ $scan->created_at->format('d M, Y \a\t H:i') }}
                            </div>

                            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 flex flex-col md:flex-row overflow-hidden hover:shadow-2xl hover:shadow-indigo-100 transition-all duration-500 group">
                                
                                {{-- Hình ảnh Scan --}}
                                <div class="md:w-1/3 relative group overflow-hidden bg-black flex items-center justify-center min-h-[250px]">
                                    <img src="{{ str_starts_with($scan->image_path, 'http') ? $scan->image_path : asset('storage/' . trim($scan->image_path, '/')) }}" 
                                         class="w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700 cursor-zoom-in" 
                                         onclick="window.open(this.src)"
                                         onerror="this.src='https://placehold.co/600x400?text=Anh+Y+Khoa'">
                                    
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-60"></div>
                                    <div class="absolute bottom-4 left-4 text-white">
                                        <p class="text-[10px] font-bold uppercase opacity-70 tracking-tighter">Scan ID</p>
                                        <p class="text-sm font-black tracking-widest">#SCAN-{{ $scan->id }}</p>
                                    </div>
                                </div>

                                {{-- Nội dung kết quả --}}
                                <div class="md:w-2/3 p-8 flex flex-col justify-between bg-white">
                                    <div>
                                        <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                                            <div>
                                                <span class="text-[10px] font-black {{ 'text-'.$color.'-500' }} uppercase tracking-[0.25em]">AI Diagnostic Result</span>
                                                <h4 class="text-3xl font-black mt-1 tracking-tight {{ $textColor }}">
                                                    {{ $statusText }}
                                                </h4>
                                                <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">Model: ResNet50 Architecture</p>
                                            </div>
                                            <div class="{{ $badgeBg }} px-6 py-4 rounded-[1.5rem] border {{ $badgeBorder }} text-center min-w-[140px] shadow-sm">
                                                <p class="text-[10px] font-bold {{ 'text-'.$color.'-400' }} uppercase tracking-widest">Độ tin cậy</p>
                                                <p class="text-3xl font-black {{ 'text-'.$color.'-600' }}">
                                                    {{ number_format($scan->confidence_score ?? $scan->confidence ?? 0, 1) }}%
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mt-8 p-5 bg-gray-50 rounded-[1.5rem] border border-gray-100 relative">
                                            <span class="absolute -top-3 left-6 px-3 bg-white text-[10px] font-black text-indigo-500 uppercase italic border border-gray-100 rounded-full">Ghi chú lâm sàng</span>
                                            <p class="text-sm text-gray-600 font-semibold leading-relaxed italic">
                                                "{{ $scan->doctor_comments ?? 'Dữ liệu được phân tích tự động qua mô hình AI deep learning. Không có ghi chú bổ sung từ bác sĩ.' }}"
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-10 flex flex-wrap items-center justify-between gap-6 pt-6 border-t border-gray-50">
                                        <div class="flex gap-3">
                                            <a href="{{ route('patients.exportPDF', $scan->id) }}" class="inline-flex items-center px-6 py-3 bg-red-50 text-red-600 rounded-2xl hover:bg-red-600 hover:text-white transition-all text-xs font-black uppercase tracking-widest shadow-sm">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Xuất PDF
                                            </a>

                                            <form action="{{ route('scans.destroy', $scan->id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa vĩnh viễn lượt quét này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-3 bg-gray-50 text-gray-400 rounded-2xl hover:bg-red-50 hover:text-red-500 transition-all shadow-sm">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="flex items-center text-[10px] font-bold text-gray-300 uppercase tracking-widest italic">
                                            <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></span>
                                            Verified by AI System v1.2
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>