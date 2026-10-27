{{-- Standalone layout: must render even when the database is unavailable --}}
<!DOCTYPE html>
<html lang="pt-MZ">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title') | Tindziva Comunicação &amp; Ideias</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="flex min-h-screen flex-col bg-cream font-sans text-ink antialiased">
    <header class="container-site flex h-20 items-center">
        <a href="{{ url('/') }}"><img src="{{ asset('images/logo-horizontal.png') }}" alt="Tindziva Comunicação & Ideias" class="h-10 w-auto sm:h-12"></a>
    </header>
    <main class="container-site relative flex flex-1 items-center overflow-hidden py-16">
        <div aria-hidden="true" class="bubble pointer-events-none absolute -right-24 top-10 size-[26rem] bg-ochre-100"></div>
        <div class="relative max-w-xl">
            <p class="font-serif text-8xl text-ochre-600 italic sm:text-9xl">@yield('code')</p>
            <h1 class="mt-4 font-display text-3xl font-semibold text-brown-800 sm:text-4xl">@yield('heading')</h1>
            <p class="mt-4 text-lg text-ink/70">@yield('message')</p>
            <div class="mt-10 flex flex-wrap gap-3">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-full bg-ochre-600 px-7 py-3.5 font-display text-[15px] font-semibold text-white hover:bg-ochre-700">Voltar ao início</a>
                @yield('actions')
            </div>
        </div>
    </main>
</body>
</html>
