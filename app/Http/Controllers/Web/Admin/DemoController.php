<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domains\Demo\Actions\ClearDemoDataAction;
use App\Domains\Demo\Actions\GenerateDemoDataAction;
use App\Domains\Demo\Http\Requests\GenerateDemoRequest;
use App\Domains\Service\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DemoController extends Controller
{
    public function index(): View
    {
        $hasServices = Service::active()->exists();

        return view('admin.demo.index', compact('hasServices'));
    }

    public function generate(GenerateDemoRequest $request, GenerateDemoDataAction $action): RedirectResponse
    {
        try {
            $stats = $action->execute(
                customerCount: (int) $request->customer_count,
                bookingCount:  (int) $request->booking_count,
                offerCount:    (int) $request->offer_count,
            );

            return redirect()->route('admin.demo.index')
                ->with('success', __('web.demo_success', [
                    'customers' => $stats['customers'],
                    'bookings'  => $stats['bookings'],
                    'offers'    => $stats['offers'],
                    'reviews'   => $stats['reviews'],
                ]));
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'no_services') {
                return redirect()->route('admin.demo.index')
                    ->with('error', __('web.demo_no_services'));
            }

            throw $e;
        }
    }

    public function clearAll(ClearDemoDataAction $action): RedirectResponse
    {
        $counts = $action->execute();

        activity()->log('demo_data_cleared');

        return redirect()->route('admin.demo.index')
            ->with('success', __('web.demo_clear_success', [
                'customers' => $counts['customers'],
                'bookings'  => $counts['bookings'],
                'reviews'   => $counts['reviews'],
                'offers'    => $counts['offers'],
            ]));
    }
}
