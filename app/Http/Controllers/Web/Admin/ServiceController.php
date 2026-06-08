<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domains\Service\Actions\StoreServiceAction;
use App\Domains\Service\Actions\StoreServiceCategoryAction;
use App\Domains\Service\Actions\UpdateServiceAction;
use App\Domains\Service\Actions\UpdateServiceCategoryAction;
use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ServiceController extends Controller
{
    // ── Categories ──────────────────────────────────────────────────────────

    public function categoriesIndex(): View
    {
        $categories = ServiceCategory::withCount('services')->orderBy('name_en')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function categoryCreate(): View
    {
        return view('admin.categories.create');
    }

    public function categoryStore(Request $request, StoreServiceCategoryAction $action): RedirectResponse
    {
        Gate::authorize('manage', ServiceCategory::class);

        $data = $request->validate([
            'name_ar' => 'required|string|max:100',
            'name_en' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        $action->execute($data);

        return redirect()->route('admin.categories.index')->with('success', __('messages.record_created'));
    }

    public function categoryEdit(ServiceCategory $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function categoryUpdate(Request $request, ServiceCategory $category, UpdateServiceCategoryAction $action): RedirectResponse
    {
        Gate::authorize('manage', ServiceCategory::class);

        $data = $request->validate([
            'name_ar' => 'required|string|max:100',
            'name_en' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_image' => 'nullable|boolean',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        $action->execute($category, $data);

        return redirect()->route('admin.categories.index')->with('success', __('messages.record_updated'));
    }

    public function categoryDestroy(ServiceCategory $category): RedirectResponse
    {
        Gate::authorize('delete', $category);

        if ($category->services()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', __('messages.category_has_services'));
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', __('messages.category_deleted'));
    }

    public function categoryToggle(ServiceCategory $category): RedirectResponse
    {
        Gate::authorize('manage', ServiceCategory::class);
        $category->update(['is_active' => ! $category->is_active]);

        return redirect()->route('admin.categories.index')->with('success', __('messages.record_updated'));
    }

    // ── Services ─────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $categoryId = $request->get('category', '');

        $query = Service::with('category')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', '%'.$search.'%')
                    ->orWhere('name_en', 'like', '%'.$search.'%');
            });
        }

        if ($categoryId) {
            $query->where('service_category_id', $categoryId);
        }

        $services = $query->paginate(15)->withQueryString();
        $categories = ServiceCategory::active()->orderBy('name_ar')->get();

        return view('admin.services.index', compact('services', 'categories', 'search', 'categoryId'));
    }

    public function create(): View
    {
        $categories = ServiceCategory::active()->orderBy('name_ar')->get();

        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request, StoreServiceAction $action): RedirectResponse
    {
        Gate::authorize('manage', Service::class);

        $data = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name_ar' => 'required|string|max:150',
            'name_en' => 'required|string|max:150',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:5',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $action->execute($data);

        return redirect()->route('admin.services.index')->with('success', __('messages.record_created'));
    }

    public function show(Service $service): View
    {
        $service->load(['category', 'offers']);

        return view('admin.services.show', compact('service'));
    }

    public function edit(Service $service): View
    {
        $categories = ServiceCategory::active()->orderBy('name_ar')->get();

        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service, UpdateServiceAction $action): RedirectResponse
    {
        Gate::authorize('manage', $service);

        $data = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name_ar' => 'required|string|max:150',
            'name_en' => 'required|string|max:150',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:5',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'remove_image' => 'nullable|boolean',
        ]);

        $action->execute($service, $data);

        return redirect()->route('admin.services.index')->with('success', __('messages.record_updated'));
    }

    public function destroy(Service $service): RedirectResponse
    {
        Gate::authorize('delete', $service);
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', __('messages.record_deleted'));
    }
}
