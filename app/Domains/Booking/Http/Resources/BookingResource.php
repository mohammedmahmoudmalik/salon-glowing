<?php

namespace App\Domains\Booking\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_date' => $this->booking_date->toDateString(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status->value,
            'buffer_minutes' => $this->buffer_minutes,
            'total_price' => $this->total_price,
            'confirmed_at' => $this->confirmed_at?->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'cancellation_reason' => $this->cancellation_reason,
            'customer' => $this->whenLoaded('customer', fn () => [
                'id' => $this->customer->id,
                'name' => $this->customer->user?->name,
                'phone' => $this->customer->user?->phone,
            ]),
            'items' => BookingItemResource::collection($this->whenLoaded('items')),
            'services' => $this->when(
                $this->relationLoaded('items'),
                fn () => $this->items->map(fn ($item) => [
                    'name' => app()->getLocale() === 'ar'
                                         ? $item->service?->name_ar
                                         : $item->service?->name_en,
                    'price' => $item->price,
                    'duration_minutes' => $item->duration_minutes,
                ])
            ),
            'total_duration_minutes' => $this->when(
                $this->relationLoaded('items'),
                fn () => $this->items->sum('duration_minutes')
            ),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
