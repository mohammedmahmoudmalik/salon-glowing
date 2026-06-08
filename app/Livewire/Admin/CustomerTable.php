<?php

namespace App\Livewire\Admin;

use App\Domains\Auth\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortBy = 'last_booking';

    public string $sortDir = 'desc';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'desc';
        }
        $this->resetPage();
    }

    public function render()
    {
        $locale = session('locale', config('app.locale'));

        $customers = Customer::with('user')
            ->withCount(['bookings'])
            ->withMax('bookings', 'booking_date')
            ->when($this->search, function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->sortBy === 'bookings_count', fn ($q) => $q->orderBy('bookings_count', $this->sortDir)
            )
            ->when($this->sortBy === 'last_booking', fn ($q) => $q->orderBy('bookings_max_booking_date', $this->sortDir)
            )
            ->paginate(15);

        return view('livewire.admin.customer-table',
            compact('customers', 'locale'));
    }
}
