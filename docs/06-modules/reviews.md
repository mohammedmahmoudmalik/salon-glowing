# وحدة التقييمات / Reviews Module

## القواعد
- التقييم بعد اكتمال الحجز فقط (`status = completed`).
- تقييم واحد لكل حجز (قيد unique على `booking_id`).
- التقييم رقم من 1 إلى 5.
- تعليق اختياري.

## الإشراف / Moderation
- الإدارة تحذف أو تخفي التقييمات المسيئة (`is_hidden`).
- التقييمات المخفية لا تظهر للعملاء ولا تدخل في المتوسطات.

## العلاقات
- `Review` belongsTo `Booking` (unique)، `Customer`، `Staff`، `Service`.

## ملفات متوقعة
- `StoreReviewRequest`
- `ReviewController`, `Admin/ReviewController`
- `CreateReviewAction`
- `ReviewResource`
- `ReviewPolicy` — تتحقق أن الحجز مكتمل ويخص العميلة وغير مقيّم سابقاً.

## ملاحظات
- متوسط تقييم الموظفة والخدمة يُحسب من التقييمات غير المخفية فقط.
- سجّل حذف/إخفاء التقييم في Activity Log.
