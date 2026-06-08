<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domains\Auth\Models\Customer;
use App\Domains\Booking\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        return view('admin.customers.index');
    }

    public function bookings(Customer $customer): View
    {
        $customer->load('user');

        $bookings = Booking::with(['items.service'])
            ->where('customer_id', $customer->id)
            ->latest('booking_date')
            ->paginate(15);

        return view('admin.customers.bookings',
            compact('customer', 'bookings'));
    }
}
