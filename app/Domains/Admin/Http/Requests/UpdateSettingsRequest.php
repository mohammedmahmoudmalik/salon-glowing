<?php

namespace App\Domains\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'salon_working_hours' => 'sometimes|array',
            'salon_working_hours.open' => 'required_with:salon_working_hours|date_format:H:i',
            'salon_working_hours.close' => 'required_with:salon_working_hours|date_format:H:i|after:salon_working_hours.open',
            'salon_working_hours.working_days' => 'required_with:salon_working_hours|array',
            'salon_working_hours.working_days.*' => 'integer|between:0,6',
            'booking_buffer_minutes' => 'sometimes|integer|min:0|max:60',
        ];
    }
}
