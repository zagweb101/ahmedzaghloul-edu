@extends('layouts.app')

@section('title', 'المجتمع')

@php
    $categoryLabels = [
        'question' => 'اسأل أحمد',
        'showcase' => 'شارك صورتك',
        'challenge' => 'تحدي الأسبوع',
        'feedback' => 'نقد وتقييم',
        'gear' => 'معدات وتجهيزات',
    ];
@endphp

@section('content')
    <x-page-header
        eyebrow="المجتمع"
        title="مساحة تطبيق وتفاعل للمصورين"
        description="شارك صورك، اسأل، احصل على تقييم، وتفاعل مع مصورين لديهم نفس الشغف."
    />

    <section class="section-block pt-0">
        <div class="container">
            @include('community.partials.tabs')

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @auth
                <div class="surface-card p-4 mb-4">
                    <h2 class="h4 mb-3">شارك المجتمع</h2>
                    <form class="d-grid gap-3" method="POST" action="{{ route('community.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div>
                            <label class="form-label" for="post-title">عنوان البوست</label>
                            <input class="form-control @error('title') is-invalid @enderror" id="post-title" name="title" value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="post-category">القسم</label>
                                <select class="form-select" id="post-category" name="category">
                                    @foreach ($categoryLabels as $value => $label)
                                        <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="post-images">صور اختيارية</label>
                                <input class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" id="post-images" name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple>
                                @error('images')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted-soft">حتى {{ \App\Services\FileStorageService::MAX_POST_IMAGES }} صور، JPG أو PNG أو WEBP، بحد أقصى 5 ميجابايت لكل صورة.</small>
                            </div>
                        </div>

                        <div>
                            <label class="form-label" for="post-body">المحتوى</label>
                            <textarea class="form-control @error('body') is-invalid @enderror" id="post-body" name="body" rows="4" required>{{ old('body') }}</textarea>
                            @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button class="btn btn-brand" style="width: fit-content;" type="submit">نشر البوست</button>
                    </form>
                </div>
            @else
                <div class="surface-card p-4 mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h2 class="h4">انضم للمشاركة</h2>
                        <p class="text-muted-soft mb-0">سجل الدخول لنشر صورك أو التعليق على أعمال الأعضاء.</p>
                    </div>
                    <a class="btn btn-brand align-self-start" href="{{ route('login') }}">تسجيل الدخول</a>
                </div>
            @endauth

            <div class="d-grid gap-3">
                @forelse ($posts as $post)
                    <article class="surface-card community-post overflow-hidden" id="post-{{ $post->id }}">
                        @include('community.partials.post-images', ['post' => $post])

                        <div class="p-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge badge-soft">{{ $categoryLabels[$post->category] ?? $post->category }}</span>
                                    @if ($post->is_pinned)
                                        <span class="badge text-bg-warning">مثبت</span>
                                    @endif
                                </div>
                                <small class="text-muted-soft">{{ $post->created_at->diffForHumans() }}</small>
                            </div>

                            <h2 class="h4">{{ $post->title }}</h2>
                            <p class="text-muted-soft preserve-lines">{{ $post->body }}</p>

                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <small class="fw-bold">{{ $post->user?->name }}</small>

                                <div class="d-flex align-items-center gap-2">
                                    @auth
                                        <form method="POST" action="{{ route('community.likes.toggle', $post) }}">
                                            @csrf
                                            <button class="btn btn-sm {{ $post->liked_by_current_user ? 'btn-brand' : 'btn-soft' }}" type="submit">
                                                إعجاب · {{ $post->liked_by_users_count }}
                                            </button>
                                        </form>

                                        @if ($post->canBeEditedBy(auth()->user()))
                                            <a class="btn btn-sm btn-soft" href="{{ route('community.edit', $post) }}">تعديل</a>
                                            <form method="POST" action="{{ route('community.destroy', $post) }}" onsubmit="return confirm('هل تريد حذف البوست؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit">حذف</button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="text-muted-soft">{{ $post->liked_by_users_count }} إعجاب</span>
                                    @endauth
                                </div>
                            </div>

                            @if ($post->comments->isNotEmpty())
                                <div class="border-top mt-4 pt-3 d-grid gap-3">
                                    @foreach ($post->comments as $comment)
                                        <div class="bg-body border rounded-3 p-3">
                                            <strong class="d-block mb-1">{{ $comment->user?->name }}</strong>
                                            @if ($comment->body)
                                                <p class="text-muted-soft preserve-lines {{ $comment->imageUrl() ? 'mb-2' : 'mb-0' }}">{{ $comment->body }}</p>
                                            @endif
                                            @if ($comment->imageUrl())
                                                <img class="comment-image" src="{{ $comment->imageUrl() }}" alt="صورة مرفقة مع تعليق {{ $comment->user?->name }}">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @auth
                                <form class="d-grid gap-2 border-top mt-4 pt-3" method="POST" action="{{ route('community.comments.store', $post) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="d-flex flex-column flex-sm-row gap-2">
                                        <input class="form-control" name="body" placeholder="اكتب تعليقك">
                                        <button class="btn btn-soft flex-shrink-0" type="submit">تعليق</button>
                                    </div>
                                    <div>
                                        <input class="form-control" name="image" type="file" accept="image/jpeg,image/png,image/webp">
                                        <small class="text-muted-soft">يمكنك إرفاق صورة مع التعليق.</small>
                                    </div>
                                </form>
                            @endauth
                        </div>
                    </article>
                @empty
                    <div class="surface-card p-4">
                        <h2 class="h4">ابدأ أول نقاش</h2>
                        <p class="text-muted-soft mb-0">المجتمع جاهز لاستقبال أول بوست من الأعضاء.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
