@extends('layouts.app')

@section('title', 'لوحة العضو')

@php
    $subscriptionLabels = [
        'free' => 'مجاني',
        'member' => 'عضوية كاملة',
        'premium' => 'عضوية متقدمة',
    ];
@endphp

@section('content')
    <section class="section-block">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4">
                <div class="d-flex align-items-center gap-3">
                    @if (auth()->user()->avatarUrl())
                        <img class="avatar-preview" src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}">
                    @else
                        <span class="avatar-placeholder">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    @endif
                    <div>
                        <p class="fw-bold text-accent mb-2">لوحة العضو</p>
                        <h1 class="display-5 fw-bold mb-2">أهلًا {{ auth()->user()->name }}</h1>
                        <p class="lead text-muted-soft mb-0">واصل التعلم، تابع اللايفات، وشارك في المجتمع.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-soft" type="submit">تسجيل الخروج</button>
                </form>
            </div>

            <div class="row g-3">
                <div class="col-lg-7">
                    <article class="surface-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                            <span class="badge badge-soft">تقدمك</span>
                            <strong>{{ $progressPercentage }}%</strong>
                        </div>
                        <div class="progress mb-3" role="progressbar" aria-label="نسبة التقدم" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                        <p class="text-muted-soft">أكملت {{ $completedCount }} من {{ $accessibleLessonsCount }} درس متاح لك.</p>

                        @if ($continueLesson)
                            <h2 class="h4">واصل: {{ $continueLesson->title }}</h2>
                            <p class="text-muted-soft">من مسار {{ $continueLesson->learningPath->title }}</p>
                            <a class="btn btn-brand" href="{{ route('lessons.show', [$continueLesson->learningPath, $continueLesson]) }}">متابعة الدرس</a>
                        @else
                            <h2 class="h4">أحسنت، أكملت الدروس المتاحة</h2>
                            <a class="btn btn-soft" href="{{ route('learning-paths.index') }}">استعرض المسارات</a>
                        @endif
                    </article>
                </div>

                <div class="col-lg-5">
                    <article class="surface-card p-4 h-100">
                        <span class="badge badge-soft mb-3">الملف الشخصي</span>
                        <form class="d-grid gap-2 mb-4" method="POST" action="{{ route('dashboard.avatar.update') }}" enctype="multipart/form-data">
                            @csrf
                            <label class="form-label" for="avatar">صورة الملف الشخصي</label>
                            <input class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" required>
                            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <button class="btn btn-soft" type="submit">رفع الصورة</button>
                        </form>

                        <span class="badge badge-soft mb-3">اشتراكك</span>
                        @if ($activeSubscription)
                            <h2 class="h4">{{ $activeSubscription->plan?->name ?? 'اشتراك نشط' }}</h2>
                            <p class="text-muted-soft">{{ $subscriptionLabels[$activeSubscription->access_level->value] ?? 'عضوية' }}</p>
                            <span class="text-muted-soft d-block">ينتهي</span>
                            <strong>{{ $activeSubscription->ends_at?->translatedFormat('d F Y') ?? 'بدون تاريخ انتهاء' }}</strong>
                        @else
                            <h2 class="h4">الحساب المجاني</h2>
                            <p class="text-muted-soft">يمكنك مشاهدة الدروس المجانية والانضمام للمجتمع.</p>
                            <a class="btn btn-brand" href="{{ route('subscription-plans.index') }}">ترقية الاشتراك</a>
                        @endif
                    </article>
                </div>

                <div class="col-lg-6">
                    <article class="surface-card p-4 h-100">
                        <span class="badge badge-soft mb-3">المسار المقترح</span>
                        <h2 class="h4">{{ $recommendedPath?->title ?? 'سيتم إضافة مسار قريبًا' }}</h2>
                        <p class="text-muted-soft">{{ $recommendedPath?->description ?? 'لوحة العضو جاهزة لاستقبال المحتوى.' }}</p>
                        @if ($recommendedPath)
                            <a class="btn btn-soft" href="{{ route('learning-paths.show', $recommendedPath) }}">عرض المسار</a>
                        @endif
                    </article>
                </div>

                <div class="col-lg-6">
                    <article class="surface-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                            <span class="badge badge-soft">الإشعارات</span>
                            @if ($unreadNotificationsCount > 0)
                                <span class="badge text-bg-warning">{{ $unreadNotificationsCount }} جديد</span>
                            @endif
                        </div>

                        @forelse ($recentNotifications as $notification)
                            @php $data = $notification->data; @endphp
                            <div class="border-top py-3 {{ $loop->first ? 'border-top-0 pt-0' : '' }}">
                                <strong class="d-block">{{ $data['actor_name'] ?? 'عضو' }}</strong>
                                <span class="text-muted-soft">{{ $data['post_title'] ?? 'بوست في المجتمع' }}</span>
                            </div>
                        @empty
                            <p class="text-muted-soft mb-0">لا توجد إشعارات بعد.</p>
                        @endforelse

                        <a class="btn btn-soft mt-3" href="{{ route('notifications.index') }}">عرض كل الإشعارات</a>

                        @if (config('push.enabled') && config('push.vapid.public_key'))
                            <button
                                class="btn btn-soft mt-2 w-100"
                                type="button"
                                data-push-enable
                                data-vapid-public-key="{{ config('push.vapid.public_key') }}"
                                data-store-url="{{ route('push-subscriptions.store') }}"
                            >
                                تفعيل إشعارات المتصفح
                            </button>
                        @endif
                    </article>
                </div>

                <div class="col-lg-6">
                    <article class="surface-card p-4 h-100">
                        <span class="badge badge-soft mb-3">{{ $registeredEvent ? 'حجزك القادم' : 'اللايف القادم' }}</span>
                        <h2 class="h4">{{ $registeredEvent?->event?->title ?? $upcomingLive?->title ?? 'لا يوجد لايف منشور حاليًا' }}</h2>
                        <p class="text-muted-soft">{{ $registeredEvent?->event?->description ?? $upcomingLive?->description ?? 'سيظهر هنا موعد اللايف القادم.' }}</p>
                        <a class="btn btn-soft" href="{{ route('live-events.index') }}">عرض اللايفات</a>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection
