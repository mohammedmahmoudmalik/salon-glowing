<?php

use App\Http\Controllers\Web\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Web\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\OwnerDashboardController as AdminOwnerDashboardController;
use App\Http\Controllers\Web\Admin\HeroImageController as AdminHeroImageController;
use App\Http\Controllers\Web\Admin\DemoController as AdminDemoController;
use App\Http\Controllers\Web\Admin\ServiceSetupController as AdminServiceSetupController;
use App\Http\Controllers\Web\Admin\OfferController as AdminOfferController;
use App\Http\Controllers\Web\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Web\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Web\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Web\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\OfferController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\ServiceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Language switcher
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('lang.switch');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

// Logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

// Cart (session-based, public)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{serviceId}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Public pages (admin/receptionist are redirected to their panel)
Route::middleware('admin.redirect')->group(function () {
    Route::get('/', [HomeController::class, '__invoke'])->name('home');
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
    Route::get('/offers/{offer}', [OfferController::class, 'show'])->name('offers.show');
});

// Auth-required customer routes
Route::middleware('auth')->group(function () {
    Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/bookings/{booking}/reschedule', [BookingController::class, 'rescheduleForm'])->name('bookings.reschedule.form');
    Route::post('/bookings/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('bookings.reschedule');
    Route::get('/bookings/{booking}/review', [BookingController::class, 'reviewForm'])->name('bookings.review.form');
    Route::post('/bookings/{booking}/review', [BookingController::class, 'storeReview'])->name('bookings.review.store');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|receptionist|owner'])->group(function () {

    // Profile accessible to all admin roles
    Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');

    // Settings page (GET): accessible to admin and owner
    Route::middleware('role:admin|owner')->group(function () {
        Route::get('/settings', [AdminSettingController::class, 'show'])->name('settings.show');
    });

    // Branding, hero, and country: owner only
    Route::middleware('role:owner')->group(function () {
        Route::post('/settings/branding', [AdminSettingController::class, 'updateBranding'])->name('settings.branding');
        Route::delete('/settings/logo', [AdminSettingController::class, 'deleteLogo'])->name('settings.logo.delete');
        Route::post('/settings/hero', [AdminSettingController::class, 'updateHero'])->name('settings.hero');
        Route::post('/settings/country', [AdminSettingController::class, 'updateCountry'])->name('settings.country');
    });

    // Admin + receptionist routes
    Route::middleware('role:admin|receptionist')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, '__invoke'])->name('dashboard');

        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'changeStatus'])->name('bookings.status');

        Route::get('/categories', [AdminServiceController::class, 'categoriesIndex'])->name('categories.index');
        Route::get('/categories/create', [AdminServiceController::class, 'categoryCreate'])->name('categories.create');
        Route::post('/categories', [AdminServiceController::class, 'categoryStore'])->name('categories.store');
        Route::get('/categories/{category}/edit', [AdminServiceController::class, 'categoryEdit'])->name('categories.edit');
        Route::post('/categories/{category}', [AdminServiceController::class, 'categoryUpdate'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminServiceController::class, 'categoryDestroy'])->name('categories.destroy');
        Route::patch('/categories/{category}/toggle', [AdminServiceController::class, 'categoryToggle'])->name('categories.toggle');

        Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create', [AdminServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [AdminServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}', [AdminServiceController::class, 'show'])->name('services.show');
        Route::get('/services/{service}/edit', [AdminServiceController::class, 'edit'])->name('services.edit');
        Route::post('/services/{service}', [AdminServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');

        Route::get('/offers', [AdminOfferController::class, 'index'])->name('offers.index');
        Route::get('/offers/create', [AdminOfferController::class, 'create'])->name('offers.create');
        Route::post('/offers', [AdminOfferController::class, 'store'])->name('offers.store');
        Route::get('/offers/{offer}/edit', [AdminOfferController::class, 'edit'])->name('offers.edit');
        Route::post('/offers/{offer}', [AdminOfferController::class, 'update'])->name('offers.update');
        Route::delete('/offers/{offer}', [AdminOfferController::class, 'destroy'])->name('offers.destroy');

        Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}/bookings', [AdminCustomerController::class, 'bookings'])->name('customers.bookings');

        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{review}/hide', [AdminReviewController::class, 'toggleHide'])->name('reviews.hide');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // Owner dashboard
    Route::middleware('role:owner')->group(function () {
        Route::get('/owner-dashboard', [AdminOwnerDashboardController::class, '__invoke'])->name('owner.dashboard');
    });

    // Demo data: owner only
    Route::middleware('role:owner')->group(function () {
        Route::get('/demo', [AdminDemoController::class, 'index'])->name('demo.index');
        Route::post('/demo', [AdminDemoController::class, 'generate'])->name('demo.generate');
        Route::delete('/demo/clear', [AdminDemoController::class, 'clearAll'])->name('demo.clear');
    });

    // Service setup: owner only
    Route::middleware('role:owner')->group(function () {
        Route::get('/setup', [AdminServiceSetupController::class, 'index'])->name('setup.index');
        Route::post('/setup/import', [AdminServiceSetupController::class, 'import'])->name('setup.import');
        Route::post('/setup/export', [AdminServiceSetupController::class, 'export'])->name('setup.export');
        Route::get('/setup/template', [AdminServiceSetupController::class, 'downloadTemplate'])->name('setup.template');
        Route::delete('/setup/clear', [AdminServiceSetupController::class, 'clearAll'])->name('setup.clear');
    });

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/social', [AdminSettingController::class, 'updateSocial'])->name('settings.social');

        // Tour video
        Route::post('/settings/tour-video', [AdminSettingController::class, 'updateTourVideo'])->name('settings.tour-video');
        Route::delete('/settings/tour-video', [AdminSettingController::class, 'deleteTourVideo'])->name('settings.tour-video.delete');
        Route::post('/settings/tour-video-texts', [AdminSettingController::class, 'updateTourVideoTexts'])->name('settings.tour-video-texts');

        // Owner video
        Route::post('/settings/owner-video', [AdminSettingController::class, 'updateOwnerVideo'])->name('settings.owner-video');
        Route::delete('/settings/owner-video', [AdminSettingController::class, 'deleteOwnerVideo'])->name('settings.owner-video.delete');
        Route::post('/settings/owner-video-texts', [AdminSettingController::class, 'updateOwnerVideoTexts'])->name('settings.owner-video-texts');

        // Hero image carousel management
        Route::get('/hero-images', [AdminHeroImageController::class, 'index'])->name('hero-images.index');
        Route::post('/hero-images', [AdminHeroImageController::class, 'store'])->name('hero-images.store');
        Route::post('/hero-images/reorder', [AdminHeroImageController::class, 'reorder'])->name('hero-images.reorder');
        Route::patch('/hero-images/{heroImage}/toggle', [AdminHeroImageController::class, 'toggle'])->name('hero-images.toggle');
        Route::delete('/hero-images/{heroImage}', [AdminHeroImageController::class, 'destroy'])->name('hero-images.destroy');
    });
});
