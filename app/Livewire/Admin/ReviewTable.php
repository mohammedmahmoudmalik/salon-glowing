<?php

namespace App\Livewire\Admin;

use App\Domains\Review\Models\Review;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function toggleHide(int $reviewId): void
    {
        $review = Review::findOrFail($reviewId);
        Gate::authorize('update', $review);
        $review->update(['is_hidden' => ! $review->is_hidden]);
    }

    public function delete(int $reviewId): void
    {
        $review = Review::findOrFail($reviewId);
        Gate::authorize('delete', $review);
        $review->delete();
    }

    public function render()
    {
        $reviews = Review::with(['customer.user', 'service'])
            ->when($this->search, fn ($q) => $q->whereHas(
                'customer.user',
                fn ($u) => $u->where('name', 'like', "%{$this->search}%")
            ))
            ->when($this->filter === 'hidden', fn ($q) => $q->where('is_hidden', true))
            ->when($this->filter === 'visible', fn ($q) => $q->where('is_hidden', false))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.admin.review-table', compact('reviews'));
    }
}
