@props(['post'])

@php
    $articleData = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $post->seoTitle(),
        'description' => $post->seoDescription('مقال تعليمي عن التصوير من بيت المصور.'),
        'url' => route('blog.show', $post),
        'datePublished' => $post->published_at?->toAtomString(),
        'dateModified' => $post->updated_at?->toAtomString(),
        'inLanguage' => 'ar',
        'author' => [
            '@type' => 'Person',
            'name' => $post->authorName(),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => route('home'),
        ],
        'image' => $post->coverImageUrl(),
        'mainEntityOfPage' => route('blog.show', $post),
    ]);

    $breadcrumbData = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'الرئيسية',
                'item' => route('home'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'المدونة',
                'item' => route('blog.index'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $post->seoTitle(),
                'item' => route('blog.show', $post),
            ],
        ],
    ];
@endphp

<script type="application/ld+json">{!! json_encode($articleData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
