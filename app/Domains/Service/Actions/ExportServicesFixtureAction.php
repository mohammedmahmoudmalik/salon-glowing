<?php

namespace App\Domains\Service\Actions;

use App\Domains\Service\Models\ServiceCategory;
use Illuminate\Support\Facades\File;

class ExportServicesFixtureAction
{
    public function execute(): string
    {
        $categories = ServiceCategory::with('services')->orderBy('name_en')->get();

        $data = $categories->map(fn (ServiceCategory $cat) => [
            'category_name_ar' => $cat->name_ar,
            'category_name_en' => $cat->name_en,
            'services' => $cat->services->map(fn ($svc) => [
                'name_ar' => $svc->name_ar,
                'name_en' => $svc->name_en,
                'price' => (float) $svc->price,
                'duration_minutes' => $svc->duration_minutes,
            ])->values()->toArray(),
        ])->toArray();

        $path = database_path('fixtures/services.json');
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $path;
    }
}
