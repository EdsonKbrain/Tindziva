<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
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
            'subject' => fake()->sentence(4),
            'message' => fake()->paragraph(),
            'status' => LeadStatus::New,
        ];
    }
}
