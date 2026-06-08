<?php

namespace App\Domains\Offer\Actions;

use App\Domains\Offer\Models\Offer;

class StoreOfferAction
{
    public function execute(array $data): Offer
    {
        $serviceIds = $data['service_ids'] ?? [];
        unset($data['service_ids']);

        $offer = Offer::create($data);
        $offer->services()->sync($serviceIds);

        activity()->performedOn($offer)->log('offer_created');

        return $offer->load('services');
    }
}
