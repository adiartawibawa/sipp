<?php

namespace Database\Factories\Schools;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BuildingFactory extends Factory
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
            'land_id' => null, // Will be set in seeder
            'code' => $this->faker->optional()->bothify('BLD-###'),
            'name' => $this->faker->word,
            'length' => $this->faker->randomFloat(2, 5, 50),
            'width' => $this->faker->randomFloat(2, 5, 50),
            'area' => function (array $attributes) {
                return $attributes['length'] * $attributes['width'];
            },
            'ownership' => $this->faker->randomElement(['sertifikat', 'imb', 'sewa']),
            'borrow_status' => $this->faker->optional()->randomElement(['pinjam pakai', 'hibah']),
            'asset_value' => $this->faker->randomFloat(2, 10000000, 500000000),
            'floors' => $this->faker->numberBetween(1, 3),
            'build_year' => $this->faker->numberBetween(1990, 2020),
            'notes' => $this->faker->optional()->sentence,
            'permit_date' => $this->faker->optional()->date(),
        ];
    }
}
