# نصوص Claude Code الجاهزة / Ready Prompts

> انسخ كل نص والصقه في Claude Code عند الوصول لتلك المرحلة.
> اعمل على **مرحلة واحدة في كل جلسة**. لا تطلب كل شيء دفعة واحدة.

---

## نص التحقق (قبل البدء)

> اقرأ ملف CLAUDE.md وكل الملفات داخل مجلد docs. لخّص لي المشروع:
> المستخدمين، الوحدات، المعمارية المطلوبة، والقرار المعماري الأساسي.
> لا تكتب أي كود الآن.

---

## المرحلة 1 — الأساس والمصادقة

> اقرأ docs/03-architecture.md و docs/05-coding-standards.md و
> docs/06-modules/auth.md و docs/02-database-schema.md و
> docs/04-api-conventions.md. نفّذ المرحلة 1 من docs/08-roadmap.md بالكامل:
>
> 1. ثبّت الحزم: laravel/sanctum و spatie/laravel-permission و
>    spatie/laravel-activitylog، وانشر ملفاتها.
> 2. أنشئ بنية مجلدات app/Domains كما في docs/03-architecture.md.
> 3. عدّل migration جدول users الموجود ليضم الحقول المطلوبة في
>    docs/02-database-schema.md (email و phone كلاهما nullable و unique،
>    avatar، is_active). أنشئ الأدوار: admin, receptionist, customer, staff.
> 4. نفّذ المصادقة بالكامل تحت app/Domains/Auth: تسجيل ودخول بالجوال
>    أو البريد (يجب توفّر أحدهما على الأقل)، خروج، عبر Sanctum.
> 5. نفّذ الملف الشخصي (تعديل الاسم والصورة والجوال والبريد وكلمة المرور).
> 6. أنشئ Global Exception Handler وبنية الاستجابة الموحّدة كما في
>    docs/04-api-conventions.md.
> 7. جهّز ملفات الترجمة ar و en.
>
> اتبع نمط Feature-Based + Service Layer بدقة: Controllers رفيعة،
> Form Requests للتحقق، API Resources للاستجابات، Actions للعمليات.
> بعد الانتهاء أنشئ Seeder لمستخدم admin تجريبي ببيانات اعتماد معروفة
> واطبعها لي، ثم اعرض ملخص ما أنشأت.

---

## المرحلة 2 — الخدمات والموظفات

> اقرأ docs/06-modules/services.md و docs/06-modules/staff.md و
> docs/02-database-schema.md. نفّذ المرحلة 2 من docs/08-roadmap.md:
>
> 1. جداول ونماذج service_categories و services. كلاهما له slug
>    يُولّد تلقائياً من name_en ويكون فريداً. رفع الصور (jpg, png, webp
>    + حد للحجم).
> 2. جداول ونماذج staff و staff_working_hours و pivot table staff_service.
> 3. جدول settings لساعات عمل الصالون العامة و Buffer الافتراضي.
> 4. endpoints عامة لتصفّح الخدمات والفئات والموظفات (مع scope active فقط).
> 5. endpoints محمية للإدارة لإدارة الخدمات والموظفات وساعات العمل.
> 6. Policies لكل وحدة، Form Requests، API Resources.
> 7. Seeders ببيانات تجريبية واقعية: فئات وخدمات وموظفات وساعات عمل.
>
> التزم بمعايير docs/05-coding-standards.md و docs/04-api-conventions.md.
> استخدم Eager Loading في كل القوائم. بعد الانتهاء شغّل
> php artisan migrate:fresh --seed وتأكد أنه يعمل، ثم اعرض ملخصاً.

---

## المرحلة 3 — الحجوزات (الأهم — خذ وقتك)

> اقرأ docs/06-modules/bookings.md كاملاً بعناية، و docs/02-database-schema.md
> و docs/03-architecture.md. هذه أهم وحدة في المشروع. نفّذ المرحلة 3
> من docs/08-roadmap.md:
>
> 1. جداول ونماذج bookings و booking_items، و enum BookingStatus.
>    الحجز بخدمة واحدة عبر booking_items — لا تضع service_id في جدول bookings.
> 2. BookingAvailabilityService يحتوي كل منطق فحص التوفر: عدم تداخل
>    المواعيد، حجز واحد للموظفة في الوقت نفسه، ضمن ساعات العمل، احترام
>    مدة الخدمة، احترام Buffer Time (10 دقائق). لا تكرّر هذا المنطق.
> 3. CreateBookingAction باستخدام DB::transaction و lockForUpdate لمنع
>    double-booking تحت التزامن.
> 4. CancelBookingAction: العميلة تلغي قبل الموعد بـ 3 ساعات على الأقل
>    فقط، الإدارة تلغي أي وقت. سجّل cancelled_at و cancelled_by_user_id
>    و cancellation_reason.
> 5. RescheduleBookingAction: مسموح فقط إذا الوقت الجديد متاح وقبل بدء الموعد.
> 6. ChangeBookingStatusAction للإدارة فقط: تأكيد الحجز (pending → confirmed
>    مع تسجيل confirmed_at)، الإكمال (→ completed)، وتحويله إلى no_show
>    (من pending أو confirmed، ولا يُسمح قبل انتهاء وقت الموعد). الحجز
>    الجديد يبدأ pending.
> 7. BookingPolicy، Form Requests، API Resources.
> 8. endpoints: إنشاء، إعادة جدولة، إلغاء، عرض حجوزاتي، عرض حجز واحد،
>    ومسارات الإدارة لتغيير الحالة.
>
> ثم اكتب Feature Tests تغطّي الحالات الحدّية: حجز موعد متداخل، تجاوز
> Buffer Time، الحجز خارج ساعات العمل، الإلغاء قبل 3 ساعات وبعدها،
> إعادة جدولة بعد بدء الموعد، وكل تحويلات الحالة. شغّل php artisan test
> وتأكد أن كل الاختبارات تنجح، ثم اعرض النتيجة.

