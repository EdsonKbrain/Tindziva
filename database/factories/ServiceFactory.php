<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->words(3, true)),
            'icon' => 'sparkles',
            'excerpt' => fake()->sentence(12),
            'description' => '<p>'.fake()->paragraph().'</p>',
            'benefits' => [fake()->sentence(4), fake()->sentence(5)],
            'sort_order' => fake()->numberBetween(0, 20),
            'is_published' => true,
        ];
    }

    public function hidden(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }
}
