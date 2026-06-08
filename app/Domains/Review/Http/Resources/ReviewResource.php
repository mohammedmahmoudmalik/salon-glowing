<?php

namespace App\Domains\Review\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'is_hidden' => $this->is_hidden,
            'service' => $this->when(
                $this->relationLoaded('service'),
                fn () => [
                    'id' => $this->service->id,
                    'name_ar' => $this->service->name_ar,
                    'name_en' => $this->service->name_en,
                ]
            ),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
