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
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-2xl font-bold text-sm flex items-center">
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
                        <span class="px-3 py-1 bg-emerald-50 rounded-lg text-xs font-bold text-emerald-600 uppercase tracking-widest">Tổng lượt quét: {{ $patient->scans->count() }}</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('patients.edit', $patient->id) }}" class="p-3 bg-gray-50 rounded-2xl hover:bg-orange-50 hover:text-orange-600 transition-all text-gray-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                </div>
            </div>

            <div class="relative">
                <h4 class="text-lg font-black text-gray-800 mb-8 flex items-center uppercase tracking-widest">
                    <span class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mr-3 text-sm">📅</span>
                    Lịch sử chẩn đoán (Timeline)
                </h4>

                @if($patient->scans->isEmpty())
                    <div class="bg-white p-16 text-center rounded-[3rem] border border-dashed border-gray-200">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-gray-400 font-bold italic text-lg">Bệnh nhân chưa có dữ liệu quét hình ảnh.</p>
                    </div>
                @else
                    <div class="absolute left-4 md:left-8 top-16 bottom-0 w-0.5 bg-gray-200"></div>

                    <div class="space-y-12 relative">
                        @foreach($patient->scans->sortByDesc('created_at') as $scan)
                        @php
                            // Tự động xác định màu sắc dựa trên kết quả
                            $isMalignant = str_contains(strtolower($scan->prediction), 'malignant');
                            $isUncertain = str_contains(strtolower($scan->prediction), 'uncertain');
                            
                            $colorClass = $isMalignant ? 'red' : ($isUncertain ? 'orange' : 'emerald');
                            $statusText = $isMalignant ? 'PHÁT HIỆN BẤT THƯỜNG' : ($isUncertain ? 'CẦN KIỂM TRA THÊM' : 'BÌNH THƯỜNG');
                        @endphp

                        <div class="relative pl-12 md:pl-20">
                            {{-- Chấm Timeline đổi màu --}}
                            <div class="absolute left-[13px] md:left-[29px] top-0 w-2.5 h-2.5 rounded-full border-4 border-white ring-4 ring-{{$colorClass}}-500 bg-{{$colorClass}}-500 z-10 shadow-sm"></div>
                            
                            <div class="absolute left-0 md:left-24 -top-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ $scan->created_at->format('M d, Y @ H:i') }}
                            </div>

                            <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-50 flex flex-col md:flex-row overflow-hidden hover:shadow-2xl hover:shadow-indigo-100 transition-all duration-300 group">
                                
                                <div class="md:w-1/4 relative group overflow-hidden bg-black flex items-center justify-center min-h-[200px]">
                                    <img src="{{ str_starts_with($scan->image_path, 'http') ? $scan->image_path : asset('storage/' . trim($scan->image_path, '/')) }}" 
                                         class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500 cursor-pointer" 
                                         onclick="window.open(this.src)"
                                         onerror="this.src='https://placehold.co/600x400?text=Loi+Duong+Dan+Anh'">
                                    
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                    <div class="absolute bottom-3 left-3 text-white">
                                        <p class="text-[10px] font-bold uppercase opacity-70">Mã lượt quét</p>
                                        <p class="text-xs font-black tracking-widest">#{{ $scan->id }}</p>
                                    </div>
                                </div>

                                <div class="md:w-3/4 p-6 md:p-8 flex flex-col justify-between">
                                    <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                                        <div>
                                            <span class="text-[10px] font-black text-{{$colorClass}}-500 uppercase tracking-[0.2em]">Kết quả chẩn đoán AI</span>
                                            <h4 class="text-3xl font-black mt-1 tracking-tighter text-{{$colorClass}}-700">
                                                {{ $statusText }}
                                                <span class="text-sm font-medium ml-2 opacity-50">({{ strtoupper($scan->prediction) }})</span>
                                            </h4>
                                        </div>
                                        <div class="bg-{{$colorClass}}-50 px-6 py-3 rounded-2xl border border-{{$colorClass}}-100 text-center min-w-[120px]">
                                            <p class="text-[10px] font-bold text-{{$colorClass}}-400 uppercase tracking-widest">Độ tin cậy</p>
                                            <p class="text-2xl font-black text-{{$colorClass}}-600">
                                                {{ $scan->confidence_score ?? $scan->confidence ?? '0' }}%
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-6 p-4 bg-[#fdfaff] rounded-2xl border border-indigo-50/50 relative">
                                        <span class="absolute -top-3 left-4 px-2 bg-white text-[10px] font-black text-indigo-400 uppercase italic">Ghi chú lâm sàng</span>
                                        <p class="text-sm text-gray-600 font-medium leading-relaxed">
                                            @if($isUncertain)
                                                Hệ thống phát hiện dấu hiệu nghi vấn nhưng chưa đủ dữ liệu khẳng định. Khuyến nghị kiểm tra chuyên sâu hoặc hội chẩn.
                                            @else
                                                {{ $scan->doctor_comments ?? 'Hệ thống tự động phân tích qua mô hình ResNet50.' }}
                                            @endif
                                        </p>
                                    </div>

                                    <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
                                        <div class="flex gap-3">
                                            <a href="{{ route('patients.exportPDF', $scan->id) }}" class="inline-flex items-center px-5 py-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all text-xs font-bold uppercase tracking-widest">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                PDF
                                            </a>

                                            <form action="{{ route('scans.destroy', $scan->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa lượt quét này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-gray-50 text-gray-400 rounded-xl hover:bg-red-50 hover:text-red-500 transition-all text-xs font-bold uppercase tracking-widest">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Xóa
                                                </button>
                                            </form>
                                        </div>

                                        <div class="text-[10px] font-bold text-gray-300 uppercase italic">
                                            AI hỗ trợ chẩn đoán v1.2 (CLAHE Enabled)
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


