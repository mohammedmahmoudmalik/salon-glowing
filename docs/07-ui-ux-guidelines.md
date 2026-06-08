# دليل التصميم والتجربة / UI-UX Guidelines

## النمط / Style
- Elegant — أنيق.
- Feminine — أنثوي.
- Luxury — فاخر.
- Minimal — بسيط.
- Mobile-First — يبدأ التصميم من الجوال.

## الألوان المقترحة / Colors
- White — أبيض (خلفية أساسية)
- Beige — بيج (خلفيات ثانوية)
- Rose Gold — ذهبي وردي (لمسات ومميزات)
- Soft Pink — وردي ناعم (أزرار وتفاعلات)

## القواعد
- الواجهة Responsive بالكامل.
- دعم RTL للعربية و LTR للإنجليزية.
- مكوّنات قابلة لإعادة الاستخدام (بطاقة خدمة، بطاقة موظفة، تقييم نجوم...).
- Lazy Loading للصور.
- مساحات بيضاء كافية — تجنّب الازدحام.
- تباين كافٍ للنصوص لسهولة القراءة.

## الواجهة التقنية / Frontend tech
الخيار المعتمد نهائياً: **Blade + Livewire**.
الواجهة تستهلك طبقة Services/Actions مباشرة، والـ REST API تبقى
متاحة بالتوازي لأي عميل مستقبلي. لا تبنِ منطق عمل مكرّراً.

## الصفحات / Pages
- العميل: Home, Services, Service Details, Staff, Booking,
  My Bookings, Profile, Login/Register.
- الإدارة: Dashboard, Services, Staff, Bookings, Offers, Reviews, Settings.

## ملاحظات
- لوحة الإدارة وظيفية وواضحة (الأولوية للسهولة لا الزخرفة).
- نماذج الحجز يجب أن تكون بسيطة وقليلة الخطوات.
