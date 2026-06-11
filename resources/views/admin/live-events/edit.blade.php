@extends('layouts.app')

@section('title', 'تعديل ' . $event->title)

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="col-lg-8">
                <p class="fw-bold text-accent mb-2">الفعاليات</p>
                <h1 class="display-6 fw-bold mb-4">تعديل اللايف</h1>

                @include('admin.partials.nav')

                <form class="surface-card p-4 d-grid gap-3" method="POST" action="{{ route('admin.live-events.update', $event) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="form-label" for="title">عنوان اللايف</label>
                        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $event->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="slug">الرابط المختصر بالإنجليزية</label>
                        <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $event->slug) }}" required>
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="description">الوصف</label>
                        <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $event->description) }}</textarea>
                    </div>

                    <x-media-field
                        label="صورة الغلاف"
                        name="cover_image"
                        accept="image/jpeg,image/png,image/webp"
                        :current-url="$event->coverImageUrl()"
                        remove-name="remove_cover_image"
                    />

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="starts_at">وقت البداية</label>
                            <input class="form-control" id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at', $event->starts_at?->format('Y-m-d\TH:i')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ends_at">وقت النهاية</label>
                            <input class="form-control" id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\TH:i')) }}">
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="location">المكان</label>
                        <input class="form-control" id="location" name="location" value="{{ old('location', $event->location) }}">
                    </div>

                    <div>
                        <label class="form-label" for="capacity">عدد المقاعد</label>
                        <input class="form-control" id="capacity" name="capacity" type="number" value="{{ old('capacity', $event->capacity) }}" min="1">
                    </div>

                    <div>
                        <label class="form-label" for="stream_url">رابط البث</label>
                        <input class="form-control" id="stream_url" name="stream_url" type="url" value="{{ old('stream_url', $event->stream_url) }}">
                    </div>

                    <div>
                        <label class="form-label" for="access_level">الوصول</label>
                        <select class="form-select" id="access_level" name="access_level">
                            @foreach ($accessLevels as $accessLevel)
                                <option value="{{ $accessLevel->value }}" @selected(old('access_level', $event->access_level->value) === $accessLevel->value)>{{ $accessLevel->value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="form-check">
                        <input class="form-check-input" name="is_published" type="checkbox" value="1" @checked(old('is_published', $event->is_published))>
                        <span class="form-check-label">نشر اللايف</span>
                    </label>

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-brand btn-lg" type="submit">حفظ التعديلات</button>
                        <a class="btn btn-soft btn-lg" href="{{ route('admin.live-events.index') }}">رجوع</a>
                    </div>
                </form>

                <form class="mt-3" method="POST" action="{{ route('admin.live-events.destroy', $event) }}" onsubmit="return confirm('سيتم حذف اللايف وملفاته. هل أنت متأكد؟')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger" type="submit">حذف اللايف نهائيًا</button>
                </form>
            </div>
        </div>
    </section>
@endsection
