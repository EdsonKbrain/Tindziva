<?php

namespace App\Models;

use App\Enums\ClientSegment;
use App\Enums\ClientType;
use App\Models\Concerns\FlushesPublicCache;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\LogsActivity;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'type', 'segment', 'website', 'description', 'services_provided', 'logo_id', 'sort_order', 'is_visible'])]
class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use FlushesPublicCache, HasFactory, HasSlug, LogsActivity;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ClientType::class,
            'segment' => ClientSegment::class,
            'services_provided' => 'array',
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public static function activityLabel(): string
    {
        return 'cliente';
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_id');
    }

    /**
     * @return HasMany<Project, $this>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * @param  Builder<Client>  $query
     */
    public function scopeVisible(Builder $query): void
    {
        $query->where('is_visible', true)->orderBy('sort_order')->orderBy('name');
    }
}
