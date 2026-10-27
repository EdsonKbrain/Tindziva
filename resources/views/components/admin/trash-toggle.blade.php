<div class="flex gap-1 rounded-lg bg-gray-200/60 p-1 text-sm">
    <a href="{{ url()->current() }}" @class(['rounded-md px-3 py-1.5 font-medium', 'bg-white shadow-xs' => ! request()->boolean('trashed'), 'text-gray-600' => request()->boolean('trashed')])>Activos</a>
    <a href="{{ url()->current() }}?trashed=1" @class(['inline-flex items-center gap-1 rounded-md px-3 py-1.5 font-medium', 'bg-white shadow-xs' => request()->boolean('trashed'), 'text-gray-600' => ! request()->boolean('trashed')])><x-lucide-trash-2 class="size-3.5" /> Reciclagem</a>
</div>
