<?php

namespace App\Domains\Booking\Http\Controllers;

use App\Domains\Booking\Actions\CancelBookingAction;
use App\Domains\Booking\Actions\CreateBookingAction;
use App\Domains\Booking\Actions\RescheduleBookingAction;
use App\Domains\Booking\Http\Requests\CancelBookingRequest;
use App\Domains\Booking\Http\Requests\RescheduleBookingRequest;
use App\Domains\Booking\Http\Requests\StoreBookingRequest;
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
        $customer = $request->user()->customer;

        if (! $customer) {
            return $this->error(__('messages.forbidden'), 403);
        }

        $bookings = Booking::where('customer_id', $customer->id)
            ->with(['items.service'])
            ->orderByDesc('booking_date')
            ->orderByDesc('start_time')
            ->paginate($request->integer('per_page', 15));

        return $this->paginatedSuccess($bookings, fn ($b) => new BookingResource($b));
    }

    public function store(StoreBookingRequest $request, CreateBookingAction $action): JsonResponse
    {
        $booking = $action->execute($request->user()->customer, $request->validated());

        return $this->created(new BookingResource($booking), __('messages.booking_created'));
    }

    public function show(Request $request, Booking $booking): JsonResponse
    {
        Gate::authorize('view', $booking);

        return $this->success(
            new BookingResource($booking->load(['items.service', 'customer.user']))
        );
    }

    public function cancel(CancelBookingRequest $request, Booking $booking, CancelBookingAction $action): JsonResponse
    {
        $booking = $action->execute($booking, $request->user(), $request->input('reason'));

        return $this->success(new BookingResource($booking), __('messages.booking_cancelled'));
    }

    public function reschedule(RescheduleBookingRequest $request, Booking $booking, RescheduleBookingAction $action): JsonResponse
    {
        $booking = $action->execute($booking, $request->validated());

        return $this->success(new BookingResource($booking), __('messages.booking_rescheduled'));
    }
}
