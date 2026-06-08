<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-black uppercase tracking-[0.18em] text-sky-700">Khôi phục tài khoản</p>
        <h1 class="mt-2 text-2xl font-black text-slate-950">Quên mật khẩu?</h1>
        <p class="mt-2 text-sm font-semibold leading-6 text-slate-500">Nhập email của bạn, hệ thống sẽ gửi liên kết đặt lại mật khẩu.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Gửi liên kết đặt lại
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
