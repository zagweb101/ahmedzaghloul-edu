@extends('layouts.app')

@section('title', 'إدارة الاشتراكات')

@section('content')
    <section class="section-block">
        <div class="container">
            <p class="fw-bold text-accent mb-2">الاشتراكات</p>
            <h1 class="display-6 fw-bold mb-4">خطط الاشتراك</h1>

            @include('admin.partials.nav')

            <div class="row g-3">
                @foreach ($plans as $plan)
                    <div class="col-lg-4">
                        <article class="surface-card p-4 h-100">
                            <h2 class="h4">{{ $plan->name }}</h2>
                            <p class="text-muted-soft">{{ $plan->description }}</p>
                            <span class="badge badge-soft">{{ $plan->is_active ? 'نشطة' : 'متوقفة' }}</span>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
