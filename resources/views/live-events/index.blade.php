@extends('layouts.app')

@section('title', 'اللايفات والفعاليات')

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
                    @php
                        $isFull = $event->capacity !== null && $event->registrations_count >= $event->capacity;
                        $canAccess = auth()->check() && auth()->user()->canAccess($event->access_level);
                    @endphp

                    <div class="col-lg-6">
                        <article class="surface-card overflow-hidden h-100 d-flex flex-column">
                            @if ($event->coverImageUrl())
                                <img class="card-cover" src="{{ $event->coverImageUrl() }}" alt="{{ $event->title }}">
                            @endif
                            <div class="p-4 d-flex flex-column flex-grow-1">
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge badge-soft">{{ $event->location ?? 'اونلاين' }}</span>
                                @if ($isFull)
                                    <span class="badge text-bg-secondary">اكتمل العدد</span>
                                @elseif ($event->capacity)
                                    <span class="badge text-bg-success">متاح {{ $event->capacity - $event->registrations_count }} مقعد</span>
                                @endif
                            </div>

                            <h2 class="h4">{{ $event->title }}</h2>
                            <p class="text-muted-soft flex-grow-1">{{ $event->description }}</p>

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

                            @auth
                                @if ($event->registered_by_current_user)
                                    <form method="POST" action="{{ route('live-events.cancel', $event) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-soft w-100" type="submit">إلغاء الحجز</button>
                                    </form>
                                @elseif (! $canAccess)
                                    <a class="btn btn-brand w-100" href="{{ route('subscription-plans.index') }}">اشترك للحجز</a>
                                @else
                                    <form method="POST" action="{{ route('live-events.register', $event) }}">
                                        @csrf
                                        <button class="btn btn-brand w-100" type="submit" @disabled($isFull)>حجز مقعد</button>
                                    </form>
                                @endif
                            @else
                                <a class="btn btn-brand w-100" href="{{ route('login') }}">سجل الدخول للحجز</a>
                            @endauth
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
