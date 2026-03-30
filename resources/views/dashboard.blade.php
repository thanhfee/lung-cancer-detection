<x-app-layout>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight">
                📊 Hệ thống Thống kê Lâm sàng
            </h2>
            <span class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100">
                Cập nhật: {{ now()->timezone('Asia/Ho_Chi_Minh')->format('H:i - d/m/Y') }}
            </span>
        </div>
    </x-slot>

    <div class="py-10 bg-[#f8fafc] pb-32">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-indigo-100/50 border border-gray-50 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Tổng bệnh nhân</p>
                        <p class="text-4xl font-black text-indigo-600 mt-2">{{ $totalPatients }}</p>
                    </div>
                    <div class="p-4 bg-indigo-50 rounded-2xl text-indigo-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-red-100/50 border border-gray-50 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Ca Ác tính (Malignant)</p>
                        <p class="text-4xl font-black text-red-600 mt-2">{{ $malignantCount }}</p>
                    </div>
                    <div class="p-4 bg-red-50 rounded-2xl text-red-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-emerald-100/50 border border-gray-50 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Ca Lành tính (Normal)</p>
                        <p class="text-4xl font-black text-emerald-600 mt-2">{{ $normalCount }}</p>
                    </div>
                    <div class="p-4 bg-emerald-50 rounded-2xl text-emerald-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50 hover:shadow-2xl transition-all duration-300">
                    <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center">
                        <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                        Tỷ lệ chẩn đoán AI
                    </h3>
                    <div class="h-72">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50 hover:shadow-2xl transition-all duration-300">
                    <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center">
                        <span class="w-3 h-3 bg-indigo-500 rounded-full mr-2"></span>
                        Bệnh nhân mới (6 tháng)
                    </h3>
                    <div class="h-72">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-50 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-white to-indigo-50/30">
                    <h3 class="text-lg font-black text-gray-800 flex items-center">
                        <span class="relative flex h-3 w-3 mr-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                        Bệnh nhân mới cập nhật
                    </h3>
                    <a href="{{ route('patients.index') }}" class="group flex items-center text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-all">
                        Xem tất cả 
                        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4">
                        @forelse($recentPatients as $patient)
                        <div class="group relative bg-gray-50/50 hover:bg-white p-5 rounded-3xl border border-transparent hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-50/50 transition-all duration-300">
                            <div class="flex items-center justify-between flex-wrap gap-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-xl font-black shadow-lg shadow-indigo-200 group-hover:scale-110 transition-transform">
                                        {{ substr($patient->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-black text-gray-900 text-base">{{ $patient->name }}</h4>
                                            <span class="text-[10px] font-bold px-2 py-0.5 bg-gray-200 text-gray-500 rounded-full uppercase tracking-tighter">#{{ $patient->patient_code }}</span>
                                        </div>
                                        <p class="text-xs font-semibold text-gray-500 mt-0.5">
                                            {{ $patient->gender == 'Male' ? 'Nam' : 'Nữ' }} • {{ $patient->age }} tuổi • <span class="text-indigo-500">{{ $patient->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-6 ml-auto">
                                    <div class="text-right hidden sm:block">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Kết quả AI</p>
                                        @if($patient->scans->count() > 0)
                                            @php $lastScan = $patient->scans->first(); @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider {{ str_contains($lastScan->prediction, 'Malignant') ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }}">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ str_contains($lastScan->prediction, 'Malignant') ? 'bg-red-600' : 'bg-emerald-600' }}"></span>
                                                {{ $lastScan->prediction }}
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-gray-100 text-gray-400">
                                                Chưa quét
                                            </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('patients.show', $patient->id) }}" class="flex items-center justify-center w-12 h-12 rounded-2xl bg-white border border-gray-100 text-gray-400 hover:text-white hover:bg-indigo-600 hover:border-indigo-600 hover:shadow-lg shadow-sm transition-all duration-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="py-20 text-center">
                            <p class="text-gray-400 font-bold italic">Chưa có dữ liệu bệnh nhân mới.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. Cấu hình Biểu đồ
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.font.weight = '600';
        
        const pieRawData = @json($pieData); 
        const hasData = pieRawData[0] > 0 || pieRawData[1] > 0;

        new Chart(document.getElementById('pieChart'), {
            type: 'doughnut',
            data: {
                labels: hasData ? ['Malignant', 'Normal/Benign'] : ['Chưa có dữ liệu'],
                datasets: [{
                    data: hasData ? pieRawData : [1],
                    backgroundColor: hasData ? ['#ef4444', '#10b981'] : ['#f1f5f9'],
                    borderWidth: hasData ? 8 : 1,
                    borderColor: '#ffffff',
                    hoverOffset: hasData ? 15 : 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '70%',
                plugins: { legend: { position: 'bottom', display: hasData, labels: { padding: 20, usePointStyle: true } }, tooltip: { enabled: hasData } }
            }
        });

        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{ label: 'Bệnh nhân mới', data: @json($counts), backgroundColor: '#6366f1', borderRadius: 12, barThickness: 40 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { drawBorder: false, color: '#f3f4f6' }, ticks: { stepSize: 1 } }, x: { grid: { display: false } } }
            }
        });

        // 2. Hàm gửi tin nhắn AI (Đã sửa lỗi 419)
        async function sendGlobalMessage(message, patientId = null) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            try {
                const response = await fetch("{{ route('ai.chat') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        message: message,
                        patient_id: patientId
                    })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.reply || "Lỗi hệ thống");
                }

                return data.reply;
            } catch (error) {
                console.error("Chat Error:", error);
                return "Lỗi: " + error.message;
            }
        }
    </script>
</x-app-layout>