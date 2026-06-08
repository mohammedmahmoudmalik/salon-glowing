<?php

namespace App\Livewire\Admin;

use App\Domains\Booking\Actions\ChangeBookingStatusAction;
use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Models\Booking;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BookingTable extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public string $sortBy = 'booking_date';

    public string $sortDir = 'desc';

    private const SORTABLE = ['booking_date', 'total_price', 'start_time'];

    public function filterStatus(string $value): void
    {
        $this->status = $value;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if (! in_array($column, self::SORTABLE, true)) {
            return;
        }

        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'desc';
        }

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function changeStatus(int $bookingId, string $newStatus): void
    {
        $booking = Booking::findOrFail($bookingId);

        try {
            app(ChangeBookingStatusAction::class)->execute($booking, BookingStatus::from($newStatus));
            session()->flash('success', __('messages.booking_status_changed'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }

        $this->dispatch('statusChanged');
    }

    public function render()
    {
        $bookings = Booking::with(['customer.user', 'items.service'])
            ->when($this->search, fn ($q) => $q->whereHas(
                'customer.user',
                fn ($u) => $u->where('name', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
            ))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('booking_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('booking_date', '<=', $this->dateTo))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(20);

        $locale = session('locale', config('app.locale'));

        return view('livewire.admin.booking-table', [
            'bookings' => $bookings,
            'statuses' => BookingStatus::cases(),
            'locale' => $locale,
        ]);
    }
}
