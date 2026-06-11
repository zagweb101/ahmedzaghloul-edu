@extends('layouts.app')

@section('title', 'إضافة درس')

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="col-lg-8">
                <p class="fw-bold text-accent mb-2">{{ $path->title }}</p>
                <h1 class="display-6 fw-bold mb-4">إضافة درس جديد</h1>

                @include('admin.partials.nav')

                <form class="surface-card p-4 d-grid gap-3" method="POST" action="{{ route('admin.learning-paths.lessons.store', $path) }}" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <label class="form-label" for="title">اسم الدرس</label>
                        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="slug">الرابط المختصر بالإنجليزية</label>
                        <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" placeholder="camera-settings" required>
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="summary">ملخص الدرس</label>
                        <textarea class="form-control" id="summary" name="summary" rows="4">{{ old('summary') }}</textarea>
                    </div>

                    <div>
                        <label class="form-label" for="thumbnail">صورة مصغرة للدرس</label>
                        <input class="form-control @error('thumbnail') is-invalid @enderror" id="thumbnail" name="thumbnail" type="file" accept="image/jpeg,image/png,image/webp">
                        @error('thumbnail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="video_url">رابط الفيديو</label>
                            <input class="form-control @error('video_url') is-invalid @enderror" id="video_url" name="video_url" type="url" value="{{ old('video_url') }}">
                            @error('video_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="pdf_url">رابط PDF خارجي (اختياري)</label>
                            <input class="form-control @error('pdf_url') is-invalid @enderror" id="pdf_url" name="pdf_url" type="url" value="{{ old('pdf_url') }}">
                            @error('pdf_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="pdf_file">رفع ملف PDF (اختياري)</label>
                        <input class="form-control @error('pdf_file') is-invalid @enderror" id="pdf_file" name="pdf_file" type="file" accept="application/pdf">
                        @error('pdf_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted-soft">الملفات المرفوعة محمية وتُحمّل فقط للمشتركين المصرح لهم.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="duration_minutes">المدة بالدقائق</label>
                            <input class="form-control" id="duration_minutes" name="duration_minutes" type="number" value="{{ old('duration_minutes') }}" min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="access_level">الوصول</label>
                            <select class="form-select" id="access_level" name="access_level">
                                @foreach ($accessLevels as $accessLevel)
                                    <option value="{{ $accessLevel->value }}">{{ $accessLevel->value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="sort_order">الترتيب</label>
                            <input class="form-control" id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $path->lessons()->count() + 1) }}" min="0">
                        </div>
                    </div>

                    <label class="form-check">
                        <input class="form-check-input" name="is_published" type="checkbox" value="1" checked>
                        <span class="form-check-label">نشر الدرس</span>
                    </label>

                    <button class="btn btn-brand btn-lg" type="submit">حفظ الدرس</button>
                </form>
            </div>
        </div>
    </section>
@endsection
