<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domains\Service\Actions\ClearAllServicesAction;
use App\Domains\Service\Actions\ExportServicesFixtureAction;
use App\Domains\Service\Actions\ImportServicesFromCsvAction;
use App\Domains\Service\Http\Requests\ImportServicesRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ServiceSetupController extends Controller
{
    public function index(): View
    {
        return view('admin.setup.index');
    }

    public function import(ImportServicesRequest $request, ImportServicesFromCsvAction $action): RedirectResponse
    {
        $result = $action->execute($request->file('csv_file'));

        return redirect()->route('admin.setup.index')
            ->with('success', __('web.setup_import_success', [
                'categories' => $result['categories'],
                'services'   => $result['services'],
            ]));
    }

    public function export(ExportServicesFixtureAction $action): RedirectResponse
    {
        $action->execute();

        return redirect()->route('admin.setup.index')
            ->with('success', __('web.setup_export_success'));
    }

    public function clearAll(ClearAllServicesAction $action): RedirectResponse
    {
        $counts = $action->execute();

        activity()->log('services_cleared_all');

        return redirect()->route('admin.setup.index')
            ->with('success', __('web.setup_clear_success', [
                'categories' => $counts['categories'],
                'offers'     => $counts['offers'],
                'bookings'   => $counts['bookings'],
                'reviews'    => $counts['reviews'],
                'customers'  => $counts['customers'],
            ]));
    }

    public function downloadTemplate(): Response
    {
        $bom = "\xEF\xBB\xBF";
        $csv = $bom."category_name_ar,category_name_en,service_name_ar,service_name_en,price,duration_minutes\n";
        $csv .= "قص الشعر,Hair Cut,قص عادي,Regular Cut,50,30\n";
        $csv .= "قص الشعر,Hair Cut,قص + تسريح,Cut & Blowdry,80,60\n";
        $csv .= "العناية بالبشرة,Skin Care,فيشل بيسك,Basic Facial,120,45\n";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="services-template.csv"',
        ]);
    }
}
