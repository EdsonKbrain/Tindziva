<?php

namespace App\Models\Concerns;

use App\Services\PublicCache;

/**
 * Invalidates cached public content whenever the model changes.
 */
trait FlushesPublicCache
{
    public static function bootFlushesPublicCache(): void
    {
        static::saved(fn () => PublicCache::flush());
        static::deleted(fn () => PublicCache::flush());
    }
}
