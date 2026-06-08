<?php

namespace App\Domains\Offer\Http\Controllers;

use App\Domains\Offer\Http\Resources\OfferResource;
use App\Domains\Offer\Models\Offer;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $offers = Offer::active()
            ->with('services')
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return $this->paginatedSuccess($offers, fn ($o) => new OfferResource($o));
    }
}
