@props(['event'])

@php
    $eventData = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Event',
        'name' => $event->seoTitle(),
        'description' => $event->seoDescription(),
        'url' => route('live-events.show', $event),
        'startDate' => $event->starts_at?->toAtomString(),
        'endDate' => $event->ends_at?->toAtomString(),
        'eventAttendanceMode' => str_contains(mb_strtolower((string) $event->location), 'اونلاين') || str_contains(mb_strtolower((string) $event->location), 'online')
            ? 'https://schema.org/OnlineEventAttendanceMode'
            : 'https://schema.org/OfflineEventAttendanceMode',
        'eventStatus' => 'https://schema.org/EventScheduled',
        'location' => [
            '@type' => 'Place',
            'name' => $event->location ?? 'اونلاين',
        ],
        'organizer' => [
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => route('home'),
        ],
        'image' => $event->coverImageUrl(),
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
                'name' => 'اللايفات',
                'item' => route('live-events.index'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $event->seoTitle(),
                'item' => route('live-events.show', $event),
            ],
        ],
    ];
@endphp

<script type="application/ld+json">{!! json_encode($eventData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
