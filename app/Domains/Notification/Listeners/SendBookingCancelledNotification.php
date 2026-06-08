<?php

namespace App\Domains\Notification\Listeners;

use App\Domains\Booking\Events\BookingCancelled;
use App\Domains\Notification\Notifications\BookingCancelledNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBookingCancelledNotification implements ShouldQueue
{
    use Queueable;

    public function handle(BookingCancelled $event): void
    {
        $event->booking->customer->user->notify(
            new BookingCancelledNotification($event->booking)
        );
    }
}
