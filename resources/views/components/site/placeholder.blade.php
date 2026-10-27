@props(['label' => null])

{{-- Brand artwork shown until a photograph is uploaded in the media library --}}
<div {{ $attributes->class('relative overflow-hidden bg-gradient-to-br from-brown-800 via-brown-700 to-ochre-700') }} role="img" aria-label="{{ $label ?? setting('company_name', 'Tindziva') }}">
    <div aria-hidden="true" class="bubble absolute -top-1/4 -right-1/5 aspect-square w-3/4 bg-ochre-600/60"></div>
    <div aria-hidden="true" class="bubble absolute -bottom-1/3 -left-1/5 aspect-square w-2/3 bg-brown-900/70"></div>
    <div aria-hidden="true" class="absolute inset-0 flex items-center justify-center gap-2">
        <span class="size-2.5 rounded-full bg-white/80"></span>
        <span class="size-2.5 rounded-full bg-white/80"></span>
        <span class="size-2.5 rounded-full bg-white/80"></span>
    </div>
    @if ($label)
        <span class="absolute inset-x-0 bottom-0 p-5 font-display text-sm font-medium text-white/80">{{ $label }}</span>
    @endif
</div>
