<?php

namespace Tests\Feature\Booking;

use App\Domains\Admin\Models\Setting;
use App\Domains\Auth\Models\Customer;
use App\Domains\Auth\Models\User;
use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Models\Booking;
use App\Domains\Booking\Models\BookingItem;
use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $customerUser;

    private Customer $customer;

    private Service $service;

    private Service $service2;

    private string $workingDate = '2026-06-01'; // Monday

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        // Travel to a fixed point: Monday 2026-06-01 at 09:00
        $this->travelTo(Carbon::create(2026, 6, 1, 9, 0, 0));

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
            'name_ar' => 'قص', 'name_en' => 'Haircut',
            'price' => 100.00, 'duration_minutes' => 60, 'is_active' => true,
        ]);

        $this->service2 = Service::create([
            'service_category_id' => $category->id,
            'name_ar' => 'صبغة', 'name_en' => 'Color',
            'price' => 150.00, 'duration_minutes' => 45, 'is_active' => true,
        ]);

        // Salon settings: open 09:00-21:00, all days
        Setting::set('salon_working_hours', [
            'open' => '09:00',
            'close' => '21:00',
            'working_days' => [0, 1, 2, 3, 4, 5, 6],
        ]);
        Setting::set('booking_buffer_minutes', 10);
        Setting::set('salon_capacity', 1);
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    private function makeBookingDirectly(array $overrides = []): Booking
    {
        $defaults = [
            'customer_id' => $this->customer->id,
            'booking_date' => $this->workingDate,
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status' => BookingStatus::Pending,
            'buffer_minutes' => 10,
            'total_price' => 100.00,
        ];

        $booking = Booking::create(array_merge($defaults, $overrides));

        BookingItem::create([
            'booking_id' => $booking->id,
            'service_id' => $this->service->id,
            'price' => $this->service->price,
            'duration_minutes' => $this->service->duration_minutes,
        ]);

        return $booking;
    }

    // ─── Create booking (single service) ─────────────────────────────────────

    public function test_customer_can_create_booking(): void
    {
        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson('/api/v1/bookings', [

                'service_ids' => [$this->service->id],
                'booking_date' => $this->workingDate,
                'start_time' => '10:00',
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.total_price', '100.00');

        $this->assertDatabaseHas('bookings', [
            'customer_id' => $this->customer->id,
            'booking_date' => $this->workingDate,
            'start_time' => '10:00:00',
            'status' => 'pending',
        ]);
    }

    // ─── Multi-service: success ───────────────────────────────────────────────

    public function test_booking_with_two_services_succeeds(): void
    {
        $response = $this->actingAs($this->customerUser, 'sanctum')
            ->postJson('/api/v1/bookings', [

                'service_ids' => [$this->service->id, $this->service2->id],
                'booking_date' => $this->workingDate,
                'start_time' => '10:00',
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending');

        // Two booking_items should be created
        $bookingId = $response->json('data.id');
        $this->assertDatabaseCount('booking_items', 2);
        $this->assertDatabaseHas('booking_items', ['booking_id' => $bookingId, 'service_id' => $this->service->id]);
        $this->assertDatabaseHas('booking_items', ['booking_id' => $bookingId, 'service_id' => $this->service2->id]);
    }

    // ─── Multi-service: correct duration ─────────────────────────────────────

    public function test_booking_with_two_services_calculates_correct_duration(): void
    {
        // service = 60 min, service2 = 45 min, buffer between = 10 min → total = 115 min
        // start 10:00 → end 11:55
        $response = $this->actingAs($this->customerUser, 'sanctum')
            ->postJson('/api/v1/bookings', [

                'service_ids' => [$this->service->id, $this->service2->id],
                'booking_date' => $this->workingDate,
                'start_time' => '10:00',
            ])
            ->assertCreated();

        $this->assertDatabaseHas('bookings', [
            'start_time' => '10:00:00',
            'end_time' => '11:55:00',
        ]);
    }

    // ─── Multi-service: total price ───────────────────────────────────────────

    public function test_multi_service_total_price_is_sum_of_services(): void
    {
        // service = 100, service2 = 150 → total = 250
        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson('/api/v1/bookings', [

                'service_ids' => [$this->service->id, $this->service2->id],
                'booking_date' => $this->workingDate,
                'start_time' => '10:00',
            ])
            ->assertCreated()
            ->assertJsonPath('data.total_price', '250.00');
    }

    // ─── Multi-service: overlap detection ────────────────────────────────────

    public function test_multi_service_booking_overlaps_existing_booking(): void
    {
        // Existing booking 10:00–11:00 (single service, 60 min)
        $this->makeBookingDirectly(['start_time' => '10:00', 'end_time' => '11:00']);

        // New booking: two services = 115 min block → 10:00→11:55 overlaps with existing
        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson('/api/v1/bookings', [

                'service_ids' => [$this->service->id, $this->service2->id],
                'booking_date' => $this->workingDate,
                'start_time' => '10:00',
            ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.start_time.0', __('messages.time_slot_taken'));
    }

    // ─── Availability: overlap ────────────────────────────────────────────────

    public function test_booking_fails_with_overlapping_time(): void
    {
        // Existing booking 10:00–11:00
        $this->makeBookingDirectly(['start_time' => '10:00', 'end_time' => '11:00']);

        // New booking at 10:30 — overlaps
        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson('/api/v1/bookings', [

                'service_ids' => [$this->service->id],
                'booking_date' => $this->workingDate,
                'start_time' => '10:30',
            ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.start_time.0', __('messages.time_slot_taken'));
    }

    // ─── Availability: buffer ─────────────────────────────────────────────────

    public function test_booking_fails_without_buffer_time(): void
    {
        // Existing booking 10:00–11:00, buffer = 10 min → next allowed start = 11:10
        $this->makeBookingDirectly(['start_time' => '10:00', 'end_time' => '11:00']);

        // Try 11:05 — too close (less than 10-min buffer)
        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson('/api/v1/bookings', [

                'service_ids' => [$this->service->id],
                'booking_date' => $this->workingDate,
                'start_time' => '11:05',
            ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.start_time.0', __('messages.time_slot_taken'));
    }

    // ─── Availability: salon hours ────────────────────────────────────────────

    public function test_booking_fails_outside_salon_hours(): void
    {
        // Salon closes at 21:00. Service = 60 min. 20:30 → ends 21:30 → outside hours.
        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson('/api/v1/bookings', [
                'service_ids' => [$this->service->id],
                'booking_date' => $this->workingDate,
                'start_time' => '20:30',
            ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.start_time.0', __('messages.outside_salon_hours'));
    }

    // ─── Cancel: customer before cutoff ──────────────────────────────────────

    public function test_customer_can_cancel_before_3_hours(): void
    {
        // Now = 09:00. Booking at 13:00 = 4 hours ahead → allowed
        $booking = $this->makeBookingDirectly(['start_time' => '13:00', 'end_time' => '14:00']);

        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson("/api/v1/bookings/{$booking->id}/cancel")
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');
    }

    // ─── Cancel: customer within cutoff ──────────────────────────────────────

    public function test_customer_cannot_cancel_within_3_hours(): void
    {
        // Now = 09:00. Booking at 11:00 = 2 hours ahead → not allowed
        $booking = $this->makeBookingDirectly(['start_time' => '11:00', 'end_time' => '12:00']);

        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson("/api/v1/bookings/{$booking->id}/cancel")
            ->assertUnprocessable()
            ->assertJsonPath('errors.booking.0', __('messages.cancellation_too_late'));
    }

    // ─── Cancel: admin can cancel anytime ────────────────────────────────────

    public function test_admin_can_cancel_anytime(): void
    {
        // Now = 09:00. Booking at 10:00 = 1 hour ahead → admin can still cancel
        $booking = $this->makeBookingDirectly(['start_time' => '10:00', 'end_time' => '11:00']);

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/bookings/{$booking->id}/cancel", ['reason' => 'Admin override'])
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
            'cancelled_by_user_id' => $this->admin->id,
        ]);
    }

    // ─── Reschedule: valid ────────────────────────────────────────────────────

    public function test_customer_can_reschedule_to_available_slot(): void
    {
        // Booking at 10:00, reschedule to 14:00 (no conflict)
        $booking = $this->makeBookingDirectly(['start_time' => '10:00', 'end_time' => '11:00']);

        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson("/api/v1/bookings/{$booking->id}/reschedule", [
                'booking_date' => $this->workingDate,
                'start_time' => '14:00',
            ])
            ->assertOk()
            ->assertJsonPath('data.start_time', '14:00:00');
    }

    // ─── Reschedule: after start ──────────────────────────────────────────────

    public function test_reschedule_fails_after_booking_starts(): void
    {
        // Now = 09:00. Booking at 08:00 → already started (08:00 < 09:00)
        $booking = $this->makeBookingDirectly(['start_time' => '08:00', 'end_time' => '09:00']);

        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson("/api/v1/bookings/{$booking->id}/reschedule", [
                'booking_date' => $this->workingDate,
                'start_time' => '15:00',
            ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.booking.0', __('messages.booking_already_started'));
    }

    // ─── Status: confirm ──────────────────────────────────────────────────────

    public function test_admin_can_confirm_booking(): void
    {
        // Booking at 13:00 (future), status = pending
        $booking = $this->makeBookingDirectly(['start_time' => '13:00', 'end_time' => '14:00']);

        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/v1/admin/bookings/{$booking->id}/status", ['status' => 'confirmed'])
            ->assertOk()
            ->assertJsonPath('data.status', 'confirmed');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);

        $this->assertNotNull(Booking::find($booking->id)->confirmed_at);
    }

    // ─── Status: complete ─────────────────────────────────────────────────────

    public function test_admin_can_complete_booking(): void
    {
        // Now = 09:00. Create confirmed booking that ended at 08:00 (past)
        $booking = $this->makeBookingDirectly([
            'start_time' => '07:00',
            'end_time' => '08:00',
            'status' => BookingStatus::Confirmed,
            'confirmed_at' => now()->subHours(2),
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/v1/admin/bookings/{$booking->id}/status", ['status' => 'completed'])
            ->assertOk()
            ->assertJsonPath('data.status', 'completed');
    }

    // ─── Status: no_show before end ───────────────────────────────────────────

    public function test_no_show_fails_before_appointment_ends(): void
    {
        // Now = 09:00. Booking at 13:00–14:00 (future). Can't mark no_show yet.
        $booking = $this->makeBookingDirectly(['start_time' => '13:00', 'end_time' => '14:00']);

        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/v1/admin/bookings/{$booking->id}/status", ['status' => 'no_show'])
            ->assertUnprocessable()
            ->assertJsonPath('errors.status.0', __('messages.appointment_not_ended'));
    }

    // ─── Status: customer forbidden ───────────────────────────────────────────

    public function test_customer_cannot_change_booking_status(): void
    {
        $booking = $this->makeBookingDirectly();

        $this->actingAs($this->customerUser, 'sanctum')
            ->patchJson("/api/v1/admin/bookings/{$booking->id}/status", ['status' => 'confirmed'])
            ->assertForbidden();
    }

    // ─── Cancel: exact 3-hour boundary ───────────────────────────────────────

    public function test_cancel_at_exactly_3_hours_is_rejected(): void
    {
        // Now = 09:00. Booking at 12:00 = exactly 3h ahead → lte(now+3h) → rejected
        $booking = $this->makeBookingDirectly(['start_time' => '12:00', 'end_time' => '13:00']);

        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson("/api/v1/bookings/{$booking->id}/cancel")
            ->assertUnprocessable()
            ->assertJsonPath('errors.booking.0', __('messages.cancellation_too_late'));
    }

    public function test_cancel_one_minute_past_3_hour_window_is_allowed(): void
    {
        // Now = 09:00. Booking at 12:01 → 3h1min ahead → gt(now+3h) → allowed
        $booking = $this->makeBookingDirectly(['start_time' => '12:01', 'end_time' => '13:01']);

        $this->actingAs($this->customerUser, 'sanctum')
            ->postJson("/api/v1/bookings/{$booking->id}/cancel")
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');
    }

    // ─── Status: invalid transitions ─────────────────────────────────────────

    public function test_cannot_confirm_already_confirmed_booking(): void
    {
        $booking = $this->makeBookingDirectly([
            'start_time' => '13:00',
            'end_time' => '14:00',
            'status' => BookingStatus::Confirmed,
            'confirmed_at' => now()->subMinutes(5),
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/v1/admin/bookings/{$booking->id}/status", ['status' => 'confirmed'])
            ->assertUnprocessable()
            ->assertJsonPath('errors.status.0', __('messages.invalid_status_transition'));
    }

    public function test_cannot_complete_pending_booking(): void
    {
        // pending → completed is not a valid transition
        $booking = $this->makeBookingDirectly([
            'start_time' => '07:00',
            'end_time' => '08:00',
            'status' => BookingStatus::Pending,
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/v1/admin/bookings/{$booking->id}/status", ['status' => 'completed'])
            ->assertUnprocessable()
            ->assertJsonPath('errors.status.0', __('messages.invalid_status_transition'));
    }

    public function test_cannot_complete_booking_before_appointment_ends(): void
    {
        // Now = 09:00. Booking 13:00–14:00 (future, confirmed). Can't complete yet.
        $booking = $this->makeBookingDirectly([
            'start_time' => '13:00',
            'end_time' => '14:00',
            'status' => BookingStatus::Confirmed,
            'confirmed_at' => now()->subMinutes(5),
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/v1/admin/bookings/{$booking->id}/status", ['status' => 'completed'])
            ->assertUnprocessable()
            ->assertJsonPath('errors.status.0', __('messages.appointment_not_ended'));
    }

    public function test_cannot_mark_no_show_on_cancelled_booking(): void
    {
        $booking = $this->makeBookingDirectly([
            'start_time' => '07:00',
            'end_time' => '08:00',
            'status' => BookingStatus::Cancelled,
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/v1/admin/bookings/{$booking->id}/status", ['status' => 'no_show'])
            ->assertUnprocessable()
            ->assertJsonPath('errors.status.0', __('messages.invalid_status_transition'));
    }
}
