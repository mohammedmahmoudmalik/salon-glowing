# معايير الكود / Coding Standards

## التنسيق / Formatting
- اتبع PSR-12.
- شغّل `./vendor/bin/pint` قبل كل commit.
- لا أكواد معطّلة أو معلّقة متروكة.

## التسمية / Naming
- الكلاسات: `PascalCase` — `CreateBookingAction`.
- الدوال والمتغيرات: `camelCase` — `checkAvailability()`.
- أعمدة قاعدة البيانات: `snake_case` — `booking_date`.
- الجداول: جمع snake_case — `booking_items`.
- الثوابت و enums: `UPPER_SNAKE` أو enum cases بـ PascalCase.
- المسارات المسماة: `bookings.store`.

## مبادئ الكود / Code principles
- كل دالة لها مسؤولية واحدة واضحة.
- Controllers رفيعة — لا منطق عمل.
- لا منطق في Blade/Livueire إلا العرض.
- استخدم Type Hints و Return Types دائماً.
- استخدم Enums بدلاً من الـ magic strings.
- استخدم Constants لأي قيمة متكررة (Buffer Time، حد الإلغاء).
- تجنّب التداخل العميق — استخدم early return.

## التحقق / Validation
- كل Request له Form Request.
- رسائل التحقق من ملفات الترجمة.
- لا تثق بأي مدخل من المستخدم.

## التعليقات والترجمة / Comments & i18n
- التعليقات بالإنجليزية ومختصرة (لماذا، لا ماذا).
- كل نص يراه المستخدم من ملفات `lang/`.
- لا نصوص عربية أو إنجليزية ثابتة في الكود.

## الأمان / Security
- كلمات المرور عبر `Hash`.
- CSRF على نماذج الويب.
- مرّر كل المدخلات عبر Validation.
- استخدم Eloquent/Query Builder (حماية من SQL Injection).
- escape المخرجات في Blade (حماية من XSS).

## الاختبارات / Testing
- اكتب Feature Tests للمسارات الحرجة.
- وحدة الحجوزات تتطلب اختبارات للحالات الحدّية:
  تعارض المواعيد، Buffer Time، الإلغاء المتأخر، خارج ساعات العمل.
- شغّل `php artisan test` بعد كل وحدة.

## Git
- commits صغيرة وواضحة.
- رسالة commit تصف التغيير بدقة.
