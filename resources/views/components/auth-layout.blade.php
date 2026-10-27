@props(['title'])

<!DOCTYPE html>
<html lang="pt-MZ">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} | {{ setting('company_name', config('app.name')) }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream">
    <div class="grid min-h-screen lg:grid-cols-2">
        <div class="relative hidden overflow-hidden bg-brown-800 lg:block">
            <div aria-hidden="true" class="bubble absolute -top-24 -left-24 size-[32rem] bg-brown-700"></div>
            <div aria-hidden="true" class="bubble absolute -right-32 -bottom-32 size-[30rem] bg-ochre-600"></div>
            <div class="relative flex h-full flex-col justify-between p-12">
                <img src="{{ asset('images/logo-horizontal-light.png') }}" alt="{{ setting('company_name', config('app.name')) }}" class="h-12 w-auto self-start">
                <div>
                    <p class="font-display text-4xl leading-tight font-semibold text-white">Gestão de conteúdos<br><span class="accent-serif text-ochre-200!">Tindziva</span></p>
                    <p class="mt-4 max-w-sm text-white/70">Páginas, portfólio, blog e pedidos comerciais — tudo num só lugar.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-center px-4 py-12 sm:px-8">
            <div class="w-full max-w-md">
                <img src="{{ asset('images/logo-horizontal.png') }}" alt="{{ setting('company_name', config('app.name')) }}" class="mb-10 h-11 w-auto lg:hidden">
                <h1 class="text-3xl font-semibold">{{ $title }}</h1>
                {{ $slot }}
                <p class="mt-10 text-sm text-ink/50"><a href="{{ route('home') }}" class="hover:text-ochre-700">← Voltar ao website</a></p>
            </div>
        </div>
    </div>
</body>
</html>
