<footer class="site-footer">
    <div class="container py-5">
        <div class="row g-4 align-items-start">
            <div class="col-lg-5">
                <x-site-logo :compact="false" class="mb-3" />
                <p class="site-footer-lead mb-0">
                    أكاديمية عربية تجمع بين التعليم المنظم وشغف التصوير — تعلّم، طبّق، وشارك أعمالك مع مجتمع يحب العدسة.
                </p>
            </div>

            <div class="col-6 col-lg-3">
                <h2 class="site-footer-heading h6">استكشف</h2>
                <ul class="site-footer-links list-unstyled d-grid gap-2 mb-0">
                    <li><a href="{{ route('learning-paths.index') }}">المسارات التعليمية</a></li>
                    <li><a href="{{ route('community.index') }}">المجتمع</a></li>
                    <li><a href="{{ route('live-events.index') }}">اللايفات</a></li>
                    <li><a href="{{ route('blog.index') }}">المدونة</a></li>
                    <li><a href="{{ route('subscription-plans.index') }}">الاشتراكات</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-4">
                <h2 class="site-footer-heading h6">ابدأ رحلتك</h2>
                <p class="text-muted-soft mb-3">انضم اليوم وجرّب أول درس، ثم اختر الخطة التي تناسبك.</p>
                <div class="d-flex flex-wrap gap-2">
                    @auth
                        <a class="btn btn-brand" href="{{ route('dashboard') }}">لوحتي</a>
                    @else
                        <a class="btn btn-brand" href="{{ route('register') }}">إنشاء حساب</a>
                        <a class="btn btn-soft" href="{{ route('subscription-plans.index') }}">الخطط</a>
                    @endauth
                </div>
            </div>
        </div>

        <div class="site-footer-bottom d-flex flex-column flex-md-row justify-content-between gap-2 pt-4 mt-4">
            <span>© {{ now()->year }} {{ config('app.name') }} — جميع الحقوق محفوظة</span>
            <span class="text-muted-soft">صُمم للمصورين العرب بفرحة وشغف</span>
        </div>
    </div>
</footer>
