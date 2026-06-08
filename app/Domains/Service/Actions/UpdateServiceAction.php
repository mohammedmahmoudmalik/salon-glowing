<?php

namespace App\Domains\Service\Actions;

use App\Domains\Service\Models\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateServiceAction
{
    public function execute(Service $service, array $data): Service
    {
        $disk = config('filesystems.default');

        if (! empty($data['remove_image'])) {
            if ($service->image && ! str_starts_with($service->image, 'http')) {
                Storage::disk($disk)->delete($service->image);
            }
            $data['image'] = null;
        } elseif (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($service->image && ! str_starts_with($service->image, 'http')) {
                Storage::disk($disk)->delete($service->image);
            }
            $data['image'] = $data['image']->store('services', $disk);
        }

        unset($data['remove_image']);

        $service->update($data);

        activity()
            ->performedOn($service)
            ->log('service_updated');

        return $service->fresh()->load('category');
    }
}
