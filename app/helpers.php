<?php

use App\Services\SettingsRepository;
use App\Support\SiteTexts;

if (! function_exists('setting')) {
    /**
     * Read a site setting managed in the CMS.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return app(SettingsRepository::class)->get($key, $default);
    }
}

if (! function_exists('site_text')) {
    /**
     * Read an interface text managed in the CMS («Textos e menu»), with :placeholder replacements.
     *
     * @param  array<string, string|int>  $replace
     */
    function site_text(string $key, array $replace = []): string
    {
        return SiteTexts::get($key, $replace);
    }
}

if (! function_exists('site_enabled')) {
    /**
     * Read an on/off switch managed in the CMS («Textos e menu»).
     */
    function site_enabled(string $key): bool
    {
        return SiteTexts::enabled($key);
    }
}
