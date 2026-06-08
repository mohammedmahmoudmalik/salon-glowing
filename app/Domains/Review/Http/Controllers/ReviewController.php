<?php

namespace App\Domains\Review\Http\Controllers;

use App\Domains\Booking\Models\Booking;
use App\Domains\Review\Actions\CreateReviewAction;
use App\Domains\Review\Http\Requests\StoreReviewRequest;
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
        $reviews = Review::visible()
            ->with(['service'])
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return $this->paginatedSuccess($reviews, fn ($r) => new ReviewResource($r));
    }

    public function store(StoreReviewRequest $request, Booking $booking, CreateReviewAction $action): JsonResponse
    {
        Gate::authorize('create', Review::class);

        $review = $action->execute($request->user()->customer, $booking, $request->validated());

        return $this->created(new ReviewResource($review), __('messages.record_created'));
    }
}
