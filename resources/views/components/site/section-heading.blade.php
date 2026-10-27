@props(['eyebrow' => null, 'title', 'text' => null, 'align' => 'left', 'tone' => 'dark'])

<div {{ $attributes->class(['reveal max-w-2xl', 'mx-auto text-center' => $align === 'center']) }}>
    @if ($eyebrow)
        <p @class(['eyebrow', 'justify-center' => $align === 'center', 'text-ochre-300!' => $tone === 'light'])>{{ $eyebrow }}</p>
    @endif
    <h2 @class(['mt-4 text-3xl font-semibold sm:text-4xl lg:text-[2.75rem] lg:leading-[1.1]', 'text-white!' => $tone === 'light'])>{!! $title !!}</h2>
    @if ($text)
        <p @class(['mt-5 text-lg leading-relaxed', 'text-ink/70' => $tone === 'dark', 'text-white/70' => $tone === 'light'])>{{ $text }}</p>
    @endif
</div>
