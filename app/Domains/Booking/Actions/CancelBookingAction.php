<?php

namespace App\Domains\Booking\Actions;

use App\Domains\Auth\Models\User;
use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Events\BookingCancelled;
use App\Domains\Booking\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class CancelBookingAction
{
    public function execute(Booking $booking, User $actor, ?string $reason = null): Booking
    {
        if (in_array($booking->status, [
            BookingStatus::Completed,
            BookingStatus::NoShow,
            BookingStatus::Cancelled,
        ])) {
            throw ValidationException::withMessages([
                'booking' => [__('messages.booking_cannot_be_cancelled')],
            ]);
        }

        if (! $actor->hasRole(['admin', 'receptionist'])) {
            $appointmentStart = Carbon::parse(
                $booking->booking_date->toDateString().' '.$booking->start_time
            );

            // Customer must cancel at least 3 hours before the appointment
            if ($appointmentStart->lte(now()->addHours(3))) {
                throw ValidationException::withMessages([
                    'booking' => [__('messages.cancellation_too_late')],
                ]);
            }
        }

        $booking->update([
            'status' => BookingStatus::Cancelled,
            'cancelled_at' => now(),
            'cancelled_by_user_id' => $actor->id,
            'cancellation_reason' => $reason,
        ]);

        activity()
            ->performedOn($booking)
            ->causedBy($actor)
            ->log('booking_cancelled');

        event(new BookingCancelled($booking));

        return $booking->fresh();
    }
}
