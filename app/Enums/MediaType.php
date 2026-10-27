<?php

namespace App\Enums;

enum MediaType: string
{
    case Image = 'image';
    case Video = 'video';
    case Document = 'document';

    public function label(): string
    {
        return match ($this) {
            self::Image => 'Imagem',
            self::Video => 'Vídeo',
            self::Document => 'Documento',
        };
    }

    public static function fromMime(string $mimeType): self
    {
        return match (true) {
            str_starts_with($mimeType, 'image/') => self::Image,
            str_starts_with($mimeType, 'video/') => self::Video,
            default => self::Document,
        };
    }
}
