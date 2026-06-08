<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                {{-- Nút quay lại hồ sơ chi tiết của bệnh nhân --}}
                <a href="{{ route('patients.show', $scan->patient->id) }}" class="p-2 bg-white rounded-xl shadow-sm hover:bg-gray-50 transition-all group">
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight">Kết quả phân tích AI</h2>
                    <p class="text-sm text-gray-500 font-medium italic">Chi tiết chẩn đoán hình ảnh X-quang</p>
                </div>
            </div>

            <div class="flex flex-col gap-3 lg:min-w-[430px]">
                <a href="{{ route('patients.exportPDF', $scan->id) }}" class="inline-flex items-center px-5 py-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all text-xs font-black uppercase tracking-widest shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Xuất PDF
                </a>

                <form action="{{ route('patients.sendReportEmail', $scan->id, false) }}" method="POST" class="flex overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-sky-100">
                    @csrf
                    <input type="email" name="recipient_email" value="{{ old('recipient_email') }}" required placeholder="Gmail bệnh nhân"
                           class="min-w-0 flex-1 border-0 px-4 py-2.5 text-sm font-semibold text-slate-700 focus:ring-0">
                    <button type="submit" class="bg-[#06488f] px-4 py-2.5 text-xs font-black uppercase tracking-widest text-white transition hover:bg-[#053a73]">
                        Gửi PDF
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Thông báo thành công --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-2xl font-bold text-sm flex items-center animate-fade-in-up">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl font-bold text-sm animate-fade-in-up">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Tính toán các Class Tĩnh cho giao diện --}}
            @php
                $predictionLower = strtolower($scan->prediction);
                $isMalignant = str_contains($predictionLower, 'malignant') || str_contains($predictionLower, 'ác tính');
                $isUncertain = str_contains($predictionLower, 'uncertain') || str_contains($predictionLower, 'nghi ngờ');
                
                if ($isMalignant) {
                    $statusText = 'PHÁT HIỆN BẤT THƯỜNG';
                    $subColorText = 'text-red-500';
                    $textColor = 'text-red-700';
                    $badgeBg = 'bg-red-50';
                    $badgeBorder = 'border-red-100';
                    $barColor = 'bg-red-500';
                } elseif ($isUncertain) {
                    $statusText = 'CẦN KIỂM TRA THÊM';
                    $subColorText = 'text-orange-500';
                    $textColor = 'text-orange-700';
                    $badgeBg = 'bg-orange-50';
                    $badgeBorder = 'border-orange-100';
                    $barColor = 'bg-orange-500';
                } else {
                    $statusText = 'BÌNH THƯỜNG';
                    $subColorText = 'text-emerald-500';
                    $textColor = 'text-emerald-700';
                    $badgeBg = 'bg-emerald-50';
                    $badgeBorder = 'border-emerald-100';
                    $barColor = 'bg-emerald-500';
                }

                $confidenceValue = $scan->confidence_score ?? $scan->confidence ?? 0;
                $confidencePercent = $confidenceValue <= 1 ? $confidenceValue * 100 : $confidenceValue;
                $confidencePercent = max(0, min(100, $confidencePercent));
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Cột trái: Hiển thị hình ảnh X-quang y khoa --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-[2.5rem] p-6 shadow-xl shadow-gray-200/50 border border-gray-100 sticky top-6 text-center">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Hình ảnh X-quang gốc</h3>
                        
                        <div class="relative rounded-[2rem] overflow-hidden bg-black flex items-center justify-center min-h-[300px] border border-gray-100 shadow-inner group">
                            <img src="{{ str_starts_with($scan->image_path, 'http') ? $scan->image_path : asset('storage/' . trim($scan->image_path, '/')) }}" 
                                 class="w-full h-auto object-contain max-h-[450px] opacity-95 group-hover:scale-105 transition-all duration-500 cursor-zoom-in"
                                 onclick="window.open(this.src)"
                                 onerror="this.src='https://placehold.co/600x600?text=Khong+Tim+Thay+Anh+Y+Khoa'">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none"></div>
                        </div>
                        
                        <p class="text-xs font-bold text-gray-400 mt-4 tracking-widest uppercase">Mã số quét: #SCAN-{{ $scan->id }}</p>
                        <p class="text-[11px] text-gray-400 font-medium italic mt-1">Bấm vào ảnh để xem kích thước đầy đủ</p>
                    </div>
                </div>

                {{-- Cột phải: Thông tin chẩn đoán chi tiết của Model AI và Bác sĩ --}}
                <div class="lg:col-span-2 space-y-8">
                    
                    {{-- Box 1: Thông tin hành chính bệnh nhân --}}
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-gray-200/50 border border-gray-100 flex flex-wrap gap-6 items-center">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-black shadow-sm">
                            {{ substr($scan->patient->name, 0, 1) }}
                        </div>
                        <div class="flex-grow">
                            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Hồ sơ bệnh nhân</span>
                            <h3 class="text-xl font-black text-gray-900 mt-0.5">{{ $scan->patient->name }}</h3>
                            <div class="flex flex-wrap gap-4 mt-1.5 text-xs font-bold text-gray-500">
                                <span>Mã: <b class="text-gray-700">#{{ $scan->patient->patient_code }}</b></span>
                                <span>•</span>
                                <span>Giới tính: <b class="text-gray-700">{{ $scan->patient->gender == 'Male' ? 'Nam' : 'Nữ' }}</b></span>
                                <span>•</span>
                                <span>Tuổi: <b class="text-gray-700">{{ $scan->patient->age }} tuổi</b></span>
                            </div>
                        </div>
                    </div>

                    {{-- Box 2: Kết quả phân tích sâu từ AI --}}
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-gray-200/50 border border-gray-100 space-y-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 border-b border-gray-50 pb-6">
                            <div>
                                <span class="text-[10px] font-black {{ $subColorText }} uppercase tracking-[0.25em]">AI Diagnostic Result</span>
                                <h4 class="text-3xl font-black mt-1 tracking-tight {{ $textColor }}">
                                    {{ $statusText }}
                                </h4>
                                <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">Model kiến trúc: EfficientNet Deep Learning</p>
                            </div>
                            
                            <div class="{{ $badgeBg }} px-6 py-4 rounded-[1.5rem] border {{ $badgeBorder }} text-center min-w-[150px] shadow-sm">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Độ tin cậy</p>
                                <p class="text-3xl font-black {{ $textColor }}">
                                    {{ number_format($confidencePercent, 1) }}%
                                </p>
                            </div>
                        </div>

                        {{-- Thanh Progress mô phỏng độ chắc chắn của thuật toán --}}
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs font-black text-gray-500 uppercase tracking-wider">
                                <span>Trạng thái phân bổ trọng số AI</span>
                                <<span>{{ number_format($confidencePercent, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden shadow-inner">
                                <div class="h-full {{ $barColor }} rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $confidencePercent }}%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Thời gian thực hiện</p>
                                <p class="text-sm font-bold text-gray-700 mt-1">{{ $scan->created_at->format('d/m/Y \l\ú\c H:i:s') }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Bác sĩ phụ trách quét</p>
                                <p class="text-sm font-bold text-gray-700 mt-1">{{ $scan->doctor->name ?? 'Hệ thống tự động' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Box 3: Ghi chú lâm sàng của bác sĩ --}}
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative">
                        <span class="absolute -top-3 left-8 px-4 bg-white text-xs font-black text-indigo-600 uppercase italic border border-gray-100 rounded-full shadow-sm py-0.5">Ghi chú lâm sàng bổ sung</span>
                        
                        <div class="p-6 bg-gray-50 rounded-[1.5rem] border border-gray-100 min-h-[100px] flex items-center">
                            <p class="text-sm text-gray-600 font-semibold leading-relaxed italic w-full">
                                "{{ $scan->doctor_comments ?? 'Dữ liệu được phân tích tự động qua mô hình AI deep learning kết nối Flask API thành công. Chưa có ghi chú bổ sung từ hội đồng bác sĩ lâm sàng.' }}"
                            </p>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-gray-50">
                            <a href="{{ route('patients.show', $scan->patient->id) }}" class="inline-flex items-center text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                                </svg>
                                Quay lại Timeline hồ sơ bệnh nhân
                            </a>
                            
                            <div class="flex items-center text-[10px] font-bold text-gray-300 uppercase tracking-widest italic">
                                <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></span>
                                Verified by AI System v1.2
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
