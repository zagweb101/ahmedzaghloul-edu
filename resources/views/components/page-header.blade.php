@props([
    'eyebrow' => null,
    'title',
    'description' => null,
])

<section class="section-block">
    <div class="container">
        <div class="col-lg-8">
            @if ($eyebrow)
                <p class="section-eyebrow">{{ $eyebrow }}</p>
            @endif
            <h1 class="display-5 fw-bold mb-3">{{ $title }}</h1>
            @if ($description)
                <p class="lead text-muted-soft mb-0">{{ $description }}</p>
            @endif
        </div>
    </div>
</section>
