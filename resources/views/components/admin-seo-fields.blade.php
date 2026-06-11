@props([
    'model' => null,
])

<div class="border-top pt-3">
    <h2 class="h6 mb-3">تحسين محركات البحث (SEO)</h2>
    <div class="d-grid gap-3">
        <div>
            <label class="form-label" for="seo_title">عنوان SEO (اختياري)</label>
            <input class="form-control" id="seo_title" name="seo_title" value="{{ old('seo_title', $model?->seo_title) }}" maxlength="255">
        </div>
        <div>
            <label class="form-label" for="seo_description">وصف SEO (اختياري)</label>
            <textarea class="form-control" id="seo_description" name="seo_description" rows="2" maxlength="500">{{ old('seo_description', $model?->seo_description) }}</textarea>
        </div>
        <div>
            <label class="form-label" for="seo_keywords">كلمات مفتاحية (اختياري)</label>
            <input class="form-control" id="seo_keywords" name="seo_keywords" value="{{ old('seo_keywords', $model?->seo_keywords) }}" placeholder="تصوير، إضاءة، بورتريه">
        </div>
    </div>
</div>
