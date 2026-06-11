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
@endphp

@section('content')
    <main id="top">
        <section class="hero container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <p class="fw-bold text-accent mb-3">من جدة إلى كل المصورين العرب</p>
                    <h1 class="hero-title fw-bold mb-4">مجتمع تعليمي لتطوير المصورين مع أحمد زغلول</h1>
                    <p class="lead text-muted-soft mb-4">
                        تعلم التصوير من الأساسيات للاحتراف عبر مسارات واضحة، فيديوهات مستمرة،
                        لايفات، تحديات، ومجتمع يساعدك تطبق وتتطور.
                    </p>
                    <div class="d-grid d-sm-flex gap-2 mb-5">
                        <a href="{{ route('subscription-plans.index') }}" class="btn btn-brand btn-lg">ابدأ الاشتراك</a>
                        <a href="{{ route('learning-paths.index') }}" class="btn btn-soft btn-lg">شاهد المسارات</a>
                    </div>
                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <div class="surface-card p-3">
                                <strong class="fs-4 d-block">8</strong>
                                <span class="text-muted-soft">مسارات تعليمية</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="surface-card p-3">
                                <strong class="fs-4 d-block">3</strong>
                                <span class="text-muted-soft">مستويات</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="surface-card p-3">
                                <strong class="fs-4 d-block">مستمرة</strong>
                                <span class="text-muted-soft">لايفات وفعاليات</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <aside class="surface-card p-4">
                        <div class="d-flex justify-content-between text-muted-soft mb-4">
                            <span>لوحة العضو</span>
                            <strong>اليوم</strong>
                        </div>
                        <div class="bg-body border rounded-3 p-4 mb-3">
                            <small class="fw-bold text-accent">ابدأ من هنا</small>
                            <h2 class="h3 mt-3">اساسيات التصوير الفوتوغرافي</h2>
                            <p class="text-muted-soft mb-0">أكمل أول درس، ثم شارك تطبيقك داخل المجتمع.</p>
                        </div>
                        <div class="border-top py-3">
                            <span class="d-block text-muted-soft">اللايف القادم</span>
                            <strong>كيف تبدأ رحلتك في التصوير</strong>
                        </div>
                        <div class="border-top pt-3">
                            <span class="d-block text-muted-soft">تحدي الأسبوع</span>
                            <strong>صورة بورتريه بضوء طبيعي</strong>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <section id="paths" class="section-block">
            <div class="container">
                <div class="col-lg-8 mb-4">
                    <p class="fw-bold text-accent mb-2">رحلات تعليمية واضحة</p>
                    <h2 class="display-5 fw-bold">المسارات</h2>
                    <p class="lead text-muted-soft">كل مسار مبني حول نتيجة عملية، وليس مجرد مجموعة فيديوهات.</p>
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

        <section id="community" class="section-block" style="background: var(--bm-surface);">
            <div class="container">
                <div class="row align-items-start g-5">
                    <div class="col-lg-5">
                        <p class="fw-bold text-accent mb-2">قيمة الاشتراك الحقيقية</p>
                        <h2 class="display-6 fw-bold">مجتمع تطبيقي، مش مكتبة فيديوهات فقط</h2>
                        <p class="text-muted-soft fs-5">العضو يتعلم، يطبق، يسأل، يشارك أعماله، ويحصل على توجيه داخل بيئة منظمة للمصورين.</p>
                    </div>
                    <div class="col-lg-7">
                        <div class="row g-3">
                            @foreach (['اسأل أحمد', 'شارك صورتك', 'تحدي الأسبوع', 'نقد وتقييم', 'معدات وتجهيزات', 'فعاليات جدة'] as $section)
                                <div class="col-md-6">
                                    <div class="bg-body border rounded-3 p-4 fw-bold">{{ $section }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="live" class="section-block">
            <div class="container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <p class="fw-bold text-accent mb-2">لايفات وفعاليات</p>
                        <h2 class="display-6 fw-bold">{{ $upcomingLive?->title ?? 'لايفات وفعاليات قادمة' }}</h2>
                        <p class="text-muted-soft fs-5 mb-0">{{ $upcomingLive?->description ?? 'صفحة اللايفات تعرض الموعد، الموضوع، التسجيل، وأرشيف اللايفات السابقة للمشتركين.' }}</p>
                    </div>
                    <div class="col-lg-4">
                        <div class="surface-card p-4">
                            <span class="text-muted-soft">الموعد</span>
                            <strong class="fs-3 d-block my-2">{{ $upcomingLive?->starts_at?->translatedFormat('d F Y - h:i A') ?? 'يحدد لاحقا' }}</strong>
                            <small class="text-muted-soft">{{ $upcomingLive ? ($accessLabels[$upcomingLive->access_level->value] ?? 'للمشتركين') : 'قريبًا' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="blog" class="section-block" style="background: var(--bm-surface);">
            <div class="container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <p class="fw-bold text-accent mb-2">المدونة</p>
                        <h2 class="display-6 fw-bold">مقالات ونصائح التصوير</h2>
                        <p class="text-muted-soft fs-5 mb-0">محتوى مكتوب يساعدك على فهم أساسيات التصوير قبل التطبيق داخل المسارات والمجتمع.</p>
                    </div>
                    <div class="col-lg-4">
                        <a href="{{ route('blog.index') }}" class="btn btn-brand btn-lg w-100">اقرأ المدونة</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="plans" class="section-block">
            <div class="container">
                <div class="col-lg-8 mb-4">
                    <p class="fw-bold text-accent mb-2">اشتراكات بسيطة</p>
                    <h2 class="display-5 fw-bold">اختر الخطة المناسبة</h2>
                    <p class="lead text-muted-soft">نبدأ بثلاث خطط واضحة، ونترك لايف تايم كعرض إطلاق لاحقًا إذا احتجناه.</p>
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
    </main>
@endsection
