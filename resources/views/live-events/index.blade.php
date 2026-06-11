@extends('layouts.app')

@section('title', 'اللايفات والفعاليات')
@section('meta_description', 'جدول اللايفات التعليمية والورش والفعاليات الأونلاين والحضورية في بيت المصور مع أحمد زغلول.')
@section('canonical', route('live-events.index'))

@section('content')
    <x-page-header
        eyebrow="لايفات وفعاليات"
        title="جدول اللقاءات القادمة"
        description="تابع اللايفات التعليمية، الورش، والفعاليات الحضورية أو الأونلاين."
    />

    <section class="section-block pt-0">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row g-3">
                @forelse ($events as $event)
                    <div class="col-lg-6">
                        <article class="surface-card overflow-hidden h-100 d-flex flex-column">
                            @if ($event->coverImageUrl())
                                <a href="{{ route('live-events.show', $event) }}">
                                    <img class="card-cover" src="{{ $event->coverImageUrl() }}" alt="{{ $event->title }}">
                                </a>
                            @endif
                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge badge-soft">{{ $event->location ?? 'اونلاين' }}</span>
                                    @if ($event->capacity !== null && $event->registrations_count >= $event->capacity)
                                        <span class="badge text-bg-secondary">اكتمل العدد</span>
                                    @elseif ($event->capacity)
                                        <span class="badge text-bg-success">متاح {{ $event->capacity - $event->registrations_count }} مقعد</span>
                                    @endif
                                </div>

                                <h2 class="h4">
                                    <a class="text-reset text-decoration-none" href="{{ route('live-events.show', $event) }}">{{ $event->title }}</a>
                                </h2>
                                <p class="text-muted-soft flex-grow-1">{{ $event->seoDescription() }}</p>

                                <div class="border-top pt-3 mb-3">
                                    <div class="d-flex justify-content-between gap-3 mb-2">
                                        <span class="text-muted-soft">الموعد</span>
                                        <strong>{{ $event->starts_at?->translatedFormat('d F Y - h:i A') ?? 'يحدد لاحقا' }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between gap-3">
                                        <span class="text-muted-soft">الحجوزات</span>
                                        <strong>{{ $event->registrations_count }}{{ $event->capacity ? ' / ' . $event->capacity : '' }}</strong>
                                    </div>
                                </div>

                                @include('live-events.partials.registration-actions', ['event' => $event])
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="surface-card p-4 text-muted-soft">لا توجد فعاليات منشورة حاليًا.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
