<x-app-layout>
    <head>
        <style>
            @keyframes pageFadeUp {
                from { opacity: 0; transform: translateY(16px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .page-reveal {
                animation: pageFadeUp .55s ease both;
            }

            .clinical-card,
            .patient-list-row {
                transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, background .22s ease;
            }

            .clinical-card:hover,
            .patient-list-row:hover {
                transform: translateY(-2px);
                box-shadow: 0 18px 45px rgba(14, 116, 144, .14);
            }
        </style>
    </head>

    @php
        $patientCollection = $patients->getCollection();
        $pageTotal = $patientCollection->count();
        $pageWithScan = $patientCollection->filter(fn ($patient) => $patient->scans->count() > 0)->count();
        $pagePending = max($pageTotal - $pageWithScan, 0);
        $pageMalignant = $patientCollection->filter(function ($patient) {
            $prediction = optional($patient->scans->first())->prediction;
            return $prediction && str_contains($prediction, 'Malignant');
        })->count();
        $pageNormal = $patientCollection->filter(function ($patient) {
            $prediction = optional($patient->scans->first())->prediction;
            return $prediction && !str_contains($prediction, 'Malignant');
        })->count();
        $lastUpdated = now()->timezone('Asia/Ho_Chi_Minh')->format('H:i - d/m/Y');
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-sky-700">LungCare AI</p>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-950">Hồ sơ bệnh nhân</h2>
                <p class="mt-1 text-sm font-semibold text-slate-500">Quản lý bệnh nhân, trạng thái quét AI và lịch sử chẩn đoán.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex w-fit items-center gap-2 rounded-lg border border-sky-100 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Cập nhật: {{ $lastUpdated }}
                </span>
                <a href="{{ route('patients.create') }}" class="inline-flex h-10 items-center rounded-lg bg-orange-500 px-4 text-sm font-extrabold text-white shadow-sm shadow-orange-100 transition hover:bg-orange-600">
                    Đăng ký bệnh nhân
                </a>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-[#eef8ff] pb-24">
        <main class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            <section class="page-reveal clinical-card overflow-hidden rounded-lg bg-gradient-to-r from-white via-white to-sky-100 shadow-sm ring-1 ring-sky-100">
                <div class="grid gap-6 p-6 lg:grid-cols-[1fr_360px] lg:p-8">
                    <div>
                        <p class="inline-flex items-center gap-2 rounded-lg bg-sky-50 px-3 py-2 text-xs font-black uppercase tracking-[0.18em] text-sky-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Quản lý hồ sơ lâm sàng
                        </p>
                        <h1 class="mt-5 max-w-3xl text-3xl font-black leading-tight tracking-tight text-[#06488f] sm:text-4xl">
                            Theo dõi bệnh nhân và trạng thái chẩn đoán AI
                        </h1>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                            Tìm kiếm hồ sơ, kiểm tra kết quả quét gần nhất và mở nhanh các thao tác cần thiết cho từng bệnh nhân.
                        </p>
                    </div>

                    <form action="{{ route('patients.index') }}" method="GET" class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-sky-100">
                        <label for="patient-search" class="text-xs font-black uppercase tracking-widest text-slate-400">Tìm kiếm hồ sơ</label>
                        <div class="mt-3 flex gap-2">
                            <div class="relative min-w-0 flex-1">
                                <input id="patient-search" type="text" name="search" value="{{ request('search') }}" placeholder="Tên hoặc mã bệnh nhân..." class="h-11 w-full rounded-lg border-0 bg-slate-50 pl-10 pr-4 text-sm font-semibold text-slate-700 ring-1 ring-slate-100 placeholder:text-slate-400 focus:ring-2 focus:ring-sky-500">
                                <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/></svg>
                            </div>
                            <button type="submit" class="inline-flex h-11 items-center rounded-lg bg-[#06488f] px-4 text-sm font-extrabold text-white transition hover:bg-[#053a73]">Lọc</button>
                        </div>
                        @if(request('search'))
                            <a href="{{ route('patients.index') }}" class="mt-3 inline-flex text-sm font-bold text-sky-700 hover:text-[#06488f]">Xóa bộ lọc</a>
                        @endif
                    </form>
                </div>
            </section>

            <section class="page-reveal grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="clinical-card rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-100">
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">Tổng hồ sơ</p>
                    <p class="mt-3 text-3xl font-black text-slate-950">{{ $patients->total() }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-500">Theo bộ lọc hiện tại</p>
                </div>
                <div class="clinical-card rounded-lg bg-red-50 p-5 shadow-sm ring-1 ring-red-100">
                    <p class="text-xs font-black uppercase tracking-widest text-red-500">Nguy cơ cao</p>
                    <p class="mt-3 text-3xl font-black text-red-700">{{ $pageMalignant }}</p>
                    <p class="mt-2 text-sm font-semibold text-red-700/70">Trong trang hiện tại</p>
                </div>
                <div class="clinical-card rounded-lg bg-emerald-50 p-5 shadow-sm ring-1 ring-emerald-100">
                    <p class="text-xs font-black uppercase tracking-widest text-emerald-600">Đã phân loại</p>
                    <p class="mt-3 text-3xl font-black text-emerald-700">{{ $pageNormal }}</p>
                    <p class="mt-2 text-sm font-semibold text-emerald-700/70">Bình thường / lành tính</p>
                </div>
                <div class="clinical-card rounded-lg bg-amber-50 p-5 shadow-sm ring-1 ring-amber-100">
                    <p class="text-xs font-black uppercase tracking-widest text-amber-600">Chờ quét AI</p>
                    <p class="mt-3 text-3xl font-black text-amber-700">{{ $pagePending }}</p>
                    <p class="mt-2 text-sm font-semibold text-amber-700/70">Cần bổ sung ảnh scan</p>
                </div>
            </section>

            <section class="page-reveal overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-100">
                <div class="flex flex-col gap-4 border-b border-slate-100 bg-gradient-to-r from-white via-sky-50 to-cyan-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-sky-700">Danh sách hồ sơ</p>
                        <h3 class="mt-1 text-lg font-black text-slate-950">Bệnh nhân trong hệ thống</h3>
                    </div>
                    <span class="inline-flex w-fit items-center gap-2 rounded-lg bg-white px-3 py-2 text-xs font-bold text-slate-500 ring-1 ring-slate-100">
                        <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                        Trang {{ $patients->currentPage() }} / {{ $patients->lastPage() }}
                    </span>
                </div>

                <div class="bg-slate-50/60 p-4 sm:p-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @forelse($patients as $patient)
                            @php
                                $lastScan = $patient->scans->first();
                                $prediction = $lastScan?->prediction;
                                $isMalignant = $prediction && str_contains($prediction, 'Malignant');
                                $initial = mb_substr($patient->name, 0, 1, 'UTF-8');
                                $latestConfidence = $lastScan?->confidence_score ?? 0;
                                $latestConfidencePercent = $latestConfidence <= 1 ? $latestConfidence * 100 : $latestConfidence;
                                $latestConfidencePercent = max(0, min(100, $latestConfidencePercent));
                                $statusLabel = $prediction ?: 'Chưa quét';
                                $statusClass = $prediction
                                    ? ($isMalignant ? 'bg-red-50 text-red-700 ring-red-100' : 'bg-emerald-50 text-emerald-700 ring-emerald-100')
                                    : 'bg-amber-50 text-amber-700 ring-amber-100';
                                $dotClass = $prediction ? ($isMalignant ? 'bg-red-600' : 'bg-emerald-600') : 'bg-amber-500';
                            @endphp

                            <article class="patient-list-row relative flex min-h-[320px] flex-col overflow-hidden rounded-lg bg-white ring-1 ring-slate-100">
                                <div class="h-1.5 w-full {{ $dotClass }}"></div>

                                <div class="flex items-start justify-between gap-3 p-5">
                                    <div class="flex min-w-0 items-center gap-4">
                                        <div class="relative flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-[#06488f] text-xl font-black text-white shadow-sm">
                                            {{ $initial }}
                                            <span class="absolute -right-1 -top-1 h-3.5 w-3.5 rounded-full ring-2 ring-white {{ $dotClass }}"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="truncate text-lg font-black text-slate-950">{{ $patient->name }}</h4>
                                            <p class="mt-1 text-xs font-black uppercase tracking-widest text-slate-400">#{{ $patient->patient_code }}</p>
                                        </div>
                                    </div>

                                    <span class="inline-flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-[11px] font-black uppercase tracking-wide ring-1 {{ $statusClass }}">
                                        <span class="h-2 w-2 rounded-full {{ $dotClass }}"></span>
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 px-5">
                                    <div class="rounded-lg bg-slate-50 p-3 ring-1 ring-slate-100">
                                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Tuổi</p>
                                        <p class="mt-1 text-sm font-black text-slate-800">{{ $patient->age }} tuổi</p>
                                    </div>
                                    <div class="rounded-lg bg-slate-50 p-3 ring-1 ring-slate-100">
                                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Giới tính</p>
                                        <p class="mt-1 text-sm font-black text-slate-800">{{ $patient->gender == 'Male' ? 'Nam' : 'Nữ' }}</p>
                                    </div>
                                </div>

                                <div class="px-5 pt-4">
                                    <div class="flex items-center justify-between text-xs font-black uppercase tracking-widest text-slate-400">
                                        <span>Độ tin cậy AI</span>
                                        <span class="text-sky-700">{{ number_format($latestConfidencePercent, 1) }}%</span>
                                    </div>
                                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full {{ $isMalignant ? 'bg-red-500' : ($prediction ? 'bg-emerald-500' : 'bg-amber-400') }}" style="width: {{ $latestConfidencePercent }}%"></div>
                                    </div>
                                </div>

                                <div class="mt-4 px-5">
                                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">Ngày tạo</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-600">{{ $patient->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y') }}</p>
                                </div>

                                <div class="mt-auto border-t border-slate-100 bg-slate-50/70 p-4">
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('patients.show', $patient->id) }}" class="inline-flex h-10 items-center justify-center rounded-lg bg-[#06488f] px-3 text-sm font-extrabold text-white transition hover:bg-[#053a73]" title="Xem chi tiết">
                                            Chi tiết
                                        </a>
                                        <a href="{{ route('patients.scan', $patient->id) }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-sky-200 bg-white px-3 text-sm font-extrabold text-sky-700 transition hover:bg-sky-50" title="Quét AI">
                                            Quét AI
                                        </a>
                                    </div>

                                    @if(auth()->user()->role === 'admin')
                                        <div class="mt-2 grid grid-cols-3 gap-2">
                                            <a href="{{ route('patients.edit', $patient->id) }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-orange-200 hover:bg-orange-50 hover:text-orange-600" title="Sửa hồ sơ">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.41-9.41a2 2 0 1 1 2.82 2.82L11.83 15H9v-2.83l8.59-8.58z"/></svg>
                                            </a>
                                            <button type="button" onclick="openGlobalChat({{ $patient->id }})" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600" title="Nhắn tin với AI">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-5l-5 5v-5z"/></svg>
                                            </button>
                                            <button type="button" onclick="confirmDelete({{ $patient->id }})" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600" title="Xóa hồ sơ">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 7-.87 12.14A2 2 0 0 1 16.14 21H7.86a2 2 0 0 1-1.99-1.86L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>

                                        <form id="delete-form-{{ $patient->id }}" action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full rounded-lg bg-white px-6 py-16 text-center ring-1 ring-slate-100">
                                <p class="font-bold text-slate-400">Không tìm thấy bệnh nhân nào.</p>
                                <a href="{{ route('patients.create') }}" class="mt-4 inline-flex h-10 items-center rounded-lg bg-[#06488f] px-4 text-sm font-extrabold text-white transition hover:bg-[#053a73]">Đăng ký bệnh nhân mới</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            @if($patients->hasPages())
                <div class="page-reveal rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-100">
                    {{ $patients->appends(request()->query())->links() }}
                </div>
            @endif
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Xóa hồ sơ bệnh nhân?',
                text: 'Dữ liệu và lịch sử quét AI sẽ bị xóa vĩnh viễn.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#06488f',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy bỏ',
                customClass: { popup: 'rounded-lg p-8' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                customClass: { popup: 'rounded-lg' }
            });
        @endif
    </script>
</x-app-layout>
