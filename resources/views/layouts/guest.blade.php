<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LungCare AI') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="grid min-h-screen bg-[#eef8ff] lg:grid-cols-[1fr_520px]">
            <section class="hidden bg-gradient-to-br from-[#06488f] via-sky-700 to-cyan-500 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <a href="/" class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white shadow-sm">
                        <span class="text-xl font-black text-[#0a8ed8]">LC</span>
                    </div>
                    <div>
                        <p class="text-xl font-black leading-5">LungCare AI</p>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-100">Clinical Intelligence</p>
                    </div>
                </a>

                <div>
                    <p class="mb-5 inline-flex rounded-lg bg-white/15 px-3 py-2 text-xs font-black uppercase tracking-[0.18em] text-cyan-100 ring-1 ring-white/20">
                        Hệ thống hỗ trợ y khoa
                    </p>
                    <h1 class="max-w-xl text-5xl font-black leading-tight tracking-tight">Quản lý hồ sơ và phân tích ảnh phổi bằng AI</h1>
                    <p class="mt-5 max-w-xl text-base leading-7 text-sky-50">
                        Đồng bộ dữ liệu bệnh nhân, kết quả scan và tư vấn AI trong một hệ thống lâm sàng gọn gàng.
                    </p>
                </div>

                <p class="text-sm font-semibold text-cyan-100">© {{ now()->year }} LungCare AI</p>
            </section>

            <main class="flex min-h-screen flex-col items-center justify-center px-4 py-10 sm:px-6">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white shadow-sm">
                        <span class="text-xl font-black text-[#0a8ed8]">LC</span>
                    </div>
                    <div>
                        <p class="text-xl font-black leading-5 text-[#06488f]">LungCare AI</p>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-sky-700">Clinical Intelligence</p>
                    </div>
                </div>

                <div class="w-full max-w-md overflow-hidden rounded-lg bg-white p-6 shadow-xl shadow-sky-100 ring-1 ring-sky-100 sm:p-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
