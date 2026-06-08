<?php

namespace App\Livewire;

use App\Domains\Admin\Models\Setting;
use App\Domains\Booking\Actions\CreateBookingAction;
use App\Domains\Booking\Services\BookingAvailabilityService;
use App\Domains\Service\Models\Service;
use App\Services\CartService;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class BookingForm extends Component
{
    public array $serviceIds = [];

    public string $bookingDate = '';

    public string $startTime = '';

    public string $notes = '';

    public array $availableSlots = [];

    public string $errorMessage = '';

    public bool $fromCart = false;

    public function mount(?int $serviceId = null): void
    {
        $cart = app(CartService::class);

        if ($serviceId) {
            $this->serviceIds = [$serviceId];
        } elseif ($cart->count() > 0) {
            $this->serviceIds = $cart->ids();
            $this->fromCart = true;
        }
    }

    public function updatedServiceIds(): void
    {
        $this->startTime = '';
        $this->availableSlots = [];
    }

    public function updatedBookingDate(): void
    {
        $this->startTime = '';
        $this->loadSlots();
    }

    private function loadSlots(): void
    {
        $this->availableSlots = [];

        if (! $this->bookingDate || empty($this->serviceIds)) {
            return;
        }

        $buffer = (int) Setting::get('booking_buffer_minutes', 10);

        $this->availableSlots = app(BookingAvailabilityService::class)
            ->getAvailableSlots($this->serviceIds, $this->bookingDate, $buffer);
    }

    public function removeService(int $serviceId): void
    {
        $cart = app(CartService::class);
        $cart->remove($serviceId);

        $this->dispatch('cart-updated', count: $cart->count());

        $this->serviceIds = array_values(array_filter(
            $this->serviceIds,
            fn ($id) => (int) $id !== $serviceId
        ));

        $this->startTime = '';
        $this->bookingDate = '';
        $this->availableSlots = [];

        if (empty($this->serviceIds)) {
            $this->redirect(route('cart.index'), navigate: true);
        }
    }

    public function selectSlot(string $slot): void
    {
        $this->startTime = $slot;
    }

    public function submit(): void
    {
        $this->errorMessage = '';

        $this->validate([
            'serviceIds'    => 'required|array|min:1',
            'serviceIds.*'  => 'exists:services,id',
            'bookingDate'   => 'required|date|after_or_equal:today',
            'startTime'     => 'required|string',
            'notes'         => 'nullable|string|max:500',
        ]);

        $customer = auth()->user()?->customer;

        if (! $customer) {
            $this->errorMessage = __('messages.unauthenticated');

            return;
        }

        try {
            app(CreateBookingAction::class)->execute($customer, [
                'service_ids'  => $this->serviceIds,
                'booking_date' => $this->bookingDate,
                'start_time'   => $this->startTime,
                'notes'        => $this->notes,
            ]);

            app(CartService::class)->clear();

            session()->flash('success', __('messages.booking_created'));

            $this->redirect(route('bookings.index'), navigate: true);
        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            $this->errorMessage = $firstError ?? __('messages.server_error');
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage() ?: __('messages.server_error');
        }
    }

    public function render()
    {
        $allServices = $this->fromCart
            ? collect()
            : Service::active()->with('category')->orderBy('name_en')->get();

        $selectedServices = empty($this->serviceIds)
            ? collect()
            : Service::whereIn('id', $this->serviceIds)->get();

        $buffer = max(0, $selectedServices->count() - 1) * 10;
        $totalDuration = $selectedServices->sum('duration_minutes') + $buffer;
        $totalPrice = $selectedServices->sum('price');

        $estimatedEndTime = ($this->startTime && $selectedServices->isNotEmpty())
            ? Carbon::parse($this->startTime)->addMinutes($totalDuration)->format('H:i')
            : null;

        return view('livewire.booking-form', compact(
            'allServices',
            'selectedServices',
            'totalDuration',
            'totalPrice',
            'estimatedEndTime'
        ));
    }
}
