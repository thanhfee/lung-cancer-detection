<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="p-2.5 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight">Hồ sơ Bệnh nhân</h2>
                    <p class="text-sm text-gray-500 font-medium">Quản lý chẩn đoán bằng trí tuệ nhân tạo</p>
                </div>
            </div>
            
            <a href="{{ route('patients.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-2xl font-bold text-sm text-white shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Đăng ký bệnh nhân mới
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="relative group">
                <form action="{{ route('patients.index') }}" method="GET" class="flex gap-3">
                    <div class="relative flex-grow">
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Tìm kiếm bệnh nhân (Tên, Mã BN, Số điện thoại)..." 
                            class="w-full pl-12 pr-4 py-4 bg-white border-none rounded-3xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all text-gray-600 font-medium">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="px-8 bg-white text-gray-900 rounded-3xl font-bold shadow-sm hover:bg-gray-50 border border-gray-100 transition-all">
                        Lọc
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($patients as $patient)
                <div class="group bg-white rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-indigo-100 transition-all duration-300 overflow-hidden flex flex-col">
                    <div class="p-6 pb-0 flex justify-between items-start">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-1">Mã hồ sơ</span>
                            <span class="text-sm font-bold text-gray-400 uppercase">#{{ $patient->patient_code }}</span>
                        </div>
                        @if($patient->scans->count() > 0)
                            @php $lastScan = $patient->scans->first(); @endphp
                            <span class="px-3 py-1.5 rounded-xl text-[11px] font-black uppercase tracking-wider {{ str_contains($lastScan->prediction, 'Malignant') ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }}">
                                {{ $lastScan->prediction }}
                            </span>
                        @else
                            <span class="px-3 py-1.5 bg-gray-50 text-gray-400 border border-gray-100 rounded-xl text-[11px] font-black uppercase tracking-wider">Chưa quét</span>
                        @endif
                    </div>

                    <div class="p-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-indigo-100">
                                {{ substr($patient->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-extrabold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $patient->name }}</h3>
                                <p class="text-sm text-gray-500 font-medium">{{ $patient->gender }} • {{ $patient->age }} tuổi</p>
                            </div>
                        </div>

                        <div class="space-y-2 mt-4">
                            <div class="flex justify-between text-[11px] font-bold uppercase tracking-widest text-gray-400">
                                <span>Độ tin cậy AI</span>
                                <span class="text-indigo-600">{{ $patient->scans->count() > 0 ? number_format($patient->scans->first()->confidence_score * 100, 1) . '%' : '0%' }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-indigo-500 h-full rounded-full transition-all duration-500 shadow-[0_0_8px_rgba(99,102,241,0.6)]" 
                                     style="width: {{ $patient->scans->count() > 0 ? $patient->scans->first()->confidence_score * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto p-4 bg-gray-50/50 border-t border-gray-50 grid grid-cols-4 gap-2">
                        <a href="{{ route('patients.show', $patient->id) }}" class="flex flex-col items-center p-2 hover:bg-white rounded-xl transition-all group/btn" title="Xem chi tiết">
                            <svg class="w-5 h-5 text-gray-400 group-hover/btn:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        
                        <a href="{{ route('patients.scan', $patient->id) }}" class="flex flex-col items-center p-2 hover:bg-white rounded-xl transition-all group/btn" title="Quét AI">
                            <svg class="w-5 h-5 text-gray-400 group-hover/btn:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </a>

                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('patients.edit', $patient->id) }}" class="flex flex-col items-center p-2 hover:bg-white rounded-xl transition-all group/btn" title="Sửa hồ sơ">
                            <svg class="w-5 h-5 text-gray-400 group-hover/btn:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        
                        <button type="button" onclick="confirmDelete({{ $patient->id }})" class="flex flex-col items-center p-2 hover:bg-white rounded-xl transition-all group/btn" title="Xóa">
                            <svg class="w-5 h-5 text-gray-400 group-hover/btn:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>

                        <form id="delete-form-{{ $patient->id }}" action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 bg-white rounded-[2rem] border border-dashed border-gray-200 flex flex-col items-center">
                    <p class="text-gray-400 font-bold italic">Không tìm thấy bệnh nhân nào...</p>
                </div>
                @endforelse
            </div>

            @if($patients->hasPages())
                <div class="mt-8 bg-white p-4 rounded-3xl shadow-sm border border-gray-100">
                    {{ $patients->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Xác nhận xóa
        function confirmDelete(id) {
            Swal.fire({
                title: 'Xóa hồ sơ bệnh nhân?',
                text: "Dữ liệu và lịch sử quét AI sẽ bị xóa vĩnh viễn!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#f43f5e',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy bỏ',
                customClass: { popup: 'rounded-[2rem] p-8' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }

        // Hiển thị thông báo thành công từ Session
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                customClass: { popup: 'rounded-[2rem]' }
            });
        @endif
    </script>
</x-app-layout>