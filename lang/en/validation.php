<?php

return [
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute field must be a string.',
    'email' => 'The :attribute field must be a valid email address.',
    'unique' => 'The :attribute has already been taken.',
    'min' => [
        'string' => 'The :attribute field must be at least :min characters.',
    ],
    'max' => [
        'string' => 'The :attribute field must not be greater than :max characters.',
        'file' => 'The :attribute must not be greater than :max kilobytes.',
    ],
    'confirmed' => 'The :attribute field confirmation does not match.',
    'numeric' => 'The :attribute field must be a number.',
    'integer' => 'The :attribute field must be an integer.',
    'boolean' => 'The :attribute field must be true or false.',
    'in' => 'The selected :attribute is invalid.',
    'date' => 'The :attribute field must be a valid date.',
    'uploaded' => 'The :attribute failed to upload — make sure the file size is within the allowed limit.',
    'mimes' => 'The :attribute must be a file of type: :values.',
    'image' => 'The :attribute must be an image.',
    'regex' => 'The :attribute field format is invalid.',
    'nullable' => '',
    'attributes' => [
        'name' => 'name',
        'email' => 'email',
        'phone' => 'phone',
        'password' => 'password',
        'avatar' => 'avatar',
        'current_password' => 'current password',
    ],
];
