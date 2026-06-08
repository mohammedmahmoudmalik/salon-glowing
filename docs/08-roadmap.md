# خارطة الطريق / Roadmap

> ضع علامة [x] على كل مهمة عند إنجازها. اعمل على مرحلة واحدة في كل مرة.

## المرحلة 1 — الأساس / Foundation ✅
- [x] تثبيت الحزم (Sanctum, spatie/permission, spatie/activitylog)
- [x] إعداد `.env` والتوقيت Asia/Riyadh
- [x] إنشاء بنية مجلدات `app/Domains`
- [x] جدول users + الأدوار
- [x] جدول customers + Customer model + علاقة user→customer
- [x] User model: علاقتا customer() و staff()
- [x] المصادقة (تسجيل/دخول/خروج بـ Sanctum)
- [x] التسجيل ينشئ سجل customers تلقائياً (داخل DB::transaction)
- [x] الملف الشخصي
- [x] Global Exception Handler + بنية الاستجابة الموحّدة
- [x] إعداد ملفات الترجمة ar/en
- [x] SetLocale Middleware (Accept-Language / ?lang) على كل مسارات API
- [x] phpunit.xml يستخدم MySQL (قاعدة salon_booking_test)
- [x] Feature Tests لوحدة Auth (16 اختباراً — جميعها ناجحة)

## المرحلة 2 — البيانات الأساسية / Core data ✅
- [x] service_categories
- [x] services + رفع الصور
- [x] staff + ساعات العمل
- [x] staff_service (الخدمات لكل موظفة)
- [x] settings (ساعات عمل الصالون)
- [x] Seeders للبيانات التجريبية

## المرحلة 3 — الحجوزات / Bookings (القلب) ✅
- [x] جداول bookings + booking_items
- [x] BookingAvailabilityService (منطق التوفر)
- [x] CreateBookingAction مع Transaction
- [x] CancelBookingAction (قاعدة 3 ساعات)
- [x] RescheduleBookingAction
- [x] ChangeBookingStatusAction (تأكيد/إكمال/no_show — للإدارة)
- [x] BookingPolicy
- [x] Feature Tests للحالات الحدّية وتحويلات الحالة

## المرحلة 4 — الإضافات / Extras ✅
- [x] offers + offer_service
- [x] ActiveOfferService وتطبيق العرض على السعر
- [x] reviews + الإشراف
- [x] notifications + Events/Listeners
- [x] تذكير المواعيد (Scheduled Command)

## المرحلة 5 — لوحة الإدارة / Admin ✅
- [x] Dashboard (إحصائيات، إيرادات، أداء) — DashboardStatsService + DashboardController (GET /admin/dashboard)
- [x] إدارة الخدمات — AdminServiceController مع تمييز صلاحية الحذف (admin فقط)
- [x] إدارة الموظفات + إنشاء حساب الموظفة — AdminStaffController مع تمييز صلاحية الحذف (admin فقط)
- [x] إدارة الحجوزات (تأكيد/إكمال/إلغاء/no_show) — AdminBookingController + ChangeBookingStatusAction
- [x] إدارة العروض — AdminOfferController
- [x] إدارة التقييمات — AdminReviewController (إخفاء/حذف)
- [x] الإعدادات — SettingController (GET+PATCH /admin/settings، admin فقط)

## المرحلة 6 — الواجهة / Frontend ✅
- [x] تثبيت Livewire وإعداده (التقنية محسومة: Blade + Livewire)
- [x] إعداد RTL/LTR وتبديل اللغة (SetWebLocale middleware، session-based)
- [x] ألوان مخصصة عبر Tailwind v4 @theme (Rose Gold، Beige، Soft Pink)
- [x] صفحات العميل: Home, Services, Service Details, Staff,
      Booking, My Bookings, Profile, Login/Register (8 صفحات)
