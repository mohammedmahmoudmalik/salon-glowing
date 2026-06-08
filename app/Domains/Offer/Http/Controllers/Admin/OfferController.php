<?php

namespace App\Domains\Offer\Http\Controllers\Admin;

use App\Domains\Offer\Actions\StoreOfferAction;
use App\Domains\Offer\Actions\UpdateOfferAction;
use App\Domains\Offer\Http\Requests\StoreOfferRequest;
use App\Domains\Offer\Http\Requests\UpdateOfferRequest;
use App\Domains\Offer\Http\Resources\OfferResource;
use App\Domains\Offer\Models\Offer;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OfferController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Offer::class);

        $offers = Offer::with('services')
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return $this->paginatedSuccess($offers, fn ($o) => new OfferResource($o));
    }

    public function store(StoreOfferRequest $request, StoreOfferAction $action): JsonResponse
    {
        Gate::authorize('create', Offer::class);

        $offer = $action->execute($request->validated());

        return $this->created(new OfferResource($offer), __('messages.record_created'));
    }

    public function show(Offer $offer): JsonResponse
    {
        Gate::authorize('view', $offer);

        return $this->success(new OfferResource($offer->load('services')));
    }

    public function update(UpdateOfferRequest $request, Offer $offer, UpdateOfferAction $action): JsonResponse
    {
        Gate::authorize('update', $offer);

        $offer = $action->execute($offer, $request->validated());

        return $this->success(new OfferResource($offer), __('messages.record_updated'));
    }

    public function destroy(Offer $offer): JsonResponse
    {
        Gate::authorize('delete', $offer);

        activity()->performedOn($offer)->log('offer_deleted');

        $offer->delete();

        return $this->noContent();
    }
}
