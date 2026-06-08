<?php

namespace App\Http\Controllers\Web;

use App\Domains\Admin\Models\Setting;
use App\Domains\Offer\Models\Offer;
use App\Domains\Service\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $services = Service::active()
            ->with('category')
            ->orderBy('name_en')
            ->take(6)
            ->get();

        $offers = Offer::active()
            ->with('services')
            ->latest()
            ->take(3)
            ->get();

        $heroTitleAr    = Setting::get('hero_title_ar', '');
        $heroTitleEn    = Setting::get('hero_title_en', '');
        $heroSubtitleAr = Setting::get('hero_subtitle_ar', '');
        $heroSubtitleEn = Setting::get('hero_subtitle_en', '');
        $heroCtaAr      = Setting::get('hero_cta_ar', '');
        $heroCtaEn      = Setting::get('hero_cta_en', '');

        $ownerVideoPath = Setting::get('owner_video');
        $ownerVideoUrl  = $ownerVideoPath
            ? \Illuminate\Support\Facades\Storage::url($ownerVideoPath)
            : null;

        $tourVideoPath = Setting::get('tour_video');
        $tourVideoUrl  = $tourVideoPath
            ? \Illuminate\Support\Facades\Storage::url($tourVideoPath)
            : null;

        $tourVideoTabAr    = Setting::get('tour_video_tab_ar')   ?: __('web.tab_tour');
        $tourVideoTabEn    = Setting::get('tour_video_tab_en')   ?: __('web.tab_tour');
        $tourVideoTitleAr  = Setting::get('tour_video_title_ar') ?: __('web.tour_title');
        $tourVideoTitleEn  = Setting::get('tour_video_title_en') ?: __('web.tour_title');
        $tourVideoDescAr   = Setting::get('tour_video_desc_ar')  ?: __('web.tour_desc');
        $tourVideoDescEn   = Setting::get('tour_video_desc_en')  ?: __('web.tour_desc');

        $ownerVideoTabAr   = Setting::get('owner_video_tab_ar')   ?: __('web.tab_owner');
        $ownerVideoTabEn   = Setting::get('owner_video_tab_en')   ?: __('web.tab_owner');
        $ownerVideoTitleAr = Setting::get('owner_video_title_ar') ?: __('web.owner_story_title');
        $ownerVideoTitleEn = Setting::get('owner_video_title_en') ?: __('web.owner_story_title');
        $ownerVideoDescAr  = Setting::get('owner_video_desc_ar')  ?: __('web.owner_story_desc');
        $ownerVideoDescEn  = Setting::get('owner_video_desc_en')  ?: __('web.owner_story_desc');

        return view('home', compact(
            'services', 'offers',
            'heroTitleAr', 'heroTitleEn',
            'heroSubtitleAr', 'heroSubtitleEn',
            'heroCtaAr', 'heroCtaEn',
            'ownerVideoUrl', 'tourVideoUrl',
            'tourVideoTabAr', 'tourVideoTabEn', 'tourVideoTitleAr', 'tourVideoTitleEn', 'tourVideoDescAr', 'tourVideoDescEn',
            'ownerVideoTabAr', 'ownerVideoTabEn', 'ownerVideoTitleAr', 'ownerVideoTitleEn', 'ownerVideoDescAr', 'ownerVideoDescEn',
        ));
    }
}
