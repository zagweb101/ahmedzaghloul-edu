@extends('layouts.app')

@section('title', 'إدارة المدونة')

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <p class="fw-bold text-accent mb-2">المدونة</p>
                    <h1 class="display-6 fw-bold mb-0">المقالات</h1>
                </div>
                <a class="btn btn-brand" href="{{ route('admin.blog-posts.create') }}">مقال جديد</a>
            </div>

            @include('admin.partials.nav')

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="surface-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>العنوان</th>
                                <th>الحالة</th>
                                <th>تاريخ النشر</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($posts as $post)
                                <tr>
                                    <td>{{ $post->title }}</td>
                                    <td>
                                        <span class="badge {{ $post->is_published ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $post->is_published ? 'منشور' : 'مسودة' }}
                                        </span>
                                    </td>
                                    <td>{{ $post->published_at?->translatedFormat('d/m/Y') ?? '—' }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-soft btn-sm" href="{{ route('admin.blog-posts.edit', $post) }}">تعديل</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-muted-soft">لا توجد مقالات بعد.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
