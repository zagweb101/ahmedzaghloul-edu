@extends('layouts.app')

@section('title', 'تعديل ' . $lesson->title)

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="col-lg-8">
                <p class="fw-bold text-accent mb-2">{{ $path->title }}</p>
                <h1 class="display-6 fw-bold mb-4">تعديل الدرس</h1>

                @include('admin.partials.nav')

                <form class="surface-card p-4 d-grid gap-3" method="POST" action="{{ route('admin.learning-paths.lessons.update', [$path, $lesson]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="form-label" for="title">اسم الدرس</label>
                        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $lesson->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="slug">الرابط المختصر بالإنجليزية</label>
                        <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $lesson->slug) }}" required>
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="summary">ملخص الدرس</label>
                        <textarea class="form-control" id="summary" name="summary" rows="4">{{ old('summary', $lesson->summary) }}</textarea>
                    </div>

                    <x-media-field
                        label="صورة مصغرة للدرس"
                        name="thumbnail"
                        accept="image/jpeg,image/png,image/webp"
                        :current-url="$lesson->thumbnailUrl()"
                        remove-name="remove_thumbnail"
                    />

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="video_url">رابط الفيديو</label>
                            <input class="form-control" id="video_url" name="video_url" type="url" value="{{ old('video_url', $lesson->video_url) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="pdf_url">رابط PDF خارجي (اختياري)</label>
                            <input class="form-control" id="pdf_url" name="pdf_url" type="url" value="{{ old('pdf_url', $lesson->pdf_url) }}">
                        </div>
                    </div>

                    <x-media-field
                        label="ملف PDF مرفوع"
                        name="pdf_file"
                        accept="application/pdf"
                        :current-url="$lesson->pdf_path ? route('lessons.pdf', [$path, $lesson]) : null"
                        remove-name="remove_pdf_file"
                        file-label="عرض الملف الحالي"
                        hint="الملفات المرفوعة محمية وتُحمّل فقط للمشتركين المصرح لهم."
                    />

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="duration_minutes">المدة بالدقائق</label>
                            <input class="form-control" id="duration_minutes" name="duration_minutes" type="number" value="{{ old('duration_minutes', $lesson->duration_minutes) }}" min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="access_level">الوصول</label>
                            <select class="form-select" id="access_level" name="access_level">
                                @foreach ($accessLevels as $accessLevel)
                                    <option value="{{ $accessLevel->value }}" @selected(old('access_level', $lesson->access_level->value) === $accessLevel->value)>{{ $accessLevel->value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="sort_order">الترتيب</label>
                            <input class="form-control" id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $lesson->sort_order) }}" min="0">
                        </div>
                    </div>

                    <label class="form-check">
                        <input class="form-check-input" name="is_published" type="checkbox" value="1" @checked(old('is_published', $lesson->is_published))>
                        <span class="form-check-label">نشر الدرس</span>
                    </label>

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-brand btn-lg" type="submit">حفظ التعديلات</button>
                        <a class="btn btn-soft btn-lg" href="{{ route('admin.learning-paths.lessons.index', $path) }}">رجوع</a>
                    </div>
                </form>

                <form class="mt-3" method="POST" action="{{ route('admin.learning-paths.lessons.destroy', [$path, $lesson]) }}" onsubmit="return confirm('سيتم حذف الدرس وملفاته. هل أنت متأكد؟')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger" type="submit">حذف الدرس نهائيًا</button>
                </form>
            </div>
        </div>
    </section>
@endsection
