@props(['client'])

<div {{ $attributes->class('flex h-24 items-center justify-center rounded-2xl border border-brown-100 bg-white px-5') }} title="{{ $client->name }}">
    @if ($client->logo)
        <img src="{{ $client->logo->url() }}" alt="{{ $client->name }}" loading="lazy" decoding="async" class="max-h-16 w-auto max-w-full object-contain">
    @else
        <span class="text-center font-display text-sm leading-tight font-semibold text-brown-700">{{ $client->name }}</span>
    @endif
</div>
