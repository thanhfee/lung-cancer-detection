<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-sky-700">LungCare AI</p>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-950">Hồ sơ cá nhân</h2>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-lg border border-sky-100 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                {{ auth()->user()->email }}
            </span>
        </div>
    </x-slot>

    <div class="min-h-screen bg-[#eef8ff] py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-lg bg-gradient-to-r from-white via-white to-sky-100 shadow-sm ring-1 ring-sky-100">
                <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between lg:p-8">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-[#06488f] text-2xl font-black text-white shadow-sm">
                            {{ mb_substr(auth()->user()->name, 0, 1, 'UTF-8') }}
                        </div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-sky-700">Tài khoản hệ thống</p>
                            <h1 class="mt-1 text-2xl font-black text-slate-950">{{ auth()->user()->name }}</h1>
                            <p class="mt-1 text-sm font-semibold text-slate-500">{{ ucfirst(auth()->user()->role ?? 'user') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('dashboard') }}" class="inline-flex h-11 w-fit items-center rounded-lg bg-[#06488f] px-4 text-sm font-extrabold text-white transition hover:bg-[#053a73]">Quay lại dashboard</a>
                </div>
            </section>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-100">
                    @include('profile.partials.update-profile-information-form')
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-100">
                    @include('profile.partials.update-password-form')
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-red-100 lg:col-span-2">
                    @include('profile.partials.delete-user-form')
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
