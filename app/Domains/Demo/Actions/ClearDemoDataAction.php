<?php

namespace App\Domains\Demo\Actions;

use App\Domains\Auth\Models\Customer;
use App\Domains\Auth\Models\User;
use App\Domains\Booking\Models\Booking;
use App\Domains\Offer\Models\Offer;
use App\Domains\Review\Models\Review;
use Illuminate\Support\Facades\DB;

class ClearDemoDataAction
{
    public function execute(): array
    {
        return DB::transaction(function () {
            $customerIds = Customer::whereIn(
                'user_id',
                User::role('customer')->pluck('id')
            )->pluck('id');

            $counts = [
                'customers' => $customerIds->count(),
                'bookings'  => Booking::whereIn('customer_id', $customerIds)->count(),
                'reviews'   => Review::whereIn('customer_id', $customerIds)->count(),
                'offers'    => Offer::count(),
            ];

            // Step 1: Delete users with 'customer' role — cascade chain:
            //   users → customers (user_id FK cascadeOnDelete)
            //         → bookings  (customer_id FK cascadeOnDelete)
            //         → booking_items (booking_id FK cascadeOnDelete)
            //         → reviews   (booking_id FK cascadeOnDelete)
            //         → reviews   (customer_id FK cascadeOnDelete — already gone)
            User::role('customer')->delete();

            // Step 2: Offers → cascades offer_service (offer_id FK cascadeOnDelete)
            Offer::query()->delete();

            return $counts;
        });
    }
}
