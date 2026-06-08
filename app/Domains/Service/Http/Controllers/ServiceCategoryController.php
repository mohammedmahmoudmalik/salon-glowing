<?php

namespace App\Domains\Service\Http\Controllers;

use App\Domains\Service\Http\Resources\ServiceCategoryResource;
use App\Domains\Service\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $categories = ServiceCategory::active()
            ->withCount('services')
            ->orderBy('name_en')
            ->paginate($request->integer('per_page', 15));

        return $this->paginatedSuccess($categories, fn ($c) => new ServiceCategoryResource($c));
    }

    public function show(ServiceCategory $serviceCategory): JsonResponse
    {
        abort_unless($serviceCategory->is_active, 404);

        return $this->success(new ServiceCategoryResource($serviceCategory->loadCount('services')));
    }
}
