@php
    $websiteData = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => config('app.name'),
        'url' => route('home'),
        'inLanguage' => 'ar',
        'description' => 'منصة تعليمية عربية لتطوير المصورين مع أحمد زغلول.',
        'publisher' => [
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => route('home'),
        ],
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => route('learning-paths.index') . '?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];
@endphp

<script type="application/ld+json">{!! json_encode($websiteData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
