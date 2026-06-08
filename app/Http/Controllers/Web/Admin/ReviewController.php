<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domains\Review\Models\Review;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::with(['customer.user', 'service'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleHide(Review $review): RedirectResponse
    {
        Gate::authorize('update', $review);
        $review->update(['is_hidden' => ! $review->is_hidden]);

        return back()->with('success', __('messages.record_updated'));
    }

    public function destroy(Review $review): RedirectResponse
    {
        Gate::authorize('delete', $review);
        $review->delete();

        return back()->with('success', __('messages.record_deleted'));
    }
}
