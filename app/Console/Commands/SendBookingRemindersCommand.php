<?php

namespace App\Console\Commands;

use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Models\Booking;
use App\Domains\Notification\Notifications\BookingReminderNotification;
use Illuminate\Console\Command;

class SendBookingRemindersCommand extends Command
{
    protected $signature = 'bookings:send-reminders';

    protected $description = 'Send reminder notifications for bookings starting in 45–75 minutes';

    public function handle(): void
    {
        $from = now()->addMinutes(45)->format('H:i');
        $to = now()->addMinutes(75)->format('H:i');

        $bookings = Booking::where('status', BookingStatus::Confirmed)
            ->whereDate('booking_date', now()->toDateString())
            ->whereTime('start_time', '>=', $from)
            ->whereTime('start_time', '<', $to)
            ->with('customer.user')
            ->get();

        foreach ($bookings as $booking) {
            $booking->customer->user->notify(new BookingReminderNotification($booking));
        }

        $this->info("Sent {$bookings->count()} booking reminders.");
    }
}
