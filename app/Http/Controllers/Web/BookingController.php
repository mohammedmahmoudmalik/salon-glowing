<?php

namespace App\Http\Controllers\Web;

use App\Domains\Booking\Actions\CancelBookingAction;
use App\Domains\Booking\Actions\RescheduleBookingAction;
use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Models\Booking;
use App\Domains\Review\Actions\CreateReviewAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(Request $request): View
    {
        $serviceId = $request->query('service_id');

        return view('booking.create', compact('serviceId'));
    }

    public function index(): View|RedirectResponse
    {
        $customer = auth()->user()->customer;

        if (! $customer) {
            return redirect()->route('home');
        }

        $bookings = Booking::where('customer_id', $customer->id)
            ->with(['items.service'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('booking.index', compact('bookings'));
    }

    public function cancel(Booking $booking, CancelBookingAction $action): RedirectResponse
    {
        abort_unless($booking->customer_id === auth()->user()->customer?->id, 403);

        try {
            $action->execute($booking, auth()->user());

            return back()->with('success', __('messages.booking_cancelled'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rescheduleForm(Booking $booking): View
    {
        abort_unless($booking->customer_id === auth()->user()->customer?->id, 403);

        return view('booking.reschedule', compact('booking'));
    }

    public function reschedule(Booking $booking, Request $request, RescheduleBookingAction $action): RedirectResponse
    {
        abort_unless($booking->customer_id === auth()->user()->customer?->id, 403);

        $data = $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
        ]);

        try {
            $action->execute($booking, $data);

            return redirect()->route('bookings.index')->with('success', __('messages.booking_rescheduled'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reviewForm(Booking $booking): View
    {
        abort_unless($booking->customer_id === auth()->user()->customer?->id, 403);
        abort_unless($booking->status === BookingStatus::Completed, 404);

        return view('booking.review', compact('booking'));
    }

    public function storeReview(Booking $booking, Request $request, CreateReviewAction $action): RedirectResponse
    {
        abort_unless($booking->customer_id === auth()->user()->customer?->id, 403);

        $data = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            $action->execute(auth()->user()->customer, $booking, $data);

            return redirect()->route('bookings.index')->with('success', __('messages.record_created'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
