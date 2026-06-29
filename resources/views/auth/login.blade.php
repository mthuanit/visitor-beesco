<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Đăng nhập') }} - Visitor Control</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Hide default browser password reveal icon */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen relative overflow-hidden bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-200 transition-colors duration-250">
    
    <!-- Floating Language Switcher & Theme Toggle -->
    <div class="absolute top-6 right-6 z-20 flex items-center space-x-2 bg-white/85 border border-slate-200 dark:bg-slate-900/80 dark:border-slate-800/80 rounded-xl p-1.5 backdrop-blur-md shadow-lg shadow-black/5 dark:shadow-black/10 transition-colors duration-205">
        <div class="flex items-center space-x-1.5 text-[10px] font-bold">
            <a href="{{ route('lang.switch', 'vi') }}" class="px-2.5 py-1 rounded-lg transition-colors {{ app()->getLocale() === 'vi' ? 'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-600/30 dark:text-blue-400 dark:border-blue-500/20' : 'text-slate-400 hover:text-slate-650 dark:text-slate-500 dark:hover:text-slate-300' }}">VI</a>
            <a href="{{ route('lang.switch', 'en') }}" class="px-2.5 py-1 rounded-lg transition-colors {{ app()->getLocale() === 'en' ? 'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-600/30 dark:text-blue-400 dark:border-blue-500/20' : 'text-slate-400 hover:text-slate-650 dark:text-slate-500 dark:hover:text-slate-300' }}">EN</a>
        </div>
        <div class="h-4 border-l border-slate-250 dark:border-slate-800"></div>
        <!-- Theme Toggle Button -->
        <button id="btn-theme-toggle" class="text-slate-400 hover:text-slate-650 dark:text-slate-500 dark:hover:text-slate-300 transition-colors p-1" title="Toggle Light/Dark Theme">
            <i class="fas fa-moon dark:hidden text-sm"></i>
            <i class="fas fa-sun hidden dark:block text-sm text-amber-400"></i>
        </button>
    </div>

    <!-- Background Glow Effects -->
    <div class="absolute w-96 h-96 bg-blue-500/10 rounded-full blur-[100px] -top-20 -left-20"></div>
    <div class="absolute w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px] -bottom-20 -right-20"></div>

    <div class="bg-white/90 border border-slate-200 dark:bg-slate-900/80 dark:border-slate-800/80 p-8 rounded-2xl shadow-2xl w-96 text-center backdrop-blur-xl relative z-10 transition-colors duration-250">
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/logo.png') }}" alt="BEESCO Logo" class="h-16 sm:h-20 w-auto object-contain drop-shadow-md">
        </div>
        
        <h2 class="text-2xl font-bold tracking-wider text-slate-800 dark:text-slate-100 mb-1 transition-colors duration-250">{{ __('VISITOR CONTROL') }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 transition-colors duration-250">{{ __('Hệ thống Kiểm soát Khách ra vào') }}</p>

        <form action="/login" method="POST" class="space-y-5">
            @csrf
            
            <div class="text-left">
                <label for="username" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Tài khoản') }}</label>
                <div class="relative">
                    <input type="text" name="username" id="username" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-250 text-slate-850 focus:border-blue-500 dark:bg-slate-950 dark:border-slate-850 dark:focus:border-blue-500/80 dark:text-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all" placeholder="{{ __('Tên đăng nhập') }}" required autofocus>
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>

            <div class="text-left">
                <label for="password" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Mật khẩu') }}</label>
                <div class="relative">
                    <input type="password" name="password" id="password" class="w-full pl-11 pr-10 py-3 bg-slate-50 border border-slate-250 text-slate-850 focus:border-blue-500 dark:bg-slate-950 dark:border-slate-850 dark:focus:border-blue-500/80 dark:text-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all" placeholder="••••••••" required>
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                        <i class="fas fa-lock"></i>
                    </div>
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors focus:outline-none">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            @if($errors->has('username'))
                <div class="p-3.5 bg-rose-50 border border-rose-200 dark:bg-rose-950/40 text-rose-650 dark:text-rose-400 text-xs rounded-xl dark:border-rose-900/40 text-left flex items-start space-x-2">
                    <i class="fas fa-exclamation-circle mt-0.5 shrink-0"></i>
                    <span>{{ __($errors->first('username')) }}</span>
                </div>
            @endif

            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 active:scale-[0.98] transition-all text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/20 text-sm">
                {{ __('Đăng nhập') }}
            </button>
        </form>
        
        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800/80 text-[10px] text-slate-400 dark:text-slate-500 tracking-wider uppercase transition-colors duration-250">
             IT
        </div>
    </div>

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

        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (togglePassword) {
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle eye icon classes
                if (type === 'text') {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            });
        }
    </script>
</body>
</html>
