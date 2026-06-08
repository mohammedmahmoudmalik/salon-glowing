<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $__salonName = \App\Domains\Admin\Models\Setting::get('salon_name', config('app.name')); @endphp
    <title>{{ $title ?? __('web.admin_dashboard') }} | {{ $__salonName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
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
<body class="min-h-screen bg-gray-50 text-gray-800 flex">

    {{-- Sidebar --}}
    <aside class="w-64 min-h-screen bg-white shadow-sm flex flex-col shrink-0 fixed inset-y-0 start-0 z-40 hidden lg:flex">
        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-gray-100">
            <a href="{{ route('home') }}" class="block">
                <img src="{{ $__logoUrl }}"
                     alt="{{ $__salonName }}"
                     class="w-auto object-contain"
                     style="max-height:44px; max-width:140px;">
            </a>
            <p class="text-xs text-gray-400 mt-1">{{ __('web.admin_dashboard') }}</p>
        </div>

        {{-- Nav links --}}
        <nav class="flex-1 px-4 py-5 space-y-1 overflow-y-auto">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard',       'label' => __('web.admin_dashboard'), 'icon' => '◈', 'roles' => ['admin', 'receptionist']],
                    ['route' => 'admin.owner.dashboard', 'label' => __('web.admin_dashboard'), 'icon' => '◈', 'roles' => ['owner']],
                    ['route' => 'admin.bookings.index', 'label' => __('web.admin_bookings'), 'icon' => '◇', 'roles' => ['admin', 'receptionist']],
                    ['route' => 'admin.customers.index', 'label' => __('web.customers'), 'icon' => '👥', 'roles' => ['admin', 'receptionist']],
                    ['route' => 'admin.offers.index', 'label' => __('web.admin_offers'), 'icon' => '❋', 'roles' => ['admin', 'receptionist']],
                    ['route' => 'admin.categories.index', 'label' => __('web.admin_categories'), 'icon' => '◑', 'roles' => ['admin', 'receptionist']],
                    ['route' => 'admin.services.index', 'label' => __('web.admin_services'), 'icon' => '◆', 'roles' => ['admin', 'receptionist']],
['route' => 'admin.reviews.index', 'label' => __('web.admin_reviews'), 'icon' => '★', 'roles' => ['admin', 'receptionist']],
                    ['route' => 'admin.settings.show', 'label' => __('web.admin_settings'), 'icon' => '⚙', 'roles' => ['admin', 'owner']],
                    ['route' => 'admin.setup.index', 'label' => __('web.setup_services'), 'icon' => '⬆', 'roles' => ['owner']],
                    ['route' => 'admin.demo.index',  'label' => __('web.demo_title'),     'icon' => '⚗', 'roles' => ['owner']],
                ];
            @endphp

            @foreach($navItems as $item)
                @if(auth()->user()->hasAnyRole($item['roles']))
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                              {{ request()->routeIs($item['route'] . '*') ? 'bg-beige text-rose-gold' : 'text-gray-600 hover:bg-beige hover:text-rose-gold' }}">
                        <span class="text-base">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        {{-- User info + Logout --}}
        <div class="px-4 py-4 border-t border-gray-100 flex items-center gap-3">
            <a href="{{ route('admin.profile.show') }}" class="flex items-center gap-3 flex-1 min-w-0 group">
                @if(auth()->user()->avatar)
                    <img src="{{ Storage::url(auth()->user()->avatar) }}"
                         alt="{{ auth()->user()->name }}"
                         class="w-9 h-9 rounded-full object-cover shrink-0 ring-2 ring-rose-gold/20 group-hover:ring-rose-gold/50 transition">
                @else
                    <div class="w-9 h-9 rounded-full bg-beige flex items-center justify-center text-rose-gold font-semibold text-sm shrink-0 ring-2 ring-rose-gold/20 group-hover:ring-rose-gold/50 transition">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-700 font-medium truncate group-hover:text-rose-gold transition">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ app()->getLocale() === 'ar' ? 'عرض الملف الشخصي' : 'View profile' }}</p>
                </div>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="{{ __('web.admin_logout') }}"
                        class="text-gray-400 hover:text-rose-gold transition p-1 rounded-lg hover:bg-beige">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main area --}}
    <div class="flex-1 lg:ms-64 flex flex-col min-h-screen">
        {{-- Top bar --}}
        <header class="bg-white shadow-sm sticky top-0 z-30 lg:hidden">
            <div class="flex items-center justify-between px-4 h-14">
                <a href="{{ route('home') }}" class="shrink-0">
                    <img src="{{ $__logoUrl }}"
                         alt="{{ $__salonName }}"
                         class="w-auto object-contain"
                         style="max-height:36px; max-width:120px;">
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ app()->getLocale() === 'ar' ? route('lang.switch', 'en') : route('lang.switch', 'ar') }}"
                       class="flex items-center gap-1 px-2 py-1 rounded-full border border-gray-200 text-gray-500 hover:border-rose-gold hover:text-rose-gold transition-all duration-200">
                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                        <span class="text-[10px] font-semibold leading-none">{{ app()->getLocale() === 'ar' ? 'EN' : 'ع' }}</span>
                    </a>
                    <a href="{{ route('admin.profile.show') }}" class="shrink-0 group">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}"
                                 alt="{{ auth()->user()->name }}"
                                 class="w-8 h-8 rounded-full object-cover ring-2 ring-rose-gold/20 group-hover:ring-rose-gold/50 transition">
                        @else
                            <div class="w-8 h-8 rounded-full bg-beige flex items-center justify-center text-rose-gold font-semibold text-sm ring-2 ring-rose-gold/20 group-hover:ring-rose-gold/50 transition">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </a>
                    <button id="sidebar-toggle" class="text-gray-500 hover:text-rose-gold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        {{-- Desktop top bar with language switcher --}}
        <div class="hidden lg:flex items-center justify-end gap-5 px-8 py-2.5 bg-white border-b border-gray-100">
            {{-- Language switcher --}}
            <a href="{{ app()->getLocale() === 'ar' ? route('lang.switch', 'en') : route('lang.switch', 'ar') }}"
               class="flex items-center gap-1 px-2.5 py-1.5 rounded-full border border-gray-200 text-gray-500 hover:border-rose-gold hover:text-rose-gold transition-all duration-200">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                          d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
                <span class="text-[11px] font-semibold tracking-wide leading-none">
                    {{ app()->getLocale() === 'ar' ? 'EN' : 'ع' }}
                </span>
            </a>
            {{-- User avatar + name --}}
            <a href="{{ route('admin.profile.show') }}" class="flex items-center gap-2.5 group">
                @if(auth()->user()->avatar)
                    <img src="{{ Storage::url(auth()->user()->avatar) }}"
                         alt="{{ auth()->user()->name }}"
                         class="w-8 h-8 rounded-full object-cover ring-2 ring-rose-gold/20 group-hover:ring-rose-gold/50 transition">
                @else
                    <div class="w-8 h-8 rounded-full bg-beige flex items-center justify-center text-rose-gold font-semibold text-sm ring-2 ring-rose-gold/20 group-hover:ring-rose-gold/50 transition">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
                <span class="text-sm text-gray-600 font-medium group-hover:text-rose-gold transition">{{ auth()->user()->name }}</span>
            </a>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 p-6 lg:p-8">
            {{ $slot }}
        </main>
    </div>

    {{-- Mobile sidebar overlay --}}
    <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/30 z-30 lg:hidden"></div>
    <aside id="mobile-sidebar"
           class="fixed inset-y-0 inset-s-0 w-72 bg-white shadow-xl z-40 lg:hidden transition-transform duration-200"
           style="transform: translateX({{ app()->getLocale() === 'ar' ? '100%' : '-100%' }})">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <a href="{{ route('home') }}" class="shrink-0">
                <img src="{{ $__logoUrl }}"
                     alt="{{ $__salonName }}"
                     class="w-auto object-contain"
                     style="max-height:38px; max-width:130px;">
            </a>
            <button id="sidebar-close" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <nav class="px-4 py-5 space-y-1 overflow-y-auto max-h-[calc(100vh-80px)]">
            @foreach($navItems as $item)
                @if(auth()->user()->hasAnyRole($item['roles']))
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                              {{ request()->routeIs($item['route'] . '*') ? 'bg-beige text-rose-gold' : 'text-gray-600 hover:bg-beige hover:text-rose-gold' }}">
                        <span class="text-base shrink-0">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
            <div class="pt-4 mt-4 border-t border-gray-100">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:bg-red-50 hover:text-red-500 transition w-full">
                        <span class="text-base shrink-0">↩</span>
                        {{ __('web.admin_logout') }}
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <script>
        (function () {
            const isRtl = document.documentElement.dir === 'rtl';
            const hiddenTransform = isRtl ? 'translateX(100%)' : 'translateX(-100%)';
            const openTransform   = 'translateX(0)';

            const btn     = document.getElementById('sidebar-toggle');
            const closeBtn = document.getElementById('sidebar-close');
            const overlay = document.getElementById('sidebar-overlay');
            const sidebar = document.getElementById('mobile-sidebar');

            function openSidebar() {
                sidebar.style.transform = openTransform;
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            function closeSidebar() {
                sidebar.style.transform = hiddenTransform;
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }

            if (btn)      btn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (overlay)  overlay.addEventListener('click', closeSidebar);
        })();
    </script>

    @livewireScripts
</body>
</html>
