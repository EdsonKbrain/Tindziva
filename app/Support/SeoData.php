<?php

namespace App\Support;

use App\Models\Media;
use App\Services\SettingsRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Str;

/**
 * Metadata rendered in the document head of public pages.
 */
class SeoData
{
    /**
     * @param  list<array<string, mixed>>  $schemas
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $canonical = null,
        public ?string $image = null,
        public string $type = 'website',
        public bool $noindex = false,
        public ?string $prev = null,
        public ?string $next = null,
        public ?string $publishedTime = null,
        public ?string $modifiedTime = null,
        public array $schemas = [],
    ) {}

    /**
     * Build metadata for a model that uses the HasSeo concern.
     */
    public static function for(object $model, string $fallbackTitle, ?string $fallbackDescription = null, ?Media $fallbackImage = null): self
    {
        $image = $model->ogImage ?? $fallbackImage;

        return new self(
            title: $model->meta_title ?: $fallbackTitle,
            description: $model->meta_description ?: Str::limit(trim(strip_tags((string) $fallbackDescription)), 160),
            image: $image?->url(),
        );
    }

    public function title(): string
    {
        $siteName = (string) setting('company_name', config('app.name'));
        $title = Text::plain($this->title ?: setting('seo_title', $siteName));

        return $title === $siteName || str_contains($title, $siteName) ? $title : "{$title} | {$siteName}";
    }

    public function description(): string
    {
        return (string) ($this->description ?: setting('seo_description', ''));
    }

    public function image(): ?string
    {
        return $this->image ?: app(SettingsRepository::class)->media('seo_og_image_id')?->url();
    }

    public function canonical(): string
    {
        return $this->canonical ?: url()->current();
    }

    /**
     * Adds canonical and rel prev/next links for paginated listings.
     *
     * @param  Paginator<int, mixed>  $paginator
     */
    public function paginate(Paginator $paginator): self
    {
        $page = $paginator->currentPage();
        $this->canonical = $page > 1 ? $paginator->url($page) : url()->current();
        $this->prev = $page > 1 ? $paginator->previousPageUrl() : null;
        $this->next = $paginator->hasMorePages() ? $paginator->nextPageUrl() : null;

        if ($page > 1 && $this->title) {
            $this->title .= " — Página {$page}";
        }

        return $this;
    }

    /**
     * @param  array<string, mixed>  $schema
     */
    public function withSchema(array $schema): self
    {
        $this->schemas[] = $schema;

        return $this;
    }

    /**
     * @param  list<array{name: string, url: string}>  $items
     */
    public function withBreadcrumbs(array $items): self
    {
        return $this->withSchema([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(fn (array $item, int $index): array => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ], $items, array_keys($items)),
        ]);
    }
}
