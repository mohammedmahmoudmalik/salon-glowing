<?php

namespace App\Http\Controllers\Web;

use App\Domains\Offer\Services\ActiveOfferService;
use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $categories = ServiceCategory::active()->orderBy('name_en')->get();
        $selectedCategory = $request->integer('category') ?: null;

        $services = Service::active()
            ->with('category')
            ->when($selectedCategory, fn ($q) => $q->where('service_category_id', $selectedCategory))
            ->orderBy('name_en')
            ->paginate(12);

        return view('services.index', compact('categories', 'services', 'selectedCategory'));
    }

    public function show(Service $service, ActiveOfferService $offerService): View
    {
        abort_unless($service->is_active, 404);

        $service->load(['category']);

        $activeOffer = $offerService->getForService($service);
        $finalPrice = $offerService->calculateFinalPrice($service, $activeOffer);

        return view('services.show', compact('service', 'activeOffer', 'finalPrice'));
    }
}
