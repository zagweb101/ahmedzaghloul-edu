@extends('layouts.app')

@section('title', 'إضافة مسار')

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="col-lg-8">
                <p class="fw-bold text-accent mb-2">المحتوى</p>
                <h1 class="display-6 fw-bold mb-4">إضافة مسار جديد</h1>

                @include('admin.partials.nav')

                <form class="surface-card p-4 d-grid gap-3" method="POST" action="{{ route('admin.learning-paths.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <label class="form-label" for="title">اسم المسار</label>
                        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="slug">الرابط المختصر بالإنجليزية</label>
                        <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" placeholder="portrait-lighting" required>
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="description">الوصف</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="border-top pt-3">
                        <h2 class="h6 mb-3">تحسين محركات البحث (SEO)</h2>
                        <div class="d-grid gap-3">
                            <div>
                                <label class="form-label" for="seo_title">عنوان SEO (اختياري)</label>
                                <input class="form-control" id="seo_title" name="seo_title" value="{{ old('seo_title') }}" maxlength="255">
                            </div>
                            <div>
                                <label class="form-label" for="seo_description">وصف SEO (اختياري)</label>
                                <textarea class="form-control" id="seo_description" name="seo_description" rows="2" maxlength="500">{{ old('seo_description') }}</textarea>
                            </div>
                            <div>
                                <label class="form-label" for="seo_keywords">كلمات مفتاحية (اختياري)</label>
                                <input class="form-control" id="seo_keywords" name="seo_keywords" value="{{ old('seo_keywords') }}" placeholder="تصوير، إضاءة، بورتريه">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="cover_image">صورة الغلاف</label>
                        <input class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" type="file" accept="image/jpeg,image/png,image/webp">
                        @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted-soft">JPG أو PNG أو WEBP، بحد أقصى 5 ميجابايت.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="level">المستوى</label>
                            <select class="form-select" id="level" name="level">
                                @foreach ($levels as $level)
                                    <option value="{{ $level->value }}">{{ $level->value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="access_level">الوصول</label>
                            <select class="form-select" id="access_level" name="access_level">
                                @foreach ($accessLevels as $accessLevel)
                                    <option value="{{ $accessLevel->value }}">{{ $accessLevel->value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="sort_order">الترتيب</label>
                            <input class="form-control" id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', 0) }}" min="0">
                        </div>
                    </div>

                    <label class="form-check">
                        <input class="form-check-input" name="is_published" type="checkbox" value="1" checked>
                        <span class="form-check-label">نشر المسار</span>
                    </label>

                    <button class="btn btn-brand btn-lg" type="submit">حفظ المسار</button>
                </form>
            </div>
        </div>
    </section>
@endsection
