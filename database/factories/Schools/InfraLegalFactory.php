<?php

namespace Database\Factories\Schools;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class InfraLegalFactory extends Factory
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
            'status' => $this->faker->randomElement(['sertifikat', 'imb', 'girik', 'sewa']),
            'doc_no' => $this->faker->optional()->bothify('DOC-####'),
            'doc_date' => $this->faker->optional()->date(),
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
