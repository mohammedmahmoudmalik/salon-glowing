<?php

namespace App\Domains\Service\Actions;

use App\Domains\Auth\Models\User;
use App\Domains\Booking\Models\Booking;
use App\Domains\Offer\Models\Offer;
use App\Domains\Review\Models\Review;
use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClearAllServicesAction
{
    public function execute(): array
    {
        // Collect image paths BEFORE delete — mass deletes bypass Eloquent events,
        // so the deleting() hooks that clean storage won't fire automatically.
        $categoryImages = ServiceCategory::whereNotNull('image')
            ->where('image', 'not like', 'http%')
            ->pluck('image')
            ->toArray();

        $serviceImages = Service::whereNotNull('image')
            ->where('image', 'not like', 'http%')
            ->pluck('image')
            ->toArray();

        $counts = DB::transaction(function () {
            $counts = [
                'categories' => ServiceCategory::count(),
                'offers'     => Offer::count(),
                'bookings'   => Booking::count(),
                'reviews'    => Review::count(),
                'customers'  => User::role('customer')->count(),
            ];

            // Step 1: Offers → cascades offer_service (offer_id FK)
            Offer::query()->delete();

            // Step 2: Bookings → cascades booking_items (booking_id FK)
            //                  → cascades reviews     (booking_id FK)
            Booking::query()->delete();

            // Step 3: ServiceCategories → cascades services
            //         services          → cascades booking_items (service_id FK — already empty)
            //                          → cascades reviews        (service_id FK — already empty)
            //                          → cascades offer_service  (service_id FK — already empty)
            ServiceCategory::query()->delete();

            // Step 4: Customer users → cascades customers (user_id FK)
            User::role('customer')->delete();

            return $counts;
        });

        // Clean up storage files after successful transaction
        $allImages = array_merge($categoryImages, $serviceImages);
        if ($allImages) {
            Storage::disk(config('filesystems.default'))->delete($allImages);
        }

        return $counts;
    }
}
