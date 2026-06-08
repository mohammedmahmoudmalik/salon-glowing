# مخطط قاعدة البيانات / Database Schema

## الجداول الأساسية / Core tables

### users
المصادقة الموحّدة. role عبر spatie/laravel-permission.
- id, name, password
- email (nullable, unique), phone (nullable, unique)
- ملاحظة: phone و email كلاهما nullable، لكن يجب توفّر أحدهما
  على الأقل عند التسجيل (تحقّق في Form Request).
- avatar (nullable), is_active (default true), email_verified_at, timestamps
- الأدوار: admin, receptionist, customer, staff

### customers
ملف العميلة. مرتبط بـ user.
- id, user_id (FK), notes (nullable), timestamps

### staff
ملف الموظفة. مرتبط بـ user.
- id, user_id (FK), specialty, bio, is_active, timestamps

### staff_working_hours
ساعات عمل كل موظفة لكل يوم.
- id, staff_id (FK), day_of_week (0-6), start_time, end_time, is_off

### service_categories
- id, name_ar, name_en, slug, is_active, timestamps
- `slug` يُولَّد تلقائياً من `name_en`، فريد، يُستخدم في الروابط.

### services
- id, service_category_id (FK), name_ar, name_en, slug, description_ar,
  description_en, price (decimal), duration_minutes (int), image,
  is_active, timestamps
- `slug` يُولَّد تلقائياً من `name_en`، فريد.

### staff_service (pivot)
الخدمات التي تقدّمها كل موظفة.
- staff_id (FK), service_id (FK)

### bookings
> القرار المعتمد: الحجز الواحد بخدمة واحدة (تطابق قواعد العمل الأصلية).
> جدول booking_items يبقى للمرونة المستقبلية لكن في الإصدار الحالي
> كل حجز له صف واحد فقط في booking_items.
- id, customer_id (FK), staff_id (FK)
- booking_date (date), start_time (time), end_time (time)
- status (enum: pending, confirmed, completed, cancelled, no_show)
- buffer_minutes (int, default 10), total_price (decimal)
- confirmed_at (nullable), cancelled_at (nullable)
- cancelled_by_user_id (nullable FK → users): من قام بالإلغاء فعلياً
  (العميلة نفسها أو موظف إدارة)
- cancellation_reason (nullable)
- timestamps

### booking_items
تفاصيل الخدمة داخل الحجز. في الإصدار الحالي صف واحد لكل حجز.
- id, booking_id (FK), service_id (FK), price, duration_minutes, timestamps

### offers
- id, title_ar, title_en, description_ar, description_en, image
- discount_type (enum: percentage, fixed), discount_value (decimal)
- starts_at (datetime), ends_at (datetime), is_active, timestamps

### offer_service (pivot)
- offer_id (FK), service_id (FK)

### reviews
- id, booking_id (FK, unique), customer_id (FK), staff_id (FK)
- service_id (FK) — مكرّر عمداً (denormalized) لتسهيل حساب
  متوسط تقييم كل خدمة دون المرور عبر booking_items
- rating (1-5), comment (nullable)
- is_hidden (bool, default false), timestamps

### notifications
جدول إشعارات Laravel القياسي.
أنشئ migration الجدول عبر `php artisan make:notifications-table` ثم migrate.
(ملاحظة: الأمر القديم `notifications:table` لم يعد متاحاً في Laravel الحديث).

### settings
إعدادات النظام (ساعات العمل العامة، Buffer الافتراضي ...).
- id, key, value (json), timestamps

### activity_log
من spatie/laravel-activitylog — للـ Audit Logs.

## أهم العلاقات / Key relationships
- `User` hasOne `Customer` / hasOne `Staff`
- `Booking` belongsTo `Customer`, `Staff`
- `Booking` hasMany `BookingItem` (الخدمة تُعرف عبر booking_items)
- `Booking` hasOne `Review`
- `Service` belongsTo `ServiceCategory`
- `Service` belongsToMany `Staff` / `Offer`
- `Staff` hasMany `StaffWorkingHour`
- `Review` belongsTo `Booking` (قيد unique على booking_id)

## قواعد على مستوى قاعدة البيانات
- unique على `users.phone`.
- unique على `reviews.booking_id`.
- فهارس على `bookings (staff_id, booking_date)` و `(customer_id, booking_date)`.
- استخدم enums أو constant classes لحالات الحجز.
