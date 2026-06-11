@props(['path', 'lesson'])

@php
    $lessonUrl = route('lessons.show', [$path, $lesson]);
    $description = \Illuminate\Support\Str::limit(strip_tags((string) $lesson->summary), 160, '…')
        ?: 'درس تعليمي في بيت المصور.';

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
                'name' => 'المسارات',
                'item' => route('learning-paths.index'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $path->title,
                'item' => route('learning-paths.show', $path),
            ],
            [
                '@type' => 'ListItem',
                'position' => 4,
                'name' => $lesson->title,
                'item' => $lessonUrl,
            ],
        ],
    ];

    $learningResource = [
        '@context' => 'https://schema.org',
        '@type' => 'LearningResource',
        'name' => $lesson->title,
        'description' => $description,
        'url' => $lessonUrl,
        'inLanguage' => 'ar',
        'isPartOf' => [
            '@type' => 'Course',
            'name' => $path->seoTitle(),
            'url' => route('learning-paths.show', $path),
        ],
        'provider' => [
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => route('home'),
        ],
    ];

    if ($lesson->thumbnailUrl()) {
        $learningResource['image'] = $lesson->thumbnailUrl();
    }

    if ($lesson->duration_minutes) {
        $learningResource['timeRequired'] = 'PT' . $lesson->duration_minutes . 'M';
    }
@endphp

<script type="application/ld+json">{!! json_encode($breadcrumbData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($learningResource, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
