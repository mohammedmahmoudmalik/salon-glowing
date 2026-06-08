<?php

namespace App\Domains\Notification\Notifications;

use App\Domains\Booking\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.booking_reminder_subject'))
            ->greeting(__('notifications.greeting', ['name' => $notifiable->name]))
            ->line(__('notifications.booking_reminder_line', [
                'date' => $this->booking->booking_date->format('Y-m-d'),
                'time' => substr($this->booking->start_time, 0, 5),
            ]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'booking_reminder',
            'booking_id' => $this->booking->id,
            'booking_date' => $this->booking->booking_date->format('Y-m-d'),
            'start_time' => $this->booking->start_time,
        ];
    }
}
