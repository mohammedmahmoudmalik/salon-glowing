<?php

namespace App\Domains\Review\Actions;

use App\Domains\Auth\Models\Customer;
use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Models\Booking;
use App\Domains\Review\Models\Review;
use Illuminate\Validation\ValidationException;

class CreateReviewAction
{
    public function execute(Customer $customer, Booking $booking, array $data): Review
    {
        if ($booking->status !== BookingStatus::Completed) {
            throw ValidationException::withMessages([
                'booking' => [__('messages.booking_not_completed')],
            ]);
        }

        if ($booking->customer_id !== $customer->id) {
            throw ValidationException::withMessages([
                'booking' => [__('messages.booking_not_owned')],
            ]);
        }

        if (Review::where('booking_id', $booking->id)->exists()) {
            throw ValidationException::withMessages([
                'booking' => [__('messages.review_already_exists')],
            ]);
        }

        $firstItem = $booking->items()->first();

        if (! $firstItem) {
            throw ValidationException::withMessages([
                'booking' => [__('messages.booking_not_completed')],
            ]);
        }

        $review = Review::create([
            'booking_id'  => $booking->id,
            'customer_id' => $customer->id,
            'service_id'  => $firstItem->service_id,
            'rating'      => $data['rating'],
            'comment'     => $data['comment'] ?? null,
        ]);

        activity()->performedOn($review)->causedBy($customer->user)->log('review_created');

        return $review->load(['booking', 'service']);
    }
}
