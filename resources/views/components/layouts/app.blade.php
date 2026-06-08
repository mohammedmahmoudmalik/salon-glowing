<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $__salonName = \App\Domains\Admin\Models\Setting::get('salon_name', config('app.name')); @endphp
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
        $__siteLogo  = Setting::get('site_logo');
        $__logoUrl   = $__siteLogo
            ? \Illuminate\Support\Facades\Storage::url($__siteLogo)
            : asset('images/logo.jpg');
    @endphp
    @if($__colorCss)
    <style>:root { {!! $__colorCss !!} }</style>
    @endif
</head>
<body class="min-h-screen bg-beige text-salon-text antialiased">

    {{-- ── Social media links data ──────────────────────── --}}
    @php
        $socialSvgs = [
            'social_whatsapp'  => ['label' => 'WhatsApp',  'svg' => '<svg viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>'],
            'social_instagram' => ['label' => 'Instagram', 'svg' => '<svg viewBox="0 0 24 24"><defs><linearGradient id="ig-grad" x1="0%" y1="100%" x2="100%" y2="0%"><stop offset="0%" style="stop-color:#f09433"/><stop offset="25%" style="stop-color:#e6683c"/><stop offset="50%" style="stop-color:#dc2743"/><stop offset="75%" style="stop-color:#cc2366"/><stop offset="100%" style="stop-color:#bc1888"/></linearGradient></defs><path fill="url(#ig-grad)" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>'],
            'social_snapchat'  => ['label' => 'Snapchat',  'svg' => '<svg viewBox="0 0 24 24" fill="#FFFC00" stroke="#888" stroke-width="0.3"><path d="M12.206.793c.99 0 4.347.276 5.93 3.821.529 1.193.403 3.219.299 4.847l-.003.06c-.012.18-.022.345-.03.51.075.045.203.09.401.09.3-.016.659-.12 1.033-.301.165-.088.344-.104.464-.104.182 0 .359.029.509.09.45.149.734.479.734.838.015.449-.39.839-1.213 1.168-.089.029-.209.075-.344.119-.45.135-1.139.36-1.333.81-.09.224-.061.524.12.868l.015.015c.06.136 1.526 3.468 4.791 4.013.255.044.435.27.42.509 0 .075-.015.149-.045.225-.24.569-1.273.988-3.146 1.271-.059.091-.12.375-.164.57-.029.179-.074.36-.134.553-.076.271-.27.405-.555.405h-.03c-.135 0-.313-.031-.538-.074-.36-.075-.765-.135-1.273-.135-.3 0-.599.015-.913.074-.6.104-1.123.464-1.723.884-.853.599-1.826 1.288-3.294 1.288-.06 0-.119-.015-.018-.015h-.06c-1.469 0-2.427-.675-3.279-1.288-.599-.42-1.107-.779-1.707-.884-.314-.045-.629-.074-.928-.074-.54 0-.958.089-1.272.149-.211.043-.391.074-.54.074-.374 0-.523-.224-.583-.42-.061-.192-.09-.389-.135-.567-.046-.181-.105-.494-.166-.57-1.918-.222-2.95-.642-3.189-1.226-.031-.063-.052-.15-.055-.225-.015-.243.165-.465.42-.509 3.264-.54 4.73-3.87 4.791-4.013l.016-.029c.18-.345.224-.645.119-.869-.195-.434-.884-.658-1.332-.809-.121-.029-.24-.074-.346-.119-1.107-.435-1.257-.93-1.197-1.273.09-.479.674-.793 1.168-.793.139 0 .293.029.479.09.405.195.748.3 1.05.3.224 0 .378-.06.479-.105l-.031-.569c-.098-1.626-.225-3.651.301-4.845C7.748 1.07 11.111.793 12.101.793h.105z"/></svg>'],
            'social_tiktok'    => ['label' => 'TikTok',    'svg' => '<svg viewBox="0 0 24 24" fill="#000000"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.78a4.85 4.85 0 01-1.01-.09z"/></svg>'],
            'social_twitter'   => ['label' => 'X',         'svg' => '<svg viewBox="0 0 24 24" fill="#000000"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>'],
            'social_facebook'  => ['label' => 'Facebook',  'svg' => '<svg viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'],
            'google_maps_url'  => ['label' => __('web.google_maps_url'), 'svg' => '<svg viewBox="0 0 24 24" fill="#EA4335"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>'],
        ];
        $socialSettings = \App\Domains\Admin\Models\Setting::whereIn('key', array_keys($socialSvgs))->get()->keyBy('key');
        $hasActive = $socialSettings->contains(fn($s) => ($s->value['is_active'] ?? false) && !empty($s->value['url']));
    @endphp

    {{-- ── Navbar ────────────────────────────────────────── --}}
    <nav class="bg-white/95 backdrop-blur-sm border-b border-gray-100/80 sticky top-0 z-50" style="box-shadow:0 1px 16px color-mix(in srgb,var(--color-rose-gold) 6%,transparent);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="shrink-0 flex items-center">
                    <img src="{{ $__logoUrl }}"
                         alt="{{ $__salonName }}"
                         class="w-auto object-contain"
                         style="max-height:50px; max-width:160px;">
                </a>

                {{-- Desktop nav links --}}
                <div class="hidden md:flex items-center gap-7">
                    <a href="{{ route('home') }}"
                       class="nav-link text-[13px] font-medium tracking-wide {{ request()->routeIs('home') ? 'text-rose-gold nav-active' : 'text-gray-500 hover:text-rose-gold' }}">
                        {{ __('web.nav_home') }}
                    </a>
                    <a href="{{ route('services.index') }}"
                       class="nav-link text-[13px] font-medium tracking-wide {{ request()->routeIs('services.*') ? 'text-rose-gold nav-active' : 'text-gray-500 hover:text-rose-gold' }}">
                        {{ __('web.nav_services') }}
                    </a>
                    <a href="{{ route('offers.index') }}"
                       class="nav-link text-[13px] font-medium tracking-wide {{ request()->routeIs('offers.*') ? 'text-rose-gold nav-active' : 'text-gray-500 hover:text-rose-gold' }}">
                        {{ __('web.nav_offers') }}
                    </a>

                    @auth
                        <a href="{{ route('bookings.index') }}"
                           class="nav-link text-[13px] font-medium tracking-wide {{ request()->routeIs('bookings.*') ? 'text-rose-gold nav-active' : 'text-gray-500 hover:text-rose-gold' }}">
                            {{ __('web.nav_my_bookings') }}
                        </a>
                        @if(auth()->user()->hasAnyRole(['admin','receptionist']))
                            <a href="{{ route('admin.dashboard') }}"
                               class="nav-link text-[13px] font-medium tracking-wide text-gray-500 hover:text-rose-gold">
                                {{ __('web.nav_dashboard') }}
                            </a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="nav-link text-[13px] font-medium tracking-wide text-gray-500 hover:text-rose-gold">
                                {{ __('web.nav_logout') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="nav-link text-[13px] font-medium tracking-wide text-gray-500 hover:text-rose-gold">
                            {{ __('web.nav_login') }}
                        </a>
                    @endauth
                </div>

                {{-- Right side: cart + profile + language + mobile toggle --}}
                <div class="flex items-center gap-3">

                    {{-- Cart icon --}}
                    @php $cartCount = app(\App\Services\CartService::class)->count(); @endphp
                    <a href="{{ route('cart.index') }}"
                       class="relative text-gray-400 hover:text-rose-gold flex items-center justify-center transition-colors duration-200"
                       aria-label="{{ __('web.nav_cart') }}"
                       x-data="{ count: {{ $cartCount }} }"
                       x-on:cart-updated.window="count = $event.detail.count">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span x-show="count > 0" x-text="count"
                              class="absolute -top-1.5 -inset-e-1.5 w-4 h-4 text-[9px] font-bold bg-rose-gold text-white rounded-full flex items-center justify-center"
                              style="{{ $cartCount > 0 ? '' : 'display:none' }}"></span>
                    </a>

                    {{-- Profile icon --}}
                    @auth
                    <a href="{{ route('profile.show') }}"
                       title="{{ __('web.nav_profile') }}"
                       aria-label="{{ __('web.nav_profile') }}"
                       class="text-gray-400 hover:text-rose-gold flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('profile.*') ? 'text-rose-gold' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                  d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                       title="{{ __('web.nav_login') }}"
                       aria-label="{{ __('web.nav_login') }}"
                       class="text-gray-400 hover:text-rose-gold flex items-center justify-center transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                  d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </a>
                    @endauth

                    {{-- Language switcher --}}
                    <a href="{{ app()->getLocale() === 'ar' ? route('lang.switch', 'en') : route('lang.switch', 'ar') }}"
                       title="{{ app()->getLocale() === 'ar' ? 'Switch to English' : 'التبديل للعربية' }}"
                       aria-label="{{ app()->getLocale() === 'ar' ? 'Switch to English' : 'Switch to Arabic' }}"
                       class="flex items-center gap-1 px-2.5 py-1.5 rounded-full border border-gray-200 text-gray-500 hover:border-rose-gold hover:text-rose-gold transition-all duration-200">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                        <span class="text-[11px] font-semibold tracking-wide leading-none">
                            {{ app()->getLocale() === 'ar' ? 'EN' : 'ع' }}
                        </span>
                    </a>

                    {{-- Mobile menu button --}}
                    <button id="mobile-menu-btn" class="md:hidden text-gray-500 hover:text-rose-gold p-1" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobile-menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile menu --}}
            <div id="mobile-menu" class="hidden md:hidden border-t border-gray-50 py-4">
                <div class="flex flex-col gap-4">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-700 hover:text-rose-gold">{{ __('web.nav_home') }}</a>
                    <a href="{{ route('services.index') }}" class="text-sm font-medium text-gray-700 hover:text-rose-gold">{{ __('web.nav_services') }}</a>
                    <a href="{{ route('offers.index') }}" class="text-sm font-medium text-gray-700 hover:text-rose-gold">{{ __('web.nav_offers') }}</a>
                    @auth
                        <a href="{{ route('bookings.index') }}" class="text-sm font-medium text-gray-700 hover:text-rose-gold">{{ __('web.nav_my_bookings') }}</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-gray-700 hover:text-rose-gold">{{ __('web.nav_logout') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-rose-gold">{{ __('web.nav_login') }}</a>
                        <a href="{{ route('register') }}" class="text-sm font-medium text-gray-700 hover:text-rose-gold">{{ __('web.nav_register') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ── Social bar — below main nav ───────────────────── --}}
    @if($hasActive)
    <div class="w-full border-b border-rose-gold/15" style="background:var(--color-beige, #F5F0E8);">
        <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-center gap-4">
            <span class="text-[11px] tracking-widest uppercase text-rose-gold/70 font-semibold hidden sm:block">
                {{ app()->getLocale() === 'ar' ? 'تابعينا' : 'Follow Us' }}
            </span>
            <div class="h-3 w-px bg-rose-gold/20 hidden sm:block"></div>
            <div class="flex items-center gap-4">
                @foreach($socialSvgs as $key => $social)
                    @php $value = $socialSettings->get($key)?->value ?? []; @endphp
                    @if(($value['is_active'] ?? false) && !empty($value['url']))
                        <a href="{{ $value['url'] }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           title="{{ $social['label'] }}"
                           class="w-4.5 h-4.5 opacity-60 hover:opacity-100 hover:scale-110 block" style="transition:opacity 220ms ease,transform 220ms ease;">
                            {!! $social['svg'] !!}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── Flash messages ───────────────────────────────── --}}
    @if(session('success'))
        <div class="w-full" style="background:linear-gradient(to right,#f0fdf4,#dcfce7,#f0fdf4); border-bottom:1px solid #bbf7d0;">
            <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-3 text-green-700 text-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="w-full" style="background:linear-gradient(to right,#fff1f2,#ffe4e6,#fff1f2); border-bottom:1px solid #fecdd3;">
            <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-3 text-red-600 text-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- ── Main content ─────────────────────────────────── --}}
    <main>
        {{ $slot }}
    </main>

    {{-- ── Footer ───────────────────────────────────────── --}}
    <footer class="mt-24" style="background:linear-gradient(to bottom, color-mix(in srgb,var(--color-beige) 30%,white), var(--color-beige)); border-top:1px solid color-mix(in srgb, var(--color-rose-gold) 12%, transparent);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 items-start">

                {{-- Brand column --}}
                <div class="flex flex-col items-center md:items-start gap-3">
                    <img src="{{ $__logoUrl }}"
                         alt="{{ $__salonName }}"
                         class="w-auto object-contain opacity-90"
                         style="max-height:48px; max-width:150px;">
                    <p class="text-[12px] text-rose-gold/70 tracking-widest uppercase font-medium text-center md:text-start">
                        {{ app()->getLocale() === 'ar' ? 'جمال · أناقة · راحة' : 'Beauty · Elegance · Serenity' }}
                    </p>
                </div>

                {{-- Quick links --}}
                <div class="flex flex-col items-center gap-2">
                    <p class="text-[11px] tracking-widest uppercase text-gray-400 font-medium mb-1">
                        {{ app()->getLocale() === 'ar' ? 'روابط سريعة' : 'Quick Links' }}
                    </p>
                    <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-rose-gold">{{ __('web.nav_home') }}</a>
                    <a href="{{ route('services.index') }}" class="text-sm text-gray-500 hover:text-rose-gold">{{ __('web.nav_services') }}</a>
                    <a href="{{ route('offers.index') }}" class="text-sm text-gray-500 hover:text-rose-gold">{{ __('web.nav_offers') }}</a>
                </div>

                {{-- Social links --}}
                @if($hasActive)
                <div class="flex flex-col items-center md:items-end gap-3">
                    <p class="text-[11px] tracking-widest uppercase text-gray-400 font-medium">
                        {{ app()->getLocale() === 'ar' ? 'تابعينا' : 'Follow Us' }}
                    </p>
                    <div class="flex gap-4 flex-wrap justify-center md:justify-end">
                        @foreach($socialSvgs as $key => $social)
                            @php $value = $socialSettings->get($key)?->value ?? []; @endphp
                            @if(($value['is_active'] ?? false) && !empty($value['url']))
                                <a href="{{ $value['url'] }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   title="{{ $social['label'] }}"
                                   class="w-7 h-7 opacity-60 hover:opacity-100 hover:scale-110 block">
                                    {!! $social['svg'] !!}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

            </div>

            {{-- Copyright --}}
            <div class="mt-10 pt-6 border-t border-rose-gold/10 text-center">
                <p class="text-[11px] text-gray-400 tracking-wide">
                    © {{ date('Y') }} {{ $__salonName }}
                    @if(app()->getLocale() !== 'ar') · All rights reserved @endif
                </p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('serviceView', {
                mode: localStorage.getItem('serviceView') || 'grid',
                set(val) {
                    this.mode = val;
                    localStorage.setItem('serviceView', val);
                }
            });
        });

        document.getElementById('mobile-menu-btn')?.addEventListener('click', function () {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>

    @livewireScripts
</body>
</html>
