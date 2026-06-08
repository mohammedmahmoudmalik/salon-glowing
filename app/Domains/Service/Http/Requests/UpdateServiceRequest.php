<?php

namespace App\Domains\Service\Http\Requests;

use App\Domains\Service\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage', Service::class);
    }

    public function rules(): array
    {
        return [
            'service_category_id' => ['sometimes', 'integer', 'exists:service_categories,id'],
            'name_ar' => ['sometimes', 'string', 'max:150'],
            'name_en' => ['sometimes', 'string', 'max:150'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'duration_minutes' => ['sometimes', 'integer', 'min:5'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
