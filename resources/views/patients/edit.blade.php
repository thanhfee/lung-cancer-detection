<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('patients.index') }}" class="p-2 bg-white rounded-xl shadow-sm hover:bg-gray-50 transition-all group">
                <svg class="w-6 h-6 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight">Chỉnh sửa Hồ sơ</h2>
                <p class="text-sm text-gray-500 font-medium">Cập nhật thông tin bệnh nhân: <span class="text-indigo-600 font-bold">{{ $patient->name }}</span></p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#f8fafc]">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                
                <div class="lg:w-1/3">
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-indigo-100/50 border border-gray-100 sticky top-8">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-24 h-24 rounded-[2rem] bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg shadow-indigo-100 mb-4">
                                {{ substr($patient->name, 0, 1) }}
                            </div>
                            <h3 class="text-xl font-black text-gray-900">{{ $patient->name }}</h3>
                            <p class="text-sm font-bold text-indigo-500 uppercase tracking-widest mt-1">#{{ $patient->patient_code }}</p>
                            
                            <div class="mt-6 w-full space-y-3">
                                <div class="p-4 bg-gray-50 rounded-2xl flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-400 uppercase">Trạng thái AI</span>
                                    @if($patient->scans->count() > 0)
                                        <span class="text-xs font-black {{ str_contains($patient->scans->first()->prediction, 'Malignant') ? 'text-red-600' : 'text-emerald-600' }}">
                                            {{ $patient->scans->first()->prediction }}
                                        </span>
                                    @else
                                        <span class="text-xs font-bold text-gray-300 italic">Chưa quét</span>
                                    @endif
                                </div>
                                <div class="p-4 bg-gray-50 rounded-2xl flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-400 uppercase">Ngày đăng ký</span>
                                    <span class="text-xs font-bold text-gray-700">{{ $patient->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:w-2/3">
                    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-indigo-100/50 border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-800">Cập nhật thông tin chi tiết</h3>
                        </div>

                        <form action="{{ route('patients.update', $patient->id) }}" method="POST" class="p-8 space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Mã Bệnh Nhân (Cố định)</label>
                                    <input type="text" name="patient_code" value="{{ $patient->patient_code }}" readonly
                                        class="w-full px-5 py-4 bg-gray-100 border-none rounded-2xl font-bold text-gray-400 cursor-not-allowed">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Họ và Tên</label>
                                    <input type="text" name="name" value="{{ old('name', $patient->name) }}" required
                                        class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-medium text-gray-700 transition-all">
                                    @error('name') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Tuổi</label>
                                    <input type="number" name="age" value="{{ old('age', $patient->age) }}" required
                                        class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-medium text-gray-700 transition-all">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Giới tính</label>
                                    <select name="gender" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-medium text-gray-700 transition-all">
                                        <option value="Male" {{ (old('gender', $patient->gender) == 'Male' || old('gender', $patient->gender) == 'Nam') ? 'selected' : '' }}>Nam</option>
                                        <option value="Female" {{ (old('gender', $patient->gender) == 'Female' || old('gender', $patient->gender) == 'Nữ') ? 'selected' : '' }}>Nữ</option>
                                        <option value="Other" {{ old('gender', $patient->gender) == 'Other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Số điện thoại liên hệ</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1.38a1 1 0 01-.93-.647l-.228-.688a1 1 0 00-1.14-.682 10.96 10.96 0 01-5.118-2.072 10.96 10.96 0 01-2.072-5.118 1 1 0 00-.682-1.14l-.688-.228A1 1 0 013 11.38V5z"/></svg>
                                    </span>
                                    <input type="text" name="phone" value="{{ old('phone', $patient->phone) }}"
                                        class="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-medium text-gray-700 transition-all"
                                        placeholder="090 123 4567">
                                </div>
                            </div>

                            <div class="pt-6 flex items-center justify-end space-x-4 border-t border-gray-50">
                                <a href="{{ route('patients.index') }}" class="px-8 py-4 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors">
                                    Hủy thay đổi
                                </a>
                                <button type="submit" class="px-10 py-4 bg-orange-500 text-white rounded-2xl font-bold shadow-lg shadow-orange-100 hover:bg-orange-600 hover:-translate-y-1 transition-all duration-200">
                                    Cập nhật hồ sơ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>