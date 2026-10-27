<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicCache;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\LogsActivity;
use Database\Factories\PostCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'sort_order'])]
class PostCategory extends Model
{
    /** @use HasFactory<PostCategoryFactory> */
    use FlushesPublicCache, HasFactory, HasSlug, LogsActivity;

    public static function activityLabel(): string
    {
        return 'categoria do blog';
    }

    /**
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
