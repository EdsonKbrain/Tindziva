<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => ucfirst(fake()->unique()->sentence(6)),
            'excerpt' => fake()->sentence(16),
            'content' => collect(fake()->paragraphs(4))->map(fn ($paragraph) => "<p>{$paragraph}</p>")->implode(''),
            'status' => PostStatus::Published,
            'published_at' => now()->subDays(fake()->numberBetween(1, 60)),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => PostStatus::Draft, 'published_at' => null]);
    }

    public function scheduled(?\DateTimeInterface $at = null): static
    {
        return $this->state(fn () => ['status' => PostStatus::Scheduled, 'published_at' => $at ?? now()->addDay()]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
