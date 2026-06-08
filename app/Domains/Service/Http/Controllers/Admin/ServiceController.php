<?php

namespace App\Domains\Service\Http\Controllers\Admin;

use App\Domains\Service\Actions\StoreServiceAction;
use App\Domains\Service\Actions\UpdateServiceAction;
use App\Domains\Service\Http\Requests\StoreServiceRequest;
use App\Domains\Service\Http\Requests\UpdateServiceRequest;
use App\Domains\Service\Http\Resources\ServiceResource;
use App\Domains\Service\Models\Service;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ServiceController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $services = Service::with(['category', 'activeOffers'])
            ->orderBy('name_en')
            ->paginate($request->integer('per_page', 15));

        return $this->paginatedSuccess($services, fn ($s) => new ServiceResource($s));
    }

    public function store(StoreServiceRequest $request, StoreServiceAction $action): JsonResponse
    {
        $service = $action->execute($request->validated());

        return $this->created(new ServiceResource($service), __('messages.record_created'));
    }

    public function show(Service $service): JsonResponse
    {
        return $this->success(new ServiceResource($service->load(['category', 'activeOffers'])));
    }

    public function update(UpdateServiceRequest $request, Service $service, UpdateServiceAction $action): JsonResponse
    {
        $service = $action->execute($service, $request->validated());

        return $this->success(new ServiceResource($service), __('messages.record_updated'));
    }

    public function destroy(Service $service): JsonResponse
    {
        Gate::authorize('delete', Service::class);

        $service->delete();

        return $this->noContent();
    }
}
