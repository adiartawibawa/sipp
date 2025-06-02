<?php

namespace Database\Factories\Schools;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class InfraRelocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'entity_id' => null, // Will be set in seeder
            'entity_type' => null, // Will be set in seeder
            'from' => $this->faker->address,
            'to' => $this->faker->address,
            'moved_at' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
