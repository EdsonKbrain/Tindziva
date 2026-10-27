@if ($paginator->hasPages())
    @php($isAdmin = request()->routeIs('admin.*'))
    <nav role="navigation" aria-label="Paginação" class="flex items-center justify-between gap-4">
        <p @class(['hidden text-sm sm:block', 'text-gray-600' => $isAdmin, 'text-ink/60' => ! $isAdmin])>
            A mostrar <span class="font-semibold">{{ $paginator->firstItem() }}</span>–<span class="font-semibold">{{ $paginator->lastItem() }}</span> de <span class="font-semibold">{{ $paginator->total() }}</span>
        </p>

        <ul class="flex flex-1 items-center justify-center gap-1.5 sm:flex-none">
            <li>
                @if ($paginator->onFirstPage())
                    <span @class(['grid place-items-center opacity-40', 'size-9 rounded-lg border border-gray-200' => $isAdmin, 'size-11 rounded-full border border-brown-100' => ! $isAdmin]) aria-disabled="true" aria-label="Anterior"><x-lucide-chevron-left class="size-4" /></span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" @class(['grid place-items-center transition', 'size-9 rounded-lg border border-gray-200 hover:bg-gray-50' => $isAdmin, 'size-11 rounded-full border border-brown-100 text-brown-800 hover:border-brown-800' => ! $isAdmin]) aria-label="Anterior"><x-lucide-chevron-left class="size-4" /></a>
                @endif
            </li>

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="px-2 text-sm opacity-50">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="{{ abs($page - $paginator->currentPage()) > 1 && ! in_array($page, [1, $paginator->lastPage()]) ? 'hidden sm:block' : '' }}">
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" @class(['grid place-items-center text-sm font-semibold', 'size-9 rounded-lg bg-brown-800 text-white' => $isAdmin, 'size-11 rounded-full bg-brown-800 text-white' => ! $isAdmin])>{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" @class(['grid place-items-center text-sm transition', 'size-9 rounded-lg border border-gray-200 hover:bg-gray-50' => $isAdmin, 'size-11 rounded-full border border-brown-100 text-brown-800 hover:border-brown-800' => ! $isAdmin]) aria-label="Página {{ $page }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" @class(['grid place-items-center transition', 'size-9 rounded-lg border border-gray-200 hover:bg-gray-50' => $isAdmin, 'size-11 rounded-full border border-brown-100 text-brown-800 hover:border-brown-800' => ! $isAdmin]) aria-label="Seguinte"><x-lucide-chevron-right class="size-4" /></a>
                @else
                    <span @class(['grid place-items-center opacity-40', 'size-9 rounded-lg border border-gray-200' => $isAdmin, 'size-11 rounded-full border border-brown-100' => ! $isAdmin]) aria-disabled="true" aria-label="Seguinte"><x-lucide-chevron-right class="size-4" /></span>
                @endif
            </li>
        </ul>
    </nav>
@endif
