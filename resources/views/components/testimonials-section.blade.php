@php
    $testimonials = config('testimonials.items', []);
@endphp

@if (count($testimonials) > 0)
    <section id="testimonials" class="section-block section-surface">
        <div class="container">
            <div class="row align-items-end g-4 mb-4">
                <div class="col-lg-8">
                    <p class="section-eyebrow">شهادات الأعضاء</p>
                    <h2 class="display-6 fw-bold mb-2">مصورون جرّبوا بيت المصور وشاركونا تجربتهم</h2>
                    <p class="text-muted-soft fs-5 mb-0">قصص حقيقية من أعضاء يتعلمون ويطبّقون ويتطورون كل أسبوع.</p>
                </div>
                <div class="col-lg-4">
                    <a href="{{ route('register') }}" class="btn btn-brand w-100">كن التالي</a>
                </div>
            </div>

            <div class="row g-3">
                @foreach ($testimonials as $testimonial)
                    <div class="col-lg-4">
                        <article class="testimonial-card surface-card p-4 h-100">
                            <div class="testimonial-stars mb-3" aria-label="تقييم 5 من 5">
                                @for ($i = 0; $i < 5; $i++)
                                    <span aria-hidden="true">★</span>
                                @endfor
                            </div>

                            <blockquote class="testimonial-quote mb-4">
                                «{{ $testimonial['quote'] }}»
                            </blockquote>

                            <div class="testimonial-highlight mb-4">
                                {{ $testimonial['highlight'] }}
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <span class="testimonial-avatar" aria-hidden="true">
                                    {{ mb_substr($testimonial['name'], 0, 1) }}
                                </span>
                                <div>
                                    <strong class="d-block">{{ $testimonial['name'] }}</strong>
                                    <small class="text-muted-soft">
                                        {{ $testimonial['role'] }} · {{ $testimonial['city'] }}
                                    </small>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
