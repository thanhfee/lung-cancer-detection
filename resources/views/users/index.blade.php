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

            .doctor-card {
                transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
            }

            .doctor-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 18px 45px rgba(14, 116, 144, .14);
            }
        </style>
    </head>

    @php
        $doctorCount = $users->count();
        $lastUpdated = now()->timezone('Asia/Ho_Chi_Minh')->format('H:i - d/m/Y');
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-sky-700">LungCare AI</p>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-950">Danh sách bác sĩ</h2>
                <p class="mt-1 text-sm font-semibold text-slate-500">Quản lý tài khoản bác sĩ trong hệ thống chẩn đoán AI.</p>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-lg border border-sky-100 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Cập nhật: {{ $lastUpdated }}
            </span>
        </div>
    </x-slot>

    <div class="min-h-screen bg-[#eef8ff] pb-24">
        <main class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="page-reveal rounded-lg border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="page-reveal rounded-lg border border-red-100 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <section class="page-reveal doctor-card overflow-hidden rounded-lg bg-gradient-to-r from-white via-white to-sky-100 shadow-sm ring-1 ring-sky-100">
                <div class="grid gap-6 p-6 lg:grid-cols-[1fr_320px] lg:p-8">
                    <div>
                        <p class="inline-flex items-center gap-2 rounded-lg bg-sky-50 px-3 py-2 text-xs font-black uppercase tracking-[0.18em] text-sky-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Quản trị nhân sự y tế
                        </p>
                        <h1 class="mt-5 max-w-3xl text-3xl font-black leading-tight tracking-tight text-[#06488f] sm:text-4xl">
                            Theo dõi và quản lý tài khoản bác sĩ
                        </h1>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                            Danh sách bác sĩ được phép thao tác với hồ sơ bệnh nhân, quét AI và theo dõi kết quả chẩn đoán.
                        </p>
                    </div>

                    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-sky-100">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Tổng bác sĩ</p>
                        <p class="mt-3 text-4xl font-black text-[#06488f]">{{ $doctorCount }}</p>
                        <p class="mt-2 text-sm font-semibold text-slate-500">Tài khoản role doctor</p>
                    </div>
                </div>
            </section>

            <section class="page-reveal overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-100">
                <div class="flex flex-col gap-4 border-b border-slate-100 bg-gradient-to-r from-white via-sky-50 to-cyan-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-sky-700">Doctor list</p>
                        <h3 class="mt-1 text-lg font-black text-slate-950">Danh sách bác sĩ</h3>
                    </div>
                    <span class="inline-flex w-fit items-center gap-2 rounded-lg bg-white px-3 py-2 text-xs font-bold text-slate-500 ring-1 ring-slate-100">
                        <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                        {{ $doctorCount }} tài khoản
                    </span>
                </div>

                <div class="bg-slate-50/60 p-4 sm:p-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @forelse($users as $user)
                            @php
                                $initial = mb_substr($user->name, 0, 1, 'UTF-8');
                            @endphp

                            <article class="doctor-card flex min-h-[250px] flex-col overflow-hidden rounded-lg bg-white ring-1 ring-slate-100">
                                <div class="h-1.5 w-full bg-[#06488f]"></div>

                                <div class="flex items-start gap-4 p-5">
                                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-[#06488f] text-xl font-black text-white shadow-sm">
                                        {{ $initial }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="truncate text-lg font-black text-slate-950">{{ $user->name }}</h4>
                                        <p class="mt-1 break-all text-sm font-semibold text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 px-5">
                                    <div class="rounded-lg bg-slate-50 p-3 ring-1 ring-slate-100">
                                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">ID</p>
                                        <p class="mt-1 text-sm font-black text-slate-800">#{{ $user->id }}</p>
                                    </div>
                                    <div class="rounded-lg bg-emerald-50 p-3 ring-1 ring-emerald-100">
                                        <p class="text-xs font-black uppercase tracking-widest text-emerald-600">Vai trò</p>
                                        <p class="mt-1 text-sm font-black text-emerald-700">Doctor</p>
                                    </div>
                                </div>

                                <div class="mt-4 px-5">
                                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">Ngày tạo</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-600">{{ $user->created_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y') ?? 'Chưa có dữ liệu' }}</p>
                                </div>

                                <div class="mt-auto border-t border-slate-100 bg-slate-50/70 p-4">
                                    @if(auth()->user()->role === 'admin')
                                        <button type="button" onclick="confirmDeleteDoctor({{ $user->id }}, @js($user->name))" class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-4 text-sm font-extrabold text-red-600 transition hover:bg-red-50">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 7-.87 12.14A2 2 0 0 1 16.14 21H7.86a2 2 0 0 1-1.99-1.86L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>
                                            Xóa bác sĩ
                                        </button>

                                        <form id="delete-doctor-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @else
                                        <span class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-slate-100 px-4 text-sm font-bold text-slate-500">
                                            Chỉ admin được xóa
                                        </span>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full rounded-lg bg-white px-6 py-16 text-center ring-1 ring-slate-100">
                                <p class="font-bold text-slate-400">Chưa có tài khoản bác sĩ nào.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDeleteDoctor(id, name) {
            Swal.fire({
                title: 'Xóa bác sĩ?',
                text: `Tài khoản "${name}" sẽ bị xóa khỏi hệ thống. Các kết quả scan cũ sẽ được giữ lại nhưng không còn gắn bác sĩ này.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#06488f',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy bỏ',
                customClass: { popup: 'rounded-lg p-8' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-doctor-form-' + id).submit();
                }
            });
        }
    </script>
</x-app-layout>
