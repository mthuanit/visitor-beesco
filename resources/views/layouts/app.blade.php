<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VISITOR CONTROL - BEESCO</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <script>
        // Check local storage or default to light
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            padding-bottom: {{ Route::currentRouteName() === 'visitors.gate' ? '45px' : '70px' }};
        }
        /* Custom scrollbar for premium feel */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .dark ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }
        html:not(.dark) ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        html:not(.dark) ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        html:not(.dark) ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        /* Global date picker color override */
        .dark input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
        @if(request()->has('iframe'))
        body {
            padding-bottom: 0 !important;
        }
        main {
            padding: 1.25rem !important;
            max-width: 100% !important;
        }
        @endif
    </style>
</head>
<body class="min-h-screen relative bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-200 transition-colors duration-200">

    @if(!request()->has('iframe'))
    <nav class="bg-white border-b border-slate-200 dark:bg-slate-900 dark:border-slate-800 shadow-md dark:shadow-black/10 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="BEESCO Logo" class="h-8 sm:h-10 w-auto">
                    </a>
                </div>
                <div class="flex items-center space-x-2">

                    <!-- Dynamic Navigation based on Mode -->
                    @if(!request()->is('*trucks*'))
                        <!-- Visitor Mode Menu -->
                        @if(auth()->check() && !auth()->user()->isFactoryAccount())
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ Route::currentRouteName() === 'admin.dashboard' ? 'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-600/20 dark:text-blue-400 dark:border-blue-500/30' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}">
                            <i class="fas fa-chart-line mr-1.5"></i>{{ __('Dashboard') }}
                        </a>
                        @endif
                        @if(!auth()->check() || !auth()->user()->isManagerAccount())
                        <a href="{{ route('visitors.gate') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ Route::currentRouteName() === 'visitors.gate' ? 'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-600/20 dark:text-blue-400 dark:border-blue-500/30' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}">
                            <i class="fas fa-door-open mr-1.5"></i>{{ __('Kiểm soát Cổng') }}
                        </a>
                        @endif
                        <a href="{{ route('visitors.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ Route::currentRouteName() === 'visitors.index' ? 'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-600/20 dark:text-blue-400 dark:border-blue-500/30' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}">
                            <i class="fas fa-users mr-1.5"></i>{{ __('Danh sách') }}
                        </a>
                        <a href="{{ route('cards.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ Route::currentRouteName() === 'cards.index' ? 'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-600/20 dark:text-blue-400 dark:border-blue-500/30' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}">
                            <i class="fas fa-id-card mr-1.5"></i>{{ __('Trạng thái Thẻ') }}
                        </a>
                        
                        @if(auth()->check() && !auth()->user()->isFactoryAccount() && !auth()->user()->isManagerAccount())
                        <a href="{{ route('admin.login-history') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ Route::currentRouteName() === 'admin.login-history' ? 'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-600/20 dark:text-blue-400 dark:border-blue-500/30' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}">
                            <i class="fas fa-history mr-1.5"></i>{{ __('Lịch sử Đăng nhập') }}
                        </a>
                        @endif
                    @else
                        <!-- Truck Mode Menu -->
                        @if(auth()->check())
                        <a href="{{ route('admin.trucks.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ Route::currentRouteName() === 'admin.trucks.dashboard' ? 'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-600/20 dark:text-blue-400 dark:border-blue-500/30' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}">
                            <i class="fas fa-chart-line mr-1.5"></i>{{ __('Hoạt động') }}
                        </a>
                        @endif
                        @if(!auth()->check() || !auth()->user()->isManagerAccount())
                        <a href="{{ route('trucks.gate') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ Route::currentRouteName() === 'trucks.gate' ? 'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-600/20 dark:text-blue-400 dark:border-blue-500/30' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}">
                            <i class="fas fa-door-open mr-1.5"></i>{{ __('Kiểm soát Cổng') }}
                        </a>
                        @endif
                        @if(auth()->check() && !auth()->user()->isFactoryAccount())
                        <a href="{{ route('admin.trucks.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ Route::currentRouteName() === 'admin.trucks.index' ? 'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-600/20 dark:text-blue-400 dark:border-blue-500/30' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}">
                            <i class="fas fa-truck-moving mr-1.5"></i>{{ __('Quản lý Xe tải') }}
                        </a>
                        <a href="{{ route('admin.drivers.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ Route::currentRouteName() === 'admin.drivers.index' ? 'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-600/20 dark:text-blue-400 dark:border-blue-500/30' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}">
                            <i class="fas fa-user-tie mr-1.5"></i>{{ __('Quản lý Tài xế') }}
                        </a>
                        @endif
                    @endif
                    
                    <!-- Language Switcher -->
                    <div class="ml-4 pl-4 border-l border-slate-250 dark:border-slate-800 flex items-center space-x-1.5 text-[10px] font-bold">
                        <a href="{{ route('lang.switch', 'vi') }}" class="px-2 py-1 rounded transition-colors {{ app()->getLocale() === 'vi' ? 'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-600/30 dark:text-blue-400 dark:border-blue-500/20' : 'text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300' }}">VI</a>
                        <a href="{{ route('lang.switch', 'en') }}" class="px-2 py-1 rounded transition-colors {{ app()->getLocale() === 'en' ? 'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-600/30 dark:text-blue-400 dark:border-blue-500/20' : 'text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300' }}">EN</a>
                    </div>

                    <!-- Theme Toggle Button -->
                    <button id="btn-theme-toggle" class="ml-4 pl-4 border-l border-slate-250 dark:border-slate-800 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors p-1 flex items-center justify-center" title="Toggle Light/Dark Theme">
                        <span class="dark:hidden flex items-center"><i class="fas fa-moon text-sm"></i></span>
                        <span class="hidden dark:flex items-center"><i class="fas fa-sun text-sm text-amber-400"></i></span>
                    </button>

                    <!-- User Info & Logout Button -->
                    @if(auth()->check())
                    <form action="{{ route('logout') }}" method="POST" class="ml-4 pl-4 border-l border-slate-250 dark:border-slate-800 flex items-center mt-[1px]">
                        @csrf
                        <div class="mr-4 flex items-center text-xs font-bold text-slate-600 dark:text-slate-300">
                            <i class="fas fa-user-circle mr-1.5 text-blue-500 text-sm"></i>
                            @php
                                $roleLabel = 'ADMIN';
                                if (auth()->user()->isFactoryAccount()) {
                                    $roleLabel = 'XƯỞNG ' . auth()->user()->factory_code;
                                } elseif (auth()->user()->isManagerAccount()) {
                                    $roleLabel = 'MANAGER';
                                }
                            @endphp
                            {{ $roleLabel }}
                        </div>
                        <button type="submit" class="text-xs font-semibold text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors flex items-center space-x-1 border-l pl-4 border-slate-200 dark:border-slate-700">
                            <i class="fas fa-sign-out-alt text-sm"></i>
                            <span class="mt-[1px]">{{ __('Đăng xuất') }}</span>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </nav>
    @endif

    <div class="flex">
        <!-- Sidebar Mode Switcher on the Left Edge -->
        @if(!request()->has('iframe') && auth()->check())
        <aside class="w-20 flex flex-col items-center py-6 shrink-0 gap-4">
            <a href="{{ auth()->user()->isFactoryAccount() ? route('visitors.gate') : route('admin.dashboard') }}" 
               class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center text-center transition-all active:scale-95 {{ !request()->is('*trucks*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'bg-slate-200 text-slate-500 hover:text-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-700' }}"
               title="{{ __('Chế độ Khách (Visitor)') }}">
                <i class="fas fa-user-friends text-lg"></i>
                <span class="text-[9px] font-black mt-1">{{ __('Khách') }}</span>
            </a>
            <a href="{{ auth()->user()->isFactoryAccount() ? route('trucks.gate') : route('admin.trucks.dashboard') }}" 
               class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center text-center transition-all active:scale-95 {{ request()->is('*trucks*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'bg-slate-200 text-slate-500 hover:text-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-700' }}"
               title="{{ __('Chế độ Xe tải (Truck)') }}">
                <i class="fas fa-truck text-lg"></i>
                <span class="text-[9px] font-black mt-1">{{ __('Xe tải') }}</span>
            </a>
        </aside>
        @endif

        <!-- Main Content Area -->
        <div class="flex-1 min-w-0">
            <main class="{{ in_array(Route::currentRouteName(), ['visitors.gate', 'trucks.gate']) ? 'max-w-[95%] xl:max-w-[1450px] py-2' : 'max-w-7xl py-8' }} mx-auto px-4">
                @yield('content')
            </main>
        </div>
    </div>

    @if(!request()->has('iframe'))
    <footer class="absolute bottom-0 w-full text-center py-2.5 bg-white border-t border-slate-200 dark:bg-slate-900 dark:border-slate-800 text-xs text-slate-500 font-medium transition-colors duration-200">
         <span class="text-slate-400">IT</span>
    </footer>
    @endif

    <script>
        const btnThemeToggle = document.getElementById('btn-theme-toggle');
        if (btnThemeToggle) {
            btnThemeToggle.addEventListener('click', () => {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            });
        }
    </script>
</body>
</html>
