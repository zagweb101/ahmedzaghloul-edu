@extends('layouts.app')

@section('title', 'الاشتراكات')
@section('meta_description', 'خطط اشتراك بيت المصور: مجاني، شهري، وسنوي للوصول إلى المسارات والمجتمع واللايفات.')
@section('canonical', route('subscription-plans.index'))

@section('content')
    <x-page-header
        eyebrow="اشتراكات"
        title="اختر الخطة المناسبة"
        description="ابدأ مجانًا ثم انتقل إلى خطة تفتح لك المحتوى، اللايفات، والمجتمع."
    />

    <section class="section-block pt-0">
        <div class="container">
            <div class="row g-3">
                @foreach ($plans as $plan)
                    <div class="col-lg-4">
                        <article class="surface-card plan-card p-4 {{ $plan->is_featured ? 'featured-plan' : '' }}">
                            <h2 class="h3">
                                <a class="text-reset text-decoration-none" href="{{ route('subscription-plans.show', $plan) }}">{{ $plan->name }}</a>
                            </h2>
                            <strong class="fs-3 d-block my-3">{{ $plan->formattedPrice() }}</strong>
                            <p class="text-muted-soft">{{ $plan->seoDescription() }}</p>
                            <ul class="check-list list-unstyled d-grid gap-2 my-4">
                                @foreach ($plan->features ?? [] as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                            <a href="{{ route('subscription-plans.show', $plan) }}" class="btn {{ $plan->is_featured ? 'btn-light' : 'btn-soft' }} w-100 mb-2">
                                تفاصيل الخطة
                            </a>
                            @auth
                                <a href="{{ route('subscription-plans.checkout', $plan) }}" class="btn btn-brand w-100">
                                    {{ $plan->isFree() ? 'ابدأ مجانًا' : 'اشترك الآن' }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-brand w-100">
                                    {{ $plan->isFree() ? 'سجل وابدأ مجانًا' : 'سجل للاشتراك' }}
                                </a>
                            @endauth
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
