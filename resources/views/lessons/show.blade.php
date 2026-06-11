@extends('layouts.app')

@section('title', $lesson->title . ' — ' . $path->title)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $lesson->summary), 160, '…') ?: 'درس تعليمي في مسار ' . $path->title . ' على منصة بيت المصور.')
@section('canonical', route('lessons.show', [$path, $lesson]))

@if ($lesson->thumbnailUrl())
    @section('meta_image', $lesson->thumbnailUrl())
@endif

@push('head')
    <x-structured-data.lesson :path="$path" :lesson="$lesson" />
@endpush

@section('content')
    <section class="section-block">
        <div class="container">
            <nav class="text-muted-soft mb-3" aria-label="مسار التنقل">
                <a class="text-reset" href="{{ route('learning-paths.index') }}">المسارات</a>
                <span class="mx-2">/</span>
                <a class="text-reset" href="{{ route('learning-paths.show', $path) }}">{{ $path->title }}</a>
            </nav>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="row g-4">
                <div class="col-lg-8">
                    <h1 class="display-6 fw-bold mb-3">{{ $lesson->title }}</h1>
                    <p class="lead text-muted-soft">{{ $lesson->summary }}</p>

                    @if ($canAccess)
                        <div class="lesson-player surface-card mb-4">
                            @if ($lesson->video_url)
                                <div class="ratio ratio-16x9">
                                    <iframe src="{{ $lesson->video_url }}" title="{{ $lesson->title }}" allowfullscreen></iframe>
                                </div>
                            @else
                                <div class="lesson-placeholder d-grid place-items-center text-center p-4">
                                    <div>
                                        <strong class="d-block fs-4 mb-2">الفيديو سيضاف قريبًا</strong>
                                        <span class="text-muted-soft">صفحة الدرس جاهزة لاستقبال رابط الفيديو.</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2">
                            @auth
                                @if (! $progress?->completed_at)
                                    <form method="POST" action="{{ route('lessons.complete', [$path, $lesson]) }}">
                                        @csrf
                                        <button class="btn btn-brand" type="submit">تحديد كمكتمل</button>
                                    </form>
                                @else
                                    <span class="btn btn-soft disabled">✓ درس مكتمل</span>
                                @endif
                            @endauth

                            @if ($lesson->pdf_path)
                                <a class="btn btn-soft" href="{{ route('lessons.pdf', [$path, $lesson]) }}">تحميل ملف PDF</a>
                            @elseif ($lesson->pdf_url)
                                <a class="btn btn-soft" href="{{ $lesson->pdf_url }}" target="_blank" rel="noopener">فتح ملف PDF</a>
                            @endif

                            @if ($nextLesson)
                                <a class="btn btn-soft" href="{{ route('lessons.show', [$path, $nextLesson]) }}">الدرس التالي</a>
                            @endif
                        </div>
                    @else
                        <div class="surface-card p-4 p-md-5 text-center">
                            <span class="lesson-lock d-inline-grid place-items-center mb-3" aria-hidden="true">🔒</span>
                            <h2 class="h3">هذا الدرس للمشتركين</h2>
                            <p class="text-muted-soft">اشترك للوصول إلى الفيديو والملفات ومتابعة تقدمك داخل المسار.</p>
                            @guest
                                <a class="btn btn-brand" href="{{ route('login') }}">تسجيل الدخول</a>
                            @else
                                <a class="btn btn-brand" href="{{ route('subscription-plans.index') }}">عرض الاشتراكات</a>
                            @endguest
                        </div>
                    @endif
                </div>

                <aside class="col-lg-4">
                    <div class="surface-card p-4">
                        <span class="text-muted-soft d-block">المسار</span>
                        <strong class="d-block mb-3">{{ $path->title }}</strong>
                        <span class="text-muted-soft d-block">مدة الدرس</span>
                        <strong>{{ $lesson->duration_minutes ?? '-' }} دقيقة</strong>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
