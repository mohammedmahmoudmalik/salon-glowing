<?php

namespace App\Domains\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone', 'regex:/^[+0-9]{7,20}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (empty($this->email) && empty($this->phone)) {
                $validator->errors()->add('email', __('validation.required', ['attribute' => __('validation.attributes.email')]));
                $validator->errors()->add('phone', __('validation.required', ['attribute' => __('validation.attributes.phone')]));
            }
        });
    }

    public function messages(): array
    {
        return [
            'email.unique' => __('validation.unique', ['attribute' => __('validation.attributes.email')]),
            'phone.unique' => __('validation.unique', ['attribute' => __('validation.attributes.phone')]),
        ];
    }
}
