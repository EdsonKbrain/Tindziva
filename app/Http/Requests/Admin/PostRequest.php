<?php

namespace App\Http\Requests\Admin;

use App\Enums\Permission;
use App\Enums\PostStatus;
use App\Http\Requests\Concerns\HasSeoRules;
use App\Models\Post;
use App\Services\HtmlSanitizer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    use HasSeoRules;

    public function authorize(): bool
    {
        /** @var Post|null $post */
        $post = $this->route('post');

        return $post ? $this->user()->can('update', $post) : $this->user()->can('create', Post::class);
    }

    protected function prepareForValidation(): void
    {
        $this->booleans(['is_featured']);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Post|null $post */
        $post = $this->route('post');

        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:180', 'alpha_dash', Rule::unique('posts', 'slug')->ignore($post?->id)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string', 'max:500000'],
            'cover_image_id' => ['nullable', 'integer', 'exists:media,id'],
            'post_category_id' => ['nullable', 'integer', 'exists:post_categories,id'],
            'tags' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in($this->allowedStatuses($post))],
            'published_at' => [
                Rule::requiredIf($this->input('status') === PostStatus::Scheduled->value),
                'nullable',
                'date',
                Rule::when($this->input('status') === PostStatus::Scheduled->value, ['after:now']),
            ],
            'is_featured' => ['boolean'],
            'user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('is_active', true)->orWhere('id', $post?->user_id)),
                Rule::prohibitedIf(! $this->user()->hasPermission(Permission::ManageAllPosts) && ! $this->user()->isSuperAdmin()),
            ],
            ...$this->seoRules(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Não tem permissão para publicar ou agendar artigos. Guarde como rascunho para revisão.',
            'published_at.after' => 'A data de agendamento deve ser no futuro.',
            'user_id.prohibited' => 'Não tem permissão para alterar o autor do artigo.',
        ];
    }

    /**
     * Users without publishing rights can only save drafts or keep the current status.
     *
     * @return list<string>
     */
    private function allowedStatuses(?Post $post): array
    {
        if ($this->user()->can('publish', [Post::class, $post])) {
            return array_column(PostStatus::cases(), 'value');
        }

        return array_values(array_unique(array_filter([PostStatus::Draft->value, $post?->status?->value])));
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        /** @var Post|null $post */
        $post = $this->route('post');
        $data = $this->safe()->except(['tags']);
        $data['content'] = app(HtmlSanitizer::class)->sanitize($data['content'] ?? null);

        $status = PostStatus::from($data['status']);
        $publishedAt = filled($data['published_at'] ?? null) ? Carbon::parse($data['published_at']) : null;

        $data['published_at'] = match ($status) {
            PostStatus::Published => $publishedAt ?? $post?->published_at ?? now(),
            PostStatus::Scheduled => $publishedAt,
            PostStatus::Draft => $publishedAt,
        };

        if (! array_key_exists('user_id', $data) || $data['user_id'] === null) {
            unset($data['user_id']);
        }

        return $data;
    }

    /**
     * @return list<string>
     */
    public function tagNames(): array
    {
        return collect(explode(',', (string) $this->input('tags')))
            ->map(fn (string $tag): string => trim(strip_tags($tag)))
            ->filter()
            ->unique(fn (string $tag): string => mb_strtolower($tag))
            ->take(15)
            ->values()
            ->all();
    }
}
