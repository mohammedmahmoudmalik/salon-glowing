# معايير الـ API / API Conventions

## الإصدار والمسارات / Versioning
- جميع المسارات تحت `/api/v1`.
- RESTful: GET, POST, PUT/PATCH, DELETE.
- أسماء المسارات بصيغة الجمع: `/api/v1/bookings`.

## شكل الاستجابة الموحّد / Standard response

نجاح:
```json
{
  "success": true,
  "message": "تم بنجاح",
  "data": { },
  "meta": { }
}
```

خطأ:
```json
{
  "success": false,
  "message": "وصف الخطأ",
  "errors": { "field": ["..."] }
}
```

استخدم Trait أو Base Resource موحّد لإنتاج هذا الشكل.

## رموز HTTP / Status codes
- 200 نجاح، 201 إنشاء، 204 حذف بدون محتوى.
- 401 غير مصادق، 403 ممنوع، 404 غير موجود.
- 422 خطأ تحقق، 429 تجاوز الحد، 500 خطأ خادم.

## المصادقة / Authentication
- Laravel Sanctum (Bearer tokens).
- مسارات عامة: تصفّح الخدمات والموظفات والعروض.
- مسارات محمية: الحجوزات، الملف الشخصي، التقييمات، لوحة الإدارة.

## القواعد العامة / General rules
- Pagination لكل القوائم (default 15 لكل صفحة).
- Eager Loading دائماً لتفادي مشكلة N+1.
- معالجة الأخطاء مركزياً عبر Global Exception Handler.
- رسائل الأخطاء مترجمة (lang files).
- Rate Limiting على مسارات المصادقة وإنشاء الحجز.
- لا تكشف بيانات داخلية حساسة في الاستجابات.

## معايير الترجمة / Localization
- يقرأ النظام لغة الاستجابة من header `Accept-Language` أو معامل `lang`.
- جميع رسائل API تأتي من ملفات `lang/ar` و `lang/en`.
