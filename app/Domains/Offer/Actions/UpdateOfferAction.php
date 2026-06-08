<?php

namespace App\Domains\Offer\Actions;

use App\Domains\Offer\Models\Offer;

class UpdateOfferAction
{
    public function execute(Offer $offer, array $data): Offer
    {
        $serviceIds = array_key_exists('service_ids', $data) ? $data['service_ids'] : null;
        unset($data['service_ids']);

        $offer->update($data);

        if ($serviceIds !== null) {
            $offer->services()->sync($serviceIds);
        }

        activity()->performedOn($offer)->log('offer_updated');

        return $offer->load('services');
    }
}
