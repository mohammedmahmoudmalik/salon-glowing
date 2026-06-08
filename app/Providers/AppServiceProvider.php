<?php

namespace App\Providers;

use App\Domains\Admin\Models\Setting;
use App\Domains\Admin\Policies\SettingPolicy;
use App\Domains\Auth\Models\User;
use App\Domains\Booking\Events\BookingCancelled;
use App\Domains\Booking\Events\BookingConfirmed;
use App\Domains\Booking\Events\BookingCreated;
use App\Domains\Booking\Models\Booking;
use App\Domains\Booking\Policies\BookingPolicy;
use App\Domains\Notification\Listeners\SendBookingCancelledNotification;
use App\Domains\Notification\Listeners\SendBookingConfirmedNotification;
use App\Domains\Notification\Listeners\SendBookingCreatedNotification;
use App\Domains\Offer\Models\Offer;
use App\Domains\Offer\Policies\OfferPolicy;
use App\Domains\Review\Models\Review;
use App\Domains\Review\Policies\ReviewPolicy;
use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use App\Domains\Service\Policies\ServicePolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Gate::policy(ServiceCategory::class, ServicePolicy::class);
        Gate::policy(Service::class, ServicePolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Offer::class, OfferPolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);

        Gate::define('view-dashboard', fn (User $user) => $user->hasAnyRole(['admin', 'receptionist']));

        Event::listen(BookingCreated::class, SendBookingCreatedNotification::class);
        Event::listen(BookingConfirmed::class, SendBookingConfirmedNotification::class);
        Event::listen(BookingCancelled::class, SendBookingCancelledNotification::class);
    }
}
