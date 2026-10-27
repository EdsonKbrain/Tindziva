@php
    $value = fn (string $key, $default = null) => old($key, $values[$key] ?? $default);
    $tabGroups = ['identidade' => 'general', 'contactos' => 'contact', 'redes' => 'social', 'whatsapp' => 'whatsapp', 'rodape' => 'footer', 'seo' => 'seo', 'newsletter' => 'newsletter'];
    $tabsWithErrors = array_keys(array_filter($tabGroups, fn (string $group) => array_intersect(App\Http\Requests\Admin\SettingsRequest::GROUPS[$group], $errors->keys()) !== []));
@endphp

<x-admin.layout title="Configurações">
    <x-admin.page-header title="Configurações do website" description="Identidade, contactos, redes sociais, WhatsApp, rodapé, SEO e newsletter. Os textos de botões, menu e formulários editam-se em «Textos e menu».">
        @can(App\Enums\Permission::ManagePages->value)
            <x-admin.button :href="route('admin.texts.edit')" variant="secondary" icon="type">Textos e menu</x-admin.button>
        @endcan
    </x-admin.page-header>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="grid gap-6 lg:grid-cols-[15rem_1fr]"
          x-data="{ tab: @js($tabsWithErrors[0] ?? null) ?? (@js(array_keys($tabGroups)).includes(window.location.hash.slice(1)) ? window.location.hash.slice(1) : 'identidade') }">
        @csrf @method('PUT')

        <nav class="space-y-1 lg:sticky lg:top-24 lg:self-start" aria-label="Secções das configurações">
            <button type="button" @click="tab = 'identidade'; history.replaceState(null, '', '#identidade')" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium transition" :class="tab === 'identidade' ? 'bg-white text-gray-900 shadow-xs ring-1 ring-gray-200' : 'text-gray-600 hover:bg-white/60'">Identidade @if (in_array('identidade', $tabsWithErrors, true))<span class="ml-2 inline-block size-2 rounded-full bg-red-500" aria-label="Contém erros"></span>@endif</button>
            <button type="button" @click="tab = 'contactos'; history.replaceState(null, '', '#contactos')" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium transition" :class="tab === 'contactos' ? 'bg-white text-gray-900 shadow-xs ring-1 ring-gray-200' : 'text-gray-600 hover:bg-white/60'">Contactos @if (in_array('contactos', $tabsWithErrors, true))<span class="ml-2 inline-block size-2 rounded-full bg-red-500" aria-label="Contém erros"></span>@endif</button>
            <button type="button" @click="tab = 'redes'; history.replaceState(null, '', '#redes')" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium transition" :class="tab === 'redes' ? 'bg-white text-gray-900 shadow-xs ring-1 ring-gray-200' : 'text-gray-600 hover:bg-white/60'">Redes sociais @if (in_array('redes', $tabsWithErrors, true))<span class="ml-2 inline-block size-2 rounded-full bg-red-500" aria-label="Contém erros"></span>@endif</button>
            <button type="button" @click="tab = 'whatsapp'; history.replaceState(null, '', '#whatsapp')" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium transition" :class="tab === 'whatsapp' ? 'bg-white text-gray-900 shadow-xs ring-1 ring-gray-200' : 'text-gray-600 hover:bg-white/60'">WhatsApp @if (in_array('whatsapp', $tabsWithErrors, true))<span class="ml-2 inline-block size-2 rounded-full bg-red-500" aria-label="Contém erros"></span>@endif</button>
            <button type="button" @click="tab = 'rodape'; history.replaceState(null, '', '#rodape')" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium transition" :class="tab === 'rodape' ? 'bg-white text-gray-900 shadow-xs ring-1 ring-gray-200' : 'text-gray-600 hover:bg-white/60'">Rodapé @if (in_array('rodape', $tabsWithErrors, true))<span class="ml-2 inline-block size-2 rounded-full bg-red-500" aria-label="Contém erros"></span>@endif</button>
            <button type="button" @click="tab = 'seo'; history.replaceState(null, '', '#seo')" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium transition" :class="tab === 'seo' ? 'bg-white text-gray-900 shadow-xs ring-1 ring-gray-200' : 'text-gray-600 hover:bg-white/60'">SEO e Analytics @if (in_array('seo', $tabsWithErrors, true))<span class="ml-2 inline-block size-2 rounded-full bg-red-500" aria-label="Contém erros"></span>@endif</button>
            <button type="button" @click="tab = 'newsletter'; history.replaceState(null, '', '#newsletter')" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium transition" :class="tab === 'newsletter' ? 'bg-white text-gray-900 shadow-xs ring-1 ring-gray-200' : 'text-gray-600 hover:bg-white/60'">Newsletter @if (in_array('newsletter', $tabsWithErrors, true))<span class="ml-2 inline-block size-2 rounded-full bg-red-500" aria-label="Contém erros"></span>@endif</button>
        </nav>

        <div class="space-y-6">
            @if ($errors->any())
                <div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">Há campos com erros nos separadores assinalados a vermelho.</div>
            @endif
        <div x-show="tab === 'identidade'" x-cloak id="identidade">
            <x-admin.card title="Identidade">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input name="company_name" label="Nome da empresa" :value="$value('company_name', 'Tindziva Comunicação & Ideias')" required />
                    <x-admin.input name="company_tagline" label="Slogan" :value="$value('company_tagline')" />
                    <x-admin.media-field name="logo_id" label="Logótipo (fundo claro)" :media="$media->get($value('logo_id'))" help="Vazio: usa o logótipo oficial." />
                    <x-admin.media-field name="logo_light_id" label="Logótipo (fundo escuro)" :media="$media->get($value('logo_light_id'))" />
                    <x-admin.media-field name="favicon_id" label="Favicon" :media="$media->get($value('favicon_id'))" help="PNG quadrado, 512×512 px." />
                </div>
            </x-admin.card>
        </div>
        <div x-show="tab === 'contactos'" x-cloak id="contactos">
            <x-admin.card title="Contactos">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input name="contact_email" type="email" label="E-mail institucional" :value="$value('contact_email')" />
                    <x-admin.input name="notification_email" type="email" label="E-mail para notificações" :value="$value('notification_email')" help="Recebe os novos pedidos e mensagens. Vazio: usa o institucional." />
                    <x-admin.input name="contact_phone" label="Telefone" :value="$value('contact_phone')" placeholder="+258 84 000 0000" />
                    <x-admin.input name="contact_phone_secondary" label="Telefone alternativo" :value="$value('contact_phone_secondary')" />
                    <x-admin.textarea name="contact_address" label="Endereço" :value="$value('contact_address')" rows="2" class="sm:col-span-2" />
                    <x-admin.input name="map_embed_url" type="url" label="Mapa (URL de incorporação do Google Maps)" :value="$value('map_embed_url')" class="sm:col-span-2"
                        help="No Google Maps: Partilhar → Incorporar um mapa → copie apenas o endereço do atributo src." />
                </div>
            </x-admin.card>
        </div>
        <div x-show="tab === 'redes'" x-cloak id="redes">
            <x-admin.card title="Redes sociais">
                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach (['social_facebook' => 'Facebook', 'social_instagram' => 'Instagram', 'social_linkedin' => 'LinkedIn', 'social_youtube' => 'YouTube', 'social_x' => 'X (Twitter)', 'social_tiktok' => 'TikTok'] as $key => $label)
                        <x-admin.input :name="$key" type="url" :label="$label" :value="$value($key)" placeholder="https://" />
                    @endforeach
                </div>
            </x-admin.card>
        </div>
        <div x-show="tab === 'whatsapp'" x-cloak id="whatsapp">
            <x-admin.card title="WhatsApp">
                <div class="space-y-5">
                    <x-admin.toggle name="whatsapp_enabled" label="Mostrar botão flutuante" :checked="(bool) $value('whatsapp_enabled')" />
                    <x-admin.input name="whatsapp_number" label="Número (com indicativo)" :value="$value('whatsapp_number')" placeholder="258840000000" />
                    <x-admin.textarea name="whatsapp_message" label="Mensagem inicial" :value="$value('whatsapp_message')" rows="2" />
                </div>
            </x-admin.card>
        </div>
        <div x-show="tab === 'rodape'" x-cloak id="rodape">
            <x-admin.card title="Rodapé">
                <div class="space-y-5">
                    <x-admin.textarea name="footer_text" label="Texto de apresentação" :value="$value('footer_text')" rows="3" />
                    <x-admin.input name="footer_copyright" label="Direitos de autor" :value="$value('footer_copyright')" />
                </div>
            </x-admin.card>
        </div>
        <div x-show="tab === 'seo'" x-cloak id="seo">
            <x-admin.card title="SEO global">
                <div class="space-y-5">
                    <x-admin.input name="seo_title" label="Título predefinido" :value="$value('seo_title')" maxlength="70" />
                    <x-admin.textarea name="seo_description" label="Descrição predefinida" :value="$value('seo_description')" :maxlength="320" rows="3" />
                    <x-admin.media-field name="seo_og_image_id" label="Imagem de partilha predefinida" :media="$media->get($value('seo_og_image_id'))" help="1200×630 px." />
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-admin.input name="seo_google_verification" label="Google Search Console (código de verificação)" :value="$value('seo_google_verification')" />
                        <x-admin.input name="analytics_id" label="Google Analytics (ID de medição)" :value="$value('analytics_id')" placeholder="G-XXXXXXX" />
                    </div>
                </div>
            </x-admin.card>
        </div>
        <div x-show="tab === 'newsletter'" x-cloak id="newsletter">
            <x-admin.card title="Newsletter">
                <div class="space-y-5">
                    <x-admin.toggle name="newsletter_enabled" label="Formulário de subscrição activo" :checked="(bool) $value('newsletter_enabled', true)" />
                    <x-admin.input name="newsletter_title" label="Título" :value="$value('newsletter_title')" />
                    <x-admin.textarea name="newsletter_text" label="Texto" :value="$value('newsletter_text')" rows="2" />
                </div>
            </x-admin.card>
        </div>

            <div class="sticky bottom-4 flex justify-end">
                <x-admin.button class="shadow-lg" icon="check">Guardar configurações</x-admin.button>
            </div>
        </div>
    </form>
</x-admin.layout>
