<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Models\Concerns\FlushesPublicCache;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\LogsActivity;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'post_category_id', 'title', 'slug', 'excerpt', 'content', 'cover_image_id', 'status', 'published_at', 'is_featured', 'meta_title', 'meta_description', 'og_image_id'])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use FlushesPublicCache, HasFactory, HasSeo, HasSlug, LogsActivity, SoftDeletes;

    public const WORDS_PER_MINUTE = 200;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'reading_time' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Post $post): void {
            $words = count(preg_split('/\s+/u', trim(strip_tags((string) $post->content)), -1, PREG_SPLIT_NO_EMPTY));
            $post->reading_time = max(1, (int) ceil($words / self::WORDS_PER_MINUTE));
        });
    }

    public static function activityLabel(): string
    {
        return 'artigo';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<PostCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_image_id');
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Posts visible on the public website.
     *
     * @param  Builder<Post>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scheduled posts whose publication date has arrived.
     *
     * @param  Builder<Post>  $query
     */
    public function scopeDueForPublication(Builder $query): void
    {
        $query->where('status', PostStatus::Scheduled)->where('published_at', '<=', now());
    }

    public function isLive(): bool
    {
        return $this->status === PostStatus::Published && $this->published_at?->isPast();
    }

    public function url(): string
    {
        return route('blog.show', $this->slug);
    }
}
