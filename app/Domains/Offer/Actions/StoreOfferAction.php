<?php

namespace App\Domains\Offer\Actions;

use App\Domains\Offer\Models\Offer;
use Illuminate\Http\UploadedFile;

class StoreOfferAction
{
    public function execute(array $data): Offer
    {
        $serviceIds = $data['service_ids'] ?? [];
        unset($data['service_ids']);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('offers', config('filesystems.default'));
        }

        $offer = Offer::create($data);
        $offer->services()->sync($serviceIds);

        activity()->performedOn($offer)->log('offer_created');

        return $offer->load('services');
    }
}
