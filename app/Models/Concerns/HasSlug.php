<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Generates a unique slug from the model's title or name when none is given.
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::saving(function (Model $model): void {
            $source = $model->getAttribute($model->slugSourceAttribute());
            $slug = Str::slug((string) ($model->slug ?: $source));

            $model->slug = $model->uniqueSlug($slug !== '' ? $slug : Str::lower(Str::random(8)));
        });
    }

    public function slugSourceAttribute(): string
    {
        return array_key_exists('title', $this->attributes) ? 'title' : 'name';
    }

    public function uniqueSlug(string $slug): string
    {
        $slug = Str::limit($slug, 180, '');
        $candidate = $slug;
        $suffix = 2;

        while ($this->slugExists($candidate)) {
            $candidate = "{$slug}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }

    private function slugExists(string $slug): bool
    {
        $query = static::query()->where('slug', $slug)->whereKeyNot($this->getKey());

        if (in_array(SoftDeletes::class, class_uses_recursive($this), true)) {
            $query->withTrashed();
        }

        return $query->exists();
    }
}
