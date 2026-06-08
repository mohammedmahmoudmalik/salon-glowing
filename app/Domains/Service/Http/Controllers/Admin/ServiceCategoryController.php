<?php

namespace App\Domains\Service\Http\Controllers\Admin;

use App\Domains\Service\Actions\StoreServiceCategoryAction;
use App\Domains\Service\Actions\UpdateServiceCategoryAction;
use App\Domains\Service\Http\Requests\StoreServiceCategoryRequest;
use App\Domains\Service\Http\Requests\UpdateServiceCategoryRequest;
use App\Domains\Service\Http\Resources\ServiceCategoryResource;
use App\Domains\Service\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ServiceCategoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $categories = ServiceCategory::withCount('services')
            ->orderBy('name_en')
            ->paginate($request->integer('per_page', 15));

        return $this->paginatedSuccess($categories, fn ($c) => new ServiceCategoryResource($c));
    }

    public function store(StoreServiceCategoryRequest $request, StoreServiceCategoryAction $action): JsonResponse
    {
        $category = $action->execute($request->validated());

        return $this->created(new ServiceCategoryResource($category), __('messages.record_created'));
    }

    public function show(ServiceCategory $category): JsonResponse
    {
        return $this->success(new ServiceCategoryResource($category->loadCount('services')));
    }

    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $category, UpdateServiceCategoryAction $action): JsonResponse
    {
        $updated = $action->execute($category, $request->validated());

        return $this->success(new ServiceCategoryResource($updated), __('messages.record_updated'));
    }

    public function destroy(ServiceCategory $category): JsonResponse
    {
        Gate::authorize('delete', ServiceCategory::class);

        $category->delete();

        return $this->noContent();
    }
}
