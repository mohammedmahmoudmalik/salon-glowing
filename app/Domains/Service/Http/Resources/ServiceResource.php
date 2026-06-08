<?php

namespace App\Domains\Service\Http\Resources;

use App\Domains\Offer\Http\Resources\OfferResource;
use App\Domains\Offer\Services\ActiveOfferService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $offerService = app(ActiveOfferService::class);
        $activeOffer = $offerService->getForService($this->resource);

        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug' => $this->slug,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'price' => $this->price,
            'original_price' => $this->price,
            'final_price' => $offerService->calculateFinalPrice($this->resource, $activeOffer),
            'active_offer' => $activeOffer ? new OfferResource($activeOffer) : null,
            'duration_minutes' => $this->duration_minutes,
            'image_url' => $this->image_url,
            'is_active' => $this->is_active,
            'category' => new ServiceCategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
