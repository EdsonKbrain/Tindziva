@props(['title' => null, 'description' => null, 'padding' => true])

<section {{ $attributes->class('admin-card') }}>
    @if ($title)
        <header class="border-b border-gray-100 px-5 py-4">
            <h2 class="font-display text-base font-semibold text-gray-900">{{ $title }}</h2>
            @if ($description)
                <p class="mt-0.5 text-sm text-gray-500">{{ $description }}</p>
            @endif
        </header>
    @endif
    <div @class(['p-5' => $padding])>
        {{ $slot }}
    </div>
</section>
