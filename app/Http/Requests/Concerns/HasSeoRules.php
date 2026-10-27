<?php

namespace App\Http\Requests\Concerns;

trait HasSeoRules
{
    /**
     * @return array<string, list<string>>
     */
    protected function seoRules(): array
    {
        return [
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'og_image_id' => ['nullable', 'integer', 'exists:media,id'],
        ];
    }

    /**
     * Normalise checkbox inputs into booleans before validation.
     *
     * @param  list<string>  $fields
     */
    protected function booleans(array $fields): void
    {
        $this->merge(collect($fields)->mapWithKeys(fn (string $field) => [$field => $this->boolean($field)])->all());
    }
}
