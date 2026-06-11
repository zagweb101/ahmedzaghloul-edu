@extends('layouts.app')

@section('title', $event->seoTitle())
@section('meta_description', $event->seoDescription())
@section('canonical', route('live-events.show', $event))
@section('meta_type', 'article')

@if ($event->coverImageUrl())
    @section('meta_image', $event->coverImageUrl())
@endif

@push('head')
    @if ($event->seo_keywords)
        <meta name="keywords" content="{{ $event->seo_keywords }}">
    @endif

    <x-structured-data.event :event="$event" />
@endpush

@section('content')
    <nav class="container pt-4" aria-label="مسار التنقل">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="{{ route('live-events.index') }}">اللايفات</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $event->title }}</li>
        </ol>
    </nav>

    <section class="section-block">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <article class="surface-card overflow-hidden">
                @if ($event->coverImageUrl())
                    <img class="card-cover" src="{{ $event->coverImageUrl() }}" alt="{{ $event->title }}">
                @endif

                <div class="p-4 p-lg-5">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge badge-soft">{{ $event->location ?? 'اونلاين' }}</span>
                        @if ($event->capacity !== null && $event->registrations_count >= $event->capacity)
                            <span class="badge text-bg-secondary">اكتمل العدد</span>
                        @elseif ($event->capacity)
                            <span class="badge text-bg-success">متاح {{ $event->capacity - $event->registrations_count }} مقعد</span>
                        @endif
                    </div>

                    <h1 class="display-6 fw-bold mb-4">{{ $event->title }}</h1>
                    <p class="lead text-muted-soft">{{ $event->description }}</p>

                    <div class="row g-3 my-4">
                        <div class="col-md-6">
                            <div class="bg-body border rounded-3 p-3">
                                <span class="text-muted-soft d-block mb-1">الموعد</span>
                                <strong>{{ $event->starts_at?->translatedFormat('d F Y - h:i A') ?? 'يحدد لاحقًا' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-body border rounded-3 p-3">
                                <span class="text-muted-soft d-block mb-1">الحجوزات</span>
                                <strong>{{ $event->registrations_count }}{{ $event->capacity ? ' / ' . $event->capacity : '' }}</strong>
                            </div>
                        </div>
                    </div>

                    @include('live-events.partials.registration-actions', ['event' => $event])
                </div>
            </article>
        </div>
    </section>
@endsection
