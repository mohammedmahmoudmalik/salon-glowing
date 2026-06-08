# وحدة المصادقة / Auth Module

## النطاق
التسجيل، تسجيل الدخول، الخروج، إدارة الملف الشخصي، الأدوار.

## التسجيل
- التسجيل برقم الجوال أو البريد الإلكتروني.
- يجب توفّر أحدهما على الأقل (الجوال أو البريد) — تحقّق في RegisterRequest.
- رقم الجوال فريد إذا وُجد (قيد unique).
- البريد فريد إذا وُجد (قيد unique).
- كلمة المرور مشفّرة عبر Hash.
- عند التسجيل كعميلة: أنشئ سجل `customers` مرتبط بالـ user.

## تسجيل الدخول
- يقبل الجوال أو البريد + كلمة المرور.
- يُصدر Sanctum token.

## الملف الشخصي
يمكن للعميلة تعديل: الاسم، الصورة، الجوال، البريد، كلمة المرور.
- تغيير الجوال يخضع لقيد التفرّد.
- رفع الصورة يتبع `docs/01-business-rules.md` بند الوسائط.

## الأدوار / Roles
- `admin` — صلاحيات كاملة.
- `receptionist` — إدارة الحجوزات والعملاء.
- `customer` — الحجز والتقييم.
- `staff` — عرض حجوزاتها.
استخدم spatie/laravel-permission.

## إنشاء الحسابات حسب الدور / Account creation
- **العميلة (customer):** تسجّل نفسها عبر التسجيل العام.
- **الموظفة (staff):** الإدارة تنشئ حسابها من لوحة الإدارة (لا تسجيل ذاتي).
  عند إنشائها يُنشأ user بدور staff + سجل staff مرتبط به.
- **الإدارة (admin/receptionist):** تُنشأ عبر Seeder أو من admin موجود.
لا يستطيع أحد تسجيل نفسه كـ staff أو admin عبر التسجيل العام.

## ملفات متوقعة / Expected files
> كل ملفات هذه الوحدة تحت `app/Domains/Auth/` حسب نمط المعمارية.
- `RegisterRequest`, `LoginRequest`, `UpdateProfileRequest`
- `AuthController`, `ProfileController`
- `RegisterUserAction`, `UpdateProfileAction`
- `UserResource`

## ملاحظات
- سجّل أحداث الدخول/التسجيل في Activity Log.
- طبّق Rate Limiting على مسارات الدخول والتسجيل.
