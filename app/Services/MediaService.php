<?php

namespace App\Services;

use App\Enums\MediaType;
use App\Models\Media;
use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores uploads in the media library, optimising images on the way in.
 */
class MediaService
{
    public const MAX_IMAGE_DIMENSION = 2400;

    public const THUMBNAIL_WIDTH = 480;

    public const IMAGE_MIMES = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    public const VIDEO_MIMES = ['mp4', 'webm'];

    public const DOCUMENT_MIMES = ['pdf'];

    /**
     * @return list<string>
     */
    public static function allowedExtensions(): array
    {
        return [...self::IMAGE_MIMES, ...self::VIDEO_MIMES, ...self::DOCUMENT_MIMES];
    }

    /**
     * @param  array{title?: string|null, alt_text?: string|null, caption?: string|null}  $attributes
     */
    public function store(UploadedFile $file, array $attributes = []): Media
    {
        $disk = (string) config('filesystems.media_disk', 'public');
        $directory = 'media/'.now()->format('Y/m');
        $mimeType = (string) $file->getMimeType();
        $type = MediaType::fromMime($mimeType);
        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'ficheiro';
        $fileName = Str::limit($baseName, 80, '').'-'.Str::lower(Str::random(8));

        $width = null;
        $height = null;
        $thumbnailPath = null;
        $contents = null;

        if ($type === MediaType::Image && $extension !== 'gif' && ($image = $this->loadImage($file->getRealPath(), $extension))) {
            $image = $this->constrain($image, self::MAX_IMAGE_DIMENSION);
            $width = imagesx($image);
            $height = imagesy($image);
            $contents = $this->encode($image, $extension);

            $thumbnail = $this->constrain($image, self::THUMBNAIL_WIDTH);
            $thumbnailExtension = function_exists('imagewebp') ? 'webp' : 'jpg';
            $thumbnailPath = "{$directory}/thumbs/{$fileName}.{$thumbnailExtension}";
            Storage::disk($disk)->put($thumbnailPath, $this->encode($thumbnail, $thumbnailExtension), 'public');
        } elseif ($type === MediaType::Image && ($size = @getimagesize($file->getRealPath()))) {
            [$width, $height] = $size;
        }

        $path = "{$directory}/{$fileName}.{$extension}";

        if ($contents !== null) {
            Storage::disk($disk)->put($path, $contents, 'public');
        } else {
            Storage::disk($disk)->putFileAs($directory, $file, "{$fileName}.{$extension}", 'public');
        }

        return Media::create([
            'disk' => $disk,
            'directory' => $directory,
            'path' => $path,
            'thumbnail_path' => $thumbnailPath,
            'original_name' => Str::limit($file->getClientOriginalName(), 250, ''),
            'mime_type' => $mimeType,
            'type' => $type,
            'size' => Storage::disk($disk)->size($path),
            'width' => $width,
            'height' => $height,
            'title' => $attributes['title'] ?? Str::headline($baseName),
            'alt_text' => $attributes['alt_text'] ?? null,
            'caption' => $attributes['caption'] ?? null,
            'uploaded_by' => Auth::id(),
        ]);
    }

    public function delete(Media $media): void
    {
        Storage::disk($media->disk)->delete(array_filter([$media->path, $media->thumbnail_path]));

        $media->delete();
    }

    private function loadImage(string $path, string $extension): ?GdImage
    {
        $image = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        if (! $image instanceof GdImage) {
            return null;
        }

        if (in_array($extension, ['jpg', 'jpeg'], true)) {
            $image = $this->applyExifOrientation($image, $path);
        }

        return $image;
    }

    private function applyExifOrientation(GdImage $image, string $path): GdImage
    {
        $orientation = function_exists('exif_read_data') ? (@exif_read_data($path)['Orientation'] ?? 1) : 1;

        $rotated = match ((int) $orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };

        return $rotated instanceof GdImage ? $rotated : $image;
    }

    private function constrain(GdImage $image, int $maxDimension): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if (max($width, $height) <= $maxDimension) {
            return $image;
        }

        $ratio = $maxDimension / max($width, $height);
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $resized;
    }

    private function encode(GdImage $image, string $extension): string
    {
        ob_start();

        match ($extension) {
            'png' => (function () use ($image): void {
                imagesavealpha($image, true);
                imagepng($image, null, 8);
            })(),
            'webp' => imagewebp($image, null, 82),
            default => (function () use ($image): void {
                imageinterlace($image, true);
                imagejpeg($image, null, 82);
            })(),
        };

        return (string) ob_get_clean();
    }
}
