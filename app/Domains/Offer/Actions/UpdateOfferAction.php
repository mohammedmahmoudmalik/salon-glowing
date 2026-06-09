<?php

namespace App\Domains\Offer\Actions;

use App\Domains\Offer\Models\Offer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateOfferAction
{
    public function execute(Offer $offer, array $data): Offer
    {
        $serviceIds = array_key_exists('service_ids', $data) ? $data['service_ids'] : null;
        unset($data['service_ids']);

        $disk = config('filesystems.default');

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($offer->image && ! str_starts_with($offer->image, 'http')) {
                Storage::disk($disk)->delete($offer->image);
            }
            $data['image'] = $data['image']->store('offers', $disk);
        }

        $offer->update($data);

        if ($serviceIds !== null) {
            $offer->services()->sync($serviceIds);
        }

        activity()->performedOn($offer)->log('offer_updated');

        return $offer->load('services');
    }
}
