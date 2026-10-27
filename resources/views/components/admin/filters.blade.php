@props(['placeholder' => 'Pesquisar…'])

<form method="GET" {{ $attributes->class('admin-card mb-4 flex flex-col gap-3 p-3 sm:flex-row sm:items-center') }}>
    <div class="relative flex-1">
        <x-lucide-search class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-gray-400" />
        <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}" class="admin-input pl-9" aria-label="Pesquisar">
    </div>
    {{ $slot }}
    <div class="flex gap-2">
        <x-admin.button variant="dark" icon="filter">Filtrar</x-admin.button>
        @if (collect(request()->except('page'))->filter()->isNotEmpty())
            <x-admin.button :href="url()->current()" variant="ghost">Limpar</x-admin.button>
        @endif
    </div>
</form>
