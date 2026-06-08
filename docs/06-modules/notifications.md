# وحدة الإشعارات / Notifications Module

## متى تُرسل الإشعارات
- إنشاء الحجز.
- تأكيد الحجز.
- إلغاء الحجز.
- تذكير بالموعد.

## القنوات
- Email (مفعّل).
- SMS — اختياري مستقبلاً (اترك قناة قابلة للإضافة لاحقاً).

## التنفيذ
- استخدم Laravel Notifications.
- اربط الإشعارات بـ Events:
  `BookingCreated`, `BookingConfirmed`, `BookingCancelled`.
- تذكير الموعد عبر Scheduled Command + Queue.
- استخدم Queue لإرسال الإشعارات (لا ترسل بشكل متزامن).

## ملفات متوقعة
- `BookingCreatedNotification`
- `BookingConfirmedNotification`
- `BookingCancelledNotification`
- `BookingReminderNotification`
- `SendBookingRemindersCommand` (يعمل عبر scheduler)
- Listeners لكل Event.

## ملاحظات
- محتوى الإشعارات مترجم (ar/en) حسب لغة العميلة.
- جدول `notifications` القياسي لتخزين الإشعارات داخل التطبيق إن لزم.
