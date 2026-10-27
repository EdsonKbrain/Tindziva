<?php

namespace Database\Factories;

use App\Enums\MediaType;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->slug(2);

        return [
            'disk' => 'public',
            'directory' => 'media/'.now()->format('Y/m'),
            'path' => 'media/'.now()->format('Y/m')."/{$name}.jpg",
            'original_name' => "{$name}.jpg",
            'mime_type' => 'image/jpeg',
            'type' => MediaType::Image,
            'size' => fake()->numberBetween(50_000, 900_000),
            'width' => 1600,
            'height' => 1000,
            'title' => ucfirst(str_replace('-', ' ', $name)),
            'alt_text' => fake()->sentence(4),
        ];
    }
}
