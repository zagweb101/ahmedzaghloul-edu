@php
    $imageUrls = $post->imageUrls();
@endphp

@if ($imageUrls->isNotEmpty())
    @if ($imageUrls->count() === 1)
        <img class="community-image" src="{{ $imageUrls->first() }}" alt="صورة مرفقة مع {{ $post->title }}">
    @else
        <div id="postCarousel{{ $post->id }}" class="carousel slide community-carousel" data-bs-ride="false">
            <div class="carousel-inner">
                @foreach ($imageUrls as $index => $imageUrl)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <img class="community-image" src="{{ $imageUrl }}" alt="صورة {{ $index + 1 }} من {{ $post->title }}">
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#postCarousel{{ $post->id }}" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">السابق</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#postCarousel{{ $post->id }}" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">التالي</span>
            </button>
            <div class="carousel-indicators">
                @foreach ($imageUrls as $index => $imageUrl)
                    <button
                        type="button"
                        data-bs-target="#postCarousel{{ $post->id }}"
                        data-bs-slide-to="{{ $index }}"
                        @class(['active' => $index === 0])
                        aria-label="صورة {{ $index + 1 }}"
                    ></button>
                @endforeach
            </div>
        </div>
    @endif
@endif
