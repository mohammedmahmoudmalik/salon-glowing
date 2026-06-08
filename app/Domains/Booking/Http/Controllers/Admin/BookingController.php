<?php

namespace App\Domains\Booking\Http\Controllers\Admin;

use App\Domains\Booking\Actions\ChangeBookingStatusAction;
use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Http\Requests\ChangeBookingStatusRequest;
use App\Domains\Booking\Http\Resources\BookingResource;
use App\Domains\Booking\Models\Booking;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('changeStatus', Booking::class);

        $bookings = Booking::with(['items.service', 'customer.user'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->date, fn ($q, $d) => $q->where('booking_date', $d))
            ->orderByDesc('booking_date')
            ->orderByDesc('start_time')
            ->paginate($request->integer('per_page', 15));

        return $this->paginatedSuccess($bookings, fn ($b) => new BookingResource($b));
    }

    public function changeStatus(
        ChangeBookingStatusRequest $request,
        Booking $booking,
        ChangeBookingStatusAction $action
    ): JsonResponse {
        $booking = $action->execute($booking, BookingStatus::from($request->validated()['status']));

        return $this->success(new BookingResource($booking), __('messages.booking_status_changed'));
    }
}
