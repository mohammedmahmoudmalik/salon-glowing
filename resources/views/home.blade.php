<x-layouts.app :title="config('app.name')">

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- HERO                                                --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <section class="relative overflow-hidden"
             data-hero-section
             style="background:var(--color-beige);">

        {{-- Background: tour video (if uploaded) else image carousel --}}
        <div class="hero-bg {{ $tourVideoUrl ? 'hero-has-video' : '' }}" id="hero-bg" aria-hidden="true">
            @if($tourVideoUrl)
                <video class="hero-video-bg" src="{{ $tourVideoUrl }}"
                       autoplay muted loop playsinline preload="auto">
                </video>
            @endif
            {{-- .hero-slide divs injected by JS when no tour video --}}
            <div class="hero-overlay"></div>
        </div>

        {{-- Decorative blurred circles (z-10 — above overlay, behind text) --}}
        <div class="absolute -top-24 -inset-s-24 w-80 h-80 rounded-full opacity-30 pointer-events-none z-10"
             style="background:radial-gradient(circle, var(--color-soft-pink) 0%, transparent 70%);"></div>
        <div class="absolute -bottom-16 -inset-e-16 w-96 h-96 rounded-full opacity-20 pointer-events-none z-10"
             style="background:radial-gradient(circle, var(--color-rose-gold) 0%, transparent 70%);"></div>

        {{-- Decorative thin lines (z-10) --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden z-10">
            <svg class="absolute top-0 inset-s-0 w-full h-full opacity-[0.04]" preserveAspectRatio="xMidYMid slice" viewBox="0 0 800 500" style="color:var(--color-rose-gold);">
                <circle cx="650" cy="80" r="280" fill="none" stroke="currentColor" stroke-width="1"/>
                <circle cx="650" cy="80" r="200" fill="none" stroke="currentColor" stroke-width="0.6"/>
                <circle cx="650" cy="80" r="340" fill="none" stroke="currentColor" stroke-width="0.4"/>
            </svg>
        </div>

        {{-- Parallax wrapper — text content (z-20, topmost) --}}
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-36 z-20"
             data-hero-parallax>
            <div class="text-center max-w-3xl mx-auto">

                {{-- Eyebrow — fades in first --}}
                <p class="text-[11px] tracking-[0.25em] uppercase text-rose-gold font-medium mb-5 opacity-80 hero-el"
                   data-hero-el="hero-fade-in"
                   data-hero-delay="hero-d1">
                    {{ app()->getLocale() === 'ar' ? 'صالون تجميل نسائي' : 'Women\'s Beauty Salon' }}
                </p>

                {{-- Main title — slides up with depth shadow --}}
                @php
                    $locale = app()->getLocale();
                    $heroTitle    = $locale === 'ar' ? ($heroTitleAr    ?: __('web.hero_title'))    : ($heroTitleEn    ?: __('web.hero_title'));
                    $heroSubtitle = $locale === 'ar' ? ($heroSubtitleAr ?: __('web.hero_subtitle')) : ($heroSubtitleEn ?: __('web.hero_subtitle'));
                    $heroCta      = $locale === 'ar' ? ($heroCtaAr      ?: __('web.nav_services'))  : ($heroCtaEn      ?: __('web.nav_services'));
                @endphp
                <h1 class="font-bold text-salon-text hero-el hero-heading"
                    data-hero-el="hero-slide-up"
                    data-hero-delay="hero-d2">
                    {{ $heroTitle }}
                </h1>

                {{-- Subtitle — follows the title --}}
                <p class="mt-5 text-gray-500 max-w-xl mx-auto hero-el hero-subtext"
                   data-hero-el="hero-slide-up"
                   data-hero-delay="hero-d3">
                    {{ $heroSubtitle }}
                </p>

                {{-- CTAs — scale in last --}}
                <div class="mt-10 flex flex-wrap items-center justify-center gap-4 hero-el"
                     data-hero-el="hero-scale-fade"
                     data-hero-delay="hero-d4">
                    <a href="{{ route('services.index') }}"
                       class="inline-flex items-center gap-2 bg-white/90 backdrop-blur-sm text-rose-gold font-semibold px-8 py-3.5 rounded-full border border-rose-gold/60 hover:bg-rose-gold hover:text-white hover:border-rose-gold text-sm tracking-wide"
                       style="box-shadow:0 4px 16px color-mix(in srgb,var(--color-rose-gold) 18%,transparent);">
                        {{ $heroCta }}
                    </a>
                </div>

            </div>
        </div>

        {{-- Bottom fade into next section (z-20, above everything) --}}
        <div class="absolute bottom-0 inset-x-0 h-12 pointer-events-none z-20"
             style="background: linear-gradient(to bottom, transparent, var(--color-beige));"></div>
    </section>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- TRUST STRIP                                         --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <section class="border-y border-rose-gold/10 py-5" style="background:color-mix(in srgb,var(--color-beige) 50%,transparent);">
        <div class="max-w-5xl mx-auto px-4">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
                @php
                    $features = app()->getLocale() === 'ar'
                        ? [
                            ['icon'=>'✦','label'=>'خبرة متميزة'],
                            ['icon'=>'✦','label'=>'منتجات فاخرة'],
                            ['icon'=>'✦','label'=>'حجز سهل'],
                            ['icon'=>'✦','label'=>'راحة وخصوصية'],
                          ]
                        : [
                            ['icon'=>'✦','label'=>'Expert Staff'],
                            ['icon'=>'✦','label'=>'Premium Products'],
                            ['icon'=>'✦','label'=>'Easy Booking'],
                            ['icon'=>'✦','label'=>'Privacy & Comfort'],
                          ];
                @endphp
                @foreach($features as $f)
                    <div class="flex flex-col items-center gap-1.5">
                        <span class="text-rose-gold text-xs">{{ $f['icon'] }}</span>
                        <span class="text-[12px] tracking-wide text-gray-600 font-medium">{{ $f['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- OUR STORY (Tour + Owner — tabbed when both exist)   --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @if($tourVideoUrl || $ownerVideoUrl)
    <section class="py-16 sm:py-20 overflow-hidden" style="background:var(--color-beige);" data-story-section>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Section header --}}
            <div class="text-center mb-8 sm:mb-10 story-reveal" data-story-reveal>
                <p class="text-[11px] tracking-[0.25em] uppercase font-medium mb-3 opacity-75"
                   style="color:var(--color-rose-gold);">
                    {{ __('web.our_world_eyebrow') }}
                </p>
                <div class="flex items-center justify-center gap-4">
                    <span class="h-px w-12 sm:w-16"
                          style="background:linear-gradient(to var(--tw-gradient-direction,right),transparent,color-mix(in srgb,var(--color-rose-gold) 28%,transparent));"></span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-salon-text">{{ __('web.our_world_title') }}</h2>
                    <span class="h-px w-12 sm:w-16"
                          style="background:linear-gradient(to var(--tw-gradient-direction,left),transparent,color-mix(in srgb,var(--color-rose-gold) 28%,transparent));"></span>
                </div>
            </div>

            {{-- Tabs — only when both videos exist --}}
            @if($tourVideoUrl && $ownerVideoUrl)
            <div class="flex justify-center mb-8 sm:mb-10 story-reveal" data-story-reveal>
                <div class="inline-flex rounded-2xl p-1 gap-1"
                     style="background:color-mix(in srgb,var(--color-rose-gold) 7%,white);border:1px solid color-mix(in srgb,var(--color-rose-gold) 12%,transparent);">
                    <button id="tab-btn-owner" onclick="storyTab('owner')"
                            class="story-tab-btn story-tab-active flex items-center gap-2 px-4 sm:px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>{{ app()->getLocale() === 'ar' ? $ownerVideoTabAr : $ownerVideoTabEn }}</span>
                    </button>
                    <button id="tab-btn-tour" onclick="storyTab('tour')"
                            class="story-tab-btn flex items-center gap-2 px-4 sm:px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ app()->getLocale() === 'ar' ? $tourVideoTabAr : $tourVideoTabEn }}</span>
                    </button>
                </div>
            </div>
            @endif

            {{-- ── Tour Video Panel ─────────────────────────────── --}}
            @if($tourVideoUrl)
            <div id="story-panel-tour" class="story-panel {{ $tourVideoUrl && $ownerVideoUrl ? '' : 'story-panel-active' }}">
                <div class="flex flex-col lg:flex-row items-center gap-10 lg:gap-16 story-reveal" data-story-reveal>

                    {{-- Portrait video --}}
                    <div class="w-64 sm:w-72 lg:w-60 shrink-0 mx-auto lg:mx-0 lg:ms-40 relative">
                        <div class="absolute -inset-2 sm:-inset-4 rounded-3xl pointer-events-none hidden sm:block"
                             style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 16%,transparent);"></div>
                        <div class="relative rounded-2xl overflow-hidden"
                             style="box-shadow:0 16px 48px color-mix(in srgb,var(--color-rose-gold) 14%,transparent);">
                            <video id="tour-video-player"
                                   src="{{ $tourVideoUrl }}"
                                   muted loop playsinline preload="auto" controls
                                   class="w-full aspect-9/16 object-cover bg-gray-900 block">
                            </video>
                        </div>
                    </div>

                    {{-- Text --}}
                    <div class="flex-1 text-center lg:text-start">
                        <p class="text-[11px] tracking-[0.25em] uppercase font-medium mb-3 opacity-75"
                           style="color:var(--color-rose-gold);">
                            {{ app()->getLocale() === 'ar' ? $tourVideoTabAr : $tourVideoTabEn }}
                        </p>
                        <h3 class="font-bold text-salon-text mb-4 leading-tight"
                            style="font-size:clamp(1.5rem,2.5vw,2.25rem);">
                            {{ app()->getLocale() === 'ar' ? $tourVideoTitleAr : $tourVideoTitleEn }}
                        </h3>
                        <p class="text-gray-500 leading-relaxed mb-8 max-w-md mx-auto lg:mx-0">
                            {{ app()->getLocale() === 'ar' ? $tourVideoDescAr : $tourVideoDescEn }}
                        </p>
                        <a href="{{ route('services.index') }}"
                           class="inline-flex items-center font-semibold px-8 py-3.5 rounded-full text-sm tracking-wide transition-all active:scale-95"
                           style="background:var(--color-rose-gold);color:#fff;box-shadow:0 4px 16px color-mix(in srgb,var(--color-rose-gold) 30%,transparent);border:1px solid color-mix(in srgb,var(--color-rose-gold) 80%,transparent);">
                            {{ __('web.tour_cta') }}
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- ── Owner Video Panel ────────────────────────────── --}}
            @if($ownerVideoUrl)
            <div id="story-panel-owner" class="story-panel story-panel-active">
                <div class="flex flex-col lg:flex-row items-center gap-10 lg:gap-16 story-reveal" data-story-reveal>

                    {{-- Portrait video --}}
                    <div class="w-64 sm:w-72 lg:w-60 shrink-0 mx-auto lg:mx-0 lg:ms-40 relative">
                        <div class="absolute -inset-2 sm:-inset-4 rounded-3xl pointer-events-none hidden sm:block"
                             style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 16%,transparent);"></div>
                        <div id="owner-video-wrap"
                             class="relative rounded-2xl overflow-hidden cursor-pointer"
                             style="box-shadow:0 16px 48px color-mix(in srgb,var(--color-rose-gold) 14%,transparent);">
                            <video id="owner-video-player"
                                   src="{{ $ownerVideoUrl }}"
                                   preload="auto" playsinline
                                   class="w-full aspect-9/16 object-cover bg-gray-900 block">
                            </video>
                            {{-- Play overlay --}}
                            <div id="owner-video-overlay"
                                 class="absolute inset-0 flex flex-col items-center justify-center"
                                 style="background:linear-gradient(135deg,color-mix(in srgb,var(--color-rose-gold) 55%,#000) 0%,color-mix(in srgb,var(--color-rose-gold) 25%,#000) 100%);transition:opacity .3s;">
                                <button id="owner-play-btn"
                                        aria-label="{{ app()->getLocale() === 'ar' ? 'تشغيل الفيديو' : 'Play video' }}"
                                        class="rounded-full flex items-center justify-center hover:scale-110 active:scale-95 transition-transform"
                                        style="width:72px;height:72px;background:rgba(255,255,255,0.18);border:2px solid rgba(255,255,255,0.5);">
                                    <svg class="w-8 h-8 text-white ms-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </button>
                                <p class="mt-4 text-white/75 text-sm font-medium tracking-wide">
                                    {{ app()->getLocale() === 'ar' ? 'اضغطي للمشاهدة' : 'Tap to watch' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Text --}}
                    <div class="flex-1 text-center lg:text-start">
                        <p class="text-[11px] tracking-[0.25em] uppercase font-medium mb-3 opacity-75"
                           style="color:var(--color-rose-gold);">
                            {{ app()->getLocale() === 'ar' ? $ownerVideoTabAr : $ownerVideoTabEn }}
                        </p>
                        <h3 class="font-bold text-salon-text mb-4 leading-tight"
                            style="font-size:clamp(1.5rem,2.5vw,2.25rem);">
                            {{ app()->getLocale() === 'ar' ? $ownerVideoTitleAr : $ownerVideoTitleEn }}
                        </h3>
                        <p class="text-gray-500 leading-relaxed mb-8 max-w-md mx-auto lg:mx-0">
                            {{ app()->getLocale() === 'ar' ? $ownerVideoDescAr : $ownerVideoDescEn }}
                        </p>
                        <a href="{{ route('services.index') }}"
                           class="inline-flex items-center font-semibold px-8 py-3.5 rounded-full text-sm tracking-wide transition-all active:scale-95"
                           style="background:var(--color-rose-gold);color:#fff;box-shadow:0 4px 16px color-mix(in srgb,var(--color-rose-gold) 30%,transparent);border:1px solid color-mix(in srgb,var(--color-rose-gold) 80%,transparent);">
                            {{ __('web.owner_story_cta') }}
                        </a>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </section>

    <style>
    /* Panels */
    .story-panel { display: none; }
    .story-panel.story-panel-active { display: block; }

    /* Tab buttons */
    .story-tab-btn {
        color: var(--color-rose-gold);
        background: transparent;
    }
    .story-tab-btn.story-tab-active {
        background: var(--color-rose-gold);
        color: white;
        box-shadow: 0 4px 14px color-mix(in srgb,var(--color-rose-gold) 28%,transparent);
    }

    /* Entrance animation */
    .story-reveal {
        opacity: 0;
        transform: translateY(22px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .story-reveal.story-reveal-in {
        opacity: 1;
        transform: translateY(0);
    }
    @media (prefers-reduced-motion: reduce) {
        .story-reveal { opacity: 1; transform: none; transition: none; }
    }
    </style>

    <script>
    (function () {
        var isAr       = document.documentElement.dir === 'rtl';
        var tourVideo  = document.getElementById('tour-video-player');
        var ownerVideo = document.getElementById('owner-video-player');
        var tourPanel  = document.getElementById('story-panel-tour');
        var ownerPanel = document.getElementById('story-panel-owner');
        var btnTour    = document.getElementById('tab-btn-tour');
        var btnOwner   = document.getElementById('tab-btn-owner');

        /* ── Play with sound; fall back to muted if browser blocks ── */
        function playWithSound(video) {
            if (!video) return;
            video.muted = false;
            var p = video.play();
            if (p && p.catch) {
                p.catch(function () {
                    video.muted = true;
                    video.play().catch(function () {});
                });
            }
        }

        /* ── Dismiss owner overlay & show native controls ─────────── */
        function dismissOwnerOverlay() {
            var ov = document.getElementById('owner-video-overlay');
            if (ov) { ov.style.opacity = '0'; ov.style.pointerEvents = 'none'; }
            if (ownerVideo) ownerVideo.controls = true;
        }

        /* ── Play whichever panel is currently active ─────────────── */
        function playActivePanel() {
            var ownerActive = ownerPanel && ownerPanel.classList.contains('story-panel-active');
            if (ownerActive) {
                if (tourVideo) { tourVideo.muted = true; tourVideo.pause(); }
                if (!ownerVideo) return;
                var ov = document.getElementById('owner-video-overlay');
                var overlayVisible = ov && ov.style.pointerEvents !== 'none';
                if (overlayVisible) {
                    /* First autoplay attempt — try with sound; if browser blocks,
                       keep overlay visible so the user's tap provides the gesture */
                    ownerVideo.muted = false;
                    var p = ownerVideo.play();
                    if (p && p.then) {
                        p.then(function () {
                            dismissOwnerOverlay();
                        }).catch(function () {
                            ownerVideo.muted = true;
                            ownerVideo.play().catch(function () {});
                            /* Overlay stays — user tap will call playOwnerManual */
                        });
                    } else {
                        dismissOwnerOverlay();
                    }
                } else {
                    /* User already interacted; play with sound */
                    playWithSound(ownerVideo);
                }
            } else {
                if (ownerVideo) { ownerVideo.pause(); ownerVideo.muted = true; }
                playWithSound(tourVideo);
            }
        }

        /* ── Tab switching ─────────────────────────────────────────── */
        window.storyTab = function (tab) {
            if (!tourPanel || !ownerPanel) return;
            var showTour = (tab === 'tour');

            tourPanel.classList.toggle('story-panel-active', showTour);
            ownerPanel.classList.toggle('story-panel-active', !showTour);
            if (btnTour)  btnTour.classList.toggle('story-tab-active', showTour);
            if (btnOwner) btnOwner.classList.toggle('story-tab-active', !showTour);

            if (showTour) {
                if (ownerVideo) { ownerVideo.pause(); ownerVideo.muted = true; }
                playWithSound(tourVideo);
            } else {
                if (tourVideo) { tourVideo.muted = true; tourVideo.pause(); }
                dismissOwnerOverlay();
                playWithSound(ownerVideo);
            }
        };

        /* ── Owner manual play (fallback if autoplay blocked) ─────── */
        var ownerOverlay = document.getElementById('owner-video-overlay');
        var ownerPlayBtn = document.getElementById('owner-play-btn');
        function playOwnerManual() { dismissOwnerOverlay(); playWithSound(ownerVideo); }
        if (ownerOverlay) ownerOverlay.addEventListener('click', playOwnerManual);
        if (ownerPlayBtn) ownerPlayBtn.addEventListener('click', function (e) {
            e.stopPropagation(); playOwnerManual();
        });

        /* ── Play / pause based on the video element's own visibility ── */
        if ('IntersectionObserver' in window) {
            var videoIo = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    var vid         = e.target;
                    var ownerActive = ownerPanel && ownerPanel.classList.contains('story-panel-active');
                    if (e.isIntersecting) {
                        var isTourActive  = (vid === tourVideo)  && !ownerActive;
                        var isOwnerActive = (vid === ownerVideo) && ownerActive;
                        if (isTourActive || isOwnerActive) playActivePanel();
                    } else {
                        vid.pause();
                    }
                });
            }, { threshold: 0.4 });

            if (tourVideo)  videoIo.observe(tourVideo);
            if (ownerVideo) videoIo.observe(ownerVideo);
        }

        /* ── Entrance animations ───────────────────────────────────── */
        if ('IntersectionObserver' in window) {
            var revealIo = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) {
                        e.target.classList.add('story-reveal-in');
                        revealIo.unobserve(e.target);
                    }
                });
            }, { threshold: 0.1 });
            document.querySelectorAll('[data-story-reveal]').forEach(function (el) { revealIo.observe(el); });
        } else {
            document.querySelectorAll('[data-story-reveal]').forEach(function (el) {
                el.classList.add('story-reveal-in');
            });
        }
    }());
    </script>
    @endif

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- ACTIVE OFFERS                                       --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @if($offers->count())
    <section class="py-20" style="background: linear-gradient(135deg, var(--color-beige) 0%, color-mix(in srgb,var(--color-beige) 30%,white) 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Section header --}}
            <div class="text-center mb-12">
                <p class="text-[11px] tracking-[0.2em] uppercase text-rose-gold font-medium mb-3 opacity-70">
                    {{ app()->getLocale() === 'ar' ? 'عروض حصرية' : 'Exclusive Deals' }}
                </p>
                <div class="flex items-center justify-center gap-4">
                    <span class="h-px w-16 bg-linear-to-r from-transparent to-rose-gold/30"></span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-salon-text">{{ __('web.active_offers') }}</h2>
                    <span class="h-px w-16 bg-linear-to-l from-transparent to-rose-gold/30"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($offers as $offer)
                    <a href="{{ route('offers.show', $offer) }}"
                       class="group bg-white rounded-2xl overflow-hidden card-lift"
                       style="box-shadow:0 2px 12px color-mix(in srgb,var(--color-rose-gold) 8%,transparent); border:1px solid color-mix(in srgb,var(--color-rose-gold) 8%,transparent);">

                        {{-- Image --}}
                        @if($offer->image_url)
                            <div class="aspect-video overflow-hidden">
                                <img src="{{ $offer->image_url }}" loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105"
                                     style="transition:transform 400ms ease;">
                            </div>
                        @else
                            <div class="aspect-video flex items-center justify-center"
                                 style="background:linear-gradient(135deg, var(--color-beige), var(--color-soft-pink-light));">
                                <svg class="w-12 h-12 text-rose-gold/20" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            </div>
                        @endif

                        <div class="p-5">
                            {{-- Discount badge --}}
                            <span class="inline-flex items-center text-[11px] font-bold tracking-wide px-3 py-1 rounded-full mb-3"
                                  style="background:color-mix(in srgb,var(--color-rose-gold) 8%,transparent); color:var(--color-rose-gold);">
                                @if($offer->discount_type?->value === 'percentage')
                                    {{ number_format($offer->discount_value, 0) }}% {{ __('web.discount') }}
                                @else
                                    -{{ number_format($offer->discount_value, 0) }} {{ currency() }}
                                @endif
                            </span>

                            <h3 class="font-semibold text-salon-text group-hover:text-rose-gold line-clamp-1 text-base">
                                {{ app()->getLocale() === 'ar' ? $offer->title_ar : $offer->title_en }}
                            </h3>

                            @php $desc = app()->getLocale() === 'ar' ? $offer->description_ar : $offer->description_en; @endphp
                            @if($desc)
                                <p class="text-[13px] text-gray-500 line-clamp-2 mt-1.5">{{ $desc }}</p>
                            @endif

                            @if($offer->ends_at)
                                <p class="text-[11px] text-gray-400 mt-2 flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ __('web.offer_expires') }}: {{ $offer->ends_at->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="text-center mt-10">
                <a href="{{ route('offers.index') }}"
                   class="inline-flex items-center gap-2 bg-white border border-rose-gold/30 text-rose-gold font-semibold px-8 py-3 rounded-full hover:bg-rose-gold hover:text-white hover:border-rose-gold text-sm tracking-wide"
                   style="box-shadow:0 2px 8px color-mix(in srgb,var(--color-rose-gold) 10%,transparent);">
                    {{ __('web.view_all') }}
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- FEATURED SERVICES                                   --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <section class="py-20 bg-beige">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Section header --}}
            <div class="text-center mb-12">
                <p class="text-[11px] tracking-[0.2em] uppercase text-rose-gold font-medium mb-3 opacity-70">
                    {{ app()->getLocale() === 'ar' ? 'خدماتنا المميزة' : 'Our Signature Services' }}
                </p>
                <div class="flex items-center justify-center gap-4">
                    <span class="h-px w-16 bg-linear-to-r from-transparent to-rose-gold/30"></span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-salon-text">{{ __('web.featured_services') }}</h2>
                    <span class="h-px w-16 bg-linear-to-l from-transparent to-rose-gold/30"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $service)
                    @livewire('service-card', ['service' => $service], key($service->id))
                @endforeach
            </div>

            <div class="text-center mt-10">
                <a href="{{ route('services.index') }}"
                   class="inline-flex items-center gap-2 bg-white border border-rose-gold/30 text-rose-gold font-semibold px-8 py-3 rounded-full hover:bg-rose-gold hover:text-white hover:border-rose-gold text-sm tracking-wide"
                   style="box-shadow:0 2px 8px color-mix(in srgb,var(--color-rose-gold) 10%,transparent);">
                    {{ __('web.view_all') }}
                </a>
            </div>
        </div>
    </section>


