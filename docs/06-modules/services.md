# وحدة الخدمات / Services Module

## بنية الخدمة
كل خدمة: الاسم (ar/en)، الوصف (ar/en)، السعر، المدة بالدقائق،
صورة، الفئة، حالة التفعيل.

## الفئات / Categories
service_categories مع أمثلة: Hair, Makeup, Nails, Facial, Spa.
كل فئة لها اسم بالعربية والإنجليزية وحالة تفعيل.

## التوفر
- الإدارة تفعّل/تعطّل أي خدمة.
- الخدمات المعطّلة لا تظهر للعملاء (scope `active`).
- الفئات المعطّلة لا تظهر للعملاء.

## العلاقات
- `Service` belongsTo `ServiceCategory`.
- `Service` belongsToMany `Staff`.
- `Service` belongsToMany `Offer`.

## ملفات متوقعة
- `StoreServiceRequest`, `UpdateServiceRequest`
- `ServiceController`, `Admin/ServiceController`
- `ServiceResource`, `ServiceCategoryResource`
- `ServicePolicy`

## ملاحظات
- رفع الصور يتبع قواعد الوسائط (jpg/png/webp + حد للحجم).
- استخدم Eager Loading للفئة والعروض عند عرض القوائم.
- العرض الساري يُعرض ضمن بيانات الخدمة.
