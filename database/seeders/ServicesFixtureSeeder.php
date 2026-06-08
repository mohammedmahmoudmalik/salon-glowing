<?php

namespace Database\Seeders;

use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ServicesFixtureSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('fixtures/services.json');

        if (! File::exists($path)) {
            $this->command->warn('No services fixture found at database/fixtures/services.json — skipping.');
            return;
        }

        $data = json_decode(File::get($path), true);

        if (empty($data)) {
            $this->command->warn('services.json is empty — skipping.');
            return;
        }

        $categoriesCreated = 0;
        $servicesCreated = 0;

        foreach ($data as $catData) {
            $category = ServiceCategory::firstOrCreate(
                ['name_en' => $catData['category_name_en']],
                [
                    'name_ar'   => $catData['category_name_ar'],
                    'is_active' => true,
                ]
            );

            if ($category->wasRecentlyCreated) {
                $categoriesCreated++;
            }

            foreach ($catData['services'] ?? [] as $svcData) {
                $service = Service::firstOrCreate(
                    [
                        'service_category_id' => $category->id,
                        'name_en'             => $svcData['name_en'],
                    ],
                    [
                        'name_ar'          => $svcData['name_ar'],
                        'price'            => $svcData['price'],
                        'duration_minutes' => $svcData['duration_minutes'],
                        'is_active'        => true,
                    ]
                );

                if ($service->wasRecentlyCreated) {
                    $servicesCreated++;
                }
            }
        }

        $this->command->info("Services fixture: {$categoriesCreated} categories, {$servicesCreated} services created.");
    }
}
