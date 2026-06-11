@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'canonical' => null,
    'robots' => null,
])

@php
    $siteName = config('app.name');
    $pageTitle = $title ? $title . ' | ' . $siteName : $siteName;
    $metaDescription = $description ?? 'منصة تعليمية عربية لتطوير المصورين مع أحمد زغلول: مسارات، دروس، لايفات، ومجتمع تطبيقي.';
    $canonicalUrl = $canonical ?? url()->current();
    $metaImage = $image ?: config('seo.default_og_image');
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
@if ($robots)
    <meta name="robots" content="{{ $robots }}">
@endif
<link rel="canonical" href="{{ $canonicalUrl }}">
<meta property="og:locale" content="ar_SA">
<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:site_name" content="{{ $siteName }}">
@if ($metaImage)
    <meta property="og:image" content="{{ $metaImage }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
