@extends('layouts.app')

@section('title', $post->seoTitle())
@section('meta_description', $post->seoDescription('مقال تعليمي عن التصوير من بيت المصور.'))
@section('canonical', route('blog.show', $post))
@section('meta_type', 'article')

@if ($post->coverImageUrl())
    @section('meta_image', $post->coverImageUrl())
@endif

@push('head')
    @if ($post->seo_keywords)
        <meta name="keywords" content="{{ $post->seo_keywords }}">
    @endif

    <x-structured-data.article :post="$post" />
@endpush

@section('content')
    <nav class="container pt-4" aria-label="مسار التنقل">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">المدونة</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $post->title }}</li>
        </ol>
    </nav>

    <section class="section-block">
        <div class="container">
            <article class="col-lg-8 mx-auto">
                @if ($post->coverImageUrl())
                    <img class="card-cover rounded-3 mb-4" src="{{ $post->coverImageUrl() }}" alt="{{ $post->title }}">
                @endif

                <p class="text-muted-soft mb-2">
                    {{ $post->published_at?->translatedFormat('d F Y') }}
                    · {{ $post->authorName() }}
                </p>
                <h1 class="display-6 fw-bold mb-4">{{ $post->title }}</h1>

                <div class="article-body text-muted-soft fs-5">
                    {!! nl2br(e($post->body)) !!}
                </div>
            </article>
        </div>
    </section>
@endsection
