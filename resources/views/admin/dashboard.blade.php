@extends('layouts.app')

@section('title', 'لوحة الإدارة')

@section('content')
    <section class="section-block">
        <div class="container">
            <p class="fw-bold text-accent mb-2">لوحة الإدارة</p>
            <h1 class="display-5 fw-bold mb-4">إدارة المنصة</h1>

            @include('admin.partials.nav')

            <div class="row g-3">
                @foreach ([
                    'users' => 'المستخدمين',
                    'paths' => 'المسارات',
                    'lessons' => 'الدروس',
                    'events' => 'اللايفات',
                    'plans' => 'الاشتراكات',
                    'posts' => 'بوستات المجتمع',
                ] as $key => $label)
                    <div class="col-6 col-lg-4">
                        <div class="surface-card p-4">
                            <strong class="fs-2 d-block">{{ $counts[$key] }}</strong>
                            <span class="text-muted-soft">{{ $label }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
