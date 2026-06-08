<?php

namespace App\Domains\Booking\Http\Resources;

use App\Domains\Service\Http\Resources\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service' => new ServiceResource($this->whenLoaded('service')),
            'price' => $this->price,
            'duration_minutes' => $this->duration_minutes,
        ];
    }
}
