<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\HasSeoRules;
use App\Models\Page;
use App\Services\HtmlSanitizer;
use App\Support\SectionSanitizer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    use HasSeoRules;

    /**
     * Slugs used by fixed public routes.
     */
    public const RESERVED_SLUGS = [
        'admin', 'login', 'logout', 'sobre-nos', 'servicos', 'portfolio', 'blog', 'contactos',
        'sitemap', 'sitemap.xml', 'robots.txt', 'storage', 'build', 'up', 'newsletter', 'propostas',
        'forgot-password', 'reset-password', 'user',
    ];

    public function authorize(): bool
    {
        /** @var Page|null $page */
        $page = $this->route('page');

        return $page ? $this->user()->can('update', $page) : $this->user()->can('create', Page::class);
    }

    protected function prepareForValidation(): void
    {
        $this->booleans(['is_published', 'show_in_menu']);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Page|null $page */
        $page = $this->route('page');
        $isSystem = $page?->is_system ?? false;

        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => $isSystem ? ['prohibited'] : ['nullable', 'string', 'max:180', 'alpha_dash', Rule::notIn(self::RESERVED_SLUGS), Rule::unique('pages', 'slug')->ignore($page?->id)],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string', 'max:500000'],
            'hero_image_id' => ['nullable', 'integer', 'exists:media,id'],
            'sections' => ['nullable', 'array'],
            'is_published' => ['boolean'],
            'show_in_menu' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            ...$this->seoRules(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.not_in' => 'Este endereço está reservado pelo sistema.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        /** @var Page|null $page */
        $page = $this->route('page');
        $template = $page?->template ?? 'default';

        $data = $this->safe()->except(['sections']);
        $data['content'] = app(HtmlSanitizer::class)->sanitize($data['content'] ?? null);
        $data['sections'] = app(SectionSanitizer::class)->clean($template, (array) $this->input('sections', []));
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($page?->is_system) {
            $data['is_published'] = true;
        }

        return $data;
    }
}
