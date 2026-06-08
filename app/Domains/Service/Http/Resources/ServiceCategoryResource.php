<?php

namespace App\Domains\Service\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug' => $this->slug,
            'image_url' => $this->image_url,
            'is_active' => $this->is_active,
            'services_count' => $this->whenCounted('services'),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
