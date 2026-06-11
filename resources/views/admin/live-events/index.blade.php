@extends('layouts.app')

@section('title', 'إدارة اللايفات')

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                <div>
                    <p class="fw-bold text-accent mb-2">الفعاليات</p>
                    <h1 class="display-6 fw-bold mb-0">إدارة اللايفات</h1>
                </div>
                <a class="btn btn-brand align-self-start" href="{{ route('admin.live-events.create') }}">إضافة لايف</a>
            </div>

            @include('admin.partials.nav')

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="d-grid gap-3">
                @foreach ($events as $event)
                    <article class="surface-card overflow-hidden">
                        <div class="d-flex flex-column flex-md-row gap-3 p-4">
                            @if ($event->coverImageUrl())
                                <img class="admin-thumb admin-thumb-wide" src="{{ $event->coverImageUrl() }}" alt="{{ $event->title }}">
                            @endif
                            <div class="flex-grow-1">
                                <h2 class="h4">{{ $event->title }}</h2>
                                <p class="text-muted-soft">{{ $event->description }}</p>
                                <span class="text-muted-soft">{{ $event->starts_at?->translatedFormat('d F Y - h:i A') ?? 'يحدد لاحقا' }}</span>
                                <span class="text-muted-soft d-block mt-2">{{ $event->registrations_count }} حجز{{ $event->capacity ? ' من ' . $event->capacity : '' }}</span>
                            </div>
                            <a class="btn btn-soft align-self-start" href="{{ route('admin.live-events.edit', $event) }}">تعديل</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
