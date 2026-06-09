<?php

namespace App\Domains\Booking\Services;

use App\Domains\Admin\Models\Setting;
use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Models\Booking;
use App\Domains\Service\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class BookingAvailabilityService
{
    public function calculateEndTime(
        Carbon $startTime,
        array $serviceIds,
        int $bufferMinutes = 10
    ): Carbon {
        $totalMinutes = Service::whereIn('id', $serviceIds)->sum('duration_minutes');
        $bufferTotal = max(0, count($serviceIds) - 1) * $bufferMinutes;

        return $startTime->copy()->addMinutes($totalMinutes + $bufferTotal);
    }

    public function check(
        array $serviceIds,
        string $bookingDate,
        string $startTime,
        int $bufferMinutes = 10,
        ?int $excludeBookingId = null
    ): void {
        $services = Service::whereIn('id', $serviceIds)->get();

        foreach ($services as $service) {
            if (! $service->is_active) {
                throw ValidationException::withMessages([
                    'service_ids' => [__('messages.service_not_available')],
                ]);
            }
        }

        $start = Carbon::parse($bookingDate.' '.$startTime);
        $end = $this->calculateEndTime($start, $serviceIds, $bufferMinutes);

        $this->checkSalonHours($bookingDate, $start, $end);
        $this->checkCapacity($bookingDate, $start, $end, $bufferMinutes, $excludeBookingId);
    }

    private function checkSalonHours(string $bookingDate, Carbon $start, Carbon $end): void
    {
        $salonHours = Setting::get('salon_working_hours');

        if (! $salonHours) {
            throw ValidationException::withMessages([
                'booking_date' => [__('messages.salon_closed')],
            ]);
        }

        $dayOfWeek = Carbon::parse($bookingDate)->dayOfWeek;
        $workingDays = $salonHours['working_days'] ?? [0, 1, 2, 3, 4];

        if (! in_array($dayOfWeek, $workingDays)) {
            throw ValidationException::withMessages([
                'booking_date' => [__('messages.salon_closed')],
            ]);
        }

        $salonOpen = Carbon::parse($bookingDate.' '.$salonHours['open']);
        $salonClose = Carbon::parse($bookingDate.' '.$salonHours['close']);

        if ($start->lt($salonOpen) || $end->gt($salonClose)) {
            throw ValidationException::withMessages([
                'start_time' => [__('messages.outside_salon_hours')],
            ]);
        }
    }

    private function checkCapacity(
        string $bookingDate,
        Carbon $start,
        Carbon $end,
        int $bufferMinutes,
        ?int $excludeBookingId
    ): void {
        $capacity = (int) Setting::get('salon_capacity', 3);

        $concurrent = Booking::where('booking_date', $bookingDate)
            ->whereNotIn('status', [BookingStatus::Cancelled->value, BookingStatus::NoShow->value])
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->where(function ($query) use ($start, $end, $bufferMinutes) {
                $query
                    ->whereRaw('ADDTIME(end_time, SEC_TO_TIME(? * 60)) > ?', [$bufferMinutes, $start->format('H:i:s')])
                    ->whereRaw('start_time < ADDTIME(?, SEC_TO_TIME(? * 60))', [$end->format('H:i:s'), $bufferMinutes]);
            })
            ->lockForUpdate()
            ->count();

        if ($concurrent >= $capacity) {
            throw ValidationException::withMessages([
                'start_time' => [__('messages.time_slot_taken')],
            ]);
        }
    }

    public function getAvailableSlots(
        array $serviceIds,
        string $bookingDate,
        int $bufferMinutes = 10,
        ?int $excludeBookingId = null
    ): array {
        $salonHours = Setting::get('salon_working_hours');

        if (! $salonHours) {
            return [];
        }

        $dayOfWeek = Carbon::parse($bookingDate)->dayOfWeek;
        $workingDays = $salonHours['working_days'] ?? [0, 1, 2, 3, 4];

        if (! in_array($dayOfWeek, $workingDays)) {
            return [];
        }

        $slotStart = Carbon::parse($bookingDate.' '.$salonHours['open']);
        $slotEnd = Carbon::parse($bookingDate.' '.$salonHours['close']);

        $totalDuration = Service::whereIn('id', $serviceIds)->sum('duration_minutes')
            + max(0, count($serviceIds) - 1) * $bufferMinutes;

        $capacity = (int) Setting::get('salon_capacity', 3);

        $bookings = Booking::where('booking_date', $bookingDate)
            ->whereNotIn('status', [BookingStatus::Cancelled->value, BookingStatus::NoShow->value])
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->get(['start_time', 'end_time']);

        $slots = [];
        $current = $slotStart->copy();
        $now = now();
        $isToday = $bookingDate === $now->toDateString();

        while ($current->copy()->addMinutes($totalDuration)->lte($slotEnd)) {
            // Skip slots that have already started (or will start within the buffer) for today
            if ($isToday && $current->lte($now)) {
                $current->addMinutes(15);

                continue;
            }

            $appointmentEnd = $current->copy()->addMinutes($totalDuration);
            $concurrent = 0;

            foreach ($bookings as $booking) {
                $bStart = Carbon::parse($bookingDate.' '.$booking->start_time);
                $bEnd = Carbon::parse($bookingDate.' '.$booking->end_time);

                if ($appointmentEnd->copy()->addMinutes($bufferMinutes)->gt($bStart) &&
                    $bEnd->copy()->addMinutes($bufferMinutes)->gt($current)) {
                    $concurrent++;
                }
            }

            if ($concurrent < $capacity) {
                $slots[] = $current->format('H:i');
            }

            $current->addMinutes(15);
        }

        return $slots;
    }
}
