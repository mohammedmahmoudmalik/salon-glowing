# وحدة الموظفات / Staff Module

## معلومات الموظفة
الاسم، الصورة، التخصص، وصف مختصر، ساعات العمل،
الخدمات المقدّمة، حالة النشاط.

## ساعات العمل
- جدول `staff_working_hours`: يوم الأسبوع + وقت بداية/نهاية + هل يوم إجازة.
- تُستخدم في فحص توفر الحجز.

## التوفر للحجز
لا تظهر الموظفة للحجز إذا:
- كانت غير نشطة (`is_active = false`).
- خارج ساعات عملها للوقت المطلوب.
- لديها حجز متداخل بنفس الوقت.

## العلاقات
- `Staff` belongsTo `User`.
- `Staff` hasMany `StaffWorkingHour`.
- `Staff` belongsToMany `Service`.
- `Staff` hasMany `Booking`.

## ملفات متوقعة
- `StoreStaffRequest`, `UpdateStaffRequest`, `UpdateWorkingHoursRequest`
- `StaffController`, `Admin/StaffController`
- `StaffResource`, `StaffWorkingHourResource`
- `StaffPolicy`

## ملاحظات
- منطق التوفر الزمني يُستهلك من `BookingAvailabilityService`.
- عرض متوسط تقييم الموظفة محسوباً من reviews غير المخفية.
