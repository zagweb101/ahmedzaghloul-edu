@extends('layouts.app')

@section('title', 'إدارة المسارات')

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                <div>
                    <p class="fw-bold text-accent mb-2">المحتوى</p>
                    <h1 class="display-6 fw-bold mb-0">إدارة المسارات</h1>
                </div>
                <a class="btn btn-brand align-self-start" href="{{ route('admin.learning-paths.create') }}">إضافة مسار</a>
            </div>

            @include('admin.partials.nav')

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="d-grid gap-3">
                @foreach ($paths as $path)
                    <article class="surface-card overflow-hidden">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3 p-4">
                            <div class="d-flex gap-3">
                                @if ($path->coverImageUrl())
                                    <img class="admin-thumb" src="{{ $path->coverImageUrl() }}" alt="{{ $path->title }}">
                                @endif
                                <div>
                                    <h2 class="h4">{{ $path->title }}</h2>
                                    <p class="text-muted-soft mb-0">{{ $path->description }}</p>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-md-end gap-2">
                                <div class="text-muted-soft">
                                    <span class="d-block">{{ $path->lessons_count }} درس</span>
                                    <span class="d-block">{{ $path->is_published ? 'منشور' : 'مسودة' }}</span>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-soft" href="{{ route('admin.learning-paths.edit', $path) }}">تعديل</a>
                                    <a class="btn btn-soft" href="{{ route('admin.learning-paths.lessons.index', $path) }}">الدروس</a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
