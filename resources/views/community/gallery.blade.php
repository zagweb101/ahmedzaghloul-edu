@extends('layouts.app')

@section('title', 'معرض الصور')

@php
    $categoryLabels = [
        'question' => 'اسأل أحمد',
        'showcase' => 'شارك صورتك',
        'challenge' => 'تحدي الأسبوع',
        'feedback' => 'نقد وتقييم',
        'gear' => 'معدات وتجهيزات',
    ];
@endphp

@section('content')
    <x-page-header
        eyebrow="المجتمع"
        title="معرض صور الأعضاء"
        description="استعرض أعمال المصورين، التحديات، والتطبيقات العملية داخل المجتمع."
    />

    <section class="section-block pt-0">
        <div class="container">
            @include('community.partials.tabs')

            <div class="d-flex flex-wrap gap-2 mb-4">
                <a class="btn {{ $activeCategory ? 'btn-soft' : 'btn-brand' }}" href="{{ route('community.gallery') }}">الكل</a>
                @foreach ($categoryLabels as $value => $label)
                    <a
                        class="btn {{ $activeCategory === $value ? 'btn-brand' : 'btn-soft' }}"
                        href="{{ route('community.gallery', ['category' => $value]) }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if ($images->isEmpty())
                <div class="surface-card p-4 text-muted-soft">
                    لا توجد صور منشورة في هذا القسم بعد. شارك أول صورة من صفحة النقاشات.
                </div>
            @else
                <div class="gallery-grid">
                    @foreach ($images as $image)
                        @php
                            $post = $image->post;
                            $postImageUrls = $post->imageUrls();
                        @endphp
                        <button
                            class="gallery-item"
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#galleryModal"
                            data-image="{{ $image->url() }}"
                            data-images="{{ $postImageUrls->toJson() }}"
                            data-start="{{ ($index = $postImageUrls->values()->search($image->url())) === false ? 0 : $index }}"
                            data-title="{{ $post->title }}"
                            data-author="{{ $post->user?->name }}"
                            data-category="{{ $categoryLabels[$post->category] ?? $post->category }}"
                            data-likes="{{ $post->liked_by_users_count }}"
                        >
                            <img src="{{ $image->url() }}" alt="{{ $post->title }}" loading="lazy">
                            <span class="gallery-item-overlay">
                                <strong class="d-block">{{ $post->title }}</strong>
                                <small>{{ $post->user?->name }}</small>
                                @if ($postImageUrls->count() > 1)
                                    <small class="d-block mt-1">{{ $postImageUrls->count() }} صور</small>
                                @endif
                            </span>
                        </button>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $images->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>

    <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <div>
                        <span class="badge badge-soft mb-2" id="galleryModalCategory"></span>
                        <h2 class="modal-title h5 mb-0" id="galleryModalLabel"></h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body p-0 position-relative">
                    <img class="gallery-modal-image" id="galleryModalImage" src="" alt="">
                    <button class="gallery-modal-nav gallery-modal-prev d-none" type="button" id="galleryModalPrev" aria-label="الصورة السابقة">‹</button>
                    <button class="gallery-modal-nav gallery-modal-next d-none" type="button" id="galleryModalNext" aria-label="الصورة التالية">›</button>
                </div>
                <div class="modal-footer border-top justify-content-between">
                    <small class="text-muted-soft" id="galleryModalMeta"></small>
                    <a class="btn btn-soft" href="{{ route('community.index') }}">العودة للنقاشات</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('galleryModal');
            if (!modal) return;

            let currentImages = [];
            let currentIndex = 0;

            const renderImage = () => {
                const image = currentImages[currentIndex];
                if (!image) return;

                document.getElementById('galleryModalImage').src = image;
                document.getElementById('galleryModalImage').alt = document.getElementById('galleryModalLabel').textContent;
                document.getElementById('galleryModalMeta').textContent = `${document.getElementById('galleryModalMeta').dataset.base || ''} · ${currentIndex + 1} / ${currentImages.length}`;
            };

            modal.addEventListener('show.bs.modal', (event) => {
                const trigger = event.relatedTarget;
                if (!trigger) return;

                document.getElementById('galleryModalLabel').textContent = trigger.dataset.title || '';
                document.getElementById('galleryModalCategory').textContent = trigger.dataset.category || '';
                document.getElementById('galleryModalMeta').dataset.base = `${trigger.dataset.author || ''} · ${trigger.dataset.likes || 0} إعجاب`;

                currentImages = JSON.parse(trigger.dataset.images || '[]');
                if (currentImages.length === 0 && trigger.dataset.image) {
                    currentImages = [trigger.dataset.image];
                }

                currentIndex = Number(trigger.dataset.start);
                if (Number.isNaN(currentIndex) || currentIndex < 0) {
                    currentIndex = 0;
                }

                const hasMany = currentImages.length > 1;
                document.getElementById('galleryModalPrev').classList.toggle('d-none', !hasMany);
                document.getElementById('galleryModalNext').classList.toggle('d-none', !hasMany);

                renderImage();
            });

            document.getElementById('galleryModalPrev')?.addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
                renderImage();
            });

            document.getElementById('galleryModalNext')?.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % currentImages.length;
                renderImage();
            });
        })();
    </script>
@endsection
