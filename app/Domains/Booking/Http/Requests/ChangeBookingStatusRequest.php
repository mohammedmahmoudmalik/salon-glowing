<?php

namespace App\Domains\Booking\Http\Requests;

use App\Domains\Booking\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;

class ChangeBookingStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('changeStatus', Booking::class);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:confirmed,completed,no_show'],
        ];
    }
}
