<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-sky-100 bg-cyan-100/90 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-white shadow-sm">
                        <span class="text-lg font-black text-[#0a8ed8]">LC</span>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-base font-black leading-5 text-[#06488f]">LungCare AI</p>
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-700">Clinical Intelligence</p>
                    </div>
                </a>

                <div class="hidden items-center gap-2 md:flex">
                    <a href="{{ route('dashboard') }}" class="rounded-lg px-4 py-2 text-sm font-extrabold transition {{ request()->routeIs('dashboard') ? 'bg-white text-[#06488f] shadow-sm' : 'text-[#06488f] hover:bg-white/70' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('patients.index') }}" class="rounded-lg px-4 py-2 text-sm font-extrabold transition {{ request()->routeIs('patients.*') ? 'bg-white text-[#06488f] shadow-sm' : 'text-[#06488f] hover:bg-white/70' }}">
                        Bệnh nhân
                    </a>
                    <a href="{{ route('news.index') }}" class="rounded-lg px-4 py-2 text-sm font-extrabold transition {{ request()->routeIs('news.*') ? 'bg-white text-[#06488f] shadow-sm' : 'text-[#06488f] hover:bg-white/70' }}">
                        Tin tức
                    </a>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('users.index') }}" class="rounded-lg px-4 py-2 text-sm font-extrabold transition {{ request()->routeIs('users.*') ? 'bg-white text-[#06488f] shadow-sm' : 'text-[#06488f] hover:bg-white/70' }}">
                            Bác sĩ
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                <div class="text-right">
                    <p class="text-sm font-black text-slate-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs font-semibold text-sky-700">{{ ucfirst(Auth::user()->role ?? 'user') }}</p>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex h-11 items-center gap-2 rounded-lg bg-white px-3 text-sm font-bold text-slate-600 shadow-sm ring-1 ring-sky-100 transition hover:bg-sky-50">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#06488f] text-xs font-black text-white">
                                {{ mb_substr(Auth::user()->name, 0, 1, 'UTF-8') }}
                            </span>
                            <svg class="h-4 w-4 text-sky-700" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.29 7.29a1 1 0 0 1 1.42 0L10 10.59l3.29-3.3a1 1 0 1 1 1.42 1.42l-4 4a1 1 0 0 1-1.42 0l-4-4a1 1 0 0 1 0-1.42z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Hồ sơ cá nhân
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Đăng xuất
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex md:hidden">
                <button @click="open = ! open" class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-white text-[#06488f] shadow-sm ring-1 ring-sky-100">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-sky-100 bg-white md:hidden">
        <div class="space-y-1 px-4 py-3">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')">
                Bệnh nhân
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('news.index')" :active="request()->routeIs('news.*')">
                Tin tức
            </x-responsive-nav-link>
            @if(Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    Bác sĩ
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="border-t border-slate-100 px-4 py-3">
            <div class="font-black text-slate-900">{{ Auth::user()->name }}</div>
            <div class="text-sm font-semibold text-slate-500">{{ Auth::user()->email }}</div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Hồ sơ cá nhân
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Đăng xuất
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
