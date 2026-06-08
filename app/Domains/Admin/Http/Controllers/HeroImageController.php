<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Admin\Models\HeroImage;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class HeroImageController extends Controller
{
    use ApiResponse;

    // Public endpoint — returns ordered array of active image URLs for the frontend carousel.
    public function index(): JsonResponse
    {
        $urls = HeroImage::active()
            ->get()
            ->map(fn (HeroImage $img) => $img->url)
            ->values();

        return $this->success($urls);
    }
}
