# دليل إنشاء صالون جديد من القالب

---

## 1. انسخ المشروع

كل صالون مشروع مستقل بمجلد خاص به.

```powershell
# ينسخ كل شيء ما عدا .git و node_modules و .env (vendor يُنسخ لتجنب إعادة التحميل)
robocopy "d:\Laravel Projects\Saloons\salon-template" "d:\Laravel Projects\Saloons\salon-nour" /E /XD .git node_modules /XF .env
```

> **تنبيه:** استبدل `salon-nour` باسم الصالون. لا تستخدم حروفاً عربية في أسماء المجلدات — تسبب أخطاء مع Laravel على Windows.
> افتح الـ terminal داخل المجلد الجديد مباشرةً (كليك يمين → Open in Terminal) قبل تنفيذ باقي الخطوات.

---

## 2. تثبيت الحزم

```powershell
composer dump-autoload
npm install
```

---

## 3. إعداد البيئة المحلية

```powershell
copy .env.example .env
php artisan key:generate

# إعادة إنشاء رابط Storage بشكل صحيح (robocopy ينسخ المحتوى بدلاً من الرابط)
Remove-Item -Recurse -Force public\storage -ErrorAction SilentlyContinue
php artisan storage:link
```

افتح `.env` وعدّل هذه القيم:

```env
APP_NAME="اسم الصالون"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=ar
APP_FALLBACK_LOCALE=ar

DB_DATABASE=salon_nour
DB_USERNAME=root
DB_PASSWORD=

# الدولة — تحدد العملة المعروضة في كامل الموقع
# SA = السعودية (ر.س) | AE = الإمارات (د.إ) | EG = مصر (ج.م)
SALON_COUNTRY=SA

# اسم الصالون في إعدادات الـ Seeder
SALON_NAME="اسم الصالون"

# بيانات مدير النظام
ADMIN_NAME="مدير الصالون"
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=StrongPassword123

# بيانات المالك
OWNER_NAME="صاحب الصالون"
OWNER_EMAIL=owner@salon.com
OWNER_PASSWORD=OwnerPassword123
```

---

## 4. تجهيز قاعدة البيانات

أولاً أنشئ قاعدة البيانات يدوياً في phpMyAdmin أو MySQL Workbench باسم مطابق لـ `DB_DATABASE` في `.env` (مثلاً `salon_nour`).

ثم شغّل:

```powershell
php artisan migrate:fresh --seed
```

يُنشئ تلقائياً:
- الأدوار: `admin`, `receptionist`, `owner`, `customer`
- مستخدم `admin` من بيانات `.env`
- مستخدم `owner` من بيانات `.env`
- الإعدادات الافتراضية بما فيها `salon_country` و `salon_name`

---

## 5. إعداد هوية الصالون (Branding)

سجّل دخول كـ **owner** على `http://localhost:8000/admin` ثم:

**الدولة والعملة** — الإعدادات ← قسم "الدولة":
- اختر الدولة، تُحفظ تلقائياً وتُغيّر رمز العملة في كامل الموقع.

**الهوية البصرية** — الإعدادات ← قسم "Branding":
- رفع الشعار
- تخصيص الألوان الأساسية
- نصوص قسم الهيرو بالعربية والإنجليزية
- ساعات العمل والـ buffer بين الحجوزات
- روابط التواصل الاجتماعي

**صور الهيرو** — `/admin/hero-images`:
- رفع صور الخلفية الدوارة في الصفحة الرئيسية

---

## 6. إدخال خدمات الصالون

انتقل إلى `/admin/setup`:

**الخيار 1: إدخال يدوي**
- أضف الفئات والخدمات مع الأسعار والمدد عبر النموذج الديناميكي
- اضغط "حفظ الكل"

**الخيار 2: استيراد CSV**
- حمّل ملف القالب (زر "تحميل القالب")
- املأ بيانات الخدمات وارفع الملف

**تصدير JSON للنشر ← خطوة أساسية قبل Railway:**

بعد إدخال جميع الخدمات، اضغط زر **"تصدير بيانات JSON"** (أسفل الصفحة دائماً).
يحفظ الملف في `database/fixtures/services.json` — ارفعه على GitHub قبل أول deploy على Railway.

