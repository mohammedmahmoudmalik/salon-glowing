# وحدة الحجوزات / Bookings Module

> الوحدة الأهم والأكثر حساسية. اقرأها كاملة قبل أي كود.

## إنشاء حجز / Creating a booking
المدخلات المطلوبة: الخدمة، الموظفة، التاريخ، الوقت.

### قواعد التحقق (كلها إلزامية)
1. الموعد غير محجوز مسبقاً لنفس الموظفة.
2. الموظفة ليس لديها حجز متداخل بنفس الوقت.
3. الموعد ضمن ساعات عمل الموظفة وضمن ساعات عمل الصالون.
4. احترام مدة الخدمة (`end_time = start_time + duration`).
5. احترام Buffer Time بين الحجوزات (افتراضي 10 دقائق) — لا تداخل
   حتى مع فترة الـ Buffer.
6. الموظفة نشطة والخدمة مفعّلة.

كل هذا المنطق في `BookingAvailabilityService` — لا تكرّره.

## الحالات / Statuses
```
pending → confirmed → completed
pending/confirmed → cancelled
pending/confirmed → no_show
```
استخدم enum `BookingStatus`.

## تأكيد الحجز / Confirmation
- الحجز الجديد يبدأ بحالة `pending`.
- الإدارة (admin/receptionist) تؤكد الحجز يدوياً → `confirmed`.
- عند التأكيد: سجّل `confirmed_at` وأطلق حدث `BookingConfirmed`.
- العميلة لا تستطيع تأكيد حجزها بنفسها.

## NoShow
- إذا لم تحضر العميلة: الإدارة فقط تحوّل الحجز إلى `no_show`.
- يُسمح بذلك من حالة `pending` أو `confirmed`.
- لا يُسمح بذلك قبل انتهاء وقت الموعد.

## الإكمال / Completion
- الإدارة تحوّل الحجز إلى `completed` بعد تقديم الخدمة.
- التقييم متاح للعميلة فقط بعد أن يصبح الحجز `completed`.

## الإلغاء / Cancellation
- العميلة: مسموح فقط قبل الموعد بـ 3 ساعات على الأقل.
- بعد ذلك: ممنوع للعميلة.
- الإدارة: تلغي أي حجز في أي وقت.
- عند الإلغاء: سجّل `cancelled_at` و `cancelled_by_user_id` و `cancellation_reason`.

## إعادة الجدولة / Reschedule
- مسموحة فقط إذا الوقت الجديد متاح (نفس فحص التوفر).
- ممنوعة بعد بدء الموعد.

## التسعير
- `total_price` يُحسب من الخدمات + تطبيق العروض السارية تلقائياً.
- راجع `docs/06-modules/offers.md`.

## الأحداث / Events
- `BookingCreated` → إشعار.
- `BookingConfirmed` → إشعار.
- `BookingCancelled` → إشعار.
- تذكير الموعد عبر Scheduled Job.

## ملفات متوقعة / Expected files
- `StoreBookingRequest`, `RescheduleBookingRequest`, `CancelBookingRequest`
- `BookingController` (رفيع للعميلة)، `Admin/BookingController` (للإدارة)
- `CreateBookingAction`, `CancelBookingAction`, `RescheduleBookingAction`
- `ChangeBookingStatusAction` (تأكيد/إكمال/no_show — للإدارة)
- `BookingAvailabilityService`
- `BookingPolicy`
- `BookingResource`, `BookingItemResource`
- `BookingStatus` enum

## ملاحظات حرجة
- استخدم `DB::transaction` عند إنشاء الحجز + booking_items.
- استخدم قفل للصفوف (lockForUpdate) لتفادي double-booking تحت التزامن.
- سجّل كل تغيير حالة في Activity Log.
- اكتب Feature Tests لكل حالة حدّية.
