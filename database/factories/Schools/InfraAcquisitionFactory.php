<?php

namespace Database\Factories\Schools;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class InfraAcquisitionFactory extends Factory
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
            'source' => $this->faker->randomElement(['pemerintah', 'hibah', 'beli', 'bantuan']),
            'amount' => $this->faker->randomFloat(2, 1000000, 500000000),
            'year' => $this->faker->numberBetween(2010, 2023),
            'method' => $this->faker->optional()->randomElement(['tunai', 'kredit', 'lelang']),
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
