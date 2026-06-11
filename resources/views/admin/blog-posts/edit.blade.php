@extends('layouts.app')

@section('title', 'تعديل ' . $post->title)

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="col-lg-8">
                <p class="fw-bold text-accent mb-2">المدونة</p>
                <h1 class="display-6 fw-bold mb-4">تعديل المقال</h1>

                @include('admin.partials.nav')

                <form class="surface-card p-4 d-grid gap-3" method="POST" action="{{ route('admin.blog-posts.update', $post) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="form-label" for="title">العنوان</label>
                        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $post->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="slug">الرابط المختصر بالإنجليزية</label>
                        <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $post->slug) }}" required>
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="excerpt">مقتطف</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="2">{{ old('excerpt', $post->excerpt) }}</textarea>
                    </div>

                    <div>
                        <label class="form-label" for="body">المحتوى</label>
                        <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="10" required>{{ old('body', $post->body) }}</textarea>
                        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <x-admin-seo-fields :model="$post" />

                    <div>
                        <label class="form-label" for="author_name">اسم الكاتب</label>
                        <input class="form-control" id="author_name" name="author_name" value="{{ old('author_name', $post->author_name) }}">
                    </div>

                    <x-media-field
                        label="صورة الغلاف"
                        name="cover_image"
                        accept="image/jpeg,image/png,image/webp"
                        :current-url="$post->coverImageUrl()"
                        remove-name="remove_cover_image"
                    />

                    <div>
                        <label class="form-label" for="published_at">تاريخ النشر</label>
                        <input class="form-control" id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                    </div>

                    <label class="form-check">
                        <input class="form-check-input" name="is_published" type="checkbox" value="1" @checked(old('is_published', $post->is_published))>
                        <span class="form-check-label">نشر المقال</span>
                    </label>

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-brand btn-lg" type="submit">حفظ التعديلات</button>
                        <a class="btn btn-soft btn-lg" href="{{ route('admin.blog-posts.index') }}">رجوع</a>
                        @if ($post->is_published)
                            <a class="btn btn-soft btn-lg" href="{{ route('blog.show', $post) }}" target="_blank">معاينة المقال</a>
                        @endif
                    </div>
                </form>

                <form class="mt-3" method="POST" action="{{ route('admin.blog-posts.destroy', $post) }}" onsubmit="return confirm('سيتم حذف المقال نهائيًا. هل أنت متأكد؟')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger" type="submit">حذف المقال</button>
                </form>
            </div>
        </div>
    </section>
@endsection
