@props(['model'])

<x-admin.card title="SEO" description="Como esta página aparece no Google e nas redes sociais.">
    <div class="space-y-4">
        <x-admin.input name="meta_title" label="Meta título" :value="$model->meta_title" maxlength="70" help="Recomendado: até 60 caracteres. Deixe vazio para usar o título." />
        <x-admin.textarea name="meta_description" label="Meta descrição" :value="$model->meta_description" :maxlength="320" rows="3" help="Recomendado: 120 a 160 caracteres." />
        <x-admin.media-field name="og_image_id" label="Imagem de partilha (Open Graph)" :media="$model->ogImage" help="1200×630 px recomendado." />
    </div>
</x-admin.card>
