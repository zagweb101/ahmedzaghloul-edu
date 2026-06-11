@extends('layouts.app')

@section('title', 'المسارات التعليمية')
@section('meta_description', 'استكشف مسارات التعليم العملية في بيت المصور: دروس منظمة، فيديوهات، وملفات مساعدة لتطوير مهارات التصوير.')
@section('canonical', route('learning-paths.index'))

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
@endphp

@section('content')
    <x-page-header
        eyebrow="رحلات تعليمية"
        title="المسارات"
        description="ابدأ من مستواك الحالي، ثم انتقل تدريجيًا إلى التخصص الذي يناسبك."
    />

    <section class="section-block pt-0">
        <div class="container">
            <div class="row g-3">
                @foreach ($paths as $path)
                    <div class="col-md-6 col-xl-3">
                        <article class="surface-card path-card overflow-hidden">
                            @if ($path->coverImageUrl())
                                <img class="card-cover" src="{{ $path->coverImageUrl() }}" alt="{{ $path->title }}">
                            @endif
                            <div class="p-4">
                            <span class="badge badge-soft mb-3">{{ $levelLabels[$path->level->value] ?? 'مسار' }}</span>
                            <h2 class="h5">{{ $path->title }}</h2>
                            <p class="text-muted-soft">{{ $path->description }}</p>
                            <div class="d-flex justify-content-between text-muted-soft my-4">
                                <span>{{ $path->lessons_count }} درس</span>
                                <span>{{ $accessLabels[$path->access_level->value] ?? 'للمشتركين' }}</span>
                            </div>
                            <a class="btn btn-soft w-100" href="{{ route('learning-paths.show', $path) }}">عرض المسار</a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
