@extends('layouts.app')

@section('title', 'المدونة')
@section('meta_description', 'مقالات ونصائح تعليمية عن التصوير الفوتوغرافي والفيديو من بيت المصور مع أحمد زغلول.')
@section('canonical', route('blog.index'))

@section('content')
    <x-page-header
        eyebrow="المدونة"
        title="مقالات ونصائح التصوير"
        description="محتوى تعليمي مكتوب يساعدك على فهم أساسيات التصوير وتطبيقها عمليًا."
    />

    <section class="section-block pt-0">
        <div class="container">
            <div class="row g-3">
                @forelse ($posts as $post)
                    <div class="col-lg-4">
                        <article class="surface-card overflow-hidden h-100 d-flex flex-column">
                            @if ($post->coverImageUrl())
                                <img class="card-cover" src="{{ $post->coverImageUrl() }}" alt="{{ $post->title }}">
                            @endif
                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <small class="text-muted-soft mb-2">{{ $post->published_at?->translatedFormat('d F Y') }}</small>
                                <h2 class="h4">{{ $post->title }}</h2>
                                <p class="text-muted-soft flex-grow-1">{{ $post->excerpt ?: $post->seoDescription() }}</p>
                                <a class="btn btn-soft mt-3" href="{{ route('blog.show', $post) }}">اقرأ المقال</a>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="surface-card p-4 text-muted-soft">لا توجد مقالات منشورة حاليًا.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
