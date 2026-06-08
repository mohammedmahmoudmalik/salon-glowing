<?php

namespace App\Domains\Service\Actions;

use App\Domains\Service\Models\ServiceCategory;

class StoreServiceCategoryAction
{
    public function execute(array $data): ServiceCategory
    {
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('categories', config('filesystems.default'));
        }

        $category = ServiceCategory::create($data);

        activity()
            ->performedOn($category)
            ->log('service_category_created');

        return $category;
    }
}