- [x] واجهة لوحة الإدارة (Dashboard, Bookings, Services, Categories, Staff, Offers, Reviews, Settings)
- [x] Livewire Components: BookingForm, BookingTable, ReviewTable, ServiceCard, StaffCard, StarRating, AvailableSlots
- [x] تطبيق دليل التصميم (الألوان والنمط) من docs/07
- [x] صفحات خطأ مخصصة (404، 403)
- [x] جميع النصوص من ملفات lang (ar/en) — لا نصوص ثابتة

## المرحلة 8 — إعدادات Super Admin / Super Admin Settings ✅
- [x] رفع الشعار (Site Logo) — تخزين في `storage/logos` وعرضه في الهيدر والفوتر وأدمن بانل
- [x] ألوان مخصصة (Primary / Secondary) — حقن CSS variables في `<head>` لتطبيقها على كامل الموقع
- [x] نصوص قسم الهيرو (عنوان / عنوان فرعي / نص الزر) بالعربية والإنجليزية مع Fallback لملفات الترجمة
- [x] اسم الصالون الديناميكي في حقوق الطبع بالفوتر
- [x] مسارات: `POST /admin/settings/branding` و `POST /admin/settings/hero`
- [x] `SettingController::updateBranding()` و `updateHero()` — التحقق والتخزين والنشاط
- [x] قسم Branding وقسم Hero Content في صفحة الإعدادات مع color-picker مزامن
- [x] زر CTA رئيسي في الهيرو (Book Now) بدلاً من الرابط الثانوي فقط

## المرحلة 7 — التحسين والإنهاء / Polish ✅
- [x] Caching: `Setting::get/set()` بمدة 3600 ثانية + Cache::forget عند التعديل
- [x] Caching: ساعات عمل الموظفة (`staff.{id}.working_hours`) في `BookingAvailabilityService` + Cache::forget في `UpdateWorkingHoursAction`
- [x] Eager Loading: إضافة `activeOffers` لـ `ServiceController` و`Admin\ServiceController` — تصفير N+1 في `ServiceResource`
- [x] Eager Loading: `ActiveOfferService::getForService()` يستخدم العلاقة المحمّلة بدلاً من استعلام جديد
- [x] Pagination: جميع endpoints القوائم تستخدم `paginate(15)` أو `paginate(20)` ✓
- [x] تشغيل Pint — نظيف تماماً
- [x] إصلاح Bug: `BookingTable::changeStatus()` يمرر `BookingStatus::from()` بدلاً من string خام
- [x] إصلاح Bug: `Web\Admin\BookingController::changeStatus()` نفس الإصلاح
- [x] أمان: إضافة `role:admin|receptionist` middleware لجميع مسارات `/api/v1/admin/*`
- [x] أمان: CSRF على جميع نماذج Blade ✓ | XSS: `{{ }}` في كل Blade ✓ | UserResource لا يكشف password ✓
- [x] Migration جديد: indexes على `bookings(status)` و `reviews(is_hidden)` للأداء
- [x] اختبارات: 10 اختبارات جديدة (حد الـ3 ساعات بالضبط، انتقالات الحالة، صلاحيات Admin API)
- [x] النتيجة النهائية: **76/76 اختبار ناجح، 175 assertion، Pint نظيف**

## المرحلة 9 — دور المالك (Owner) ✅
- [x] إضافة دور `owner` في `RolesAndPermissionsSeeder`
- [x] إضافة مستخدم تجريبي `owner@salon.test` في `AdminUserSeeder`
- [x] إعادة هيكلة `routes/web.php`: المجموعة الخارجية `admin|receptionist|owner`، مجموعات فرعية لكل مستوى صلاحية
- [x] `RedirectAdminToPanel`: تحويل المالك مباشرةً إلى صفحة الإعدادات
- [x] Sidebar: إظهار رابط الإعدادات للمالك فقط
- [x] صفحة الإعدادات: إخفاء أقسام ساعات العمل والروابط الاجتماعية وصور الهيرو من المالك
- [x] `SettingPolicy`: تحديثها لتسمح للمالك بالعرض والتعديل
- [x] المالك يتحكم فقط في **هوية الصالون** (Branding) و**نصوص الصفحة الرئيسية** (Hero Text)

