<?php

namespace App\Domains\Review\Http\Controllers\Admin;

use App\Domains\Review\Http\Resources\ReviewResource;
use App\Domains\Review\Models\Review;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $reviews = Review::with(['service'])
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return $this->paginatedSuccess($reviews, fn ($r) => new ReviewResource($r));
    }

    public function hide(Review $review): JsonResponse
    {
        Gate::authorize('hide', $review);

        $review->update(['is_hidden' => true]);

        activity()->performedOn($review)->log('review_hidden');

        return $this->success(new ReviewResource($review), __('messages.record_updated'));
    }

    public function destroy(Review $review): JsonResponse
    {
        Gate::authorize('delete', $review);

        activity()->performedOn($review)->log('review_deleted');

        $review->delete();

        return $this->noContent();
    }
}