---

## المرحلة 4 — العروض والتقييمات والإشعارات

> اقرأ docs/06-modules/offers.md و docs/06-modules/reviews.md و
> docs/06-modules/notifications.md. نفّذ المرحلة 4 من docs/08-roadmap.md:
>
> 1. جداول ونماذج offers و offer_service. ActiveOfferService يحسب
>    العرض الساري لخدمة معيّنة ويطبّقه على السعر تلقائياً. عند تداخل
>    عرضين على نفس الخدمة طبّق العرض الأفضل للعميلة.
> 2. جدول ونموذج reviews: تقييم بعد اكتمال الحجز فقط (status = completed)،
>    تقييم واحد لكل حجز (قيد unique على booking_id)، من 1 إلى 5.
>    ReviewPolicy تتحقق من ذلك. إمكانية الإدارة لإخفاء/حذف التقييمات.
> 3. الإشعارات عبر Laravel Notifications مربوطة بـ Events: BookingCreated,
>    BookingConfirmed, BookingCancelled. أنشئ جدول الإشعارات عبر
>    php artisan make:notifications-table. أضف SendBookingRemindersCommand
>    للتذكير عبر Scheduler. استخدم Queue للإرسال.
> 4. تأكد أن سعر الخدمة المعروض للعميلة يمر عبر ActiveOfferService.
>
> اكتب اختبارات لقواعد التقييم والعروض. التزم بمعايير المشروع.
> بعد الانتهاء اعرض ملخصاً.

---

## المرحلة 5 — لوحة الإدارة

> اقرأ docs/06-modules/admin.md. نفّذ المرحلة 5 من docs/08-roadmap.md:
>
> 1. Dashboard endpoint بكل الإحصائيات: عدد الحجوزات، الحجوزات اليومية،
>    الخدمات الأكثر طلباً، تقييمات العملاء، أداء الموظفات، الإيرادات
>    اليومية والشهرية. الإيرادات تُحتسب من الحجوزات المكتملة (completed)
>    فقط، من total_price بعد العروض.
> 2. DashboardStatsService يجمّع كل الإحصائيات.
> 3. شاشات إدارة الخدمات والموظفات (مع إنشاء حساب الموظفة) والحجوزات
>    والعروض والتقييمات والإعدادات.
> 4. كل مسارات الإدارة تحت /api/v1/admin ومحمية بـ Policies & Gates مع
>    تمييز صلاحيات admin عن receptionist.
>
> استخدم Caching للإحصائيات و Eager Loading. بعد الانتهاء اعرض ملخص
> الـ endpoints.

---

## المرحلة 6 — الواجهة

> اقرأ docs/07-ui-ux-guidelines.md. نفّذ المرحلة 6 من docs/08-roadmap.md.
> التقنية محسومة: Blade + Livewire، والواجهة تستهلك طبقة Services/Actions
> مباشرة دون منطق مكرّر.
>
> 1. ثبّت Livewire وأعدّه.
> 2. جهّز دعم RTL/LTR وتبديل اللغة بين العربية والإنجليزية.
> 3. ابنِ صفحات العميل: Home, Services, Service Details, Staff, Booking,
>    My Bookings, Profile, Login/Register.
> 4. ابنِ واجهة لوحة الإدارة.
> 5. طبّق دليل التصميم: الألوان (White, Beige, Rose Gold, Soft Pink)
>    والنمط (Elegant, Feminine, Luxury, Minimal, Mobile-First).
>
> اجعل كل شيء Responsive و Mobile-First. استخدم مكوّنات قابلة لإعادة
> الاستخدام. بعد الانتهاء اعرض ملخصاً.

---

## المرحلة 7 — التحسين والإنهاء

> نفّذ المرحلة 7 من docs/08-roadmap.md، مرحلة المراجعة النهائية:
>
> 1. أضف Caching للبيانات الثابتة (الفئات، الإعدادات، ساعات العمل).
> 2. راجع كل القوائم وتأكد من وجود Eager Loading و Pagination في كل مكان.
> 3. شغّل ./vendor/bin/pint على المشروع كاملاً.
> 4. راجع التغطية بالاختبارات للمسارات الحرجة، وأضف ما ينقص خاصة في
>    وحدة الحجوزات.
> 5. راجع الأمان: CSRF، حماية XSS، Validation على كل المدخلات، عدم كشف
>    بيانات حساسة في الاستجابات.
> 6. شغّل php artisan test وتأكد أن كل شيء ينجح.
>
> بعد كل ذلك حدّث docs/08-roadmap.md وضع علامة على كل المهام المكتملة،
> واعرض تقريراً نهائياً بحالة المشروع وأي ملاحظات أو نواقص.

---

## نص بعد كل مرحلة

> شغّل php artisan migrate:fresh --seed ثم php artisan test وأخبرني
> بالنتيجة. ثم شغّل ./vendor/bin/pint. وأخيراً حدّث docs/08-roadmap.md
> وضع علامة على المهام المكتملة في هذه المرحلة.

---

## نص عند حدوث خطأ

> ظهر هذا الخطأ، حلّله وأصلحه دون كسر أي شيء آخر:
> [الصق نص الخطأ كاملاً هنا]
