<?php

namespace App\Domains\Booking\Events;

use App\Domains\Booking\Models\Booking;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Booking $booking) {}
}
