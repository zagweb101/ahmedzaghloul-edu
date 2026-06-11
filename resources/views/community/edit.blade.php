@extends('layouts.app')

@section('title', 'تعديل البوست')

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
    <section class="section-block">
        <div class="container">
            <div class="col-lg-8">
                <p class="fw-bold text-accent mb-2">المجتمع</p>
                <h1 class="display-6 fw-bold mb-4">تعديل البوست</h1>

                @include('community.partials.tabs')

                <form class="surface-card p-4 d-grid gap-3" method="POST" action="{{ route('community.update', $post) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="form-label" for="title">عنوان البوست</label>
                        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $post->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="category">القسم</label>
                        <select class="form-select" id="category" name="category">
                            @foreach ($categoryLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('category', $post->category) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label" for="body">المحتوى</label>
                        <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="5" required>{{ old('body', $post->body) }}</textarea>
                        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    @if ($post->images->isNotEmpty())
                        <div>
                            <span class="form-label d-block">الصور الحالية</span>
                            <div class="row g-2">
                                @foreach ($post->images as $image)
                                    <div class="col-6 col-md-4">
                                        <label class="edit-image-card">
                                            <img src="{{ $image->url() }}" alt="صورة البوست">
                                            <span class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="remove_image_ids[]" value="{{ $image->id }}" @checked(collect(old('remove_image_ids', []))->contains($image->id))>
                                                <span class="form-check-label">حذف هذه الصورة</span>
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="form-label" for="images">إضافة صور جديدة</label>
                        <input class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" id="images" name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple>
                        @error('images')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted-soft">يمكنك الاحتفاظ بحد أقصى {{ \App\Services\FileStorageService::MAX_POST_IMAGES }} صور في البوست الواحد.</small>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-brand" type="submit">حفظ التعديلات</button>
                        <a class="btn btn-soft" href="{{ route('community.index') }}">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
