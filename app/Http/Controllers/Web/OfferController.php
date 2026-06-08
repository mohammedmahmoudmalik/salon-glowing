<?php

namespace App\Http\Controllers\Web;

use App\Domains\Offer\Models\Offer;
use App\Domains\Offer\Services\ActiveOfferService;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(): View
    {
        $offers = Offer::active()
            ->with('services.category')
            ->latest()
            ->paginate(12);

        return view('offers.index', compact('offers'));
    }

    public function show(Offer $offer, ActiveOfferService $offerService): View
    {
        abort_if(! $offer->isActive(), 404);

        $offer->load('services.category');

        $servicesWithPrices = $offer->services->map(fn ($service) => [
            'service' => $service,
            'finalPrice' => $offerService->calculateFinalPrice($service, $offer),
        ]);

        return view('offers.show', compact('offer', 'servicesWithPrices'));
    }
}
