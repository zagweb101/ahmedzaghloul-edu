@props([
    'label',
    'name',
    'accept',
    'currentUrl' => null,
    'removeName' => null,
    'hint' => null,
    'fileLabel' => 'الملف الحالي',
])

<div {{ $attributes->merge(['class' => 'd-grid gap-2']) }}>
    <label class="form-label" for="{{ $name }}">{{ $label }}</label>

    @if ($currentUrl)
        <div class="media-preview-box">
            @if (str_contains($accept, 'pdf'))
                <a class="btn btn-soft" href="{{ $currentUrl }}" target="_blank" rel="noopener">{{ $fileLabel }}</a>
            @else
                <img class="media-preview" src="{{ $currentUrl }}" alt="{{ $label }}">
            @endif
        </div>

        @if ($removeName)
            <label class="form-check">
                <input class="form-check-input" type="checkbox" name="{{ $removeName }}" value="1" @checked(old($removeName))>
                <span class="form-check-label">حذف الملف الحالي</span>
            </label>
        @endif
    @endif

    <input
        class="form-control @error($name) is-invalid @enderror"
        id="{{ $name }}"
        name="{{ $name }}"
        type="file"
        accept="{{ $accept }}"
    >
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror

    @if ($hint)
        <small class="text-muted-soft">{{ $hint }}</small>
    @endif
</div>
