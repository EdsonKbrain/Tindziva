<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Caches public-facing queries and invalidates everything at once when content changes.
 */
class PublicCache
{
    private const VERSION_KEY = 'public-cache.version';

    /**
     * @template TValue
     *
     * @param  Closure(): TValue  $callback
     * @return TValue
     */
    public static function remember(string $key, Closure $callback, int $seconds = 600): mixed
    {
        return Cache::remember(self::versioned($key), $seconds, $callback);
    }

    public static function flush(): void
    {
        Cache::forever(self::VERSION_KEY, self::version() + 1);
    }

    private static function version(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 1);
    }

    private static function versioned(string $key): string
    {
        return 'public-cache.v'.self::version().'.'.$key;
    }
}
