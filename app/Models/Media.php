<?php

namespace App\Models;

use App\Enums\MediaType;
use App\Models\Concerns\LogsActivity;
use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

#[Fillable(['disk', 'directory', 'path', 'thumbnail_path', 'original_name', 'mime_type', 'type', 'size', 'width', 'height', 'title', 'alt_text', 'caption', 'uploaded_by'])]
class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory, LogsActivity;

    protected $table = 'media';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => MediaType::class,
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public static function activityLabel(): string
    {
        return 'ficheiro';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return $this->publicUrl($this->path);
    }

    public function thumbnailUrl(): string
    {
        return $this->thumbnail_path ? $this->publicUrl($this->thumbnail_path) : $this->url();
    }

    /**
     * Local public files are served from the current host, so links keep working if APP_URL differs.
     */
    private function publicUrl(string $path): string
    {
        if ($this->disk === 'public' && config('filesystems.disks.public.driver') === 'local') {
            return asset('storage/'.$path);
        }

        return Storage::disk($this->disk)->url($path);
    }

    public function isImage(): bool
    {
        return $this->type === MediaType::Image;
    }

    public function isVideo(): bool
    {
        return $this->type === MediaType::Video;
    }

    public function altText(): string
    {
        return $this->alt_text ?: (string) $this->title;
    }

    public function humanSize(): string
    {
        return Number::fileSize($this->size, precision: 1);
    }

    /**
     * @param  Builder<Media>  $query
     */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        if (filled($term)) {
            $query->where(fn (Builder $query) => $query
                ->where('title', 'like', "%{$term}%")
                ->orWhere('original_name', 'like', "%{$term}%")
                ->orWhere('alt_text', 'like', "%{$term}%")
                ->orWhere('caption', 'like', "%{$term}%"));
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toPickerArray(): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url(),
            'thumbnail' => $this->thumbnailUrl(),
            'title' => $this->title,
            'alt' => $this->altText(),
            'caption' => $this->caption,
            'type' => $this->type->value,
            'mime' => $this->mime_type,
            'size' => $this->humanSize(),
            'dimensions' => $this->width ? "{$this->width}×{$this->height}" : null,
        ];
    }
}
