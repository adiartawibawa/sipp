<?php

namespace Database\Factories\Schools;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RoomFactory extends Factory
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
            'building_id' => null, // Will be set in seeder
            'room_ref_id' => null, // Will be set in seeder
            'code' => $this->faker->optional()->bothify('RM-###'),
            'name' => $this->faker->word,
            'reg_no' => $this->faker->optional()->bothify('REG-####'),
            'floor' => $this->faker->numberBetween(1, 3),
            'length' => $this->faker->randomFloat(2, 3, 15),
            'width' => $this->faker->randomFloat(2, 3, 15),
            'area' => function (array $attributes) {
                return $attributes['length'] * $attributes['width'];
            },
            'capacity' => $this->faker->numberBetween(10, 50),
        ];
    }
}
