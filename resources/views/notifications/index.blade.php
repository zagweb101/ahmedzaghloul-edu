@extends('layouts.app')

@section('title', 'الإشعارات')

@php
    $actionLabels = [
        'comment' => 'علّق على بوستك',
        'like' => 'أعجب ببوستك',
        'live_registered' => 'تم تأكيد حجزك في لايف',
        'live_reminder' => 'تذكير بلايف قادم',
        'subscription_activated' => 'تم تفعيل اشتراكك',
    ];
@endphp

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                <div>
                    <p class="fw-bold text-accent mb-2">التفاعل</p>
                    <h1 class="display-6 fw-bold mb-0">الإشعارات</h1>
                </div>

                @if ($notifications->total() > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button class="btn btn-soft" type="submit">تعليم الكل كمقروء</button>
                    </form>
                @endif
            </div>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="d-grid gap-3">
                @forelse ($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $isUnread = $notification->read_at === null;
                    @endphp

                    <article class="surface-card p-4 {{ $isUnread ? 'notification-unread' : '' }}">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                            <div>
                                @if ($isUnread)
                                    <span class="badge badge-soft mb-2">جديد</span>
                                @endif
                                <h2 class="h5 mb-2">
                                    @if (! empty($data['actor_name']))
                                        <strong>{{ $data['actor_name'] }}</strong>
                                        {{ $actionLabels[$data['action'] ?? ''] ?? 'تفاعل مع بوستك' }}
                                    @else
                                        {{ $actionLabels[$data['action'] ?? ''] ?? 'إشعار جديد' }}
                                    @endif
                                </h2>
                                <p class="text-muted-soft mb-1">{{ $data['post_title'] ?? $data['event_title'] ?? 'نشاط في المنصة' }}</p>
                                @if (! empty($data['starts_at']))
                                    <small class="text-muted-soft d-block mb-1">
                                        الموعد: {{ \Illuminate\Support\Carbon::parse($data['starts_at'])->translatedFormat('d F Y - h:i A') }}
                                    </small>
                                @endif
                                @if (! empty($data['preview']))
                                    <p class="preserve-lines mb-0">"{{ \Illuminate\Support\Str::limit($data['preview'], 120) }}"</p>
                                @endif
                                <small class="text-muted-soft d-block mt-2">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>

                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                <button class="btn {{ $isUnread ? 'btn-brand' : 'btn-soft' }}" type="submit">عرض البوست</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="surface-card p-4 text-muted-soft">
                        لا توجد إشعارات حتى الآن. ستصلك هنا عند التعليق أو الإعجاب على بوستاتك.
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </section>
@endsection
