<?php

namespace App\Domains\Service\Actions;

use App\Domains\Service\Models\Service;
use Illuminate\Http\UploadedFile;

class StoreServiceAction
{
    public function execute(array $data): Service
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('services', config('filesystems.default'));
        }

        $service = Service::create($data);

        activity()
            ->performedOn($service)
            ->log('service_created');

        return $service->load('category');
    }
}
