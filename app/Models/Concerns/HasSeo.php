<?php

namespace App\Models\Concerns;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Provides editable SEO metadata (meta title, description and Open Graph image).
 */
trait HasSeo
{
    /**
     * @return BelongsTo<Media, $this>
     */
    public function ogImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_image_id');
    }
}
