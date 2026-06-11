@extends('layouts.app')

@section('title', 'إدارة المجتمع')

@section('content')
    <section class="section-block">
        <div class="container">
            <p class="fw-bold text-accent mb-2">المجتمع</p>
            <h1 class="display-6 fw-bold mb-4">مراجعة البوستات</h1>

            @include('admin.partials.nav')

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="d-grid gap-3">
                @forelse ($posts as $post)
                    <article class="surface-card p-4">
                        <div class="row g-4 align-items-start">
                            <div class="col-lg-7">
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge badge-soft">{{ $post->category }}</span>
                                    @if ($post->is_pinned)
                                        <span class="badge text-bg-warning">مثبت</span>
                                    @endif
                                    @unless ($post->is_published)
                                        <span class="badge text-bg-secondary">مخفي</span>
                                    @endunless
                                </div>
                                <h2 class="h4">{{ $post->title }}</h2>
                                <p class="text-muted-soft mb-2">{{ $post->body }}</p>
                                <small class="text-muted-soft">{{ $post->user?->name }} · {{ $post->comments_count }} تعليق · {{ $post->liked_by_users_count }} إعجاب</small>
                            </div>

                            <div class="col-lg-5">
                                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                                    <form method="POST" action="{{ route('admin.community.pinned.toggle', $post) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-soft" type="submit">{{ $post->is_pinned ? 'إلغاء التثبيت' : 'تثبيت' }}</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.community.published.toggle', $post) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-soft" type="submit">{{ $post->is_published ? 'إخفاء' : 'إظهار' }}</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.community.destroy', $post) }}" onsubmit="return confirm('هل تريد حذف البوست نهائيًا؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger" type="submit">حذف</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="surface-card p-4 text-muted-soft">لا توجد بوستات للمراجعة.</div>
                @endforelse
            </div>

            <div class="mt-4">{{ $posts->links() }}</div>
        </div>
    </section>
@endsection
