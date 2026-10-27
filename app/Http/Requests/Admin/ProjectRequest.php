<?php

namespace App\Http\Requests\Admin;

use App\Enums\Permission;
use App\Http\Requests\Concerns\HasSeoRules;
use App\Models\Project;
use App\Services\HtmlSanitizer;
use App\Support\VideoEmbed;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    use HasSeoRules;

    public function authorize(): bool
    {
        return $this->user()->can(Permission::ManageProjects->value);
    }

    protected function prepareForValidation(): void
    {
        $this->booleans(['is_published', 'is_featured', 'show_client', 'show_credits']);
        $this->merge([
            'video_urls' => array_values(array_filter(array_map('trim', (array) $this->input('video_urls', [])))),
            'credits' => array_values(array_filter(
                (array) $this->input('credits', []),
                fn ($credit): bool => is_array($credit) && filled($credit['name'] ?? null),
            )),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Project|null $project */
        $project = $this->route('project');

        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:180', 'alpha_dash', Rule::unique('projects', 'slug')->ignore($project?->id)],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'categories' => ['array'],
            'categories.*' => ['integer', 'exists:project_categories,id'],
            'services' => ['array'],
            'services.*' => ['integer', 'exists:services,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:200000'],
            'cover_image_id' => ['nullable', 'integer', 'exists:media,id'],
            'gallery' => ['array', 'max:60'],
            'gallery.*' => ['integer', 'exists:media,id'],
            'video_urls' => ['array', 'max:10'],
            'video_urls.*' => ['url', 'max:500', function (string $attribute, mixed $value, Closure $fail): void {
                if (! VideoEmbed::isSupported($value)) {
                    $fail('Apenas são suportadas ligações do YouTube ou Vimeo.');
                }
            }],
            'credits' => ['array', 'max:30'],
            'credits.*.role' => ['nullable', 'string', 'max:120'],
            'credits.*.name' => ['required', 'string', 'max:160'],
            'location' => ['nullable', 'string', 'max:160'],
            'completed_at' => ['nullable', 'date'],
            'show_client' => ['boolean'],
            'show_credits' => ['boolean'],
            'usage_permission' => ['nullable', 'string', 'max:500'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            ...$this->seoRules(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $data = $this->safe()->except(['gallery', 'categories', 'services']);
        $data['description'] = app(HtmlSanitizer::class)->sanitize($data['description'] ?? null);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['credits'] = array_map(fn (array $credit): array => [
            'role' => (string) ($credit['role'] ?? ''),
            'name' => (string) $credit['name'],
        ], $data['credits'] ?? []);

        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = $this->route('project')?->published_at ?? now();
        }

        return $data;
    }
}
