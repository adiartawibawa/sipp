<?php

namespace Database\Factories\Schools;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OtherFacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['perabot', 'alat pendidikan', 'alat olahraga', 'alat kesenian'];

        return [
            'school_id' => null, // Will be set in seeder
            'category' => $this->faker->randomElement($categories),
            'name' => $this->faker->word,
            'code' => $this->faker->optional()->bothify('FAC-###'),
            'qty' => $this->faker->numberBetween(1, 20),
            'specs' => $this->faker->optional()->sentence,
            'value' => $this->faker->randomFloat(2, 10000, 5000000),
            'acq_year' => $this->faker->numberBetween(2010, 2023),
        ];
    }
}
