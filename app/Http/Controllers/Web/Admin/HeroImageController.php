<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domains\Admin\Models\HeroImage;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HeroImageController extends Controller
{
    public function index(): View
    {
        $images = HeroImage::orderBy('order')->get();

        return view('admin.hero-images.index', compact('images'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $path = $request->file('image')->store('hero', config('filesystems.default'));

        HeroImage::create([
            'image_path' => $path,
            'order'      => (HeroImage::max('order') ?? 0) + 1,
            'is_active'  => true,
        ]);

        activity()->log('hero_image_uploaded');

        return redirect(url()->previous(route('admin.settings.show')).'#hero-images-section')
            ->with('success', __('messages.record_created'));
    }

    public function destroy(HeroImage $heroImage): RedirectResponse
    {
        if ($heroImage->image_path) {
            Storage::disk(config('filesystems.default'))->delete($heroImage->image_path);
        }

        $heroImage->delete();
        activity()->log('hero_image_deleted');

        return redirect(url()->previous(route('admin.settings.show')).'#hero-images-section')
            ->with('success', __('messages.record_deleted'));
    }

    public function toggle(HeroImage $heroImage): RedirectResponse
    {
        $heroImage->update(['is_active' => ! $heroImage->is_active]);

        return redirect(url()->previous(route('admin.settings.show')).'#hero-images-section')
            ->with('success', __('messages.record_updated'));
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:hero_images,id',
        ]);

        foreach ($request->ids as $index => $id) {
            HeroImage::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['ok' => true]);
    }
}
