<?php

namespace App\Services;

use App\Models\Media;
use App\Models\Setting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class SettingsRepository
{
    private const CACHE_KEY = 'settings.all';

    /**
     * @var array<string, string|null>|null
     */
    private ?array $values = null;

    public function get(string $key, mixed $default = null): mixed
    {
        $value = Arr::get($this->all(), $key);

        return ($value === null || $value === '') ? $default : $value;
    }

    public function bool(string $key, bool $default = false): bool
    {
        $value = $this->get($key);

        return $value === null ? $default : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function media(string $key): ?Media
    {
        $id = $this->get($key);

        return $id ? Media::find($id) : null;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function set(array $values, string $group = 'general'): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['group' => $group, 'value' => is_bool($value) ? ($value ? '1' : '0') : $value],
            );
        }

        $this->forget();
    }

    public function forget(): void
    {
        $this->values = null;
        Cache::forget(self::CACHE_KEY);
        PublicCache::flush();
    }

    /**
     * @return array<string, string|null>
     */
    public function all(): array
    {
        if ($this->values !== null) {
            return $this->values;
        }

        try {
            return $this->values = Cache::rememberForever(
                self::CACHE_KEY,
                fn (): array => Setting::query()->pluck('value', 'key')->all(),
            );
        } catch (QueryException) {
            // The settings table may not exist yet (e.g. before the first migration).
            return $this->values = [];
        }
    }
}
