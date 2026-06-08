<?php

namespace App\Domains\Booking\Actions;

use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Models\Booking;
use App\Domains\Booking\Services\BookingAvailabilityService;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class RescheduleBookingAction
{
    public function __construct(private readonly BookingAvailabilityService $availability) {}

    public function execute(Booking $booking, array $data): Booking
    {
        if (in_array($booking->status, [
            BookingStatus::Cancelled,
            BookingStatus::Completed,
            BookingStatus::NoShow,
        ])) {
            throw ValidationException::withMessages([
                'booking' => [__('messages.booking_cannot_be_rescheduled')],
            ]);
        }

        $appointmentStart = Carbon::parse(
            $booking->booking_date->toDateString().' '.$booking->start_time
        );

        if (now()->gte($appointmentStart)) {
            throw ValidationException::withMessages([
                'booking' => [__('messages.booking_already_started')],
            ]);
        }

        $serviceIds = $booking->items()->pluck('service_id')->toArray();

        $this->availability->check(
            $serviceIds,
            $data['booking_date'],
            $data['start_time'],
            $booking->buffer_minutes,
            $booking->id
        );

        $start = Carbon::parse($data['booking_date'].' '.$data['start_time']);
        $end = $this->availability->calculateEndTime($start, $serviceIds, $booking->buffer_minutes);

        $booking->update([
            'booking_date' => $data['booking_date'],
            'start_time'   => $data['start_time'],
            'end_time'     => $end->format('H:i'),
        ]);

        activity()
            ->performedOn($booking)
            ->log('booking_rescheduled');

        return $booking->fresh()->load(['items.service', 'customer.user']);
    }
}
