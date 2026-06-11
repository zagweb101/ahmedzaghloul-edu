@props([
    'compact' => false,
])

<a {{ $attributes->merge(['class' => 'site-logo text-reset d-flex align-items-center gap-2']) }} href="{{ route('home') }}">
    <img
        class="site-logo-icon"
        src="{{ asset(config('brand.logo_icon')) }}"
        alt=""
        width="44"
        height="44"
        loading="eager"
        decoding="async"
    >
    @unless ($compact)
        <span class="site-logo-text">
            <strong class="d-block site-logo-title">بيت المصور</strong>
            <small class="site-logo-subtitle">BAYT ALMOSWER · ACADEMY</small>
        </span>
        <span class="site-logo-tagline d-none d-xl-inline">{{ config('brand.tagline') }}</span>
    @endunless
</a>
