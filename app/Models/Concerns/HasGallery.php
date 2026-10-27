<?php

namespace App\Models\Concerns;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Attaches an ordered gallery of media library items to a model.
 */
trait HasGallery
{
    /**
     * @return MorphToMany<Media, $this>
     */
    public function gallery(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable', 'mediables')
            ->wherePivot('collection', 'gallery')
            ->withPivot(['collection', 'sort_order'])
            ->orderByPivot('sort_order');
    }

    /**
     * @param  array<int, int|string>  $mediaIds
     */
    public function syncGallery(array $mediaIds): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $mediaIds))));
        $payload = [];

        foreach ($ids as $position => $id) {
            $payload[$id] = ['collection' => 'gallery', 'sort_order' => $position];
        }

        $this->gallery()->sync($payload);
    }
}
