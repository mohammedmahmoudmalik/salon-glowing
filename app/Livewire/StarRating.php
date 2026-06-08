<?php

namespace App\Livewire;

use Livewire\Component;

class StarRating extends Component
{
    public int $rating = 0;

    public int $hovered = 0;

    public bool $interactive = false;

    public string $name = 'rating';

    public function setRating(int $value): void
    {
        if ($this->interactive) {
            $this->rating = $value;
            $this->dispatch('rating-updated', rating: $value, name: $this->name);
        }
    }

    public function setHovered(int $value): void
    {
        if ($this->interactive) {
            $this->hovered = $value;
        }
    }

    public function clearHovered(): void
    {
        $this->hovered = 0;
    }

    public function render()
    {
        return view('livewire.star-rating');
    }
}
