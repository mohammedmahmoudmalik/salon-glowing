<?php

namespace App\Livewire;

use Livewire\Component;

class AvailableSlots extends Component
{
    public array $slots = [];

    public string $selected = '';

    public function selectSlot(string $slot): void
    {
        $this->selected = $slot;
        $this->dispatch('slot-selected', slot: $slot);
    }

    public function render()
    {
        return view('livewire.available-slots');
    }
}
