<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domains\Admin\Actions\ProcessLogoAction;
use App\Domains\Admin\Actions\UploadOwnerVideoAction;
use App\Domains\Admin\Actions\UploadTourVideoAction;
use App\Domains\Admin\Models\HeroImage;
use App\Domains\Admin\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function show(): View
    {
        $salonHours = Setting::get('salon_working_hours', [
            'open' => '09:00',
            'close' => '21:00',
            'working_days' => [0, 1, 2, 3, 4],
        ]);
        $buffer = Setting::get('booking_buffer_minutes', 10);
        $images = HeroImage::orderBy('order')->get();

        $ownerVideoPath = Setting::get('owner_video');
        $tourVideoPath  = Setting::get('tour_video');
        $logoPath       = Setting::get('site_logo');
        $salonName      = Setting::get('salon_name', config('app.name'));
        $salonCountry   = Setting::get('salon_country', 'SA');

        $tourVideoTabAr    = Setting::get('tour_video_tab_ar', '');
        $tourVideoTabEn    = Setting::get('tour_video_tab_en', '');
        $tourVideoTitleAr  = Setting::get('tour_video_title_ar', '');
        $tourVideoTitleEn  = Setting::get('tour_video_title_en', '');
        $tourVideoDescAr   = Setting::get('tour_video_desc_ar', '');
        $tourVideoDescEn   = Setting::get('tour_video_desc_en', '');

        $ownerVideoTabAr   = Setting::get('owner_video_tab_ar', '');
        $ownerVideoTabEn   = Setting::get('owner_video_tab_en', '');
        $ownerVideoTitleAr = Setting::get('owner_video_title_ar', '');
        $ownerVideoTitleEn = Setting::get('owner_video_title_en', '');
        $ownerVideoDescAr  = Setting::get('owner_video_desc_ar', '');
        $ownerVideoDescEn  = Setting::get('owner_video_desc_en', '');

        $colors = [
            'primary_color'          => Setting::get('primary_color',          '#B76E79'),
            'primary_color_light'    => Setting::get('primary_color_light',    '#c98a93'),
            'primary_color_dark'     => Setting::get('primary_color_dark',     '#9a5a64'),
            'soft_pink_color'        => Setting::get('soft_pink_color',        '#F2A7BB'),
            'soft_pink_light_color'  => Setting::get('soft_pink_light_color',  '#f7c8d5'),
            'secondary_color'        => Setting::get('secondary_color',        '#F5F0E8'),
            'secondary_color_dark'   => Setting::get('secondary_color_dark',   '#E8E0CC'),
            'salon_text_color'       => Setting::get('salon_text_color',       '#3d2b2f'),
        ];

        $heroTitleAr    = Setting::get('hero_title_ar', '');
        $heroTitleEn    = Setting::get('hero_title_en', '');
        $heroSubtitleAr = Setting::get('hero_subtitle_ar', '');
        $heroSubtitleEn = Setting::get('hero_subtitle_en', '');
        $heroCtaAr      = Setting::get('hero_cta_ar', '');
        $heroCtaEn      = Setting::get('hero_cta_en', '');

        return view('admin.settings.show', compact(
            'salonHours', 'buffer', 'images',
            'ownerVideoPath', 'tourVideoPath', 'logoPath', 'salonName', 'salonCountry', 'colors',
            'heroTitleAr', 'heroTitleEn', 'heroSubtitleAr', 'heroSubtitleEn',
            'heroCtaAr', 'heroCtaEn',
            'tourVideoTabAr', 'tourVideoTabEn', 'tourVideoTitleAr', 'tourVideoTitleEn', 'tourVideoDescAr', 'tourVideoDescEn',
            'ownerVideoTabAr', 'ownerVideoTabEn', 'ownerVideoTitleAr', 'ownerVideoTitleEn', 'ownerVideoDescAr', 'ownerVideoDescEn',
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'open' => 'required|date_format:H:i',
            'close' => 'required|date_format:H:i|after:open',
            'working_days' => 'required|array',
            'working_days.*' => 'integer|between:0,6',
            'booking_buffer_minutes' => 'required|integer|min:0|max:60',
        ]);

        Setting::set('salon_working_hours', [
            'open' => $data['open'],
            'close' => $data['close'],
            'working_days' => array_map('intval', $data['working_days']),
        ]);

        Setting::set('booking_buffer_minutes', (int) $data['booking_buffer_minutes']);

        activity()->log('settings_updated');

        return back()->with('success', __('messages.record_updated'));
    }

    public function updateSocial(Request $request): RedirectResponse
    {
        $allowed = [
            'social_whatsapp', 'social_instagram', 'social_snapchat',
            'social_tiktok', 'social_twitter', 'social_facebook', 'google_maps_url',
        ];

        $request->validate([
            'social' => 'array',
            'social.*' => 'array',
            'social.*.url' => 'nullable|url|max:500',
            'social.*.is_active' => 'nullable',
        ]);

        foreach ($allowed as $key) {
            $data = $request->input("social.{$key}", []);
            Setting::set($key, [
                'url' => $data['url'] ?? '',
                'is_active' => isset($data['is_active']),
            ]);
        }

        activity()->log('social_settings_updated');

        return back()->with('success', __('messages.record_updated'));
    }

    public function updateBranding(Request $request, ProcessLogoAction $processLogo): RedirectResponse
    {
        $colorRule = ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'];

        $request->validate([
            'logo'                   => 'nullable|mimes:jpeg,jpg,png,webp,svg|max:2048',
            'salon_name'             => 'nullable|string|max:100',
            'primary_color'          => $colorRule,
            'primary_color_light'    => $colorRule,
            'primary_color_dark'     => $colorRule,
            'soft_pink_color'        => $colorRule,
            'soft_pink_light_color'  => $colorRule,
            'secondary_color'        => $colorRule,
            'secondary_color_dark'   => $colorRule,
            'salon_text_color'       => $colorRule,
        ]);

        if ($request->hasFile('logo')) {
            $path = $processLogo->execute(
                $request->file('logo'),
                Setting::get('site_logo'),
            );
            Setting::set('site_logo', $path);
        }

        if ($request->filled('salon_name')) {
            Setting::set('salon_name', $request->input('salon_name'));
        }

        $colorKeys = [
            'primary_color', 'primary_color_light', 'primary_color_dark',
            'soft_pink_color', 'soft_pink_light_color',
            'secondary_color', 'secondary_color_dark',
            'salon_text_color',
        ];
        foreach ($colorKeys as $key) {
            if ($request->filled($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        activity()->log('branding_settings_updated');

        return back()->with('success', __('messages.record_updated'));
    }

    public function updateCountry(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'salon_country' => ['required', 'in:SA,AE,EG'],
        ]);

        Setting::set('salon_country', $data['salon_country']);

        activity()->log('country_settings_updated');

        return back()->with('success', __('messages.record_updated'));
    }

    public function deleteLogo(): RedirectResponse
    {
        $logoPath = Setting::get('site_logo');

        if ($logoPath) {
            Storage::disk('public')->delete($logoPath);
            Setting::where('key', 'site_logo')->delete();
            Cache::forget('setting.site_logo');
        }

        activity()->log('logo_deleted');

        return back()->with('success', __('messages.record_deleted'));
    }

    public function updateHero(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hero_title_ar' => 'nullable|string|max:200',
            'hero_title_en' => 'nullable|string|max:200',
            'hero_subtitle_ar' => 'nullable|string|max:500',
            'hero_subtitle_en' => 'nullable|string|max:500',
            'hero_cta_ar' => 'nullable|string|max:100',
            'hero_cta_en' => 'nullable|string|max:100',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        activity()->log('hero_settings_updated');

        return back()->with('success', __('messages.record_updated'));
    }

    public function updateTourVideoTexts(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tour_video_tab_ar'   => 'nullable|string|max:60',
            'tour_video_tab_en'   => 'nullable|string|max:60',
            'tour_video_title_ar' => 'nullable|string|max:120',
            'tour_video_title_en' => 'nullable|string|max:120',
            'tour_video_desc_ar'  => 'nullable|string|max:400',
            'tour_video_desc_en'  => 'nullable|string|max:400',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value ?? '');
            Cache::forget("setting.{$key}");
        }

        activity()->log('tour_video_texts_updated');

        return back()->with('success', __('messages.record_updated'));
    }

    public function updateOwnerVideoTexts(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'owner_video_tab_ar'   => 'nullable|string|max:60',
            'owner_video_tab_en'   => 'nullable|string|max:60',
            'owner_video_title_ar' => 'nullable|string|max:120',
            'owner_video_title_en' => 'nullable|string|max:120',
            'owner_video_desc_ar'  => 'nullable|string|max:400',
            'owner_video_desc_en'  => 'nullable|string|max:400',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value ?? '');
            Cache::forget("setting.{$key}");
        }

        activity()->log('owner_video_texts_updated');

        return back()->with('success', __('messages.record_updated'));
    }

    public function updateTourVideo(Request $request, UploadTourVideoAction $uploadVideo): RedirectResponse
    {
        $request->validate([
            'tour_video' => 'required|file|mimes:mp4,webm,mov,ogg|max:102400',
        ]);

        $path = $uploadVideo->execute(
            $request->file('tour_video'),
            Setting::get('tour_video'),
        );

        Setting::set('tour_video', $path);

        activity()->log('tour_video_uploaded');

        return back()->with('success', __('messages.record_updated'));
    }

    public function deleteTourVideo(): RedirectResponse
    {
        $path = Setting::get('tour_video');

        if ($path) {
            Storage::disk('public')->delete($path);
            Setting::where('key', 'tour_video')->delete();
            Cache::forget('setting.tour_video');
        }

        activity()->log('tour_video_deleted');

        return back()->with('success', __('messages.record_deleted'));
    }

    public function updateOwnerVideo(Request $request, UploadOwnerVideoAction $uploadVideo): RedirectResponse
    {
        $request->validate([
            'owner_video' => 'required|file|mimes:mp4,webm,mov,ogg|max:102400',
        ]);

        $path = $uploadVideo->execute(
            $request->file('owner_video'),
            Setting::get('owner_video'),
        );

        Setting::set('owner_video', $path);

        activity()->log('owner_video_uploaded');

        return back()->with('success', __('messages.record_updated'));
    }

    public function deleteOwnerVideo(): RedirectResponse
    {
        $path = Setting::get('owner_video');

        if ($path) {
            Storage::disk('public')->delete($path);
            Setting::where('key', 'owner_video')->delete();
            Cache::forget('setting.owner_video');
        }

        activity()->log('owner_video_deleted');

        return back()->with('success', __('messages.record_deleted'));
    }
}
