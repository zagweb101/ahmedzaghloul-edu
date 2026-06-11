@props([
    'compact' => false,
])

<a {{ $attributes->merge(['class' => 'site-logo text-reset d-flex align-items-center gap-3']) }} href="{{ route('home') }}">
    <img
        class="site-logo-mark"
        src="{{ asset('icons/logo-mark.svg') }}"
        alt=""
        width="44"
        height="44"
        loading="eager"
        decoding="async"
    >
    @unless ($compact)
        <span class="site-logo-text">
            <strong class="d-block site-logo-title">بيت المصور</strong>
            <small class="site-logo-subtitle">أكاديمية أحمد زغلول</small>
        </span>
    @endunless
</a>
