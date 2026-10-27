<?php

namespace App\Http\Requests\Admin;

use App\Enums\Permission;
use App\Http\Requests\Concerns\HasSeoRules;
use App\Models\Service;
use App\Services\HtmlSanitizer;
use App\Support\PageTemplates;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    use HasSeoRules;

    public function authorize(): bool
    {
        return $this->user()->can(Permission::ManageServices->value);
    }

    protected function prepareForValidation(): void
    {
        $this->booleans(['is_published', 'is_featured']);
        $this->merge([
            'benefits' => array_values(array_filter(array_map('trim', (array) $this->input('benefits', [])))),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Service|null $service */
        $service = $this->route('service');

        return [
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', 'alpha_dash', Rule::unique('services', 'slug')->ignore($service?->id)],
            'service_category_id' => ['nullable', 'integer', 'exists:service_categories,id'],
            'icon' => ['nullable', 'string', Rule::in(PageTemplates::icons())],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:200000'],
            'benefits' => ['array', 'max:20'],
            'benefits.*' => ['string', 'max:300'],
            'image_id' => ['nullable', 'integer', 'exists:media,id'],
            'gallery' => ['array', 'max:40'],
            'gallery.*' => ['integer', 'exists:media,id'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['boolean'],
            'is_featured' => ['boolean'],
            ...$this->seoRules(),
        ];
    }

    /**
     * Validated attributes ready to persist.
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $data = $this->safe()->except(['gallery']);
        $data['description'] = app(HtmlSanitizer::class)->sanitize($data['description'] ?? null);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
