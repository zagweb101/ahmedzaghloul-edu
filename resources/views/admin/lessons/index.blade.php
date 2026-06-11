@extends('layouts.app')

@section('title', 'إدارة دروس ' . $path->title)

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                <div>
                    <p class="fw-bold text-accent mb-2">{{ $path->title }}</p>
                    <h1 class="display-6 fw-bold mb-0">إدارة الدروس</h1>
                </div>
                <a class="btn btn-brand align-self-start" href="{{ route('admin.learning-paths.lessons.create', $path) }}">إضافة درس</a>
            </div>

            @include('admin.partials.nav')

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="d-grid gap-3">
                @forelse ($lessons as $lesson)
                    <article class="surface-card p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                            <div class="d-flex gap-3">
                                @if ($lesson->thumbnailUrl())
                                    <img class="admin-thumb" src="{{ $lesson->thumbnailUrl() }}" alt="{{ $lesson->title }}">
                                @endif
                                <div>
                                    <span class="badge badge-soft mb-2">الدرس {{ $lesson->sort_order }}</span>
                                    <h2 class="h4">{{ $lesson->title }}</h2>
                                    <p class="text-muted-soft mb-0">{{ $lesson->summary }}</p>
                                    <small class="text-muted-soft">
                                        @if ($lesson->pdf_path) PDF مرفوع @endif
                                        @if ($lesson->pdf_url) · رابط PDF @endif
                                    </small>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-md-end gap-2 flex-shrink-0">
                                <div class="text-muted-soft">
                                    <span class="d-block">{{ $lesson->duration_minutes ?? '-' }} دقيقة</span>
                                    <span class="d-block">{{ $lesson->is_published ? 'منشور' : 'مسودة' }}</span>
                                </div>
                                <a class="btn btn-soft" href="{{ route('admin.learning-paths.lessons.edit', [$path, $lesson]) }}">تعديل</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="surface-card p-4 text-muted-soft">لا توجد دروس داخل هذا المسار بعد.</div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
