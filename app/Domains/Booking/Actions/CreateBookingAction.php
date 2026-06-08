<?php

namespace App\Domains\Booking\Actions;

use App\Domains\Admin\Models\Setting;
use App\Domains\Auth\Models\Customer;
use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Events\BookingCreated;
use App\Domains\Booking\Models\Booking;
use App\Domains\Booking\Models\BookingItem;
use App\Domains\Booking\Services\BookingAvailabilityService;
use App\Domains\Offer\Services\ActiveOfferService;
use App\Domains\Service\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CreateBookingAction
{
    public function __construct(
        private readonly BookingAvailabilityService $availability,
        private readonly ActiveOfferService $offerService,
    ) {}

    public function execute(Customer $customer, array $data): Booking
    {
        return DB::transaction(function () use ($customer, $data) {
            $serviceIds = $data['service_ids'];
            $bufferMinutes = (int) Setting::get('booking_buffer_minutes', 10);

            $services = Service::whereIn('id', $serviceIds)->get();

            $start = Carbon::parse($data['booking_date'].' '.$data['start_time']);
            $end = $this->availability->calculateEndTime($start, $serviceIds, $bufferMinutes);

            $this->availability->check(
                $serviceIds,
                $data['booking_date'],
                $data['start_time'],
                $bufferMinutes
            );

            $prices = $services->mapWithKeys(
                fn (Service $s) => [$s->id => $this->offerService->calculateFinalPrice($s)]
            );

            $booking = Booking::create([
                'customer_id'    => $customer->id,
                'booking_date'   => $data['booking_date'],
                'start_time'     => $data['start_time'],
                'end_time'       => $end->format('H:i'),
                'status'         => BookingStatus::Pending,
                'buffer_minutes' => $bufferMinutes,
                'total_price'    => $prices->sum(),
            ]);

            foreach ($services as $service) {
                BookingItem::create([
                    'booking_id'       => $booking->id,
                    'service_id'       => $service->id,
                    'price'            => $prices[$service->id],
                    'duration_minutes' => $service->duration_minutes,
                ]);
            }

            activity()
                ->performedOn($booking)
                ->causedBy($customer->user)
                ->log('booking_created');

            event(new BookingCreated($booking));

            return $booking->load(['items.service', 'customer.user']);
        });
    }
}
