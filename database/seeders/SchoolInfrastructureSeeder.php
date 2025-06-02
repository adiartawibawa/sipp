<?php

namespace Database\Seeders;

use App\Models\Schools\Building;
use App\Models\Schools\FacilityCondition;
use App\Models\Schools\InfraAcquisition;
use App\Models\Schools\InfraCategory;
use App\Models\Schools\InfraCondition;
use App\Models\Schools\InfraDocument;
use App\Models\Schools\InfraLegal;
use App\Models\Schools\InfraRelocation;
use App\Models\Schools\Land;
use App\Models\Schools\OtherFacility;
use App\Models\Schools\Room;
use App\Models\Schools\RoomReference;
use App\Models\Schools\School;
use App\Models\User;
use Illuminate\Database\Seeder;

class SchoolInfrastructureSeeder extends Seeder
{
    public function run()
    {
        // Get existing schools and users
        $schools = School::all();
        $users = User::all();

        if ($schools->isEmpty()) {
            $this->command->info('No schools found. Please run SchoolSeeder first.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        // Create infrastructure categories
        $infraCategories = [
            ['name' => 'Tanah', 'desc' => 'Kategori untuk tanah sekolah'],
            ['name' => 'Bangunan Pendidikan', 'desc' => 'Bangunan utama untuk kegiatan belajar mengajar'],
            ['name' => 'Bangunan Penunjang', 'desc' => 'Bangunan pendukung kegiatan sekolah'],
            ['name' => 'Bangunan Administrasi', 'desc' => 'Bangunan untuk keperluan administrasi'],
        ];

        foreach ($infraCategories as $category) {
            InfraCategory::firstOrCreate(['name' => $category['name']], $category);
        }

        // Create room references
        $roomReferences = [
            ['name' => 'Kelas', 'code' => 'RKL', 'desc' => 'Ruang kelas belajar'],
            ['name' => 'Perpustakaan', 'code' => 'RPT', 'desc' => 'Ruang perpustakaan'],
            ['name' => 'Laboratorium IPA', 'code' => 'RLI', 'desc' => 'Ruang laboratorium IPA'],
            ['name' => 'Laboratorium Komputer', 'code' => 'RLK', 'desc' => 'Ruang laboratorium komputer'],
            ['name' => 'Kantor', 'code' => 'RKT', 'desc' => 'Ruang kantor'],
            ['name' => 'Guru', 'code' => 'RGR', 'desc' => 'Ruang guru'],
            ['name' => 'UKS', 'code' => 'RUK', 'desc' => 'Ruang Unit Kesehatan Sekolah'],
            ['name' => 'Toilet', 'code' => 'RTL', 'desc' => 'Ruang toilet'],
        ];

        foreach ($roomReferences as $reference) {
            RoomReference::firstOrCreate(['name' => $reference['name']], $reference);
        }

        // Seed data for each school
        foreach ($schools as $school) {
            $this->command->info("Seeding infrastructure for school: {$school->name}");

            // Get categories
            $landCategory = InfraCategory::where('name', 'Tanah')->first();
            $buildingCategories = InfraCategory::where('name', '!=', 'Tanah')->get();
            $roomRefs = RoomReference::all();

            // Create lands
            $lands = Land::factory()
                ->count(rand(1, 3))
                ->create([
                    'school_id' => $school->id,
                    'infra_cat_id' => $landCategory->id,
                    'created_by' => $users->random()->id,
                    'updated_by' => $users->random()->id,
                ]);

            foreach ($lands as $land) {
                // Add conditions to land
                $land->conditions()->createMany(
                    InfraCondition::factory()
                        ->count(rand(1, 2))
                        ->make([
                            'entity_id' => $land->id,
                            'entity_type' => Land::class,
                        ])->toArray()
                );

                // Add legal status to land
                $land->legalStatuses()->create(
                    InfraLegal::factory()->make([
                        'entity_id' => $land->id,
                        'entity_type' => Land::class,
                    ])->toArray()
                );

                // Add documents to land
                $land->documents()->create(
                    InfraDocument::factory()->make([
                        'entity_id' => $land->id,
                        'entity_type' => Land::class,
                    ])->toArray()
                );

                // Add acquisitions to land
                $land->acquisitions()->create(
                    InfraAcquisition::factory()->make([
                        'entity_id' => $land->id,
                        'entity_type' => Land::class,
                    ])->toArray()
                );
            }

            // Create buildings on each land
            $buildings = collect();

            foreach ($lands as $land) {
                $buildings = $buildings->merge(
                    Building::factory()
                        ->count(rand(2, 5))
                        ->create([
                            'school_id' => $school->id,
                            'land_id' => $land->id,
                            'infra_cat_id' => $buildingCategories->random()->id,
                            'created_by' => $users->random()->id,
                            'updated_by' => $users->random()->id,
                        ])
                );
            }

            foreach ($buildings as $building) {
                // Add conditions to building
                $building->conditions()->createMany(
                    InfraCondition::factory()
                        ->count(rand(1, 2))
                        ->make([
                            'entity_id' => $building->id,
                            'entity_type' => Building::class,
                        ])->toArray()
                );

                // Add legal status to building
                $building->legalStatuses()->create(
                    InfraLegal::factory()->make([
                        'entity_id' => $building->id,
                        'entity_type' => Building::class,
                    ])->toArray()
                );

                // Add documents to building
                $building->documents()->create(
                    InfraDocument::factory()->make([
                        'entity_id' => $building->id,
                        'entity_type' => Building::class,
                    ])->toArray()
                );

                // Add acquisitions to building
                $building->acquisitions()->create(
                    InfraAcquisition::factory()->make([
                        'entity_id' => $building->id,
                        'entity_type' => Building::class,
                    ])->toArray()
                );

                // Add relocations to building
                if (rand(0, 1)) {
                    $building->relocations()->create(
                        InfraRelocation::factory()->make([
                            'entity_id' => $building->id,
                            'entity_type' => Building::class,
                        ])->toArray()
                    );
                }

                // Create rooms in each building
                $rooms = Room::factory()
                    ->count(rand(3, 10))
                    ->create([
                        'school_id' => $school->id,
                        'building_id' => $building->id,
                        'room_ref_id' => $roomRefs->random()->id,
                        'created_by' => $users->random()->id,
                        'updated_by' => $users->random()->id,
                    ]);

                foreach ($rooms as $room) {
                    // Add conditions to room
                    $room->conditions()->createMany(
                        InfraCondition::factory()
                            ->count(rand(1, 2))
                            ->make([
                                'entity_id' => $room->id,
                                'entity_type' => Room::class,
                            ])->toArray()
                    );

                    // Add legal status to room
                    if (rand(0, 1)) {
                        $room->legalStatuses()->create(
                            InfraLegal::factory()->make([
                                'entity_id' => $room->id,
                                'entity_type' => Room::class,
                            ])->toArray()
                        );
                    }

                    // Add documents to room
                    if (rand(0, 1)) {
                        $room->documents()->create(
                            InfraDocument::factory()->make([
                                'entity_id' => $room->id,
                                'entity_type' => Room::class,
                            ])->toArray()
                        );
                    }
                }
            }

            // Create other facilities
            $facilities = OtherFacility::factory()
                ->count(rand(5, 15))
                ->create([
                    'school_id' => $school->id,
                    'created_by' => $users->random()->id,
                    'updated_by' => $users->random()->id,
                ]);

            foreach ($facilities as $facility) {
                // Add conditions to facility
                $facility->conditions()->createMany(
                    FacilityCondition::factory()
                        ->count(rand(1, 2))
                        ->make([
                            'facil_id' => $facility->id,
                        ])->toArray()
                );

                // Add acquisitions to facility
                $facility->acquisitions()->create(
                    InfraAcquisition::factory()->make([
                        'entity_id' => $facility->id,
                        'entity_type' => OtherFacility::class,
                    ])->toArray()
                );

                // Add relocations to facility
                if (rand(0, 1)) {
                    $facility->relocations()->create(
                        InfraRelocation::factory()->make([
                            'entity_id' => $facility->id,
                            'entity_type' => OtherFacility::class,
                        ])->toArray()
                    );
                }
            }
        }

        $this->command->info('School infrastructure data seeded successfully!');
    }
}
