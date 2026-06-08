<x-app-layout>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <style>
            @keyframes dashboardFadeUp {
                from { opacity: 0; transform: translateY(18px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .dashboard-reveal {
                animation: dashboardFadeUp .65s ease both;
            }

            .dashboard-reveal:nth-of-type(2) { animation-delay: .06s; }
            .dashboard-reveal:nth-of-type(3) { animation-delay: .12s; }
            .dashboard-reveal:nth-of-type(4) { animation-delay: .18s; }
            .dashboard-reveal:nth-of-type(5) { animation-delay: .24s; }

            .professional-card {
                transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, background .22s ease;
            }

            .professional-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 18px 45px rgba(14, 116, 144, .14);
            }

            .dashboard-slide {
                opacity: 0;
                pointer-events: none;
                transform: scale(1.015);
                transition: opacity .65s ease, transform .75s ease;
            }

            .dashboard-slide.is-active {
                opacity: 1;
                pointer-events: auto;
                transform: scale(1);
            }

            .slider-dot {
                transition: width .25s ease, background .25s ease, opacity .25s ease;
            }

            .slider-dot.is-active {
                width: 2.25rem;
                background: #ffffff;
                opacity: 1;
            }

            .dashboard-slider-frame,
            .dashboard-slider-content {
                min-height: clamp(460px, 58vh, 680px);
            }

            .dashboard-slider-image {
                filter: saturate(1.08) contrast(1.05);
                transform: scale(1.01);
                transform-origin: center;
                will-change: transform;
            }

            .patient-row {
                transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
            }

            .patient-row:hover {
                transform: translateX(4px);
                box-shadow: 0 16px 36px rgba(15, 23, 42, .08);
            }

            @media print {
                nav, .no-print, #health-guide, .fixed { display: none !important; }
                main { max-width: none !important; padding: 0 !important; }
                section { box-shadow: none !important; break-inside: avoid; }
            }
        </style>
    </head>

    @php
        $actualScanTotal = array_sum($pieData ?? []);
        $scanTotal = max($actualScanTotal, 1);
        $malignantRate = round(($malignantCount / max($totalPatients, 1)) * 100);
        $normalRate = round(($normalCount / max($totalPatients, 1)) * 100);
        $uncertainRate = round(($uncertainCount / max($totalPatients, 1)) * 100);
        $lastUpdated = now()->timezone('Asia/Ho_Chi_Minh')->format('H:i - d/m/Y');
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-sky-700">Trung tâm chẩn đoán hình ảnh AI</p>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-950">Dashboard lâm sàng</h2>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-lg border border-sky-100 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Cập nhật: {{ $lastUpdated }}
            </span>
        </div>
    </x-slot>

    <div class="min-h-screen bg-[#eef8ff] pb-24">

        <main class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
            <section class="dashboard-reveal relative overflow-hidden rounded-lg bg-[#06488f] shadow-2xl shadow-sky-300/60 ring-1 ring-sky-100">
                <div class="dashboard-slider-frame relative">
                    <article class="dashboard-slide is-active absolute inset-0">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTLA5CCDydRlbQpwYhrkH-zPFxuwx1xuI1Giw&s" alt="Bác sĩ phân tích dữ liệu AI" class="dashboard-slider-image absolute inset-0 h-full w-full object-cover object-center">
                        <div class="absolute inset-0 bg-gradient-to-r from-[#06488f]/95 via-[#06488f]/70 to-[#06488f]/5"></div>
                        <div class="dashboard-slider-content relative z-10 flex max-w-4xl flex-col justify-center px-7 py-12 sm:px-12 lg:px-16">
                            <p class="mb-5 inline-flex w-fit items-center gap-2 rounded-lg bg-white/15 px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-cyan-100 ring-1 ring-white/20">
                                <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                                Chẩn đoán AI
                            </p>
                            <h2 class="text-4xl font-black leading-tight tracking-tight text-white sm:text-6xl">Theo dõi kết quả scan phổi trong một màn hình</h2>
                            <p class="mt-5 max-w-2xl text-lg leading-8 text-sky-50">Tổng hợp bệnh nhân, kết quả AI và trạng thái ưu tiên để bác sĩ nắm nhanh các ca cần xử lý.</p>
                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="#ai-diagnosis" class="inline-flex h-12 items-center rounded-lg bg-white px-6 text-sm font-extrabold text-[#06488f] transition hover:bg-sky-50">Xem chẩn đoán</a>
                                <a href="{{ route('patients.create') }}" class="inline-flex h-12 items-center rounded-lg border border-white/30 bg-white/10 px-6 text-sm font-extrabold text-white transition hover:bg-white/20">Thêm bệnh nhân</a>
                            </div>
                        </div>
                    </article>

                    <article class="dashboard-slide absolute inset-0">
                        <img src="{{ asset('images/dashboard-slider-2.jpg') }}" alt="Báo cáo lâm sàng" class="dashboard-slider-image absolute inset-0 h-full w-full object-cover object-center">
                        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-[#06488f]/76 to-sky-700/10"></div>
                        <div class="dashboard-slider-content relative z-10 flex max-w-4xl flex-col justify-center px-7 py-12 sm:px-12 lg:px-16">
                            <p class="mb-5 inline-flex w-fit items-center gap-2 rounded-lg bg-white/15 px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-cyan-100 ring-1 ring-white/20">
                                <span class="h-2 w-2 rounded-full bg-orange-300"></span>
                                Báo cáo lâm sàng
                            </p>
                            <h2 class="text-4xl font-black leading-tight tracking-tight text-white sm:text-6xl">Tạo góc nhìn quản trị rõ ràng hơn cho từng ngày</h2>
                            <p class="mt-5 max-w-2xl text-lg leading-8 text-sky-50">KPI, tỷ lệ nguy cơ và khuyến nghị nhanh giúp báo cáo nội bộ gọn, dễ đọc, dễ in.</p>
                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="#reports" class="inline-flex h-12 items-center rounded-lg bg-white px-6 text-sm font-extrabold text-[#06488f] transition hover:bg-sky-50">Xem báo cáo</a>
                                <button type="button" onclick="printClinicalReport()" class="inline-flex h-12 items-center rounded-lg border border-white/30 bg-white/10 px-6 text-sm font-extrabold text-white transition hover:bg-white/20">In nhanh</button>
                            </div>
                        </div>
                    </article>

                    <article class="dashboard-slide absolute inset-0">
                        <img src="{{ asset('images/dashboard-slider-3.jpg') }}" alt="Tư vấn sức khỏe" class="dashboard-slider-image absolute inset-0 h-full w-full object-cover object-center">
                        <div class="absolute inset-0 bg-gradient-to-r from-[#053a73]/95 via-[#0f766e]/72 to-emerald-500/10"></div>
                        <div class="dashboard-slider-content relative z-10 flex max-w-4xl flex-col justify-center px-7 py-12 sm:px-12 lg:px-16">
                            <p class="mb-5 inline-flex w-fit items-center gap-2 rounded-lg bg-white/15 px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-cyan-100 ring-1 ring-white/20">
                                <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                                Cẩm nang sức khỏe
                            </p>
                            <h2 class="text-4xl font-black leading-tight tracking-tight text-white sm:text-6xl">Hỗ trợ tư vấn bệnh nhân ngay trong dashboard</h2>
                            <p class="mt-5 max-w-2xl text-lg leading-8 text-sky-50">Các chủ đề tầm soát, dấu hiệu cần chú ý và chuẩn bị scan được nối với AI tư vấn nhanh.</p>
                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="#health-guide" class="inline-flex h-12 items-center rounded-lg bg-white px-6 text-sm font-extrabold text-[#06488f] transition hover:bg-sky-50">Mở cẩm nang</a>
                                <a href="mailto:crosszmagmajelly@gmail.com" class="inline-flex h-12 items-center rounded-lg border border-white/30 bg-white/10 px-6 text-sm font-extrabold text-white transition hover:bg-white/20">Liên hệ hỗ trợ</a>
                            </div>
                        </div>
                    </article>
                </div>

                <button type="button" data-slider-prev class="no-print absolute left-5 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-lg bg-white/90 text-[#06488f] shadow-lg transition hover:bg-white" aria-label="Slide trước">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/></svg>
                </button>
                <button type="button" data-slider-next class="no-print absolute right-5 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-lg bg-white/90 text-[#06488f] shadow-lg transition hover:bg-white" aria-label="Slide tiếp theo">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                </button>

                <div class="absolute bottom-6 left-7 z-20 flex items-center gap-2 sm:left-12 lg:left-16">
                    <button type="button" data-slider-dot="0" class="slider-dot is-active h-2.5 w-2.5 rounded-full bg-white opacity-100" aria-label="Slide 1"></button>
                    <button type="button" data-slider-dot="1" class="slider-dot h-2.5 w-2.5 rounded-full bg-white opacity-45" aria-label="Slide 2"></button>
                    <button type="button" data-slider-dot="2" class="slider-dot h-2.5 w-2.5 rounded-full bg-white opacity-45" aria-label="Slide 3"></button>
                </div>

                <div class="absolute bottom-6 right-7 z-20 hidden rounded-lg bg-white/95 px-5 py-4 shadow-lg sm:block">
                    <p class="text-xs font-black uppercase tracking-widest text-sky-700">Tổng hồ sơ</p>
                    <p class="mt-1 text-3xl font-black text-slate-950">{{ $totalPatients }}</p>
                </div>
            </section>

            <section class="dashboard-reveal professional-card grid items-center gap-8 rounded-lg bg-gradient-to-r from-white via-white to-sky-100 p-6 shadow-sm ring-1 ring-sky-100 lg:grid-cols-[1.05fr_0.95fr] lg:p-10">
                <div>
                    <div class="mb-5 inline-flex items-center gap-2 rounded-lg bg-sky-50 px-3 py-2 text-xs font-extrabold uppercase tracking-[0.18em] text-sky-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Theo dõi dữ liệu thời gian thực
                    </div>
                    <h1 class="max-w-2xl text-3xl font-black leading-tight tracking-tight text-[#06488f] sm:text-4xl">
                        Hệ thống hỗ trợ chẩn đoán ung thư phổi bằng AI
                    </h1>
                    <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                        Tập trung quản lý hồ sơ bệnh nhân, kết quả scan và xu hướng chẩn đoán để bác sĩ theo dõi nhanh các ca cần ưu tiên xử lý.
                    </p>

                    <div class="mt-7 grid max-w-2xl grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-sky-100">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Tỷ lệ ác tính</p>
                            <p class="mt-2 text-2xl font-black text-red-600">{{ $malignantRate }}%</p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-emerald-100">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Tỷ lệ bình thường</p>
                            <p class="mt-2 text-2xl font-black text-emerald-600">{{ $normalRate }}%</p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-amber-100">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Cần xem lại</p>
                            <p class="mt-2 text-2xl font-black text-amber-600">{{ $uncertainRate }}%</p>
                        </div>
                    </div>
                </div>

                <div class="relative min-h-[280px] overflow-hidden rounded-lg bg-slate-200 shadow-md">
                    <img src="data:image/jpeg;base64,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" alt="Bác sĩ phân tích dữ liệu y tế" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-tr from-[#06488f]/85 via-[#06488f]/30 to-transparent"></div>
                    <div class="absolute bottom-5 left-5 right-5 rounded-lg bg-white/95 p-4 shadow-sm">
                        <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-sky-700">Tổng số hồ sơ</p>
                        <div class="mt-2 flex items-end justify-between gap-4">
                            <p class="text-4xl font-black text-slate-950">{{ $totalPatients }}</p>
                            <p class="text-right text-sm font-semibold text-slate-500">Bệnh nhân đã được ghi nhận trong hệ thống</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="dashboard-reveal grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="professional-card rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-100">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Tổng bệnh nhân</p>
                        <span class="rounded-lg bg-sky-50 p-2 text-sky-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 0 0-5.36-1.86M17 20H7m10 0v-2c0-.66-.13-1.28-.36-1.86M7 20H2v-2a3 3 0 0 1 5.36-1.86M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
                        </span>
                    </div>
                    <p class="mt-4 text-4xl font-black text-slate-950">{{ $totalPatients }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-500">Hồ sơ đang quản lý</p>
                </div>

                <div class="professional-card rounded-lg bg-white p-5 shadow-sm ring-1 ring-red-100">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Ca ác tính</p>
                        <span class="rounded-lg bg-red-50 p-2 text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                        </span>
                    </div>
                    <p class="mt-4 text-4xl font-black text-red-600">{{ $malignantCount }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-500">Cần ưu tiên đánh giá</p>
                </div>

                <div class="professional-card rounded-lg bg-white p-5 shadow-sm ring-1 ring-emerald-100">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Bình thường</p>
                        <span class="rounded-lg bg-emerald-50 p-2 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 12 2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                        </span>
                    </div>
                    <p class="mt-4 text-4xl font-black text-emerald-600">{{ $normalCount }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-500">Kết quả lành tính / bình thường</p>
                </div>

                <div class="professional-card rounded-lg bg-white p-5 shadow-sm ring-1 ring-amber-100">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Chưa chắc chắn</p>
                        <span class="rounded-lg bg-amber-50 p-2 text-amber-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0z"/></svg>
                        </span>
                    </div>
                    <p class="mt-4 text-4xl font-black text-amber-600">{{ $uncertainCount }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-500">Cần bổ sung kiểm tra</p>
                </div>
            </section>

            <section id="ai-diagnosis" class="dashboard-reveal grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="professional-card rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-100">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-sky-700">Phân bố chẩn đoán</p>
                            <h3 class="mt-1 text-lg font-black text-slate-950">Tỷ lệ kết quả AI</h3>
                        </div>
                        <span class="rounded-lg bg-slate-50 px-3 py-2 text-xs font-bold text-slate-500">{{ $actualScanTotal }} lượt scan</span>
                    </div>
                    <div class="h-72">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>

                <div class="professional-card rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-100">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-sky-700">Xu hướng tiếp nhận</p>
                            <h3 class="mt-1 text-lg font-black text-slate-950">Bệnh nhân mới trong 6 tháng</h3>
                        </div>
                        <span class="rounded-lg bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700">Theo tháng</span>
                    </div>
                    <div class="h-72">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </section>

            <section id="reports" class="dashboard-reveal professional-card rounded-lg bg-white shadow-sm ring-1 ring-slate-100">
                <div class="flex flex-col gap-4 border-b border-slate-100 bg-gradient-to-r from-white to-sky-50 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-sky-700">Báo cáo lâm sàng</p>
                        <h3 class="mt-1 text-lg font-black text-slate-950">Tổng hợp nhanh tình hình chẩn đoán</h3>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="printClinicalReport()" class="inline-flex h-10 items-center gap-2 rounded-lg bg-[#06488f] px-4 text-sm font-extrabold text-white transition hover:bg-[#053a73]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2m-12 0h12v4H6v-4z"/></svg>
                            In báo cáo
                        </button>
                        <a href="{{ route('patients.index') }}" class="inline-flex h-10 items-center gap-2 rounded-lg border border-sky-200 bg-white px-4 text-sm font-extrabold text-[#06488f] transition hover:bg-sky-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6M4 19.5A2.5 2.5 0 0 0 6.5 22h11A2.5 2.5 0 0 0 20 19.5v-15A2.5 2.5 0 0 0 17.5 2h-11A2.5 2.5 0 0 0 4 4.5v15z"/></svg>
                            Xem hồ sơ
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-lg bg-slate-50 p-5 ring-1 ring-slate-100">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Tổng hồ sơ</p>
                        <p class="mt-3 text-3xl font-black text-slate-950">{{ $totalPatients }}</p>
                        <p class="mt-2 text-sm font-semibold text-slate-500">Bệnh nhân đã ghi nhận</p>
                    </div>
                    <div class="rounded-lg bg-red-50 p-5 ring-1 ring-red-100">
                        <p class="text-xs font-black uppercase tracking-widest text-red-500">Nhóm nguy cơ cao</p>
                        <p class="mt-3 text-3xl font-black text-red-700">{{ $malignantCount }}</p>
                        <p class="mt-2 text-sm font-semibold text-red-700/70">Cần bác sĩ đánh giá sớm</p>
                    </div>
                    <div class="rounded-lg bg-emerald-50 p-5 ring-1 ring-emerald-100">
                        <p class="text-xs font-black uppercase tracking-widest text-emerald-600">Nhóm ổn định</p>
                        <p class="mt-3 text-3xl font-black text-emerald-700">{{ $normalCount }}</p>
                        <p class="mt-2 text-sm font-semibold text-emerald-700/70">Kết quả bình thường / lành tính</p>
                    </div>
                    <div class="rounded-lg bg-amber-50 p-5 ring-1 ring-amber-100">
                        <p class="text-xs font-black uppercase tracking-widest text-amber-600">Cần theo dõi</p>
                        <p class="mt-3 text-3xl font-black text-amber-700">{{ $uncertainCount }}</p>
                        <p class="mt-2 text-sm font-semibold text-amber-700/70">Nên bổ sung kiểm tra</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 px-6 py-5">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                        <div class="rounded-lg border border-slate-100 p-4">
                            <p class="text-sm font-black text-slate-950">Kết luận nhanh</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Hệ thống đang ghi nhận {{ $malignantRate }}% hồ sơ thuộc nhóm nguy cơ cao dựa trên kết quả AI.</p>
                        </div>
                        <div class="rounded-lg border border-slate-100 p-4">
                            <p class="text-sm font-black text-slate-950">Ưu tiên trong ngày</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Rà soát các ca ác tính và ca chưa chắc chắn trước khi xuất kết luận chính thức.</p>
                        </div>
                        <div class="rounded-lg border border-slate-100 p-4">
                            <p class="text-sm font-black text-slate-950">Khuyến nghị</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Kết quả AI chỉ là tham khảo, cần đối chiếu lâm sàng và chẩn đoán hình ảnh.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="health-guide" class="dashboard-reveal professional-card rounded-lg bg-white shadow-sm ring-1 ring-slate-100">
                <div class="border-b border-slate-100 bg-gradient-to-r from-white to-emerald-50 px-6 py-5">
                    <p class="text-xs font-black uppercase tracking-widest text-emerald-700">Cẩm nang sức khỏe</p>
                    <h3 class="mt-1 text-lg font-black text-slate-950">Tủ kiến thức hỗ trợ tư vấn bệnh nhân</h3>
                </div>

                <div class="grid grid-cols-1 gap-4 p-6 lg:grid-cols-3">
                    <article class="rounded-lg border border-slate-100 p-5 transition hover:border-sky-200 hover:bg-sky-50/50">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-sky-50 text-sky-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0z"/></svg>
                        </div>
                        <h4 class="font-black text-slate-950">Tầm soát định kỳ</h4>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Nhắc bệnh nhân nguy cơ cao kiểm tra định kỳ, đặc biệt khi có tiền sử hút thuốc hoặc tiếp xúc môi trường độc hại.</p>
                        <button type="button" data-guide-title="Tầm soát định kỳ" data-guide-prompt="Tư vấn ngắn về tầm soát ung thư phổi định kỳ cho bệnh nhân nguy cơ cao. Trình bày thành các gạch đầu dòng rõ ràng, dễ hiểu cho bác sĩ tư vấn bệnh nhân." onclick="askHealthGuide(this)" class="health-guide-button mt-4 inline-flex h-10 items-center gap-2 rounded-lg bg-sky-50 px-4 text-sm font-extrabold text-[#06488f] ring-1 ring-sky-100 transition hover:bg-sky-100">
                            <span>Hỏi AI tư vấn</span>
                        </button>
                    </article>

                    <article class="rounded-lg border border-slate-100 p-5 transition hover:border-emerald-200 hover:bg-emerald-50/50">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 12 2 2 4-4m5.62-5.17A11.95 11.95 0 0 1 12 2.5 11.95 11.95 0 0 1 3.38 4.83C3.13 6.12 3 7.45 3 8.8 3 14.1 6.4 19.15 12 21.5c5.6-2.35 9-7.4 9-12.7 0-1.35-.13-2.68-.38-3.97z"/></svg>
                        </div>
                        <h4 class="font-black text-slate-950">Dấu hiệu cần chú ý</h4>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Ho kéo dài, khó thở, đau ngực, sụt cân bất thường hoặc ho ra máu cần được thăm khám sớm.</p>
                        <button type="button" data-guide-title="Dấu hiệu cần chú ý" data-guide-prompt="Liệt kê các dấu hiệu ung thư phổi cần đi khám sớm. Viết ngắn gọn, dễ hiểu, có cảnh báo cần gặp bác sĩ khi triệu chứng kéo dài hoặc nặng lên." onclick="askHealthGuide(this)" class="health-guide-button mt-4 inline-flex h-10 items-center gap-2 rounded-lg bg-emerald-50 px-4 text-sm font-extrabold text-[#06488f] ring-1 ring-emerald-100 transition hover:bg-emerald-100">
                            <span>Hỏi AI tư vấn</span>
                        </button>
                    </article>

                    <article class="rounded-lg border border-slate-100 p-5 transition hover:border-amber-200 hover:bg-amber-50/50">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75 9 17.25 19.5 6.75M12 3v2m0 14v2M3 12h2m14 0h2"/></svg>
                        </div>
                        <h4 class="font-black text-slate-950">Chuẩn bị khi chụp scan</h4>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Hướng dẫn bệnh nhân mang hồ sơ cũ, thông báo bệnh nền và làm theo chỉ định của nhân viên y tế.</p>
                        <button type="button" data-guide-title="Chuẩn bị khi chụp scan" data-guide-prompt="Hướng dẫn bệnh nhân chuẩn bị trước khi chụp X-quang hoặc CT phổi. Trình bày thành checklist ngắn, nhấn mạnh mang hồ sơ cũ và thông báo bệnh nền cho nhân viên y tế." onclick="askHealthGuide(this)" class="health-guide-button mt-4 inline-flex h-10 items-center gap-2 rounded-lg bg-amber-50 px-4 text-sm font-extrabold text-[#06488f] ring-1 ring-amber-100 transition hover:bg-amber-100">
                            <span>Hỏi AI tư vấn</span>
                        </button>
                    </article>
                </div>

                <div id="healthGuideReply" class="mx-6 mb-6 hidden overflow-hidden rounded-lg border border-sky-100 bg-white shadow-lg shadow-sky-100/70">
                    <div class="flex flex-col gap-4 bg-gradient-to-r from-[#06488f] to-sky-600 px-5 py-4 text-white sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white/15 ring-1 ring-white/20">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17 9 21l3-1.5L15 21l-.75-4M8 9a4 4 0 1 1 8 0c0 1.66-1.02 3.08-2.47 3.68L13 15h-2l-.53-2.32A4.01 4.01 0 0 1 8 9z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-sky-100">Gợi ý từ AI</p>
                                <h4 id="healthGuideReplyTitle" class="mt-1 text-lg font-black">Tư vấn sức khỏe</h4>
                            </div>
                        </div>
                        <button type="button" onclick="closeHealthGuideReply()" class="no-print inline-flex h-9 w-fit items-center rounded-lg bg-white/15 px-3 text-sm font-extrabold text-white ring-1 ring-white/20 transition hover:bg-white/25">Đóng</button>
                    </div>

                    <div class="grid gap-4 p-5 lg:grid-cols-[1fr_260px]">
                        <div class="rounded-lg bg-sky-50 p-5 ring-1 ring-sky-100">
                            <div id="healthGuideLoading" class="hidden items-center gap-3 text-sm font-bold text-sky-700">
                                <span class="h-4 w-4 animate-spin rounded-full border-2 border-sky-200 border-t-sky-700"></span>
                                AI đang phân tích nội dung tư vấn...
                            </div>
                            <div id="healthGuideError" class="hidden rounded-lg bg-red-50 p-4 text-sm font-bold leading-6 text-red-700 ring-1 ring-red-100"></div>
                            <div id="healthGuideReplyText" class="health-guide-answer whitespace-pre-line text-[15px] font-medium leading-7 text-slate-700"></div>
                        </div>

                        <div class="rounded-lg bg-slate-50 p-4 ring-1 ring-slate-100">
                            <p class="text-xs font-black uppercase tracking-widest text-slate-400">Lưu ý sử dụng</p>
                            <div class="mt-3 space-y-3 text-sm leading-6 text-slate-600">
                                <p>Kết quả AI chỉ dùng để hỗ trợ tư vấn nhanh.</p>
                                <p>Bác sĩ vẫn cần đối chiếu triệu chứng, tiền sử bệnh và kết quả chẩn đoán hình ảnh.</p>
                                <p class="font-bold text-slate-800">Không dùng thay thế kết luận y khoa chính thức.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="dashboard-reveal overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-100">
                <div class="flex flex-col gap-4 border-b border-slate-100 bg-gradient-to-r from-white via-sky-50 to-cyan-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-sky-700">Theo dõi gần đây</p>
                        <h3 class="mt-1 text-lg font-black text-slate-950">Bệnh nhân mới cập nhật</h3>
                        <p class="mt-1 text-sm font-semibold text-slate-500">Ưu tiên các hồ sơ có kết quả AI bất thường hoặc chưa được quét.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-xs font-bold text-slate-500 ring-1 ring-slate-100">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            {{ $recentPatients->count() }} hồ sơ mới
                        </span>
                        <a href="{{ route('patients.index') }}" class="inline-flex w-fit items-center gap-2 rounded-lg border border-sky-200 bg-white px-4 py-2 text-sm font-extrabold text-[#06488f] shadow-sm transition hover:bg-sky-50">
                            Xem tất cả
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                </div>

                <div class="bg-slate-50/60 p-4 sm:p-6">
                    <div class="hidden grid-cols-[1.1fr_0.7fr_0.8fr_0.7fr] gap-4 px-4 pb-3 text-xs font-black uppercase tracking-widest text-slate-400 lg:grid">
                        <span>Bệnh nhân</span>
                        <span>Thời gian</span>
                        <span>Kết quả AI</span>
                        <span class="text-right">Thao tác</span>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentPatients as $patient)
                            @php
                                $lastScan = $patient->scans->first();
                                $prediction = $lastScan?->prediction;
                                $isMalignant = $prediction && str_contains($prediction, 'Malignant');
                                $initial = mb_substr($patient->name, 0, 1, 'UTF-8');
                                $statusLabel = $prediction ? $prediction : 'Chưa quét';
                                $statusClass = $prediction
                                    ? ($isMalignant ? 'bg-red-50 text-red-700 ring-red-100' : 'bg-emerald-50 text-emerald-700 ring-emerald-100')
                                    : 'bg-amber-50 text-amber-700 ring-amber-100';
                                $dotClass = $prediction ? ($isMalignant ? 'bg-red-600' : 'bg-emerald-600') : 'bg-amber-500';
                                $priorityLabel = $isMalignant ? 'Ưu tiên cao' : ($prediction ? 'Đã phân loại' : 'Chờ quét AI');
                            @endphp
                            <div class="patient-row relative grid gap-4 rounded-lg bg-white p-4 ring-1 ring-slate-100 lg:grid-cols-[1.1fr_0.7fr_0.8fr_0.7fr] lg:items-center">
                                <div class="absolute left-0 top-3 h-[calc(100%-24px)] w-1 rounded-r-full {{ $dotClass }}"></div>

                                <div class="flex items-center gap-4 pl-2">
                                    <div class="relative flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-[#06488f] text-lg font-black text-white shadow-sm">
                                        {{ $initial }}
                                        <span class="absolute -right-1 -top-1 h-3 w-3 rounded-full ring-2 ring-white {{ $dotClass }}"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="truncate font-black text-slate-950">{{ $patient->name }}</h4>
                                            <span class="rounded-lg bg-slate-100 px-2 py-1 text-xs font-bold text-slate-500">#{{ $patient->patient_code }}</span>
                                        </div>
                                        <p class="mt-1 text-sm font-semibold text-slate-500">
                                            {{ $patient->gender == 'Male' ? 'Nam' : 'Nữ' }} • {{ $patient->age }} tuổi
                                        </p>
                                    </div>
                                </div>

                                <div class="pl-2 lg:pl-0">
                                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">Cập nhật</p>
                                    <p class="mt-1 text-sm font-bold text-slate-700">{{ $patient->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y') }}</p>
                                </div>

                                <div class="flex flex-wrap items-center gap-2 pl-2 lg:pl-0">
                                    <span class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-black uppercase tracking-wide ring-1 {{ $statusClass }}">
                                        <span class="h-2 w-2 rounded-full {{ $dotClass }}"></span>
                                        {{ $statusLabel }}
                                    </span>
                                    <span class="rounded-lg bg-slate-50 px-3 py-2 text-xs font-bold text-slate-500 ring-1 ring-slate-100">{{ $priorityLabel }}</span>
                                </div>

                                <div class="flex items-center justify-start gap-2 pl-2 lg:justify-end lg:pl-0">
                                    @if(!$prediction)
                                        <a href="{{ route('patients.scan', $patient->id) }}" class="inline-flex h-10 items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 text-sm font-extrabold text-amber-700 transition hover:bg-amber-100">
                                            Quét AI
                                        </a>
                                    @endif
                                    <a href="{{ route('patients.show', $patient->id) }}" class="inline-flex h-10 items-center gap-2 rounded-lg bg-[#06488f] px-4 text-sm font-extrabold text-white transition hover:bg-[#053a73]">
                                        Chi tiết
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-lg bg-white px-6 py-16 text-center ring-1 ring-slate-100">
                                <p class="font-bold text-slate-400">Chưa có dữ liệu bệnh nhân mới.</p>
                                <a href="{{ route('patients.create') }}" class="mt-4 inline-flex h-10 items-center rounded-lg bg-[#06488f] px-4 text-sm font-extrabold text-white transition hover:bg-[#053a73]">Thêm bệnh nhân đầu tiên</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </main>

        <footer class="dashboard-reveal border-t border-sky-100 bg-cyan-100/85">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.1fr_0.9fr_0.8fr_0.7fr] lg:px-8">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white shadow-sm">
                            <span class="text-xl font-black text-[#0a8ed8]">LC</span>
                        </div>
                        <div>
                            <p class="text-lg font-black leading-5 text-[#06488f]">LungCare AI</p>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">Clinical Intelligence</p>
                        </div>
                    </div>
                    <p class="mt-5 max-w-sm text-sm leading-6 text-slate-700">
                        Hệ thống hỗ trợ quản lý hồ sơ, phân tích ảnh y tế bằng AI và theo dõi dữ liệu lâm sàng cho bác sĩ.
                    </p>
                    <div class="mt-5 flex gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-white text-[#06488f] shadow-sm ring-1 ring-sky-100">f</span>
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-white text-red-600 shadow-sm ring-1 ring-sky-100">▶</span>
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-white text-slate-950 shadow-sm ring-1 ring-sky-100">♪</span>
                    </div>
                </div>

                <div>
                    <h4 class="text-base font-black text-slate-950">Thông tin liên hệ</h4>
                    <div class="mt-4 space-y-3 text-sm font-semibold text-slate-700">
                        <p class="flex gap-3">
                            <span class="text-sky-700">☎</span>
                            <a href="tel:0394921897" class="hover:text-[#06488f]">0394921897</a>
                        </p>
                        <p class="flex gap-3">
                            <span class="text-sky-700">✉</span>
                            <a href="mailto:crosszmagmajelly@gmail.com" class="break-all hover:text-[#06488f]">crosszmagmajelly@gmail.com</a>
                        </p>
                        <p class="flex gap-3">
                            <span class="text-sky-700">⌖</span>
                            <span>467 Lĩnh Nam, Hoàng Mai, Hà Nội</span>
                        </p>
                    </div>
                </div>

                <div>
                    <h4 class="text-base font-black text-slate-950">Chức năng chính</h4>
                    <div class="mt-4 grid gap-2 text-sm font-semibold text-slate-700">
                        <a href="{{ route('dashboard') }}" class="hover:text-[#06488f]">Dashboard tổng quan</a>
                        <a href="{{ route('patients.index') }}" class="hover:text-[#06488f]">Quản lý bệnh nhân</a>
                        <a href="#reports" class="hover:text-[#06488f]">Báo cáo lâm sàng</a>
                        <a href="#health-guide" class="hover:text-[#06488f]">Cẩm nang sức khỏe</a>
                    </div>
                </div>

                <div>
                    <h4 class="text-base font-black text-slate-950">Trực hệ thống</h4>
                    <div class="mt-4 rounded-lg bg-white p-4 shadow-sm ring-1 ring-sky-100">
                        <p class="text-sm font-bold text-slate-700">Thời gian hỗ trợ</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Thứ 2 - Chủ nhật<br>07:30 - 17:00</p>
                        <a href="mailto:crosszmagmajelly@gmail.com" class="mt-4 inline-flex h-10 items-center rounded-lg bg-[#06488f] px-4 text-sm font-extrabold text-white transition hover:bg-[#053a73]">Gửi email</a>
                    </div>
                </div>
            </div>

            <div class="bg-[#06488f] px-4 py-4 text-center text-sm font-semibold text-white">
                Bản quyền © {{ now()->year }} LungCare AI. Thông tin AI chỉ dùng để hỗ trợ, không thay thế kết luận của bác sĩ.
            </div>
        </footer>

        <div class="no-print fixed bottom-6 right-6 z-30 hidden rounded-lg bg-white p-4 shadow-xl shadow-sky-200 ring-1 ring-sky-100 md:block">
            <p class="font-black text-slate-950">LungCare AI</p>
            <p class="mt-1 text-sm font-medium text-slate-500">Tôi có thể hỗ trợ đọc nhanh dữ liệu lâm sàng.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        Chart.defaults.font.family = "'Inter', 'Plus Jakarta Sans', sans-serif";
        Chart.defaults.font.weight = '600';
        Chart.defaults.color = '#475569';

        const pieRawData = @json($pieData);
        const hasData = pieRawData[0] > 0 || pieRawData[1] > 0;

        new Chart(document.getElementById('pieChart'), {
            type: 'doughnut',
            data: {
                labels: hasData ? ['Ác tính', 'Bình thường / lành tính'] : ['Chưa có dữ liệu'],
                datasets: [{
                    data: hasData ? pieRawData : [1],
                    backgroundColor: hasData ? ['#dc2626', '#10b981'] : ['#e2e8f0'],
                    borderWidth: hasData ? 6 : 1,
                    borderColor: '#ffffff',
                    hoverOffset: hasData ? 8 : 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        display: hasData,
                        labels: { padding: 18, usePointStyle: true, pointStyle: 'circle' }
                    },
                    tooltip: { enabled: hasData }
                }
            }
        });

        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Bệnh nhân mới',
                    data: @json($counts),
                    backgroundColor: '#0ea5e9',
                    hoverBackgroundColor: '#06488f',
                    borderRadius: 8,
                    barThickness: 34
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { drawBorder: false, color: '#e2e8f0' },
                        ticks: { precision: 0, stepSize: 1 }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        const sliderRoot = document.querySelector('.dashboard-slide')?.closest('section');
        const sliderSlides = Array.from(document.querySelectorAll('.dashboard-slide'));
        const sliderDots = Array.from(document.querySelectorAll('[data-slider-dot]'));
        const sliderPrev = document.querySelector('[data-slider-prev]');
        const sliderNext = document.querySelector('[data-slider-next]');
        let sliderIndex = 0;
        let sliderTimer = null;

        function showDashboardSlide(index) {
            if (!sliderSlides.length) return;

            sliderIndex = (index + sliderSlides.length) % sliderSlides.length;

            sliderSlides.forEach((slide, slideIndex) => {
                slide.classList.toggle('is-active', slideIndex === sliderIndex);
            });

            sliderDots.forEach((dot, dotIndex) => {
                dot.classList.toggle('is-active', dotIndex === sliderIndex);
                dot.classList.toggle('opacity-45', dotIndex !== sliderIndex);
                dot.classList.toggle('opacity-100', dotIndex === sliderIndex);
            });
        }

        function startDashboardSlider() {
            if (sliderSlides.length <= 1) return;
            stopDashboardSlider();
            sliderTimer = window.setInterval(() => showDashboardSlide(sliderIndex + 1), 5200);
        }

        function stopDashboardSlider() {
            if (sliderTimer) {
                window.clearInterval(sliderTimer);
                sliderTimer = null;
            }
        }

        sliderPrev?.addEventListener('click', () => {
            showDashboardSlide(sliderIndex - 1);
            startDashboardSlider();
        });

        sliderNext?.addEventListener('click', () => {
            showDashboardSlide(sliderIndex + 1);
            startDashboardSlider();
        });

        sliderDots.forEach((dot) => {
            dot.addEventListener('click', () => {
                showDashboardSlide(Number(dot.dataset.sliderDot));
                startDashboardSlider();
            });
        });

        sliderRoot?.addEventListener('mouseenter', stopDashboardSlider);
        sliderRoot?.addEventListener('mouseleave', startDashboardSlider);
        startDashboardSlider();

        function printClinicalReport() {
            window.print();
        }

        function closeHealthGuideReply() {
            document.getElementById('healthGuideReply')?.classList.add('hidden');
        }

        function setHealthGuideButtonsDisabled(disabled) {
            document.querySelectorAll('.health-guide-button').forEach((button) => {
                button.disabled = disabled;
                button.classList.toggle('opacity-60', disabled);
                button.classList.toggle('cursor-not-allowed', disabled);
            });
        }

        function escapeHtml(value) {
            return value.replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char]));
        }

        function formatHealthGuideReply(reply) {
            const lines = reply.split(/\r?\n/).map((line) => line.trim()).filter(Boolean);
            let html = '';
            let inList = false;

            lines.forEach((line) => {
                const bullet = line.match(/^[-*•]\s+(.*)$/);

                if (bullet) {
                    if (!inList) {
                        html += '<ul class="space-y-2">';
                        inList = true;
                    }

                    html += '<li class="flex gap-2"><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-sky-600"></span><span>' + escapeHtml(bullet[1]) + '</span></li>';
                    return;
                }

                if (inList) {
                    html += '</ul>';
                    inList = false;
                }

                html += '<p class="mb-3 last:mb-0">' + escapeHtml(line) + '</p>';
            });

            if (inList) {
                html += '</ul>';
            }

            return html || '<p>AI chưa trả về nội dung tư vấn.</p>';
        }

        async function askHealthGuide(button) {
            const panel = document.getElementById('healthGuideReply');
            const title = document.getElementById('healthGuideReplyTitle');
            const text = document.getElementById('healthGuideReplyText');
            const loading = document.getElementById('healthGuideLoading');
            const errorBox = document.getElementById('healthGuideError');
            const topic = button.dataset.guidePrompt;
            const topicTitle = button.dataset.guideTitle || 'Tư vấn sức khỏe';
            const originalButtonText = button.querySelector('span')?.textContent || 'Hỏi AI tư vấn';

            title.textContent = topicTitle;
            text.textContent = '';
            errorBox.textContent = '';
            errorBox.classList.add('hidden');
            loading.classList.remove('hidden');
            loading.classList.add('flex');
            panel.classList.remove('hidden');
            panel.scrollIntoView({ behavior: 'smooth', block: 'center' });

            setHealthGuideButtonsDisabled(true);
            if (button.querySelector('span')) {
                button.querySelector('span').textContent = 'Đang hỏi AI...';
            }

            const prompt = [
                'Bạn là trợ lý y khoa hỗ trợ bác sĩ tư vấn bệnh nhân về ung thư phổi.',
                'Trả lời bằng tiếng Việt, rõ ràng, ngắn gọn, có cấu trúc gạch đầu dòng.',
                'Không khẳng định chẩn đoán. Luôn nhắc người bệnh cần gặp bác sĩ khi có triệu chứng bất thường.',
                'Chủ đề: ' + topic
            ].join('\n');

            try {
                const reply = await sendGlobalMessage(prompt);
                loading.classList.add('hidden');
                loading.classList.remove('flex');

                if (!reply || reply.startsWith('Lỗi:') || reply.startsWith('AI bận')) {
                    errorBox.textContent = reply || 'AI chưa trả về nội dung tư vấn. Vui lòng thử lại.';
                    errorBox.classList.remove('hidden');
                    return;
                }

                text.innerHTML = formatHealthGuideReply(reply);
            } catch (error) {
                loading.classList.add('hidden');
                loading.classList.remove('flex');
                errorBox.textContent = 'Không thể lấy gợi ý từ AI lúc này. Vui lòng kiểm tra kết nối hoặc thử lại sau.';
                errorBox.classList.remove('hidden');
            } finally {
                setHealthGuideButtonsDisabled(false);
                if (button.querySelector('span')) {
                    button.querySelector('span').textContent = originalButtonText;
                }
            }
        }

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
