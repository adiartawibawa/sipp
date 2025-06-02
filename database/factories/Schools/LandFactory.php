<?php

namespace Database\Factories\Schools;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => null, // Will be set in seeder
            'infra_cat_id' => null, // Will be set in seeder
            'name' => $this->faker->word,
            'cert_no' => $this->faker->optional()->bothify('SHM-####'),
            'length' => $this->faker->randomFloat(2, 10, 100),
            'width' => $this->faker->randomFloat(2, 10, 100),
            'area' => $this->faker->randomFloat(2, 100, 1000),
            'avail_area' => function (array $attributes) {
                return $attributes['area'] * $this->faker->randomFloat(2, 0.7, 0.9);
            },
            'ownership' => $this->faker->randomElement(['sertifikat', 'girik', 'sewa']),
            'njop' => $this->faker->randomFloat(2, 1000000, 50000000),
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
