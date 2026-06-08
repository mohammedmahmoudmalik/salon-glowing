<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'string' => 'حقل :attribute يجب أن يكون نصاً.',
    'email' => 'حقل :attribute يجب أن يكون بريداً إلكترونياً صحيحاً.',
    'unique' => ':attribute مستخدم مسبقاً.',
    'min' => [
        'string' => 'حقل :attribute يجب أن لا يقل عن :min حروف.',
    ],
    'max' => [
        'string' => 'حقل :attribute يجب أن لا يتجاوز :max حرفاً.',
        'file' => 'حجم :attribute يجب أن لا يتجاوز :max كيلوبايت.',
    ],
    'confirmed' => 'تأكيد :attribute غير متطابق.',
    'numeric' => 'حقل :attribute يجب أن يكون رقماً.',
    'integer' => 'حقل :attribute يجب أن يكون عدداً صحيحاً.',
    'boolean' => 'حقل :attribute يجب أن يكون صحيحاً أو خاطئاً.',
    'in' => 'القيمة المختارة في :attribute غير صحيحة.',
    'date' => 'حقل :attribute يجب أن يكون تاريخاً صحيحاً.',
    'uploaded' => 'فشل رفع :attribute — تأكد من أن حجم الملف لا يتجاوز الحد المسموح به.',
    'mimes' => 'يجب أن يكون :attribute ملفاً من نوع: :values.',
    'image' => 'يجب أن يكون :attribute صورة.',
    'regex' => 'تنسيق :attribute غير صحيح.',
    'nullable' => '',
    'attributes' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'phone' => 'رقم الجوال',
        'password' => 'كلمة المرور',
        'avatar' => 'الصورة الشخصية',
        'current_password' => 'كلمة المرور الحالية',
    ],
];
