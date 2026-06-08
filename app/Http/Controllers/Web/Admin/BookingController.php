<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domains\Booking\Actions\CancelBookingAction;
use App\Domains\Booking\Actions\ChangeBookingStatusAction;
use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::with(['customer.user', 'items.service'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('booking_date')->orderByDesc('start_time')
            ->paginate(20)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'customer.user',
            'items.service.category',
            'reviews',
        ]);

        return view('admin.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking, CancelBookingAction $action): RedirectResponse
    {
        try {
            $action->execute($booking, auth()->user());

            return redirect()->route('admin.bookings.show', $booking)
                ->with('success', __('messages.booking_cancelled'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function changeStatus(Booking $booking, Request $request, ChangeBookingStatusAction $action): RedirectResponse
    {
        $data = $request->validate(['status' => 'required|string|in:confirmed,completed,no_show']);

        try {
            $action->execute($booking, BookingStatus::from($data['status']));

            return back()->with('success', __('messages.booking_status_changed'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
