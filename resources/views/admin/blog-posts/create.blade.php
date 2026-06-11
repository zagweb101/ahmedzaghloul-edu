@extends('layouts.app')

@section('title', 'مقال جديد')

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="col-lg-8">
                <p class="fw-bold text-accent mb-2">المدونة</p>
                <h1 class="display-6 fw-bold mb-4">إضافة مقال</h1>

                @include('admin.partials.nav')

                <form class="surface-card p-4 d-grid gap-3" method="POST" action="{{ route('admin.blog-posts.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <label class="form-label" for="title">العنوان</label>
                        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="slug">الرابط المختصر بالإنجليزية</label>
                        <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" required>
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="excerpt">مقتطف</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="2">{{ old('excerpt') }}</textarea>
                    </div>

                    <div>
                        <label class="form-label" for="body">المحتوى</label>
                        <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="10" required>{{ old('body') }}</textarea>
                        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <x-admin-seo-fields />

                    <div>
                        <label class="form-label" for="author_name">اسم الكاتب</label>
                        <input class="form-control" id="author_name" name="author_name" value="{{ old('author_name', 'أحمد زغلول') }}">
                    </div>

                    <x-media-field
                        label="صورة الغلاف"
                        name="cover_image"
                        accept="image/jpeg,image/png,image/webp"
                    />

                    <div>
                        <label class="form-label" for="published_at">تاريخ النشر</label>
                        <input class="form-control" id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                    </div>

                    <label class="form-check">
                        <input class="form-check-input" name="is_published" type="checkbox" value="1" @checked(old('is_published', true))>
                        <span class="form-check-label">نشر المقال</span>
                    </label>

                    <button class="btn btn-brand btn-lg" type="submit">حفظ المقال</button>
                </form>
            </div>
        </div>
    </section>
@endsection
