<?php

namespace App\Domains\Booking\Policies;

use App\Domains\Auth\Models\User;
use App\Domains\Booking\Models\Booking;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        if ($user->hasRole(['admin', 'receptionist'])) {
            return true;
        }

        return $user->customer?->id === $booking->customer_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('customer');
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if ($user->hasRole(['admin', 'receptionist'])) {
            return true;
        }

        return $user->customer?->id === $booking->customer_id;
    }

    public function reschedule(User $user, Booking $booking): bool
    {
        if ($user->hasRole(['admin', 'receptionist'])) {
            return true;
        }

        return $user->customer?->id === $booking->customer_id;
    }

    public function changeStatus(User $user): bool
    {
        return $user->hasRole(['admin', 'receptionist']);
    }
}
