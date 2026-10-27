<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

class Text
{
    /**
     * Escape a title and render words wrapped in *asterisks* with the editorial accent style.
     */
    public static function accent(?string $text): HtmlString
    {
        $escaped = e((string) $text);

        return new HtmlString(preg_replace('/\*(.+?)\*/u', '<span class="accent-serif">$1</span>', $escaped));
    }

    /**
     * Remove accent markers for plain-text contexts such as meta tags.
     */
    public static function plain(?string $text): string
    {
        return str_replace('*', '', (string) $text);
    }
}
