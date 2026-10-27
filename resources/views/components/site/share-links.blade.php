@props(['url', 'title'])

@php
    $encodedUrl = rawurlencode($url);
    $encodedTitle = rawurlencode($title);
    $networks = [
        ['label' => 'Facebook', 'icon' => 'facebook', 'href' => "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}"],
        ['label' => 'LinkedIn', 'icon' => 'linkedin', 'href' => "https://www.linkedin.com/sharing/share-offsite/?url={$encodedUrl}"],
        ['label' => 'X', 'icon' => 'twitter', 'href' => "https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}"],
        ['label' => 'WhatsApp', 'icon' => 'message-circle', 'href' => "https://wa.me/?text={$encodedTitle}%20{$encodedUrl}"],
    ];
@endphp

<div {{ $attributes->class('flex flex-wrap items-center gap-2') }} x-data="copyLink(@js($url))">
    <span class="mr-1 text-sm font-medium text-brown-800">{{ site_text('share_label') }}</span>
    @foreach ($networks as $network)
        <a href="{{ $network['href'] }}" target="_blank" rel="noopener" class="grid size-10 place-items-center rounded-full border border-brown-100 text-brown-800 transition hover:border-ochre-600 hover:bg-ochre-600 hover:text-white" aria-label="Partilhar no {{ $network['label'] }}">
            @svg('lucide-'.$network['icon'], 'size-4')
        </a>
    @endforeach
    <button type="button" @click="copy" class="grid size-10 place-items-center rounded-full border border-brown-100 text-brown-800 transition hover:border-ochre-600 hover:bg-ochre-600 hover:text-white" aria-label="Copiar ligação">
        <x-lucide-link class="size-4" x-show="!copied" />
        <x-lucide-check class="size-4" x-show="copied" x-cloak />
    </button>
    <span x-show="copied" x-cloak x-transition class="text-sm text-ochre-700" role="status">{{ site_text('share_copied') }}</span>
</div>
