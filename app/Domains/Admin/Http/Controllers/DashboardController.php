<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Admin\Services\DashboardStatsService;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request, DashboardStatsService $stats): JsonResponse
    {
        Gate::authorize('view-dashboard');

        return $this->success($stats->all());
    }
}
