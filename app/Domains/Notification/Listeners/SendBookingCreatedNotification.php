<?php

namespace App\Domains\Notification\Listeners;

use App\Domains\Booking\Events\BookingCreated;
use App\Domains\Notification\Notifications\BookingCreatedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBookingCreatedNotification implements ShouldQueue
{
    use Queueable;

    public function handle(BookingCreated $event): void
    {
        $event->booking->customer->user->notify(
            new BookingCreatedNotification($event->booking)
        );
    }
}