{{-- ── Hero: carousel (API-driven) + text animations + parallax ── --}}
<script>
(function () {
    'use strict';

    /* ── Config ────────────────────────────────────────────────── */
    var SLIDE_INTERVAL  = 2800;   /* ms between automatic slide advances  */
    var CAROUSEL_DELAY  = 400;    /* ms after load before rotation begins */
    var API_URL         = '/api/v1/hero-images';
    var prefersReduced  = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var TOUR_VIDEO_ACTIVE = {{ $tourVideoUrl ? 'true' : 'false' }};

    /* ── Element refs ──────────────────────────────────────────── */
    var heroSection = document.querySelector('[data-hero-section]');
    var heroEls     = document.querySelectorAll('[data-hero-el]');
    var parallaxEl  = document.querySelector('[data-hero-parallax]');
    var heroBg      = document.getElementById('hero-bg');
    var heroOverlay = heroBg ? heroBg.querySelector('.hero-overlay') : null;

    if (!heroSection) return;

    /* ════════════════════════════════════════════════════════════
       1. CAROUSEL — images fetched from API, slides built dynamically
       ════════════════════════════════════════════════════════════ */
    var slides        = [];
    var current       = 0;
    var timer         = null;
    var carouselReady = false;

    function advance() {
        if (!slides.length) return;
        slides[current].classList.remove('hero-slide-active');
        current = (current + 1) % slides.length;
        slides[current].classList.add('hero-slide-active');
    }

    function startCarousel() {
        if (timer !== null || !carouselReady || prefersReduced || slides.length < 2) return;
        timer = setInterval(advance, SLIDE_INTERVAL);
    }

    function stopCarousel() {
        clearInterval(timer);
        timer = null;
    }

    function buildSlides(urls) {
        if (!heroBg || !heroOverlay || !urls.length) return;

        /* Create one <div class="hero-slide"> per image URL */
        urls.forEach(function (url) {
            var div = document.createElement('div');
            div.className = 'hero-slide';
            div.setAttribute('data-slide', '');
            div.style.backgroundImage = 'url("' + url + '")';
            heroBg.insertBefore(div, heroOverlay); /* stays behind the overlay */
            slides.push(div);
        });

        /* Preload to prevent crossfade flash */
        urls.forEach(function (src) {
            var img = new Image();
            img.src = src;
        });

        /* Activate first slide immediately */
        slides[0].classList.add('hero-slide-active');

        /* Start auto-rotation after delay */
        setTimeout(function () {
            carouselReady = true;
            startCarousel();
        }, CAROUSEL_DELAY);
    }

    /* Skip carousel when tour video is active — video covers the background */
    if (!TOUR_VIDEO_ACTIVE) {
        fetch(API_URL, { headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (payload) {
                var urls = Array.isArray(payload) ? payload
                         : (Array.isArray(payload.data) ? payload.data : []);
                buildSlides(urls);
            })
            .catch(function () {});
    }

    /* ════════════════════════════════════════════════════════════
       2. TEXT ENTRANCE ANIMATIONS
       ════════════════════════════════════════════════════════════ */
    function activateHero() {
        if (prefersReduced) {
            heroEls.forEach(function (el) {
                el.style.opacity = '1';
                el.style.transform = 'none';
            });
            return;
        }
        heroEls.forEach(function (el) {
            var anim  = el.getAttribute('data-hero-el');
            var delay = el.getAttribute('data-hero-delay');
            if (anim)  el.classList.add(anim);
            if (delay) el.classList.add(delay);
        });
    }

    /* ════════════════════════════════════════════════════════════
       3. INTERSECTION OBSERVERS
       ════════════════════════════════════════════════════════════ */
    if ('IntersectionObserver' in window) {

        /* Fire text animations once on first viewport entry */
        var textIO = new IntersectionObserver(function (entries) {
            if (entries[0].isIntersecting) {
                activateHero();
                textIO.disconnect();
            }
        }, { threshold: 0.05 });
        textIO.observe(heroSection);

        /* Pause carousel when hero scrolls off-screen; resume on return */
        var carouselIO = new IntersectionObserver(function (entries) {
            if (entries[0].isIntersecting) {
                startCarousel();
            } else {
                stopCarousel();
            }
        }, { threshold: 0 });
        carouselIO.observe(heroSection);

    } else {
        activateHero(); /* older browser fallback */
    }

    /* ════════════════════════════════════════════════════════════
       4. PARALLAX — desktop only, 10% scroll speed
       ════════════════════════════════════════════════════════════ */
    if (!prefersReduced && parallaxEl && window.matchMedia('(min-width: 1024px)').matches) {
        var ticking = false;
        window.addEventListener('scroll', function () {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(function () {
                var scrollY = window.scrollY;
                if (scrollY < heroSection.offsetHeight) {
                    parallaxEl.style.transform = 'translateY(' + (scrollY * 0.10).toFixed(2) + 'px)';
                }
                ticking = false;
            });
        }, { passive: true });
    }

}());
</script>

</x-layouts.app>

