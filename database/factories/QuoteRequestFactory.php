<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\QuoteRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuoteRequest>
 */
class QuoteRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'company' => fake()->company(),
            'email' => fake()->safeEmail(),
            'phone' => '+258 84 '.fake()->numerify('### ####'),
            'message' => fake()->paragraph(),
            'status' => LeadStatus::New,
        ];
    }
}