## المرحلة 10 — أدوات النشر (Owner Tools) ✅
- [x] **الدولة والعملة**: إضافة `salon_country` للإعدادات (SA/AE/EG) — يُغيّر رمز العملة في كامل الموقع
- [x] `currency()` global helper في `app/helpers.php` مسجّل في `composer.json` autoload
- [x] استبدال جميع `__('web.sar')` بـ `currency()` في 18 ملف Blade
- [x] `SettingController::updateCountry()` — `POST /admin/settings/country` (admin + owner)
- [x] **صفحة إعداد الخدمات** `/admin/setup` (admin + owner):
  - Livewire `ServiceQuickSetup` — إدخال يدوي ديناميكي مع `firstOrCreate`
  - `ImportServicesFromCsvAction` — رفع CSV مع UTF-8 BOM template
  - `ExportServicesFixtureAction` — تصدير `database/fixtures/services.json` للنشر على Railway
  - `ServicesFixtureSeeder` — يقرأ الـ fixture عند كل deploy (idempotent)
  - زر مسح: `ClearAllServicesAction` يحذف الكل بالترتيب الصحيح ويُنظّف Storage
- [x] **صفحة البيانات التجريبية** `/admin/demo` (owner فقط):
  - `GenerateDemoDataAction` — يولّد عملاء/حجوزات/تقييمات/عروض مرتبطة ببعضها
  - البيانات مبنية على الدولة المختارة (أسماء + أرقام هواتف)
  - توزيع الحجوزات: 3/8 مكتملة، 1/8 ملغاة، 1/8 لم تحضر، 1/8 مؤكدة، 2/8 معلقة
  - تقييمات لـ 65% من الحجوزات المكتملة بأوزان (5★: 55%، 4★: 35%، 3★: 10%)
  - زر مسح: `ClearDemoDataAction` يحذف users(customer) بالتسلسل الصحيح

## المرحلة 11 — فيديوهات الصالون / Salon Videos ✅
- [x] `UploadOwnerVideoAction` — رفع الفيديو إلى `storage/public/owner-videos` وحذف القديم
- [x] `SettingController::updateOwnerVideo()` و `deleteOwnerVideo()` — التحقق (mp4/webm/mov/ogg، max 100MB) والتخزين
- [x] مسارات: `POST /admin/settings/owner-video` و `DELETE /admin/settings/owner-video` (admin فقط)
- [x] قسم رفع الفيديو في صفحة الإعدادات مع معاينة الفيديو الحالي وزر الحذف (admin فقط)
- [x] قسم "قصتنا" في الصفحة الرئيسية: تخطيط عمودين (فيديو + نص) مع أنيميشن دخول عند التمرير
- [x] فيديو مخصص: overlay للتشغيل، controls تظهر بعد الضغط، بدون autoplay (الصوت مفعّل)
- [x] القسم يختفي تلقائياً إذا لم يُرفع فيديو بعد (conditional rendering)
- [x] `UploadTourVideoAction` — رفع فيديو جولة الصالون إلى `storage/public/tour-videos`
- [x] `SettingController::updateTourVideo()` و `deleteTourVideo()` — مسارات تحت `role:admin`
- [x] فيديو الجولة كخلفية autoplay muted loop في الـ Hero — يحل محل الـ carousel تلقائياً
- [x] `.hero-video-bg` CSS في `app.css` — object-fit cover مع translate -50%
- [x] قسم "قصة الصالون" بتبويبين (جولة + المالكة) عند رفع كلا الفيديوهين
- [x] Tab جولة الصالون: autoplay muted loop + زر تبديل الصوت (🔇/🔊)
- [x] Tab كلمة المالكة: overlay زر تشغيل + صوت فوري عند الضغط
- [x] تصميم mobile-first: مكدّس على الموبايل، جنبًا إلى جنب على الـ desktop
- [x] أنيميشن دخول بـ IntersectionObserver على كل العناصر
