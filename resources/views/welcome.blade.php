@extends('layouts.app')

@section('title', 'بيت المصور')

@section('meta_description', 'منصة تعليمية عربية لتطوير المصورين مع أحمد زغلول: مسارات تعليمية، دروس، لايفات، مجتمع، واشتراكات.')

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => config('app.url'),
            'description' => 'أكاديمية عربية لتعليم التصوير الفوتوغرافي والفيديو.',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    <x-structured-data.website />
@endpush

@php
    $levelLabels = [
        'beginner' => 'مبتدئ',
        'intermediate' => 'متوسط',
        'professional' => 'احترافي',
    ];

    $accessLabels = [
        'free' => 'مجاني للمعاينة',
        'member' => 'للمشتركين',
        'premium' => 'متقدم',
    ];

    $formatPrice = fn ($plan) => $plan->price_cents > 0
        ? number_format($plan->price_cents / 100) . ' ' . $plan->currency
        : ($plan->slug === 'free' ? '0 ريال' : 'يحدد لاحقا');

    $brandImages = config('brand.images');
@endphp

@section('content')
    <div id="top">
        <section class="hero-cinematic">
            <div class="hero-cinematic__media" aria-hidden="true">
                <img
                    src="{{ asset($brandImages['hero']) }}"
                    alt="أكاديمية بيت المصور — استوديو تعليم التصوير مع أحمد زغلول"
                    loading="eager"
                    decoding="async"
                >
            </div>
            <div class="hero-cinematic__overlay" aria-hidden="true"></div>

            <div class="container hero-cinematic__content">
                <div class="col-lg-7">
                    <p class="hero-eyebrow mb-3">{{ config('brand.tagline') }}</p>
                    <p class="brand-motto mb-4">{{ config('brand.motto') }}</p>
                    <h1 class="hero-title mb-4">
                        <span class="heading-viewfinder d-inline-block mb-2">ابدأ رحلتك في</span><br>
                        <span class="text-gradient">التصوير باحتراف</span>
                    </h1>
                    <p class="lead text-muted-soft mb-4">
                        أكاديمية عربية تجمع التعليم المنظم وشغف العدسة — مع أحمد زغلول ومجتمع يحب التصوير.
                    </p>
                    <div class="d-grid d-sm-flex gap-2 mb-5">
                        <a href="{{ route('subscription-plans.index') }}" class="btn btn-brand btn-lg">ابدأ الاشتراك</a>
                        <a href="{{ route('learning-paths.index') }}" class="btn btn-soft btn-lg">استكشف المسارات</a>
                    </div>

                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="stat-pill">
                                <strong>{{ $stats['paths'] }}</strong>
                                <span class="text-muted-soft">مسار</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-pill">
                                <strong>{{ $stats['lessons'] }}</strong>
                                <span class="text-muted-soft">درس</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-pill">
                                <strong>{{ $stats['live_events'] }}</strong>
                                <span class="text-muted-soft">لايف</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-pill">
                                <strong>{{ $stats['community_posts'] }}</strong>
                                <span class="text-muted-soft">مشاركة</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="paths" class="section-block">
            <div class="container">
                <div class="col-lg-8 mb-4">
                    <p class="section-eyebrow">رحلات تعليمية واضحة</p>
                    <h2 class="display-5 fw-bold heading-viewfinder">المسارات</h2>
                    <hr class="neon-divider">
                    <p class="lead text-muted-soft">كل مسار مبني حول نتيجة عملية — تخرج منه بصور أنت فخور فيها.</p>
                </div>
                <div class="row g-3">
                    @forelse ($paths as $path)
                        <div class="col-md-6 col-xl-3">
                            <article class="surface-card path-card overflow-hidden">
                                @if ($path->coverImageUrl())
                                    <img class="card-cover" src="{{ $path->coverImageUrl() }}" alt="{{ $path->title }}">
                                @endif
                                <div class="p-4">
                                    <span class="badge badge-soft mb-3">{{ $levelLabels[$path->level->value] ?? 'مسار' }}</span>
                                    <h3 class="h5">{{ $path->title }}</h3>
                                    <p class="text-muted-soft">{{ $path->description }}</p>
                                    <div class="d-flex justify-content-between text-muted-soft my-4">
                                        <span>{{ $path->lessons_count }} درس</span>
                                        <span>{{ $accessLabels[$path->access_level->value] ?? 'للمشتركين' }}</span>
                                    </div>
                                    <a class="btn btn-soft w-100" href="{{ route('learning-paths.show', $path) }}">عرض المسار</a>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="surface-card p-4 text-muted-soft">سيتم إضافة المسارات قريبًا.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section id="topics" class="section-block section-surface pt-0">
            <div class="container">
                <div class="col-lg-8 mb-4">
                    <p class="section-eyebrow">محتوى الأكاديمية</p>
                    <h2 class="display-6 fw-bold">تخصصات التصوير داخل المنصة</h2>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <a class="topic-card d-block text-reset" href="{{ route('learning-paths.index') }}">
                            <img src="{{ asset($brandImages['lighting_basics']) }}" alt="أساسيات الإضاءة" loading="lazy">
                            <div class="topic-card__overlay">
                                <h3 class="topic-card__title">أساسيات الإضاءة</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a class="topic-card d-block text-reset" href="{{ route('learning-paths.index') }}">
                            <img src="{{ asset($brandImages['product_photo']) }}" alt="تصوير المنتجات" loading="lazy">
                            <div class="topic-card__overlay">
                                <h3 class="topic-card__title">تصوير المنتجات</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a class="topic-card d-block text-reset" href="{{ route('learning-paths.index') }}">
                            <img src="{{ asset($brandImages['video_editing']) }}" alt="تصوير الفيديو والمونتاج" loading="lazy">
                            <div class="topic-card__overlay">
                                <h3 class="topic-card__title">الفيديو والمونتاج</h3>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section id="trainer" class="section-block">
            <div class="container">
                <div class="promo-panel promo-panel--reverse">
                    <div class="promo-panel__media">
                        <img
                            src="{{ asset($brandImages['about']) }}"
                            alt="أحمد زغلول في أكاديمية بيت المصور"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>
                    <div class="promo-panel__body">
                        <p class="section-eyebrow">من نحن</p>
                        <h2 class="display-6 fw-bold mb-3">تعليم + تصوير + مجتمع</h2>
                        <p class="text-muted-soft fs-5 mb-4">
                            بيئة تشبه الاستوديو الاحترافي — تتعلم الإضاءة والتكوين والمعالجة،
                            وتشارك أعمالك داخل مجتمع عربي يحب العدسة.
                        </p>
                        <ul class="check-list list-unstyled d-grid gap-2 mb-4">
                            <li>مسارات من المبتدئ للمحترف</li>
                            <li>لايفات وتحديات أسبوعية</li>
                            <li>توجيه مباشر من أحمد زغلول</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-brand btn-lg">انضم للأكاديمية</a>
                    </div>
                </div>
            </div>
        </section>

        <x-testimonials-section />

        <section id="community" class="section-block section-surface">
            <div class="container">
                <div class="promo-panel">
                    <div class="promo-panel__media">
                        <img
                            src="{{ asset($brandImages['workshop']) }}"
                            alt="ورشة تصوير داخل أكاديمية بيت المصور"
                            loading="lazy"
                        >
                    </div>
                    <div class="promo-panel__body">
                        <p class="section-eyebrow">المجتمع</p>
                        <h2 class="display-6 fw-bold mb-3">تطبّق وتتفاعل كل أسبوع</h2>
                        <p class="text-muted-soft fs-5 mb-4">ليس مكتبة فيديوهات فقط — مساحة تطبيق وتفاعل مع مصورين يشاركونك الشغف.</p>
                        <div class="row g-2">
                            @foreach (['اسأل أحمد', 'شارك صورتك', 'تحدي الأسبوع', 'نقد وتقييم'] as $section)
                                <div class="col-sm-6">
                                    <div class="community-chip">{{ $section }}</div>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('community.index') }}" class="btn btn-soft mt-4">ادخل المجتمع</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="live" class="section-block">
            <div class="container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <p class="section-eyebrow">لايفات وفعاليات</p>
                        <h2 class="display-6 fw-bold">{{ $upcomingLive?->title ?? 'لايفات وفعاليات قادمة' }}</h2>
                        <p class="text-muted-soft fs-5 mb-0">{{ $upcomingLive?->description ?? 'صفحة اللايفات تعرض الموعد، الموضوع، التسجيل، وأرشيف اللايفات السابقة للمشتركين.' }}</p>
                    </div>
                    <div class="col-lg-5">
                        <div class="surface-card overflow-hidden">
                            <img
                                class="card-cover"
                                src="{{ asset($brandImages['instructors']) }}"
                                alt="جلسة تدريب مباشرة في بيت المصور"
                                loading="lazy"
                            >
                            <div class="p-4">
                                <span class="text-muted-soft">الموعد</span>
                                <strong class="fs-4 d-block my-2">{{ $upcomingLive?->starts_at?->translatedFormat('d F Y - h:i A') ?? 'يحدد لاحقًا' }}</strong>
                                <a class="btn btn-brand w-100 mt-2" href="{{ route('live-events.index') }}">عرض اللايفات</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="blog" class="section-block section-surface">
            <div class="container">
                <div class="cta-band p-4 p-md-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <p class="section-eyebrow text-white-50">المدونة</p>
                            <h2 class="display-6 fw-bold mb-2">نصائح تصوير تفتح لك أفقًا جديدًا</h2>
                            <p class="text-muted-soft mb-0">محتوى مكتوب يساعدك تفهم الأساسيات قبل التطبيق داخل المسارات والمجتمع.</p>
                        </div>
                        <div class="col-lg-4">
                            <a href="{{ route('blog.index') }}" class="btn btn-light btn-lg w-100">اقرأ المدونة</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="plans" class="section-block pt-0">
            <div class="container">
                <div class="col-lg-8 mb-4">
                    <p class="section-eyebrow">اشتراكات بسيطة</p>
                    <h2 class="display-5 fw-bold">اختر الخطة وابدأ اليوم</h2>
                    <p class="lead text-muted-soft">خطط واضحة — ابدأ مجانًا ثم انتقل للخطة التي تناسبك.</p>
                </div>
                <div class="row g-3">
                    @forelse ($plans as $plan)
                        <div class="col-lg-4">
                            <article class="surface-card plan-card p-4 {{ $plan->is_featured ? 'featured-plan' : '' }}">
                                <h3>{{ $plan->name }}</h3>
                                <strong class="fs-3 d-block my-3">{{ $formatPrice($plan) }}</strong>
                                <p class="text-muted-soft">{{ $plan->description }}</p>
                                <ul class="check-list list-unstyled d-grid gap-2 my-4">
                                    @foreach ($plan->features ?? [] as $feature)
                                        <li>{{ $feature }}</li>
                                    @endforeach
                                </ul>
                                <a href="{{ route('subscription-plans.show', $plan) }}" class="btn {{ $plan->is_featured ? 'btn-light' : 'btn-soft' }} w-100">
                                    {{ $plan->slug === 'free' ? 'ابدأ مجانًا' : 'اشترك' }}
                                </a>
                            </article>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="surface-card p-4 text-muted-soft">سيتم إضافة خطط الاشتراك قريبًا.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
@endsection
