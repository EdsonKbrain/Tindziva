<?php

namespace App\Services;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer as SymfonyHtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Cleans HTML produced by the visual editor before it is stored or displayed.
 */
class HtmlSanitizer
{
    private SymfonyHtmlSanitizer $sanitizer;

    public function __construct()
    {
        $config = (new HtmlSanitizerConfig)
            ->allowSafeElements()
            ->allowElement('a', ['href', 'title', 'target', 'rel'])
            ->allowElement('img', ['src', 'alt', 'title', 'width', 'height', 'loading'])
            ->allowElement('iframe', ['src', 'width', 'height', 'allowfullscreen', 'frameborder', 'title'])
            ->allowElement('span', ['class'])
            ->allowElement('p', ['class'])
            ->allowElement('pre', ['class', 'spellcheck'])
            ->allowElement('li', ['data-list', 'class'])
            ->allowElement('ol', ['class'])
            ->allowElement('ul', ['class'])
            ->allowElement('figure', ['class'])
            ->allowElement('figcaption')
            ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
            ->allowMediaSchemes(['http', 'https'])
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->forceAttribute('a', 'rel', 'noopener noreferrer')
            ->forceAttribute('img', 'loading', 'lazy')
            ->allowMediaHosts([
                'www.youtube.com',
                'youtube.com',
                'www.youtube-nocookie.com',
                'player.vimeo.com',
                ...$this->ownHosts(),
            ])
            ->withMaxInputLength(500_000);

        $this->sanitizer = new SymfonyHtmlSanitizer($config);
    }

    public function sanitize(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        return $this->sanitizer->sanitize($html);
    }

    /**
     * Hosts that serve this application's own uploaded media.
     *
     * @return list<string>
     */
    private function ownHosts(): array
    {
        return array_values(array_filter(array_unique([
            parse_url((string) config('app.url'), PHP_URL_HOST),
            parse_url((string) config('filesystems.disks.public.url'), PHP_URL_HOST),
            parse_url((string) config('filesystems.disks.s3.url'), PHP_URL_HOST),
        ])));
    }
}
