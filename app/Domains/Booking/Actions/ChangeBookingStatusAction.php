<?php

namespace App\Domains\Booking\Actions;

use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Events\BookingConfirmed;
use App\Domains\Booking\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class ChangeBookingStatusAction
{
    public function execute(Booking $booking, BookingStatus $newStatus): Booking
    {
        $appointmentEnd = Carbon::parse(
            $booking->booking_date->toDateString().' '.$booking->end_time
        );

        $updateData = ['status' => $newStatus];

        if ($newStatus === BookingStatus::Confirmed) {
            if ($booking->status !== BookingStatus::Pending) {
                throw ValidationException::withMessages([
                    'status' => [__('messages.invalid_status_transition')],
                ]);
            }
            $updateData['confirmed_at'] = now();

        } elseif ($newStatus === BookingStatus::Completed) {
            if ($booking->status !== BookingStatus::Confirmed) {
                throw ValidationException::withMessages([
                    'status' => [__('messages.invalid_status_transition')],
                ]);
            }
            if (now()->lt($appointmentEnd)) {
                throw ValidationException::withMessages([
                    'status' => [__('messages.appointment_not_ended')],
                ]);
            }

        } elseif ($newStatus === BookingStatus::Cancelled) {
            if (! in_array($booking->status, [BookingStatus::Pending, BookingStatus::Confirmed])) {
                throw ValidationException::withMessages([
                    'status' => [__('messages.invalid_status_transition')],
                ]);
            }

        } elseif ($newStatus === BookingStatus::NoShow) {
            if (! in_array($booking->status, [BookingStatus::Pending, BookingStatus::Confirmed])) {
                throw ValidationException::withMessages([
                    'status' => [__('messages.invalid_status_transition')],
                ]);
            }
            if (now()->lt($appointmentEnd)) {
                throw ValidationException::withMessages([
                    'status' => [__('messages.appointment_not_ended')],
                ]);
            }

        } else {
            throw ValidationException::withMessages([
                'status' => [__('messages.invalid_status_transition')],
            ]);
        }

        $booking->update($updateData);

        if ($newStatus === BookingStatus::Confirmed) {
            event(new BookingConfirmed($booking));
        }

        activity()
            ->performedOn($booking)
            ->causedBy(auth()->user())
            ->log('booking_status_changed');

        return $booking->fresh();
    }
}
