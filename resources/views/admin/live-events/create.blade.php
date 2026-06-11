@extends('layouts.app')

@section('title', 'إضافة لايف')

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="col-lg-8">
                <p class="fw-bold text-accent mb-2">الفعاليات</p>
                <h1 class="display-6 fw-bold mb-4">إضافة لايف جديد</h1>

                @include('admin.partials.nav')

                <form class="surface-card p-4 d-grid gap-3" method="POST" action="{{ route('admin.live-events.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <label class="form-label" for="title">عنوان اللايف</label>
                        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="slug">الرابط المختصر بالإنجليزية</label>
                        <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" placeholder="lighting-live" required>
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="description">الوصف</label>
                        <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    </div>

                    <x-admin-seo-fields />

                    <div>
                        <label class="form-label" for="cover_image">صورة الغلاف</label>
                        <input class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" type="file" accept="image/jpeg,image/png,image/webp">
                        @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="starts_at">وقت البداية</label>
                            <input class="form-control" id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ends_at">وقت النهاية</label>
                            <input class="form-control" id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at') }}">
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="location">المكان</label>
                        <input class="form-control" id="location" name="location" value="{{ old('location', 'اونلاين') }}">
                    </div>

                    <div>
                        <label class="form-label" for="capacity">عدد المقاعد</label>
                        <input class="form-control" id="capacity" name="capacity" type="number" value="{{ old('capacity') }}" min="1" placeholder="اتركه فارغًا لو العدد غير محدود">
                    </div>

                    <div>
                        <label class="form-label" for="stream_url">رابط البث</label>
                        <input class="form-control @error('stream_url') is-invalid @enderror" id="stream_url" name="stream_url" type="url" value="{{ old('stream_url') }}">
                        @error('stream_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="access_level">الوصول</label>
                        <select class="form-select" id="access_level" name="access_level">
                            @foreach ($accessLevels as $accessLevel)
                                <option value="{{ $accessLevel->value }}">{{ $accessLevel->value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="form-check">
                        <input class="form-check-input" name="is_published" type="checkbox" value="1" checked>
                        <span class="form-check-label">نشر اللايف</span>
                    </label>

                    <button class="btn btn-brand btn-lg" type="submit">حفظ اللايف</button>
                </form>
            </div>
        </div>
    </section>
@endsection
