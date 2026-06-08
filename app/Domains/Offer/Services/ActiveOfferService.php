<?php

namespace App\Domains\Offer\Services;

use App\Domains\Offer\Enums\DiscountType;
use App\Domains\Offer\Models\Offer;
use App\Domains\Service\Models\Service;

class ActiveOfferService
{
    public function getForService(Service $service): ?Offer
    {
        $offers = $service->relationLoaded('activeOffers')
            ? $service->activeOffers
            : $service->activeOffers()->get();

        return $offers
            ->sortByDesc(fn (Offer $offer) => $this->savings($offer, (float) $service->price))
            ->first();
    }

    public function calculateFinalPrice(Service $service, ?Offer $offer = null): float
    {
        $offer ??= $this->getForService($service);

        if (! $offer) {
            return (float) $service->price;
        }

        return match ($offer->discount_type) {
            DiscountType::Percentage => round((float) $service->price - ((float) $service->price * (float) $offer->discount_value / 100), 2),
            DiscountType::Fixed => max(0.0, round((float) $service->price - (float) $offer->discount_value, 2)),
        };
    }

    private function savings(Offer $offer, float $price): float
    {
        return match ($offer->discount_type) {
            DiscountType::Percentage => $price * (float) $offer->discount_value / 100,
            DiscountType::Fixed => min($price, (float) $offer->discount_value),
        };
    }
}
