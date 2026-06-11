<!doctype html>
<html lang="ar" dir="rtl" data-bs-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#0f1029">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
        <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
        <link rel="icon" href="{{ asset('icons/icon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset('icons/icon.svg') }}">

        @php
            $defaultRobots = request()->routeIs(
                'dashboard',
                'dashboard.avatar.update',
                'notifications.*',
                'subscription-plans.checkout',
                'subscription-plans.checkout.store',
                'subscription-orders.show',
                'login',
                'login.store',
                'register',
                'register.store',
                'admin.*',
            ) ? 'noindex, nofollow' : null;
        @endphp

        <x-seo-meta
            :title="trim($__env->yieldContent('title')) ?: null"
            :description="trim($__env->yieldContent('meta_description')) ?: null"
            :canonical="trim($__env->yieldContent('canonical')) ?: null"
            :image="trim($__env->yieldContent('meta_image')) ?: null"
            :type="trim($__env->yieldContent('meta_type')) ?: 'website'"
            :robots="trim($__env->yieldContent('robots')) ?: $defaultRobots"
        />

        <x-google-analytics />

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.rtl.min.css') }}">
        @stack('head')
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body class="d-flex flex-column min-vh-100">
        <x-demo-mode-banner />

        <header class="site-header sticky-top">
            <nav class="container py-3 d-flex align-items-center justify-content-between gap-3">
                <x-site-logo />

                <div class="d-none d-lg-flex align-items-center gap-4">
                    <a href="{{ route('learning-paths.index') }}" class="site-nav-link">المسارات</a>
                    <a href="{{ route('community.index') }}" class="site-nav-link">المجتمع</a>
                    <a href="{{ route('live-events.index') }}" class="site-nav-link">اللايفات</a>
                    <a href="{{ route('blog.index') }}" class="site-nav-link">المدونة</a>
                    <a href="{{ route('subscription-plans.index') }}" class="site-nav-link">الاشتراكات</a>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-soft" type="button" data-theme-toggle>
                        تبديل المظهر
                    </button>

                    @auth
                        <a class="btn btn-soft position-relative d-none d-md-inline-flex" href="{{ route('notifications.index') }}" aria-label="الإشعارات">
                            إشعارات
                            @if ($unreadNotificationsCount > 0)
                                <span class="notification-badge">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                            @endif
                        </a>
                        @if (auth()->user()->is_admin)
                            <a class="btn btn-soft d-none d-md-inline-flex" href="{{ route('admin.dashboard') }}">الإدارة</a>
                        @endif
                        <a class="btn btn-brand d-none d-sm-inline-flex" href="{{ route('dashboard') }}">لوحتي</a>
                    @else
                        <a class="btn btn-brand d-none d-sm-inline-flex" href="{{ route('login') }}">دخول</a>
                    @endauth

                    <button
                        class="btn btn-soft d-lg-none"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#mobileNavigation"
                        aria-controls="mobileNavigation"
                        aria-label="فتح القائمة"
                    >
                        ☰
                    </button>
                </div>
            </nav>
        </header>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileNavigation" aria-labelledby="mobileNavigationLabel">
            <div class="offcanvas-header border-bottom">
                <h2 class="offcanvas-title h5 mb-0" id="mobileNavigationLabel">القائمة</h2>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="إغلاق"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column gap-2">
                <a class="btn btn-soft justify-content-start" href="{{ route('home') }}">الرئيسية</a>
                <a class="btn btn-soft justify-content-start" href="{{ route('learning-paths.index') }}">المسارات</a>
                <a class="btn btn-soft justify-content-start" href="{{ route('community.index') }}">المجتمع</a>
                <a class="btn btn-soft justify-content-start" href="{{ route('live-events.index') }}">اللايفات</a>
                <a class="btn btn-soft justify-content-start" href="{{ route('blog.index') }}">المدونة</a>
                <a class="btn btn-soft justify-content-start" href="{{ route('subscription-plans.index') }}">الاشتراكات</a>

                <div class="border-top my-2"></div>

                @auth
                    <a class="btn btn-soft justify-content-start" href="{{ route('notifications.index') }}">
                        الإشعارات@if ($unreadNotificationsCount > 0) ({{ $unreadNotificationsCount }})@endif
                    </a>
                    <a class="btn btn-brand" href="{{ route('dashboard') }}">لوحة العضو</a>
                    @if (auth()->user()->is_admin)
                        <a class="btn btn-soft" href="{{ route('admin.dashboard') }}">لوحة الإدارة</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-soft w-100" type="submit">تسجيل الخروج</button>
                    </form>
                @else
                    <a class="btn btn-brand" href="{{ route('login') }}">تسجيل الدخول</a>
                    <a class="btn btn-soft" href="{{ route('register') }}">إنشاء حساب</a>
                @endauth
            </div>
        </div>

        <main class="flex-grow-1">
            @yield('content')
        </main>

        <x-site-footer />

        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/pwa.js') }}" defer></script>
        @auth
            @if (config('push.enabled') && config('push.vapid.public_key'))
                <script src="{{ asset('js/push.js') }}" defer></script>
            @endif
        @endauth
        <script>
            (() => {
                const storageKey = 'bayt-almoswer-theme';
                const root = document.documentElement;
                const savedTheme = localStorage.getItem(storageKey);
                const initialTheme = savedTheme || 'dark';

                root.dataset.bsTheme = initialTheme;

                const updateThemeToggleLabel = () => {
                    const label = root.dataset.bsTheme === 'dark' ? 'الوضع الفاتح' : 'الوضع الداكن';

                    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
                        button.textContent = label;
                    });
                };

                updateThemeToggleLabel();

                document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const nextTheme = root.dataset.bsTheme === 'dark' ? 'light' : 'dark';
                        root.dataset.bsTheme = nextTheme;
                        localStorage.setItem(storageKey, nextTheme);
                        updateThemeToggleLabel();
                    });
                });
            })();
        </script>
    </body>
</html>
