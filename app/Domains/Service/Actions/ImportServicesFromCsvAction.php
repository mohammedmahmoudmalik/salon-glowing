<?php

namespace App\Domains\Service\Actions;

use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ImportServicesFromCsvAction
{
    public function execute(UploadedFile $file): array
    {
        $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (empty($lines)) {
            return ['categories' => 0, 'services' => 0];
        }

        $header = array_map('trim', str_getcsv(array_shift($lines)));
        if (isset($header[0])) {
            $header[0] = ltrim($header[0], "\xEF\xBB\xBF");
        }

        $categoriesCreated = 0;
        $servicesCreated = 0;

        DB::transaction(function () use ($lines, $header, &$categoriesCreated, &$servicesCreated) {
            foreach ($lines as $line) {
                $row = array_map('trim', str_getcsv($line));

                if (count($row) < count($header)) {
                    continue;
                }

                $data = array_combine($header, $row);

                if (empty($data['service_name_en'] ?? '') || empty($data['category_name_en'] ?? '')) {
                    continue;
                }

                $category = ServiceCategory::firstOrCreate(
                    ['name_en' => $data['category_name_en']],
                    [
                        'name_ar' => $data['category_name_ar'] ?? $data['category_name_en'],
                        'is_active' => true,
                    ]
                );

                if ($category->wasRecentlyCreated) {
                    $categoriesCreated++;
                }

                $service = Service::firstOrCreate(
                    [
                        'service_category_id' => $category->id,
                        'name_en' => $data['service_name_en'],
                    ],
                    [
                        'name_ar' => $data['service_name_ar'] ?? $data['service_name_en'],
                        'price' => is_numeric($data['price'] ?? '') ? $data['price'] : 0,
                        'duration_minutes' => is_numeric($data['duration_minutes'] ?? '') ? (int) $data['duration_minutes'] : 30,
                        'is_active' => true,
                    ]
                );

                if ($service->wasRecentlyCreated) {
                    $servicesCreated++;
                }
            }
        });

        return ['categories' => $categoriesCreated, 'services' => $servicesCreated];
    }
}
