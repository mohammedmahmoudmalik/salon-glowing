<?php

namespace App\Domains\Service\Http\Requests;

use App\Domains\Service\Models\ServiceCategory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage', ServiceCategory::class);
    }

    public function rules(): array
    {
        return [
            'name_ar' => ['sometimes', 'string', 'max:100'],
            'name_en' => ['sometimes', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
