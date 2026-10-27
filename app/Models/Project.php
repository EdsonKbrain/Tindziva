<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicCache;
use App\Models\Concerns\HasGallery;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\LogsActivity;
use App\Support\VideoEmbed;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['client_id', 'title', 'slug', 'excerpt', 'description', 'cover_image_id', 'video_urls', 'credits', 'location', 'completed_at', 'show_client', 'show_credits', 'usage_permission', 'is_featured', 'is_published', 'published_at', 'sort_order', 'meta_title', 'meta_description', 'og_image_id'])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use FlushesPublicCache, HasFactory, HasGallery, HasSeo, HasSlug, LogsActivity, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'video_urls' => 'array',
            'credits' => 'array',
            'completed_at' => 'date',
            'published_at' => 'datetime',
            'show_client' => 'boolean',
            'show_credits' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public static function activityLabel(): string
    {
        return 'projecto';
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_image_id');
    }

    /**
     * @return BelongsToMany<ProjectCategory, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProjectCategory::class);
    }

    /**
     * @return BelongsToMany<Service, $this>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    /**
     * @param  Builder<Project>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true)
            ->where(fn (Builder $query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    /**
     * @param  Builder<Project>  $query
     */
    public function scopeLatestFirst(Builder $query): void
    {
        $query->orderBy('sort_order')->orderByDesc('completed_at')->orderByDesc('id');
    }

    /**
     * Embeddable player URLs for the project's external videos.
     *
     * @return list<string>
     */
    public function videoEmbeds(): array
    {
        return array_values(array_filter(array_map(
            fn (?string $url): ?string => VideoEmbed::from($url),
            $this->video_urls ?? [],
        )));
    }

    /**
     * Credits are shown only when the client authorised them.
     *
     * @return list<array{role: string, name: string}>
     */
    public function publicCredits(): array
    {
        return $this->show_credits ? array_values(array_filter($this->credits ?? [], fn ($credit) => filled($credit['name'] ?? null))) : [];
    }

    public function publicClient(): ?Client
    {
        return $this->show_client ? $this->client : null;
    }

    public function url(): string
    {
        return route('portfolio.show', $this->slug);
    }
}
