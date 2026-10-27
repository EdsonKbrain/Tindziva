<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->unique()->words(3, true)),
            'template' => 'default',
            'subtitle' => fake()->sentence(),
            'content' => '<p>'.fake()->paragraph().'</p>',
            'is_published' => true,
        ];
    }
}
