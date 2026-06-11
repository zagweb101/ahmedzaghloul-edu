@php
    $tabs = [
        'community.index' => 'النقاشات',
        'community.gallery' => 'معرض الصور',
    ];
@endphp

<nav class="community-tabs d-flex flex-wrap gap-2 mb-4" aria-label="أقسام المجتمع">
    @foreach ($tabs as $route => $label)
        <a
            class="btn {{ request()->routeIs($route) ? 'btn-brand' : 'btn-soft' }}"
            href="{{ route($route) }}"
        >
            {{ $label }}
        </a>
    @endforeach
</nav>
