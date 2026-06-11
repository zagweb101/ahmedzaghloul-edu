@extends('layouts.app')

@section('title', 'SEO لخطة ' . $plan->name)

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="col-lg-8">
                <p class="fw-bold text-accent mb-2">الاشتراكات</p>
                <h1 class="display-6 fw-bold mb-4">تحرير SEO — {{ $plan->name }}</h1>

                @include('admin.partials.nav')

                <form class="surface-card p-4 d-grid gap-3" method="POST" action="{{ route('admin.subscription-plans.update', $plan) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <x-admin-seo-fields :model="$plan" />

                    <x-media-field
                        label="صورة صفحة الهبوط"
                        name="cover_image"
                        accept="image/jpeg,image/png,image/webp"
                        :current-url="$plan->coverImageUrl()"
                        remove-name="remove_cover_image"
                    />

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-brand btn-lg" type="submit">حفظ التعديلات</button>
                        <a class="btn btn-soft btn-lg" href="{{ route('admin.subscription-plans.index') }}">رجوع</a>
                        <a class="btn btn-soft btn-lg" href="{{ route('subscription-plans.show', $plan) }}" target="_blank">معاينة الصفحة</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
