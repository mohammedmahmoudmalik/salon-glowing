<?php

namespace App\Domains\Service\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportServicesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ];
    }
}
