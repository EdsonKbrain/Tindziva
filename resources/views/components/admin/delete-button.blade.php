@props(['action', 'confirm' => 'Tem a certeza de que pretende eliminar este registo?', 'label' => 'Eliminar', 'iconOnly' => false])

<form method="POST" action="{{ $action }}" onsubmit="return confirm(@js($confirm))" {{ $attributes->class('inline') }}>
    @csrf
    @method('DELETE')
    @if ($iconOnly)
        <button type="submit" class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600" aria-label="{{ $label }}" title="{{ $label }}"><x-lucide-trash-2 class="size-4" /></button>
    @else
        <x-admin.button variant="ghost" icon="trash-2" class="text-red-600! hover:bg-red-50!">{{ $label }}</x-admin.button>
    @endif
</form>
