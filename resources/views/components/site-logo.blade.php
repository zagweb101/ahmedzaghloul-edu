@props([
    'compact' => false,
])

<a {{ $attributes->merge(['class' => 'site-logo text-reset d-flex align-items-center gap-2']) }} href="{{ route('home') }}">
    <img
        class="site-logo-brandbook {{ $compact ? 'site-logo-brandbook--compact' : '' }}"
        src="{{ asset(config('brand.logo')) }}"
        alt="{{ config('app.name') }}"
        width="{{ $compact ? 120 : 168 }}"
        height="40"
        loading="eager"
        decoding="async"
    >
    @unless ($compact)
        <span class="site-logo-tagline d-none d-xl-inline">{{ config('brand.tagline') }}</span>
    @endunless
</a>
