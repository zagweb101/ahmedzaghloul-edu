@extends('layouts.app')

@section('title', 'إدارة الأعضاء')

@php
    $accessLabels = [
        'member' => 'عضوية كاملة',
        'premium' => 'عضوية متقدمة',
    ];
@endphp

@section('content')
    <section class="section-block">
        <div class="container">
            <p class="fw-bold text-accent mb-2">الأعضاء</p>
            <h1 class="display-6 fw-bold mb-4">إدارة العضويات</h1>

            @include('admin.partials.nav')

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="d-grid gap-3">
                @foreach ($users as $user)
                    @php($activeSubscription = $user->activeSubscription())
                    <article class="surface-card p-4">
                        <div class="row align-items-start g-4">
                            <div class="col-lg-4">
                                <h2 class="h5 mb-1">{{ $user->name }}</h2>
                                <span class="text-muted-soft d-block">{{ $user->email }}</span>
                                @if ($user->is_admin)
                                    <span class="badge badge-soft mt-2">مدير</span>
                                @endif
                            </div>

                            <div class="col-lg-3">
                                <span class="text-muted-soft d-block">الاشتراك الحالي</span>
                                @if ($activeSubscription)
                                    <strong class="d-block">{{ $activeSubscription->plan?->name ?? 'اشتراك' }}</strong>
                                    <small class="text-muted-soft">{{ $accessLabels[$activeSubscription->access_level->value] ?? 'عضوية' }}</small>
                                @else
                                    <strong>مجاني</strong>
                                @endif
                            </div>

                            <div class="col-lg-5">
                                <form class="row g-2" method="POST" action="{{ route('admin.users.subscription.store', $user) }}">
                                    @csrf
                                    <div class="col-sm-5">
                                        <label class="visually-hidden" for="plan-{{ $user->id }}">الخطة</label>
                                        <select class="form-select" id="plan-{{ $user->id }}" name="subscription_plan_id" required>
                                            @foreach ($plans as $plan)
                                                <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="visually-hidden" for="access-{{ $user->id }}">المستوى</label>
                                        <select class="form-select" id="access-{{ $user->id }}" name="access_level" required>
                                            @foreach ($accessLevels as $accessLevel)
                                                <option value="{{ $accessLevel->value }}">{{ $accessLabels[$accessLevel->value] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <button class="btn btn-brand w-100" type="submit">تفعيل</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </section>
@endsection
