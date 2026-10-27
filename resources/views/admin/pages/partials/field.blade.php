{{-- Renders one editable section field described by App\Support\PageTemplates --}}
@switch($field['type'])
    @case('richtext')
        <x-admin.editor :name="$name" :label="$field['label']" :value="$value" />
        @break

    @case('textarea')
        <x-admin.textarea :name="$name" :label="$field['label']" :value="$value" rows="3" />
        @break

    @case('image')
        <x-admin.media-field :name="$name" :label="$field['label']" :media="$value ? App\Models\Media::find($value) : null" />
        @break

    @case('icon')
        <x-admin.select :name="$name" :label="$field['label']" :options="array_combine($icons, $icons)" :value="$value" placeholder="Sem ícone" />
        @break

    @case('repeater')
        @php
            $blank = array_map(fn () => '', $field['fields']);
            $rows = is_array($value) ? array_values($value) : [];
        @endphp
        <div x-data="repeater(@js($rows), @js($blank), @js($mediaPreviews))">
            <div class="flex items-center justify-between">
                <span class="admin-label">{{ $field['label'] }}</span>
                <button type="button" @click="add()" class="inline-flex items-center gap-1 text-sm font-semibold text-ochre-700"><x-lucide-plus class="size-4" /> Adicionar</button>
            </div>
            <div class="space-y-3">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="rounded-lg border border-gray-200 bg-gray-50/60 p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="text-xs font-semibold tracking-wide text-gray-500 uppercase" x-text="'#' + (index + 1)"></span>
                            <div class="flex gap-1">
                                <button type="button" @click="move(index, -1)" class="rounded p-1 text-gray-400 hover:bg-white hover:text-gray-700" aria-label="Subir"><x-lucide-chevron-left class="size-4 rotate-90" /></button>
                                <button type="button" @click="move(index, 1)" class="rounded p-1 text-gray-400 hover:bg-white hover:text-gray-700" aria-label="Descer"><x-lucide-chevron-right class="size-4 rotate-90" /></button>
                                <button type="button" @click="remove(index)" class="rounded p-1 text-gray-400 hover:bg-white hover:text-red-600" aria-label="Remover"><x-lucide-trash-2 class="size-4" /></button>
                            </div>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach ($field['fields'] as $subKey => $subField)
                                <div @class(['sm:col-span-2' => $subField['type'] === 'textarea'])>
                                    <label class="admin-label text-xs!">{{ $subField['label'] }}</label>
                                    @if ($subField['type'] === 'textarea')
                                        <textarea :name="`{{ $name }}[${index}][{{ $subKey }}]`" x-model="row.{{ $subKey }}" rows="2" class="admin-input"></textarea>
                                    @elseif ($subField['type'] === 'icon')
                                        <select :name="`{{ $name }}[${index}][{{ $subKey }}]`" x-model="row.{{ $subKey }}" class="admin-input">
                                            <option value="">Sem ícone</option>
                                            @foreach ($icons as $icon)
                                                <option value="{{ $icon }}">{{ $icon }}</option>
                                            @endforeach
                                        </select>
                                    @elseif ($subField['type'] === 'image')
                                        <input type="hidden" :name="`{{ $name }}[${index}][{{ $subKey }}]`" :value="row.{{ $subKey }}">
                                        <div class="flex items-center gap-3">
                                            <button type="button" @click="pickImage(row, '{{ $subKey }}')" class="relative grid size-16 place-items-center overflow-hidden rounded-lg border-2 border-dashed border-gray-300 bg-white text-gray-400 hover:border-ochre-600">
                                                <template x-if="row.{{ $subKey }} && previews[row.{{ $subKey }}]"><img :src="previews[row.{{ $subKey }}].thumbnail" alt="" class="absolute inset-0 size-full object-cover"></template>
                                                <template x-if="!row.{{ $subKey }}"><x-lucide-image class="size-5" /></template>
                                            </button>
                                            <button type="button" x-show="row.{{ $subKey }}" @click="row.{{ $subKey }} = ''" class="text-xs text-gray-500 hover:text-red-600">Remover</button>
                                        </div>
                                    @else
                                        <input type="text" :name="`{{ $name }}[${index}][{{ $subKey }}]`" x-model="row.{{ $subKey }}" class="admin-input">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </template>
                <p x-show="rows.length === 0" class="rounded-lg border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500">Sem itens.</p>
            </div>
        </div>
        @break

    @default
        <x-admin.input :name="$name" :label="$field['label']" :value="$value" :placeholder="$field['type'] === 'url' ? '/contactos ou https://…' : null" />
@endswitch
