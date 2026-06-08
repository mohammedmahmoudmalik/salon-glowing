<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $__salonName = \App\Domains\Admin\Models\Setting::get('salon_name', config('app.name'));
        $__siteLogo  = \App\Domains\Admin\Models\Setting::get('site_logo');
        $__logoUrl   = $__siteLogo
            ? \Illuminate\Support\Facades\Storage::url($__siteLogo)
            : asset('images/logo.jpg');
    @endphp
    <title>{{ $title ?? $__salonName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @php
        use App\Domains\Admin\Models\Setting;
        $__hexRgx = '/^#[0-9A-Fa-f]{6}$/';
        $__palette = [
            'rose-gold'       => Setting::get('primary_color'),
            'rose-gold-light' => Setting::get('primary_color_light'),
            'rose-gold-dark'  => Setting::get('primary_color_dark'),
            'soft-pink'       => Setting::get('soft_pink_color'),
            'soft-pink-light' => Setting::get('soft_pink_light_color'),
            'beige'           => Setting::get('secondary_color'),
            'beige-dark'      => Setting::get('secondary_color_dark'),
            'salon-text'      => Setting::get('salon_text_color'),
        ];
        $__colorCss = '';
        foreach ($__palette as $__var => $__val) {
            if ($__val && preg_match($__hexRgx, $__val)) {
                $__colorCss .= "--color-{$__var}:{$__val};";
            }
        }
    @endphp
    @if($__colorCss)
    <style>:root { {!! $__colorCss !!} }</style>
    @endif
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-14 antialiased"
      style="background: radial-gradient(ellipse at 60% 0%, color-mix(in oklab, var(--color-soft-pink) 18%, transparent) 0%, transparent 60%), radial-gradient(ellipse at 20% 100%, color-mix(in oklab, var(--color-rose-gold) 10%, transparent) 0%, transparent 55%), var(--color-beige, #F5F0E8);">

    {{-- Language switcher --}}
    <div class="absolute top-5 inset-e-5">
        <a href="{{ app()->getLocale() === 'ar' ? route('lang.switch', 'en') : route('lang.switch', 'ar') }}"
           class="text-[11px] tracking-widest uppercase text-rose-gold/70 hover:text-rose-gold font-medium border border-rose-gold/25 hover:border-rose-gold/50 rounded px-2.5 py-1">
            {{ app()->getLocale() === 'ar' ? 'EN' : 'ع' }}
        </a>
    </div>

    <div class="w-full max-w-105">

        {{-- Brand mark --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block">
                <img src="{{ $__logoUrl }}"
                     alt="{{ $__salonName }}"
                     class="h-14 w-auto object-contain mx-auto"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <p class="text-2xl font-semibold text-rose-gold tracking-wide hidden">
                    {{ $__salonName }}
                </p>
            </a>
            <p class="mt-2 text-[11px] tracking-widest uppercase text-rose-gold/50 font-medium">
                {{ app()->getLocale() === 'ar' ? 'جمال · أناقة · راحة' : 'Beauty · Elegance · Serenity' }}
            </p>
        </div>

        {{-- Auth card --}}
        <div class="bg-white rounded-2xl p-8 sm:p-10"
             style="box-shadow: 0 8px 40px color-mix(in srgb,var(--color-rose-gold) 12%,transparent), 0 1px 4px color-mix(in srgb,var(--color-rose-gold) 8%,transparent);">
            {{ $slot }}
        </div>

        {{-- Back to home --}}
        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-[12px] text-gray-400 hover:text-rose-gold tracking-wide">
                ← {{ app()->getLocale() === 'ar' ? 'العودة للرئيسية' : 'Back to home' }}
            </a>
        </div>
    </div>

    @livewireScripts
</body>
</html>
