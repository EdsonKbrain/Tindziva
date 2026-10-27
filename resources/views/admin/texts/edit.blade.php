<x-admin.layout title="Textos e menu">
    <x-admin.page-header title="Textos e menu" description="Todos os textos fixos do website: menu, botões, rótulos e mensagens. Um campo vazio usa o texto predefinido, apresentado a cinzento." />

    <form method="POST" action="{{ route('admin.texts.update') }}"
          x-data="{ tab: (window.location.hash || '#{{ array_key_first($groups) }}').slice(1), query: '' }"
          class="grid gap-6 lg:grid-cols-[15rem_1fr]">
        @csrf @method('PUT')
        <input type="hidden" name="tab" :value="tab">

        <nav class="space-y-1 lg:sticky lg:top-24 lg:self-start" aria-label="Grupos de textos">
            <div class="relative mb-3">
                <x-lucide-search class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-gray-400" />
                <input type="search" x-model="query" placeholder="Procurar texto…" class="admin-input pl-9" aria-label="Procurar texto">
            </div>
            @foreach ($groups as $groupKey => $group)
                <button type="button" @click="tab = @js($groupKey); query = ''; history.replaceState(null, '', '#' + tab)"
                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm font-medium transition"
                        :class="tab === @js($groupKey) && !query ? 'bg-white text-gray-900 shadow-xs ring-1 ring-gray-200' : 'text-gray-600 hover:bg-white/60'">
                    {{ $group['label'] }}
                    <span class="text-xs text-gray-400">{{ count($group['fields']) }}</span>
                </button>
            @endforeach
        </nav>

        <div class="space-y-6">
            @foreach ($groups as $groupKey => $group)
                <div x-show="query ? true : tab === @js($groupKey)" x-cloak>
                    <x-admin.card :title="$group['label']" :description="$group['description'] ?? null">
                        <div class="grid gap-5 md:grid-cols-2">
                            @foreach ($group['fields'] as $key => $field)
                                @php
                                    $type = $field['type'] ?? 'text';
                                    $name = "texts[{$key}]";
                                    $value = old("texts.{$key}", $values[$key]);
                                    $search = mb_strtolower($field['label'].' '.(is_string($field['default']) ? $field['default'] : '').' '.$value);
                                @endphp
                                <div x-show="!query || @js($search).includes(query.toLowerCase())" @class(['md:col-span-2' => in_array($type, ['textarea', 'lines'], true)])>
                                    @if ($type === 'toggle')
                                        <x-admin.toggle :name="$name" :label="$field['label']" :checked="$value === null ? (bool) $field['default'] : (bool) $value" :help="$field['help'] ?? null" />
                                    @elseif ($type === 'textarea' || $type === 'lines')
                                        <label for="text-{{ $key }}" class="admin-label">{{ $field['label'] }}</label>
                                        <textarea id="text-{{ $key }}" name="{{ $name }}" rows="{{ $type === 'lines' ? 5 : 3 }}" class="admin-input" placeholder="{{ $field['default'] }}">{{ $type === 'lines' && $value === null ? $field['default'] : $value }}</textarea>
                                        @if ($field['help'] ?? null)<p class="mt-1 text-xs text-gray-500">{{ $field['help'] }}</p>@endif
                                        @error("texts.{$key}")<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    @else
                                        <label for="text-{{ $key }}" class="admin-label">{{ $field['label'] }}</label>
                                        <input id="text-{{ $key }}" type="text" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $field['default'] }}" class="admin-input">
                                        @if ($field['help'] ?? null)<p class="mt-1 text-xs text-gray-500">{{ $field['help'] }}</p>@endif
                                        @error("texts.{$key}")<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </x-admin.card>
                </div>
            @endforeach

            <div class="sticky bottom-4 flex justify-end">
                <x-admin.button class="shadow-lg" icon="check">Guardar textos</x-admin.button>
            </div>
        </div>
    </form>
</x-admin.layout>
