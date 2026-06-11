@extends('layouts.app')

@section('title', 'تعديل ' . $path->title)

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="col-lg-8">
                <p class="fw-bold text-accent mb-2">المحتوى</p>
                <h1 class="display-6 fw-bold mb-4">تعديل المسار</h1>

                @include('admin.partials.nav')

                <form class="surface-card p-4 d-grid gap-3" method="POST" action="{{ route('admin.learning-paths.update', $path) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="form-label" for="title">اسم المسار</label>
                        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $path->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="slug">الرابط المختصر بالإنجليزية</label>
                        <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $path->slug) }}" required>
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="description">الوصف</label>
                        <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $path->description) }}</textarea>
                    </div>

                    <div class="border-top pt-3">
                        <h2 class="h6 mb-3">تحسين محركات البحث (SEO)</h2>
                        <div class="d-grid gap-3">
                            <div>
                                <label class="form-label" for="seo_title">عنوان SEO (اختياري)</label>
                                <input class="form-control" id="seo_title" name="seo_title" value="{{ old('seo_title', $path->seo_title) }}" maxlength="255">
                            </div>
                            <div>
                                <label class="form-label" for="seo_description">وصف SEO (اختياري)</label>
                                <textarea class="form-control" id="seo_description" name="seo_description" rows="2" maxlength="500">{{ old('seo_description', $path->seo_description) }}</textarea>
                            </div>
                            <div>
                                <label class="form-label" for="seo_keywords">كلمات مفتاحية (اختياري)</label>
                                <input class="form-control" id="seo_keywords" name="seo_keywords" value="{{ old('seo_keywords', $path->seo_keywords) }}" placeholder="تصوير، إضاءة، بورتريه">
                            </div>
                        </div>
                    </div>

                    <x-media-field
                        label="صورة الغلاف"
                        name="cover_image"
                        accept="image/jpeg,image/png,image/webp"
                        :current-url="$path->coverImageUrl()"
                        remove-name="remove_cover_image"
                        hint="JPG أو PNG أو WEBP، بحد أقصى 5 ميجابايت."
                    />

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="level">المستوى</label>
                            <select class="form-select" id="level" name="level">
                                @foreach ($levels as $level)
                                    <option value="{{ $level->value }}" @selected(old('level', $path->level->value) === $level->value)>{{ $level->value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="access_level">الوصول</label>
                            <select class="form-select" id="access_level" name="access_level">
                                @foreach ($accessLevels as $accessLevel)
                                    <option value="{{ $accessLevel->value }}" @selected(old('access_level', $path->access_level->value) === $accessLevel->value)>{{ $accessLevel->value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="sort_order">الترتيب</label>
                            <input class="form-control" id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $path->sort_order) }}" min="0">
                        </div>
                    </div>

                    <label class="form-check">
                        <input class="form-check-input" name="is_published" type="checkbox" value="1" @checked(old('is_published', $path->is_published))>
                        <span class="form-check-label">نشر المسار</span>
                    </label>

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-brand btn-lg" type="submit">حفظ التعديلات</button>
                        <a class="btn btn-soft btn-lg" href="{{ route('admin.learning-paths.index') }}">رجوع</a>
                    </div>
                </form>

                <form class="mt-3" method="POST" action="{{ route('admin.learning-paths.destroy', $path) }}" onsubmit="return confirm('سيتم حذف المسار وجميع دروسه وملفاته. هل أنت متأكد؟')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger" type="submit">حذف المسار نهائيًا</button>
                </form>
            </div>
        </div>
    </section>
@endsection
