<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domains\Offer\Actions\StoreOfferAction;
use App\Domains\Offer\Actions\UpdateOfferAction;
use App\Domains\Offer\Models\Offer;
use App\Domains\Service\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(): View
    {
        $offers = Offer::with('services')->orderByDesc('created_at')->paginate(20);

        return view('admin.offers.index', compact('offers'));
    }

    public function create(): View
    {
        $services = Service::active()->orderBy('name_en')->get();

        return view('admin.offers.create', compact('services'));
    }

    public function store(Request $request, StoreOfferAction $action): RedirectResponse
    {
        Gate::authorize('create', Offer::class);

        $data = $request->validate([
            'title_ar' => 'required|string|max:200',
            'title_en' => 'required|string|max:200',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url|max:2000',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
        ]);

        if (empty($data['image']) && ! empty($data['image_url'])) {
            $data['image'] = $data['image_url'];
        }
        unset($data['image_url']);

        $action->execute($data);

        return redirect()->route('admin.offers.index')->with('success', __('messages.record_created'));
    }

    public function edit(Offer $offer): View
    {
        $services = Service::active()->orderBy('name_en')->get();
        $offer->load('services');

        return view('admin.offers.edit', compact('offer', 'services'));
    }

    public function update(Request $request, Offer $offer, UpdateOfferAction $action): RedirectResponse
    {
        Gate::authorize('update', $offer);

        $data = $request->validate([
            'title_ar' => 'required|string|max:200',
            'title_en' => 'required|string|max:200',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url|max:2000',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
        ]);

        if (empty($data['image']) && ! empty($data['image_url'])) {
            $data['image'] = $data['image_url'];
        }
        unset($data['image_url']);

        $action->execute($offer, $data);

        return redirect()->route('admin.offers.index')->with('success', __('messages.record_updated'));
    }

    public function destroy(Offer $offer): RedirectResponse
    {
        Gate::authorize('delete', $offer);
        $offer->delete();

        return redirect()->route('admin.offers.index')->with('success', __('messages.record_deleted'));
    }
}
