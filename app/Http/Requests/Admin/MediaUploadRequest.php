<?php

namespace App\Http\Requests\Admin;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Validator;

class MediaUploadRequest extends FormRequest
{
    private const MAX_KILOBYTES = [
        'image' => 10 * 1024,
        'video' => 60 * 1024,
        'document' => 20 * 1024,
    ];

    public function authorize(): bool
    {
        return $this->user()->can('create', Media::class);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files' => ['required', 'array', 'max:20'],
            'files.*' => ['required', 'file', 'mimes:'.implode(',', MediaService::allowedExtensions()), 'max:'.max(self::MAX_KILOBYTES)],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Apply per-type size limits (images 10 MB, videos 60 MB, PDF 20 MB).
     *
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [function (Validator $validator): void {
            foreach ((array) $this->file('files', []) as $index => $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $mime = (string) $file->getMimeType();
                $type = str_starts_with($mime, 'image/') ? 'image' : (str_starts_with($mime, 'video/') ? 'video' : 'document');

                if ($file->getSize() / 1024 > self::MAX_KILOBYTES[$type]) {
                    $validator->errors()->add("files.{$index}", sprintf(
                        'O ficheiro «%s» excede o limite de %d MB.',
                        $file->getClientOriginalName(),
                        self::MAX_KILOBYTES[$type] / 1024,
                    ));
                }
            }
        }];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'files.*.mimes' => 'Formato não suportado. Use JPG, PNG, WebP, GIF, MP4, WebM ou PDF.',
        ];
    }
}
