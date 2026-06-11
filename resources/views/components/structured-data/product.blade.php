@props(['plan'])

@php
    $productData = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $plan->seoTitle(),
        'description' => $plan->seoDescription(),
        'url' => route('subscription-plans.show', $plan),
        'image' => $plan->coverImageUrl(),
        'brand' => [
            '@type' => 'Brand',
            'name' => config('app.name'),
        ],
        'offers' => [
            '@type' => 'Offer',
            'priceCurrency' => $plan->currency,
            'price' => $plan->price_cents > 0 ? number_format($plan->price_cents / 100, 2, '.', '') : '0',
            'availability' => $plan->is_active
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'url' => route('subscription-plans.show', $plan),
        ],
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
                'name' => 'الاشتراكات',
                'item' => route('subscription-plans.index'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $plan->seoTitle(),
                'item' => route('subscription-plans.show', $plan),
            ],
        ],
    ];
@endphp

<script type="application/ld+json">{!! json_encode($productData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
