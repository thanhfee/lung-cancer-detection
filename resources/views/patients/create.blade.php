<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('patients.index') }}" class="p-2 bg-white rounded-xl shadow-sm hover:bg-gray-50 transition-all">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight">Đăng ký Bệnh nhân</h2>
                <p class="text-sm text-gray-500 font-medium">Khởi tạo hồ sơ mới vào hệ thống chẩn đoán AI</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#f8fafc]">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-indigo-100/50 border border-gray-100 overflow-hidden">
                
                <div class="bg-indigo-600 p-8 text-white flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold">Thông tin hành chính</h3>
                        <p class="text-indigo-100 text-sm">Vui lòng nhập chính xác thông tin bệnh nhân</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-md">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>

                <form action="{{ route('patients.store') }}" method="POST" class="p-8 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Mã Bệnh Nhân</label>
                            <input type="text" name="patient_code" value="{{ old('patient_code', 'BN'.time()) }}" 
                                class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-indigo-600 transition-all @error('patient_code') ring-2 ring-red-500 @enderror"
                                placeholder="VD: BN001">
                            @error('patient_code') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Họ và Tên</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-medium text-gray-700 transition-all"
                                placeholder="Nhập đầy đủ họ tên">
                            @error('name') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Tuổi</label>
                            <input type="number" name="age" value="{{ old('age') }}" required
                                class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-medium text-gray-700 transition-all"
                                placeholder="VD: 25">
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Giới tính</label>
                            <select name="gender" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-medium text-gray-700 transition-all">
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Nam</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Nữ</option>
                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Số điện thoại liên hệ</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1.38a1 1 0 01-.93-.647l-.228-.688a1 1 0 00-1.14-.682 10.96 10.96 0 01-5.118-2.072 10.96 10.96 0 01-2.072-5.118 1 1 0 00-.682-1.14l-.688-.228A1 1 0 013 11.38V5z"/></svg>
                            </span>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-medium text-gray-700 transition-all"
                                placeholder="090 123 4567">
                        </div>
                    </div>

                    <div class="pt-6 flex items-center justify-end space-x-4">
                        <a href="{{ route('patients.index') }}" class="px-8 py-4 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                            Hủy bỏ
                        </a>
                        <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-[1.5rem] font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-1 transition-all duration-200">
                            Lưu hồ sơ bệnh nhân
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>