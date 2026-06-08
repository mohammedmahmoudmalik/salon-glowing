<?php

namespace App\Domains\Demo\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateDemoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_count' => ['required', 'integer', 'min:1', 'max:100'],
            'booking_count'  => ['required', 'integer', 'min:1', 'max:500'],
            'offer_count'    => ['required', 'integer', 'min:0', 'max:10'],
        ];
    }
}
