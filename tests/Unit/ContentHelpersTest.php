<?php

namespace Tests\Unit;

use App\Services\HtmlSanitizer;
use App\Support\Text;
use App\Support\VideoEmbed;
use Tests\TestCase;

class ContentHelpersTest extends TestCase
{
    public function test_sanitizer_removes_scripts_and_event_handlers_but_keeps_formatting(): void
    {
        $html = app(HtmlSanitizer::class)->sanitize(
            '<h2>Título</h2><p onclick="x()">Texto <strong>forte</strong> <a href="javascript:alert(1)">mau</a> <a href="https://tindziva.co.mz">bom</a></p><script>alert(1)</script><iframe src="https://evil.example"></iframe><iframe src="https://www.youtube-nocookie.com/embed/abc"></iframe>'
        );

        $this->assertStringContainsString('<h2>Título</h2>', $html);
        $this->assertStringContainsString('<strong>forte</strong>', $html);
        $this->assertStringNotContainsString('onclick', $html);
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringNotContainsString('evil.example', $html);
        $this->assertStringContainsString('youtube-nocookie.com/embed/abc', $html);
        $this->assertStringContainsString('rel="noopener noreferrer"', $html);
    }

    public function test_accent_markup_is_escaped_before_styling(): void
    {
        $this->assertSame(
            'Vamos <span class="accent-serif">conversar</span> &lt;b&gt;',
            (string) Text::accent('Vamos *conversar* <b>'),
        );
        $this->assertSame('Vamos conversar', Text::plain('Vamos *conversar*'));
    }

    public function test_video_links_become_embed_urls(): void
    {
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', VideoEmbed::from('https://youtu.be/dQw4w9WgXcQ'));
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', VideoEmbed::from('https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=10'));
        $this->assertSame('https://player.vimeo.com/video/76979871', VideoEmbed::from('https://vimeo.com/76979871'));
        $this->assertNull(VideoEmbed::from('https://example.com/video'));
    }
}
