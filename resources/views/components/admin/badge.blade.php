@props(['color' => 'gray'])

<span {{ $attributes->class([
    'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap',
    'bg-gray-100 text-gray-700' => $color === 'gray',
    'bg-green-100 text-green-800' => $color === 'green',
    'bg-amber-100 text-amber-800' => $color === 'amber',
    'bg-orange-100 text-ochre-700' => $color === 'orange',
    'bg-blue-100 text-blue-800' => $color === 'blue',
    'bg-purple-100 text-purple-800' => $color === 'purple',
    'bg-red-100 text-red-800' => $color === 'red',
]) }}>{{ $slot }}</span>
