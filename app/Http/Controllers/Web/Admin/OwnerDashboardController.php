<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class OwnerDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.owner-dashboard', [
            'totalCategories' => ServiceCategory::count(),
            'totalServices'   => Service::count(),
        ]);
    }
}
