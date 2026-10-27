@props(['seo'])

@php
    $siteName = setting('company_name', config('app.name'));
    $organization = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $siteName,
        'url' => route('home'),
        'logo' => asset('images/logo-tindziva.png'),
        'email' => setting('contact_email'),
        'telephone' => setting('contact_phone'),
        'address' => setting('contact_address') ? ['@type' => 'PostalAddress', 'streetAddress' => setting('contact_address'), 'addressCountry' => 'MZ'] : null,
        'sameAs' => array_values(array_filter([
            setting('social_facebook'), setting('social_instagram'), setting('social_linkedin'),
            setting('social_youtube'), setting('social_x'), setting('social_tiktok'),
        ])),
    ]);
@endphp

<title>{{ $seo->title() }}</title>
<meta name="description" content="{{ $seo->description() }}">
<link rel="canonical" href="{{ $seo->canonical() }}">
@if ($seo->noindex)
    <meta name="robots" content="noindex, follow">
@endif
@if ($seo->prev)
    <link rel="prev" href="{{ $seo->prev }}">
@endif
@if ($seo->next)
    <link rel="next" href="{{ $seo->next }}">
@endif
@if ($verification = setting('seo_google_verification'))
    <meta name="google-site-verification" content="{{ $verification }}">
@endif

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="pt_PT">
<meta property="og:type" content="{{ $seo->type }}">
<meta property="og:title" content="{{ $seo->title() }}">
<meta property="og:description" content="{{ $seo->description() }}">
<meta property="og:url" content="{{ $seo->canonical() }}">
@if ($image = $seo->image())
    <meta property="og:image" content="{{ $image }}">
    <meta name="twitter:image" content="{{ $image }}">
@endif
@if ($seo->publishedTime)
    <meta property="article:published_time" content="{{ $seo->publishedTime }}">
    <meta property="article:modified_time" content="{{ $seo->modifiedTime }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo->title() }}">
<meta name="twitter:description" content="{{ $seo->description() }}">

@foreach ([$organization, ...$seo->schemas] as $schema)
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endforeach
