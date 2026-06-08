# المعمارية / Architecture

## القرار المعماري الأساسي / Core architectural decision
> النظام **API-first**: تُبنى REST API كاملة دائماً (المرحلة 1–5).
> الواجهة (المرحلة 6) تُبنى بـ Blade + Livewire وتستهلك نفس منطق
> الخدمات/الـ Actions مباشرة، وتبقى الـ API متاحة لأي عميل مستقبلي
> (تطبيق جوال مثلاً). لا تبنِ منطقاً مكرّراً بين الويب والـ API —
> المنطق في طبقة Services/Actions والاثنان يستهلكانها.

## النمط / Pattern: Feature-Based + Service Layer

```
app/Domains/{Module}/
  Models/                ← Eloquent models
  Services/              ← منطق العمل المعقّد متعدد الخطوات
  Actions/               ← عملية واحدة محددة (Single-purpose)
  Http/
    Controllers/         ← رفيعة، تستدعي Service/Action فقط
    Requests/            ← Form Request validation
    Resources/           ← API Resources للاستجابات
  Policies/              ← صلاحيات الوصول
  Enums/                 ← BookingStatus, DiscountType ...
```

الوحدات: `Auth`, `Service`, `Staff`, `Booking`, `Offer`, `Review`, `Notification`, `Admin`.

## مسؤوليات الطبقات / Layer responsibilities

| الطبقة | المسؤولية |
|--------|-----------|
| Controller | استقبال الطلب، استدعاء Service/Action، إرجاع Resource |
| Form Request | التحقق من المدخلات والصلاحية الأولية |
| Action | عملية واحدة (مثل CreateBookingAction) |
| Service | منطق أعمق متعدد الخطوات أو مشترك |
| Policy | من يملك صلاحية فعل ماذا |
| Resource | تشكيل بنية الاستجابة |
| Model | العلاقات، الـ casts، الـ scopes فقط |

## القواعد الصارمة / Strict rules
- لا منطق عمل داخل Controllers أو Models.
- كل endpoint له Form Request حتى لو بسيطاً.
- كل استجابة API عبر API Resource.
- Repository Pattern فقط عند الحاجة الحقيقية (استعلامات معقّدة قابلة لإعادة الاستخدام).
- Global Exception Handler موحّد يرجع بنية الاستجابة المعيارية.
- استخدم Database Transactions في العمليات الحرجة (إنشاء الحجز).
- استخدم Events/Listeners للإشعارات (إنشاء حجز → إرسال إشعار).

## التحقق من التوفر / Availability logic
منطق التحقق من تعارض المواعيد يكون في `BookingAvailabilityService`
ولا يتكرّر في أي مكان آخر.

## مثال تدفّق إنشاء حجز
```
Controller → StoreBookingRequest (validation)
           → CreateBookingAction
               → BookingAvailabilityService::check()
               → DB::transaction(create booking + items)
               → event(BookingCreated)
           → BookingResource
```
