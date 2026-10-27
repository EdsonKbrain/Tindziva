@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginação" class="flex justify-between gap-3">
        @if ($paginator->onFirstPage())
            <span class="rounded-full border border-brown-100 px-5 py-2.5 text-sm opacity-40">Anterior</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-full border border-brown-100 px-5 py-2.5 text-sm hover:border-brown-800">Anterior</a>
        @endif
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-full border border-brown-100 px-5 py-2.5 text-sm hover:border-brown-800">Seguinte</a>
        @else
            <span class="rounded-full border border-brown-100 px-5 py-2.5 text-sm opacity-40">Seguinte</span>
        @endif
    </nav>
@endif
