@props(['path'])

@php
    $durationMinutes = $path->totalDurationMinutes();
    $courseData = [
        '@context' => 'https://schema.org',
        '@type' => 'Course',
        'name' => $path->seoTitle(),
        'description' => $path->seoDescription(),
        'url' => route('learning-paths.show', $path),
        'inLanguage' => 'ar',
        'provider' => [
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => route('home'),
        ],
        'hasCourseInstance' => array_filter([
            '@type' => 'CourseInstance',
            'courseMode' => 'online',
            'courseWorkload' => $durationMinutes > 0 ? 'PT' . $durationMinutes . 'M' : null,
        ]),
        'numberOfLessons' => $path->lessons->count(),
    ];

    if ($path->coverImageUrl()) {
        $courseData['image'] = $path->coverImageUrl();
    }

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
                'name' => $path->seoTitle(),
                'item' => route('learning-paths.show', $path),
            ],
        ],
    ];
@endphp

<script type="application/ld+json">{!! json_encode($courseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
