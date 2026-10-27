<?php

namespace App\Support;

/**
 * Converts public YouTube and Vimeo links into privacy-friendly embed URLs.
 */
class VideoEmbed
{
    public static function from(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $matches)) {
            return "https://www.youtube-nocookie.com/embed/{$matches[1]}";
        }

        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $matches)) {
            return "https://player.vimeo.com/video/{$matches[1]}";
        }

        return null;
    }

    public static function isSupported(?string $url): bool
    {
        return self::from($url) !== null;
    }
}
