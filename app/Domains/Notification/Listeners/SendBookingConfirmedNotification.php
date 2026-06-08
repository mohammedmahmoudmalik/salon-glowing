<?php

namespace App\Domains\Notification\Listeners;

use App\Domains\Booking\Events\BookingConfirmed;
use App\Domains\Notification\Notifications\BookingConfirmedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBookingConfirmedNotification implements ShouldQueue
{
    use Queueable;

    public function handle(BookingConfirmed $event): void
    {
        $event->booking->customer->user->notify(
            new BookingConfirmedNotification($event->booking)
        );
    }
}