---

## 7. بيانات تجريبية (اختياري)

انتقل إلى `/admin/demo` (owner فقط) وحدد:
- عدد العملاء (1–100)
- عدد الحجوزات (1–500)
- عدد العروض (0–10)

البيانات مبنية على الدولة المختارة (أسماء + أرقام هواتف مناسبة).

لحذفها لاحقاً: زر **"مسح البيانات التجريبية"** في نفس الصفحة.

---

## 8. النشر على Railway

### أ. أنشئ مشروع جديد في Railway

1. **New Project → Empty Project**
2. أضف **MySQL** plugin
3. أضف **GitHub repo** service ← اختر الـ repo الخاص بهذا الصالون

### ب. متغيرات البيئة (Variables)

```env
APP_NAME="اسم الصالون"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<railway-domain>
APP_LOCALE=ar
APP_FALLBACK_LOCALE=ar

# احصل عليه بتشغيل: php artisan key:generate --show
APP_KEY=base64:...

SALON_NAME="اسم الصالون"
SALON_COUNTRY=SA

OWNER_NAME="صاحب الصالون"
OWNER_EMAIL=owner@salon.com
OWNER_PASSWORD=StrongOwnerPassword

ADMIN_NAME="مدير الصالون"
ADMIN_EMAIL=admin@salon.com
ADMIN_PASSWORD=StrongAdminPassword

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

RUN_SEED=true

FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
```

> **ملاحظة:** `SEED_CLASS` غير محدد — يعني Railway سيشغّل `DatabaseSeeder` كاملاً (الأفضل).

> **تحذير — Storage على Railway:** Railway يستخدم filesystem مؤقتاً، أي صور ترفعها (شعار، صور هيرو) **ستُمحى عند كل deploy جديد**. للإنتاج الحقيقي استخدم S3 أو Cloudflare R2 واضبط `FILESYSTEM_DISK=s3` مع متغيرات `AWS_*`. راجع `.env.example` لتفاصيل إعداد R2.

### ج. ما يحدث عند كل deploy تلقائياً (`start.sh`)

```
php artisan migrate --force
  ↓
php artisan db:seed --force          ← يشغّل DatabaseSeeder
  ├─ FreshInstallSeeder
  │    → ينشئ/يحدّث admin + owner + الإعدادات  (updateOrCreate — آمن للتكرار)
  └─ ServicesFixtureSeeder
       → يقرأ database/fixtures/services.json   (firstOrCreate — آمن للتكرار)
```

### د. بعد أول deploy ناجح

سجّل دخول كـ owner وأكمل ما لا يُحفظ في git:
- رفع الشعار وصور الهيرو
- روابط التواصل الاجتماعي

---

## 9. لصالون ثانٍ جديد

كرّر الخطوات 1–8 من الصفر — نفس أمر النسخ مع تغيير اسم المجلد فقط:

```powershell
robocopy "d:\Laravel Projects\Saloons\salon-template" "d:\Laravel Projects\Saloons\salon-layla" /E /XD .git node_modules /XF .env
```

كل صالون له:
- مجلد مستقل على جهازك
- Repo مستقل على GitHub
- Project مستقل في Railway
- `database/fixtures/services.json` خاص بخدماته

---

## ملخص سريع

| المهمة | الطريقة |
|--------|---------|
| تثبيت + seed | `php artisan migrate:fresh --seed` |
| تشغيل محلي | `php artisan serve` |
| إدخال الخدمات | `/admin/setup` (يدوي أو CSV) |
| تصدير للـ Railway | زر "تصدير JSON" ← ارفع `services.json` على GitHub |
| بيانات تجريبية | `/admin/demo` |
| حذف بيانات الخدمات | `/admin/setup` ← زر "مسح جميع البيانات" |
| حذف البيانات التجريبية | `/admin/demo` ← زر "مسح البيانات التجريبية" |
| تشغيل الاختبارات | `php artisan test` |
| فورمات الكود | `./vendor/bin/pint` |
