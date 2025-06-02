<?php

namespace Database\Factories\Schools;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class FacilityConditionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $conditions = ['good', 'light', 'heavy'];

        return [
            'facil_id' => null, // Will be set in seeder
            'condition' => $this->faker->randomElement($conditions),
            'slug' => $this->faker->optional()->slug,
            'percentage' => $this->faker->randomFloat(2, 10, 100),
            'notes' => $this->faker->optional()->sentence,
            'checked_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
