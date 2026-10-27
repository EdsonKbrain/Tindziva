<?php

namespace App\Support;

use App\Models\Media;
use App\Services\HtmlSanitizer;
use Illuminate\Support\Str;

/**
 * Cleans page section input according to the template schema, discarding unknown keys.
 */
class SectionSanitizer
{
    private const MAX_REPEATER_ROWS = 30;

    /**
     * @var array<int, bool>
     */
    private array $knownMedia = [];

    public function __construct(private HtmlSanitizer $htmlSanitizer) {}

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, array<string, mixed>>
     */
    public function clean(string $template, array $input): array
    {
        $schema = PageTemplates::sections($template);
        $this->knownMedia = Media::query()->whereIn('id', $this->collectMediaIds($input))->pluck('id')->flip()->map(fn () => true)->all();

        $sections = [];

        foreach ($schema as $sectionKey => $section) {
            $values = (array) ($input[$sectionKey] ?? []);
            $sections[$sectionKey] = $this->cleanFields($section['fields'], $values);

            if ($section['toggle'] ?? true) {
                $sections[$sectionKey]['_visible'] = array_key_exists('_visible', $values)
                    ? filter_var($values['_visible'], FILTER_VALIDATE_BOOLEAN)
                    : true;
            }
        }

        return $sections;
    }

    /**
     * @param  array<string, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function cleanFields(array $fields, array $values): array
    {
        $clean = [];

        foreach ($fields as $key => $field) {
            $value = $values[$key] ?? null;

            $clean[$key] = match ($field['type']) {
                'repeater' => $this->cleanRepeater($field['fields'], is_array($value) ? $value : []),
                'richtext' => $this->htmlSanitizer->sanitize(is_string($value) ? $value : null) ?: null,
                'textarea' => $this->plainText($value, 3000),
                'url' => $this->url($value),
                'image' => $this->mediaId($value),
                'icon' => in_array($value, PageTemplates::icons(), true) ? $value : null,
                default => $this->plainText($value, 500),
            };
        }

        return $clean;
    }

    /**
     * @param  array<string, array<string, mixed>>  $fields
     * @param  array<int|string, mixed>  $rows
     * @return list<array<string, mixed>>
     */
    private function cleanRepeater(array $fields, array $rows): array
    {
        $clean = [];

        foreach (array_slice(array_values($rows), 0, self::MAX_REPEATER_ROWS) as $row) {
            $row = $this->cleanFields($fields, is_array($row) ? $row : []);

            if (array_filter($row, fn ($value) => filled($value)) !== []) {
                $clean[] = $row;
            }
        }

        return $clean;
    }

    private function plainText(mixed $value, int $limit): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $text = trim(strip_tags((string) $value));

        return $text === '' ? null : Str::limit($text, $limit, '');
    }

    private function url(mixed $value): ?string
    {
        $url = $this->plainText($value, 500);

        if ($url === null) {
            return null;
        }

        return preg_match('~^(https?://|/|#|mailto:|tel:)~i', $url) ? $url : null;
    }

    private function mediaId(mixed $value): ?int
    {
        $id = filter_var($value, FILTER_VALIDATE_INT);

        return $id !== false && isset($this->knownMedia[$id]) ? $id : null;
    }

    /**
     * @param  array<mixed>  $input
     * @return list<int>
     */
    private function collectMediaIds(array $input): array
    {
        $ids = [];

        array_walk_recursive($input, function ($value) use (&$ids): void {
            if (is_numeric($value) && (int) $value > 0) {
                $ids[] = (int) $value;
            }
        });

        return array_values(array_unique($ids));
    }
}
