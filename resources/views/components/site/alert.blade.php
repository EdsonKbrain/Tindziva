@props(['type' => 'success'])

<div role="{{ $type === 'error' ? 'alert' : 'status' }}" {{ $attributes->class([
    'flex items-start gap-3 rounded-2xl p-4 text-[15px]',
    'bg-green-50 text-green-900 ring-1 ring-green-200' => $type === 'success',
    'bg-red-50 text-red-900 ring-1 ring-red-200' => $type === 'error',
]) }}>
    @if ($type === 'success')
        <x-lucide-circle-check class="mt-0.5 size-5 shrink-0 text-green-700" />
    @else
        <x-lucide-circle-alert class="mt-0.5 size-5 shrink-0 text-red-700" />
    @endif
    <div>{{ $slot }}</div>
</div>
