<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* Главна магија: Кога е штиклирано (затворено), смени ја ширината */
        #sidebar-toggle:checked~aside {
            width: 5rem;
        }

        /* Примени collapse и пред да се иницијализира checkbox-от (без скок на релоад). */
        html.sidebar-collapsed aside {
            width: 5rem;
        }

        /* w-20 */
        #sidebar-toggle:checked~aside .hide-on-collapse {
            display: none;
        }

        html.sidebar-collapsed aside .hide-on-collapse {
            display: none;
        }

        #sidebar-toggle:checked~aside .rotate-icon {
            transform: rotate(180deg);
        }

        html.sidebar-collapsed aside .rotate-icon {
            transform: rotate(180deg);
        }

        #sidebar-toggle:checked~aside .center-on-collapse {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        html.sidebar-collapsed aside .center-on-collapse {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        .sidebar-transition {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
    <script>
        (function () {
            try {
                if (localStorage.getItem('sidebar-collapsed') === '1') {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch (e) {
                // Ignore storage access errors and continue with default expanded state.
            }
        })();

        document.addEventListener('DOMContentLoaded', function () {
            var sidebarToggle = document.getElementById('sidebar-toggle');
            if (!sidebarToggle) {
                return;
            }

            var isCollapsed = document.documentElement.classList.contains('sidebar-collapsed');
            sidebarToggle.checked = isCollapsed;

            sidebarToggle.addEventListener('change', function () {
                var collapsed = sidebarToggle.checked;

                document.documentElement.classList.toggle('sidebar-collapsed', collapsed);

                try {
                    localStorage.setItem('sidebar-collapsed', collapsed ? '1' : '0');
                } catch (e) {
                    // Ignore storage access errors.
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="font-sans antialiased bg-gray-100 overflow-hidden">
    <div class="flex h-screen overflow-hidden">

        <input type="checkbox" id="sidebar-toggle" class="hidden">

        <div x-data="{ mobileMenuOpen: false }" @keydown.escape.window="mobileMenuOpen = false"
            class="md:hidden fixed top-0 left-0 right-0 z-30 bg-white border-b border-gray-100 shadow-sm">
            <div class="px-4 py-3">
                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <img src="{{ asset('storage/logos/logo.png') }}" alt="Logo" class="h-8 w-auto object-contain">
                    </a>
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen"
                        class="inline-flex items-center justify-center p-2 rounded-lg border border-gray-200 text-slate-600 bg-white hover:bg-gray-50 transition"
                        aria-label="Отвори мени">
                        <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                @if(auth()->user()->role === 'admin')
                    @php
                        $mobileAdminItems = [
                            ['route' => 'dashboard', 'label' => 'Командна табла', 'icon' => 'M3 3h7v9H3V3zm11 0h7v5h-7V3zm0 9h7v9h-7v-9zm-11 4h7v5H3v-5z'],
                            ['route' => 'students', 'label' => 'Ученици', 'icon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z M22 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75'],
                            ['route' => 'student-prices', 'label' => 'Ценовник', 'icon' => 'M12 2v20 M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'],
                            ['route' => 'lessons-log', 'label' => 'Дневник', 'icon' => 'M12 7v14 M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z'],
                            ['route' => 'invoices', 'label' => 'Фактури', 'icon' => 'M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z M14 2v4a2 2 0 0 0 2 2h4 M10 9H8 M16 13H8 M16 17H8'],
                            ['route' => 'company-info', 'label' => 'Моја Фирма', 'icon' => 'M3 21h18 M9 8h1 M9 12h1 M9 16h1 M14 8h1 M14 12h1 M14 16h1 M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16'],
                        ];
                    @endphp
                @endif

                <div x-show="mobileMenuOpen" x-transition.opacity
                    class="fixed inset-0 top-[61px] bg-slate-900/25 backdrop-blur-[1px]" @click="mobileMenuOpen = false"
                    style="display: none;"></div>

                <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-250"
                    x-transition:enter-start="opacity-0 -translate-y-3 scale-[0.98]"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 -translate-y-2 scale-[0.98]"
                    class="absolute left-3 right-3 top-full mt-3" style="display: none;">
                    <div
                        class="flex max-h-[calc(100vh-90px)] flex-col overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-2xl shadow-slate-200/80">
                        <nav class="flex-1 overflow-y-auto px-3 py-2.5 space-y-1">
                            @if(auth()->user()->role === 'admin')
                                @foreach($mobileAdminItems as $item)
                                    @if(Route::has($item['route']))
                                        <a href="{{ route($item['route']) }}" @click="mobileMenuOpen = false"
                                            class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs($item['route']) ? 'bg-blue-50 text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-gray-50 hover:text-slate-900' }}">
                                            <span
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl {{ request()->routeIs($item['route']) ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-slate-500' }}">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="{{ $item['icon'] }}"></path>
                                                </svg>
                                            </span>
                                            <span>{{ $item['label'] }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            @endif

                            @if(auth()->user()->role === 'student')
                                <a href="{{ route('student.my-statistic') }}" @click="mobileMenuOpen = false"
                                    class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('student.my-statistic') ? 'bg-blue-50 text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-gray-50 hover:text-slate-900' }}">
                                    <span
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl {{ request()->routeIs('student.my-statistic') ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-slate-500' }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </span>
                                    <span>{{ auth()->user()->role === 'admin' ? 'Моја Статистика' : 'My Statistics' }}</span>
                                </a>
                            @endif

                            <a href="{{ route('profile') }}" @click="mobileMenuOpen = false"
                                class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('profile*') ? 'bg-blue-50 text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-gray-50 hover:text-slate-900' }}">
                                <span
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl {{ request()->routeIs('profile*') ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-slate-500' }}">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                                <span>{{ auth()->user()->role === 'admin' ? 'Мој Профил' : 'My Profile' }}</span>
                            </a>
                        </nav>

                        <div class="border-t border-gray-100 bg-gray-50/80 px-4 py-3 space-y-2.5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-600 font-bold text-white shadow-lg shadow-blue-100">
                                    {{ auth()->user()->initials() }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p>
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 font-black">
                                        {{ auth()->user()->role === 'admin' ? 'Администратор' : 'Student' }}
                                    </p>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full rounded-xl bg-red-50 px-4 py-2.5 text-sm font-bold text-red-600 transition hover:bg-red-100">
                                    {{ auth()->user()->role === 'admin' ? 'Одјави се' : 'Log Out' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <aside
            class="sidebar-transition w-64 bg-white text-slate-600 flex-shrink-0 hidden md:flex flex-col shadow-sm border-r border-gray-100 overflow-hidden">

            <div class="h-16 flex items-center justify-between px-6 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center">
                    <img src="{{ asset('storage/logos/logo.png') }}" alt="Logo"
                        class="hide-on-collapse h-10 w-auto object-contain">

                    <img src="{{ asset('storage/logos/logo.png') }}" alt="Logo"
                        class="hidden show-on-collapse h-8 w-8 object-contain mx-auto">
                </div>

                <label for="sidebar-toggle"
                    class="cursor-pointer text-slate-400 hover:text-blue-600 p-1 bg-gray-50 rounded-lg transition-colors">
                    <svg class="w-5 h-5 rotate-icon transition-transform duration-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </label>
            </div>

            <nav class="flex-1 overflow-y-auto py-6 overflow-x-hidden space-y-1">
                @if(auth()->user()->role === 'admin')
                    @php
                        $adminItems = [
                            ['route' => 'dashboard', 'label' => 'Командна табла', 'icon' => 'M3 3h7v9H3V3zm11 0h7v5h-7V3zm0 9h7v9h-7v-9zm-11 4h7v5H3v-5z'],
                            ['route' => 'lessons-log', 'label' => 'Дневник', 'icon' => 'M12 7v14 M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z'],
                            ['route' => 'invoices', 'label' => 'Фактури', 'icon' => 'M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z M14 2v4a2 2 0 0 0 2 2h4 M10 9H8 M16 13H8 M16 17H8'],
                            ['route' => 'students', 'label' => 'Ученици', 'icon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z M22 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75'],
                            ['route' => 'student-prices', 'label' => 'Ценовник', 'icon' => 'M12 2v20 M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'],
                            ['route' => 'company-info', 'label' => 'Моја Фирма', 'icon' => 'M3 21h18 M9 8h1 M9 12h1 M9 16h1 M14 8h1 M14 12h1 M14 16h1 M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16'],
                        ];
                    @endphp

                    @foreach($adminItems as $item)
                        @if(Route::has($item['route']))
                            <a href="{{ route($item['route']) }}"
                                class="center-on-collapse flex items-center h-11 px-6 mx-3 rounded-xl transition-all duration-200 group {{ request()->routeIs($item['route']) ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-500 hover:bg-gray-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs($item['route']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}">
                                    </path>
                                </svg>
                                <span class="hide-on-collapse ml-3 font-semibold text-sm truncate">{{ $item['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                @endif

                {{-- Student Section --}}
                @if(auth()->user()->role === 'student')
                    <a href="{{ route('student.my-statistic') }}"
                        class="center-on-collapse flex items-center h-11 px-6 mx-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('student.my-statistic') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-500 hover:bg-gray-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('student.my-statistic') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span
                            class="hide-on-collapse ml-3 font-semibold text-sm truncate">{{ auth()->user()->role === 'admin' ? 'Моја Статистика' : 'My Statistics' }}</span>
                    </a>
                @endif

                <div class="my-4 border-t border-gray-100 mx-6 hide-on-collapse"></div>

                <a href="{{ route('profile') }}"
                    class="center-on-collapse flex items-center h-11 px-6 mx-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('profile*') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-500 hover:bg-gray-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('profile*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                        stroke-linejoin="round">
                        {{-- Lucide: User icon path --}}
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span
                        class="hide-on-collapse ml-3 font-semibold text-sm">{{ auth()->user()->role === 'admin' ? 'Мој Профил' : 'My Profile' }}</span>
                </a>
            </nav>

            <div class="p-4 bg-gray-50/50 flex items-center center-on-collapse border-t border-gray-100">
                <div
                    class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white shadow-lg shadow-blue-100 flex-shrink-0">
                    {{ auth()->user()->initials() }}
                </div>
                <div class="ml-3 overflow-hidden hide-on-collapse">
                    <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">
                        {{ auth()->user()->role === 'admin' ? 'Администратор' : 'Student' }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                @csrf
                <button type="submit"
                    class="center-on-collapse w-full flex items-center h-12 px-6 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-all group">
                    <svg class="w-5 h-5 flex-shrink-0 group-hover:scale-110 transition-transform" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                        stroke-linejoin="round">
                        {{-- Lucide: Log Out icon --}}
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span
                        class="hide-on-collapse ml-3 text-sm font-bold">{{ auth()->user()->role === 'admin' ? 'Одјави се' : 'Log Out' }}</span>
                </button>
            </form>
        </aside>

        <main class="flex-1 overflow-y-auto bg-gray-50 pt-20 md:pt-0">
            <div class="py-4 md:py-6 px-4 sm:px-6 md:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
    @livewireScripts
</body>

</html>