@props(['variant' => 'dark'])

@php
    $custom = app(\App\Services\SettingsRepository::class)->media($variant === 'light' ? 'logo_light_id' : 'logo_id');
    $src = $custom?->url() ?? asset($variant === 'light' ? 'images/logo-horizontal-light.png' : 'images/logo-horizontal.png');
@endphp

<a href="{{ route('home') }}" {{ $attributes->class('inline-flex shrink-0 items-center') }} aria-label="{{ setting('company_name', config('app.name')) }} — página inicial">
    <img src="{{ $src }}" alt="{{ setting('company_name', config('app.name')) }}" width="431" height="120" class="h-10 w-auto sm:h-12" fetchpriority="high">
</a>
