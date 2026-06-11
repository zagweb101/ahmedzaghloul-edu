@extends('layouts.app')

@section('title', $path->seoTitle())
@section('meta_description', $path->seoDescription())
@section('canonical', route('learning-paths.show', $path))

@if ($path->coverImageUrl())
    @section('meta_image', $path->coverImageUrl())
@endif

@push('head')
    @if ($path->seo_keywords)
        <meta name="keywords" content="{{ $path->seo_keywords }}">
    @endif

    <x-structured-data.course :path="$path" />
@endpush

@php
    $accessLabels = [
        'free' => 'مجاني للمعاينة',
        'member' => 'للمشتركين',
        'premium' => 'متقدم',
    ];

    $levelLabels = [
        'beginner' => 'مبتدئ',
        'intermediate' => 'متوسط',
        'professional' => 'احترافي',
    ];

    $canAccessLesson = fn ($lesson) => $lesson->access_level->value === 'free'
        || (auth()->check() && auth()->user()->canAccess($lesson->access_level));
@endphp

@section('content')
    <nav class="container pt-4" aria-label="مسار التنقل">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="{{ route('learning-paths.index') }}">المسارات</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $path->title }}</li>
        </ol>
    </nav>

    <x-page-header
        eyebrow="تفاصيل المسار"
        :title="$path->title"
        :description="$path->description"
    />

    @if ($path->coverImageUrl())
        <section class="container pb-0">
            <img class="path-hero-image" src="{{ $path->coverImageUrl() }}" alt="{{ $path->title }}">
        </section>
    @endif

    <section class="section-block pt-0">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="surface-card p-4 mb-4">
                        <h2 class="h5 mb-3">عن هذا المسار</h2>
                        <p class="text-muted-soft mb-3">{{ $path->description }}</p>
                        <div class="d-flex flex-wrap gap-3 text-muted-soft">
                            <span>{{ $path->lessons->count() }} درس</span>
                            @if ($path->totalDurationMinutes() > 0)
                                <span>{{ $path->totalDurationMinutes() }} دقيقة تعليمية</span>
                            @endif
                            <span>المستوى: {{ $levelLabels[$path->level->value] ?? $path->level->value }}</span>
                        </div>
                    </div>

                    <div class="surface-card p-4">
                        <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                            <h2 class="h4 mb-0">الدروس</h2>
                            <span class="text-muted-soft">{{ $path->lessons->count() }} درس</span>
                        </div>

                        <div class="list-group list-group-flush">
                            @forelse ($path->lessons as $lesson)
                                @php
                                    $isAccessible = $canAccessLesson($lesson);
                                    $isCompleted = $completedLessonIds->contains($lesson->id);
                                @endphp

                                <a
                                    class="lesson-row list-group-item list-group-item-action bg-transparent px-0 py-3"
                                    href="{{ route('lessons.show', [$path, $lesson]) }}"
                                >
                                    <div class="d-flex align-items-start justify-content-between gap-3">
                                        <div class="d-flex align-items-start gap-3">
                                            <span class="lesson-status {{ $isCompleted ? 'completed' : '' }}" aria-hidden="true">
                                                {{ $isCompleted ? '✓' : ($isAccessible ? '▶' : '🔒') }}
                                            </span>
                                            <div>
                                                <strong class="d-block">{{ $lesson->title }}</strong>
                                                <span class="text-muted-soft">{{ $lesson->summary }}</span>
                                            </div>
                                        </div>
                                        <span class="text-muted-soft flex-shrink-0">{{ $lesson->duration_minutes ?? '-' }} دقيقة</span>
                                    </div>
                                </a>
                            @empty
                                <p class="text-muted-soft mb-0">سيتم إضافة دروس هذا المسار قريبًا.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <aside class="col-lg-4">
                    <div class="surface-card p-4 position-sticky" style="top: 100px;">
                        <h2 class="h5">مناسب لمن؟</h2>
                        <p class="text-muted-soft">مسار تطبيقي واضح الخطوات، مع فيديوهات وملفات مساعدة وتطبيقات عملية.</p>
                        <div class="border-top pt-3 mt-3">
                            <span class="d-block text-muted-soft">الوصول</span>
                            <strong>{{ $accessLabels[$path->access_level->value] ?? 'للمشتركين' }}</strong>
                        </div>
                        @guest
                            <a class="btn btn-brand w-100 mt-4" href="{{ route('login') }}">سجل الدخول للمتابعة</a>
                        @else
                            <a class="btn btn-brand w-100 mt-4" href="{{ route('subscription-plans.index') }}">عرض خطط الاشتراك</a>
                        @endguest
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
