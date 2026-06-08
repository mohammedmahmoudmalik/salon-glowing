<?php

namespace App\Domains\Admin\Services;

use App\Domains\Auth\Models\Customer;
use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Models\Booking;
use App\Domains\Booking\Models\BookingItem;
use App\Domains\Review\Models\Review;

class DashboardStatsService
{
    public function all(): array
    {
        return [
            'total_bookings'     => $this->totalBookings(),
            'today_bookings'     => $this->todayBookings(),
            'total_customers'    => $this->totalCustomers(),
            'top_services'       => $this->topServices(),
            'average_rating'     => $this->averageRating(),
            'revenue_today'      => $this->revenueToday(),
            'revenue_this_month' => $this->revenueThisMonth(),
        ];
    }

    private function totalBookings(): int
    {
        return Booking::count();
    }

    private function todayBookings(): int
    {
        return Booking::whereDate('booking_date', today())->count();
    }

    private function totalCustomers(): int
    {
        return Customer::count();
    }

    private function topServices(): array
    {
        return BookingItem::select('service_id', \DB::raw('count(*) as bookings_count'))
            ->with('service:id,name_ar,name_en')
            ->groupBy('service_id')
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'service_id'     => $item->service_id,
                'name_ar'        => $item->service?->name_ar,
                'name_en'        => $item->service?->name_en,
                'bookings_count' => $item->bookings_count,
            ])
            ->values()
            ->all();
    }

    private function averageRating(): ?float
    {
        $avg = Review::visible()->avg('rating');

        return $avg !== null ? round((float) $avg, 2) : null;
    }

    private function revenueToday(): float
    {
        return (float) Booking::where('status', BookingStatus::Completed)
            ->whereDate('booking_date', today())
            ->sum('total_price');
    }

    private function revenueThisMonth(): float
    {
        return (float) Booking::where('status', BookingStatus::Completed)
            ->whereYear('booking_date', now()->year)
            ->whereMonth('booking_date', now()->month)
            ->sum('total_price');
    }
}
