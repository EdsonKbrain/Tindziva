@props(['icon' => 'inbox', 'title' => 'Sem registos', 'colspan' => null])

@if ($colspan)
    <tr><td colspan="{{ $colspan }}">
@endif
<div class="px-6 py-14 text-center">
    <span class="mx-auto grid size-12 place-items-center rounded-full bg-gray-100 text-gray-400">@svg('lucide-'.$icon, 'size-6')</span>
    <p class="mt-4 text-sm font-semibold text-gray-800">{{ $title }}</p>
    @if ($slot->isNotEmpty())
        <div class="mt-1 text-sm text-gray-500">{{ $slot }}</div>
    @endif
</div>
@if ($colspan)
    </td></tr>
@endif
