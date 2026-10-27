@props(['title', 'description' => null, 'back' => null])

<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        @if ($back)
            <a href="{{ $back }}" class="mb-2 inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-800"><x-lucide-chevron-left class="size-4" /> Voltar</a>
        @endif
        <h1 class="font-display text-2xl font-semibold text-gray-900">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</div>
