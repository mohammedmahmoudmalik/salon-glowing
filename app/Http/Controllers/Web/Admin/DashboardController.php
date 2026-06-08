<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domains\Admin\Services\DashboardStatsService;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(DashboardStatsService $stats): View
    {
        return view('admin.dashboard', ['stats' => $stats->all()]);
    }
}
