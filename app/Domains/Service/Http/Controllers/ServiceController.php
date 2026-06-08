<?php

namespace App\Domains\Service\Http\Controllers;

use App\Domains\Service\Http\Resources\ServiceResource;
use App\Domains\Service\Models\Service;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $services = Service::active()
            ->with(['category', 'activeOffers'])
            ->when($request->category, fn ($q, $slug) => $q->whereHas('category', fn ($c) => $c->where('slug', $slug)))
            ->orderBy('name_en')
            ->paginate($request->integer('per_page', 15));

        return $this->paginatedSuccess($services, fn ($s) => new ServiceResource($s));
    }

    public function show(Service $service): JsonResponse
    {
        abort_unless($service->is_active, 404);

        return $this->success(new ServiceResource($service->load(['category', 'activeOffers'])));
    }
}
