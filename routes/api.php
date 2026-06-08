<?php

use App\Domains\Admin\Http\Controllers\DashboardController;
use App\Domains\Admin\Http\Controllers\HeroImageController;
use App\Domains\Admin\Http\Controllers\SettingController;
use App\Domains\Auth\Http\Controllers\AuthController;
use App\Domains\Auth\Http\Controllers\ProfileController;
use App\Domains\Booking\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Domains\Booking\Http\Controllers\BookingController;
use App\Domains\Offer\Http\Controllers\Admin\OfferController as AdminOfferController;
use App\Domains\Offer\Http\Controllers\OfferController;
use App\Domains\Review\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Domains\Review\Http\Controllers\ReviewController;
use App\Domains\Service\Http\Controllers\Admin\ServiceCategoryController as AdminServiceCategoryController;
use App\Domains\Service\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Domains\Service\Http\Controllers\ServiceCategoryController;
use App\Domains\Service\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->prefix('v1')->group(function () {

    // Public auth routes
    Route::middleware('throttle:auth')->group(function () {
        Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
    });

    // Public browse routes
    Route::get('hero-images', [HeroImageController::class, 'index'])->name('hero-images.index');
    Route::get('categories', [ServiceCategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{serviceCategory:slug}', [ServiceCategoryController::class, 'show'])->name('categories.show');

    Route::get('services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');

    Route::get('offers', [OfferController::class, 'index'])->name('offers.index');
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');

        // Customer booking routes
        Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::post('bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::post('bookings/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('bookings.reschedule');
        Route::post('bookings/{booking}/review', [ReviewController::class, 'store'])->name('bookings.review.store');

        // Admin routes — requires admin or receptionist role
        Route::prefix('admin')->name('admin.')->middleware('role:admin|receptionist')->group(function () {
            Route::apiResource('categories', AdminServiceCategoryController::class);
            Route::apiResource('services', AdminServiceController::class);
            Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
            Route::patch('bookings/{booking}/status', [AdminBookingController::class, 'changeStatus'])->name('bookings.status');

            Route::apiResource('offers', AdminOfferController::class);

            Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
            Route::patch('reviews/{review}/hide', [AdminReviewController::class, 'hide'])->name('reviews.hide');
            Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

            Route::get('dashboard', DashboardController::class)->name('dashboard');
            Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
            Route::patch('settings', [SettingController::class, 'update'])->name('settings.update');
        });
    });

});
