@props(['seo' => new \App\Support\SeoData, 'bodyClass' => ''])

<!DOCTYPE html>
<html lang="pt-MZ" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#49200D">

    <x-site.seo :seo="$seo" />

    @php($favicon = app(\App\Services\SettingsRepository::class)->media('favicon_id'))
    <link rel="icon" href="{{ $favicon?->url() ?? asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" href="{{ $favicon?->url() ?? asset('favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ $favicon?->url() ?? asset('apple-touch-icon.png') }}">

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if ($analyticsId = setting('analytics_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $analyticsId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', @js($analyticsId), { anonymize_ip: true });
        </script>
    @endif
</head>
<body {{ $attributes->class(['flex min-h-screen flex-col', $bodyClass]) }}>
    <a href="#conteudo" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow-lg">
        {{ site_text('skip_to_content') }}
    </a>

    <x-site.header />

    <main id="conteudo" class="flex-1">
        {{ $slot }}
    </main>

    <x-site.footer />
    <x-site.whatsapp-button />
</body>
</html>
