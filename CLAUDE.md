# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Women's salon booking web app (single branch). Laravel 12 + Sanctum + MySQL + Blade/Livewire. Supports Arabic and English; timezone is always `Asia/Riyadh`.

## Commands

```bash
php artisan serve                        # dev server
php artisan test                         # all tests (uses salon_booking_test MySQL DB, not SQLite)
php artisan test --filter BookingTest    # single test class
php artisan test --filter test_customer_can_cancel_before_3_hours  # single test method
./vendor/bin/pint                        # format (run before every commit)
php artisan migrate:fresh --seed         # reset DB with seeders
```

## Architecture

### Dual-layer design (non-obvious)

The app has **two separate controller hierarchies** that share the same Services/Actions:

| Layer | Location | Auth |
|-------|----------|------|
| REST API | `app/Domains/{Module}/Http/Controllers/` | Sanctum Bearer tokens |
| Web (Blade) | `app/Http/Controllers/Web/` | Session (`auth` middleware) |

Both layers call the same Actions/Services. Never duplicate business logic between them.

### Domain structure

```
app/Domains/{Module}/
  Models/
  Services/     ← complex multi-step logic
  Actions/      ← single-purpose operations (e.g. CreateBookingAction)
  Http/Controllers/   ← thin: call Action/Service, return Resource
  Http/Requests/      ← one per endpoint, always
  Http/Resources/     ← every API response goes through a Resource
  Policies/
  Enums/
```

Modules: `Auth`, `Service`, `Staff`, `Booking`, `Offer`, `Review`, `Notification`, `Admin`.

### Livewire components

Live in `app/Livewire/` (not in Domains). Views in `resources/views/livewire/`.
Key components: `BookingForm`, `AvailableSlots`, `BookingTable`, `ReviewTable`, `ServiceTable`, `CustomerTable`, `ServiceCard`, `StaffCard`, `StarRating`.

### Routing

- `routes/api.php` — all under `/api/v1`; admin endpoints require `role:admin|receptionist` middleware
- `routes/web.php` — admin panel under `/admin`; settings sub-routes require `role:admin` only

### Localization

- **API**: `SetLocale` middleware reads `Accept-Language` header or `?lang=` query param.
- **Web**: `SetWebLocale` middleware reads `session('locale')`; switch via `GET /lang/{locale}`.
- All user-facing strings in `lang/ar/` and `lang/en/` (files: `messages.php`, `web.php`, `validation.php`, `notifications.php`). No hardcoded strings.

## Non-negotiable rules

- Controllers are thin — business logic only in Services/Actions.
- Every endpoint has a Form Request (even simple ones).
- Every API response goes through an API Resource.
- Use Policies & Gates for authorization (all registered in `AppServiceProvider`).
- Use `BookingAvailabilityService` for all availability checks — never duplicate this logic.
- Use `DB::transaction` + `lockForUpdate` when creating bookings to prevent double-booking.
- Log important operations with `spatie/laravel-activitylog`.
- No incomplete or placeholder code.

## Key patterns

### Setting model with caching
`Setting::get($key)` / `Setting::set($key, $value)` — caches for 3600s. Call `Cache::forget("setting.{$key}")` on update.

### Booking status flow
```
pending → confirmed → completed
pending/confirmed → cancelled | no_show
```
Always use `BookingStatus` enum (not strings). Customers can only cancel ≥3 hours before the appointment; admin/receptionist can cancel anytime.

### Offer pricing
`ActiveOfferService::getForService(Service $service)` returns the active offer. Offers apply automatically at booking time via `CreateBookingAction`. No coupon codes.

### Slug generation
`service_categories` and `services` auto-generate `slug` from `name_en`. Use slugs in route model binding (e.g. `{service:slug}`).

### Admin role split
- `admin` + `receptionist` — access to most admin routes
- `admin` only — settings page, hero images, and deleting services/staff

### Events & Listeners (registered in AppServiceProvider)
`BookingCreated` → `SendBookingCreatedNotification`
`BookingConfirmed` → `SendBookingConfirmedNotification`
`BookingCancelled` → `SendBookingCancelledNotification`
Appointment reminders run via a Scheduled Command.

## API response shape

```json
{ "success": true, "message": "...", "data": {}, "meta": {} }
{ "success": false, "message": "...", "errors": {} }
```

Pagination default: 15 per page. Always eager-load to avoid N+1.

## Before any task

1. Read the relevant file in `docs/` before writing code.
2. Follow `docs/03-architecture.md` and `docs/05-coding-standards.md`.
3. Update `docs/08-roadmap.md` after completing any task.
4. Work on one module per session.

## Detailed reference

- Business rules: `docs/01-business-rules.md`
- Database schema: `docs/02-database-schema.md`
- Architecture: `docs/03-architecture.md`
- API conventions: `docs/04-api-conventions.md`
- Coding standards: `docs/05-coding-standards.md`
- Module details: `docs/06-modules/{module}.md`
- UI/UX guidelines: `docs/07-ui-ux-guidelines.md`
- Roadmap: `docs/08-roadmap.md`
