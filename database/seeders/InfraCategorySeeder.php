<?php

namespace Database\Seeders;

use App\Models\Schools\InfraCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InfraCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all default categories from the model
        $categories = InfraCategory::defaultInfraCategory();

        // Insert each category into the database
        foreach ($categories as $category) {
            InfraCategory::firstOrCreate(
                ['code' => $category['code']], // Search by unique code
                [
                    'name' => $category['name'],
                    'type' => $category['type'],
                    'desc' => $category['desc']
                ]
            );
        }
    }
}
