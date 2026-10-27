<?php

namespace Database\Factories;

use App\Enums\ClientType;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'type' => ClientType::Client,
            'sort_order' => fake()->numberBetween(0, 20),
            'is_visible' => true,
        ];
    }
}
