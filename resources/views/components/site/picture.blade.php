@props(['media' => null, 'sizes' => '100vw', 'eager' => false, 'alt' => null, 'label' => null, 'fit' => 'cover'])

@if ($media?->isImage())
    <img
        src="{{ $media->url() }}"
        @if ($media->thumbnail_path) srcset="{{ $media->thumbnailUrl() }} {{ \App\Services\MediaService::THUMBNAIL_WIDTH }}w, {{ $media->url() }} {{ $media->width ?: 1600 }}w" sizes="{{ $sizes }}" @endif
        alt="{{ $alt ?? $media->altText() }}"
        @if ($media->width) width="{{ $media->width }}" height="{{ $media->height }}" @endif
        loading="{{ $eager ? 'eager' : 'lazy' }}"
        decoding="async"
        @if ($eager) fetchpriority="high" @endif
        {{ $attributes->class($fit === 'contain' ? 'object-contain' : 'object-cover') }}
    >
@else
    <x-site.placeholder :label="$label" {{ $attributes }} />
@endif
