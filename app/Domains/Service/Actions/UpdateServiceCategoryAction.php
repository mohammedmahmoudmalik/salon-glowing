<?php

namespace App\Domains\Service\Actions;

use App\Domains\Service\Models\ServiceCategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateServiceCategoryAction
{
    public function execute(ServiceCategory $category, array $data): ServiceCategory
    {
        $disk = config('filesystems.default');

        if (! empty($data['remove_image']) && $category->image) {
            if (! str_starts_with($category->image, 'http')) {
                Storage::disk($disk)->delete($category->image);
            }
            $data['image'] = null;
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($category->image && ! str_starts_with($category->image, 'http')) {
                Storage::disk($disk)->delete($category->image);
            }
            $data['image'] = $data['image']->store('categories', $disk);
        }

        unset($data['remove_image']);

        $category->update($data);

        activity()
            ->performedOn($category)
            ->log('service_category_updated');

        return $category->fresh();
    }
}
