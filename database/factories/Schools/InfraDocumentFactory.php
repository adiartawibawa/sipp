<?php

namespace Database\Factories\Schools;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class InfraDocumentFactory extends Factory
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
            'name' => $this->faker->word . ' document',
            'path' => 'documents/' . $this->faker->uuid . '.pdf',
        ];
    }
}
