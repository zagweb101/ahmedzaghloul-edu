@extends('layouts.app')

@section('title', $plan->seoTitle())
@section('meta_description', $plan->seoDescription())
@section('canonical', route('subscription-plans.show', $plan))

@if ($plan->coverImageUrl())
    @section('meta_image', $plan->coverImageUrl())
@endif

@push('head')
    @if ($plan->seo_keywords)
        <meta name="keywords" content="{{ $plan->seo_keywords }}">
    @endif

    <x-structured-data.product :plan="$plan" />
@endpush

@section('content')
    <nav class="container pt-4" aria-label="مسار التنقل">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="{{ route('subscription-plans.index') }}">الاشتراكات</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $plan->name }}</li>
        </ol>
    </nav>

    <section class="section-block">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <article class="surface-card overflow-hidden {{ $plan->is_featured ? 'featured-plan' : '' }}">
                        @if ($plan->coverImageUrl())
                            <img class="card-cover" src="{{ $plan->coverImageUrl() }}" alt="{{ $plan->name }}">
                        @endif

                        <div class="p-4 p-lg-5">
                            <p class="fw-bold text-accent mb-2">خطة اشتراك</p>
                            <h1 class="display-6 fw-bold mb-3">{{ $plan->name }}</h1>
                            <strong class="fs-2 d-block mb-4">{{ $plan->formattedPrice() }}</strong>
                            <p class="lead text-muted-soft">{{ $plan->description }}</p>

                            <ul class="check-list list-unstyled d-grid gap-2 my-4">
                                @foreach ($plan->features ?? [] as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>

                            @auth
                                <a href="{{ route('subscription-plans.checkout', $plan) }}" class="btn btn-brand btn-lg">
                                    {{ $plan->isFree() ? 'ابدأ مجانًا' : 'اشترك الآن' }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-brand btn-lg">
                                    {{ $plan->isFree() ? 'سجل وابدأ مجانًا' : 'سجل للاشتراك' }}
                                </a>
                            @endauth
                        </div>
                    </article>
                </div>

                <div class="col-lg-5">
                    <aside class="surface-card p-4">
                        <h2 class="h5 mb-3">لماذا هذه الخطة؟</h2>
                        <p class="text-muted-soft mb-0">
                            كل خطة في بيت المصور مصممة لتمنحك وصولًا واضحًا إلى المحتوى والمجتمع واللايفات
                            حسب مستوى التزامك في رحلة التصوير.
                        </p>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection
