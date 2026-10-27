<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicCache;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\LogsActivity;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

#[Fillable(['title', 'slug', 'template', 'subtitle', 'content', 'sections', 'hero_image_id', 'is_published', 'show_in_menu', 'sort_order', 'meta_title', 'meta_description', 'og_image_id'])]
class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use FlushesPublicCache, HasFactory, HasSeo, HasSlug, LogsActivity, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'is_published' => 'boolean',
            'is_system' => 'boolean',
            'show_in_menu' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public static function activityLabel(): string
    {
        return 'página';
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function heroImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'hero_image_id');
    }

    /**
     * Read an editable section value using dot notation.
     */
    public function section(string $key, mixed $default = null): mixed
    {
        $value = Arr::get($this->sections ?? [], $key);

        return blank($value) ? $default : $value;
    }

    /**
     * Whether a section is switched on in the CMS (sections are visible unless hidden).
     */
    public function shows(string $section): bool
    {
        return Arr::get($this->sections ?? [], "{$section}._visible", true) !== false;
    }

    /**
     * Read a repeatable section, dropping rows without content.
     *
     * @return list<array<string, mixed>>
     */
    public function items(string $key): array
    {
        return array_values(array_filter(
            (array) Arr::get($this->sections ?? [], $key, []),
            fn ($item): bool => is_array($item) && array_filter($item, 'filled') !== [],
        ));
    }

    /**
     * @param  Builder<Page>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public static function forTemplate(string $template): ?self
    {
        return static::query()->where('template', $template)->where('is_system', true)->first();
    }

    public function url(): string
    {
        return match ($this->template) {
            'home' => route('home'),
            'about' => route('about'),
            'services' => route('services.index'),
            'portfolio' => route('portfolio.index'),
            'blog' => route('blog.index'),
            'contact' => route('contact'),
            default => route('pages.show', $this->slug),
        };
    }
}
