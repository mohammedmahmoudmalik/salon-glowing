<?php

namespace Tests\Feature\Review;

use App\Domains\Auth\Models\Customer;
use App\Domains\Auth\Models\User;
use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Models\Booking;
use App\Domains\Booking\Models\BookingItem;
use App\Domains\Review\Models\Review;
use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $customerUser;

    private Customer $customer;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        $this->admin = $adminUser;

        $this->customerUser = User::factory()->create();
        $this->customerUser->assignRole('customer');
        $this->customer = Customer::create(['user_id' => $this->customerUser->id]);

        $category = ServiceCategory::create([
            'name_ar' => 'شعر', 'name_en' => 'Hair', 'is_active' => true,
        ]);

        $this->service = Service::create([
            'service_category_id' => $category->id,
            'name_ar' => 'قص شعر', 'name_en' => 'Haircut',
            'price' => 100.00, 'duration_minutes' => 60, 'is_active' => true,
        ]);
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    private function makeBookingWithStatus(BookingStatus $status): Booking
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'booking_date' => '2026-06-01',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status' => $status,
            'buffer_minutes' => 10,
            'total_price' => 100.00,
        ]);

        BookingItem::create([
            'booking_id' => $booking->id,
            'service_id' => $this->service->id,
            'price' => 100.00,
            'duration_minutes' => 60,
        ]);

        return $booking;
    }

    // ─── Review: completed booking ────────────────────────────────────────────

    public function test_customer_can_review_completed_booking(): void
    {
        $booking = $this->makeBookingWithStatus(BookingStatus::Completed);

        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson("/api/v1/bookings/{$booking->id}/review", [
                'rating' => 5,
                'comment' => 'Excellent service!',
            ])
            ->assertCreated()
            ->assertJsonPath('data.rating', 5);

        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'customer_id' => $this->customer->id,
            'rating' => 5,
        ]);
    }

    // ─── Review: non-completed booking ───────────────────────────────────────

    public function test_customer_cannot_review_non_completed_booking(): void
    {
        $booking = $this->makeBookingWithStatus(BookingStatus::Pending);

        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson("/api/v1/bookings/{$booking->id}/review", ['rating' => 5])
            ->assertUnprocessable()
            ->assertJsonPath('errors.booking.0', __('messages.booking_not_completed'));
    }

    // ─── Review: duplicate ────────────────────────────────────────────────────

    public function test_cannot_review_same_booking_twice(): void
    {
        $booking = $this->makeBookingWithStatus(BookingStatus::Completed);

        Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $this->customer->id,

            'service_id' => $this->service->id,
            'rating' => 4,
        ]);

        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson("/api/v1/bookings/{$booking->id}/review", ['rating' => 5])
            ->assertUnprocessable()
            ->assertJsonPath('errors.booking.0', __('messages.review_already_exists'));
    }

    // ─── Hide: admin ──────────────────────────────────────────────────────────

    public function test_admin_can_hide_review(): void
    {
        $booking = $this->makeBookingWithStatus(BookingStatus::Completed);

        $review = Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $this->customer->id,

            'service_id' => $this->service->id,
            'rating' => 1,
            'comment' => 'Bad.',
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/v1/admin/reviews/{$review->id}/hide")
            ->assertOk()
            ->assertJsonPath('data.is_hidden', true);

        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'is_hidden' => true]);
    }

    // ─── Hide: customer forbidden ─────────────────────────────────────────────

    public function test_customer_cannot_hide_review(): void
    {
        $booking = $this->makeBookingWithStatus(BookingStatus::Completed);

        $review = Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $this->customer->id,

            'service_id' => $this->service->id,
            'rating' => 3,
        ]);

        $this->actingAs($this->customerUser, 'sanctum')
            ->patchJson("/api/v1/admin/reviews/{$review->id}/hide")
            ->assertForbidden();
    }
}
