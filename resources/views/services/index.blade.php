<x-layouts.app :title="__('web.services_title')">

    <style>[x-cloak]{display:none!important}</style>

    {{-- Page banner --}}
    <div class="py-12 text-center border-b border-rose-gold/10"
         style="background:linear-gradient(135deg, var(--color-beige) 0%, color-mix(in srgb,var(--color-beige) 30%,white) 60%, color-mix(in srgb,var(--color-soft-pink) 10%,transparent) 100%);">
        <p class="text-[11px] tracking-[0.22em] uppercase text-rose-gold font-medium mb-3 opacity-70">
            {{ app()->getLocale() === 'ar' ? 'اكتشفي خدماتنا' : 'Discover Our Services' }}
        </p>
        <h1 class="text-3xl sm:text-4xl font-bold text-salon-text">{{ __('web.services_title') }}</h1>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Categories Navigation --}}
        <div class="relative mb-10">

            <button id="scroll-left"
                    class="absolute inset-s-0 top-1/2 -translate-y-1/2 z-10 w-9 h-9 bg-white rounded-full
                           items-center justify-center hover:bg-rose-gold hover:text-white
                           hidden md:flex opacity-40"
                    style="box-shadow:0 2px 10px color-mix(in srgb,var(--color-rose-gold) 15%,transparent); transition:opacity 250ms,background 250ms,color 250ms;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <div id="categories-bar" class="overflow-x-auto pb-2 hide-scrollbar mx-0 md:mx-12">
                <div class="flex gap-3 sm:gap-5 min-w-max px-1">

                    <a href="{{ route('services.index') }}" {{ !request('category') ? 'data-active' : '' }} class="flex flex-col items-center gap-1.5 sm:gap-2 group">
                        <div class="w-14 h-14 sm:w-22 sm:h-22 rounded-full overflow-hidden flex items-center justify-center
                                    border-2 {{ !request('category') ? 'border-rose-gold' : 'border-gray-200 group-hover:border-rose-gold/50' }}"
                             style="{{ !request('category') ? 'box-shadow:0 0 0 3px color-mix(in srgb,var(--color-rose-gold) 12%,transparent)' : '' }}">
                            <div class="w-full h-full bg-beige flex items-center justify-center">
                                <svg class="w-5 h-5 sm:w-7 sm:h-7 text-rose-gold/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M4 6h16M4 12h8m-8 6h16"/>
                                </svg>
                            </div>
                        </div>
                        <span class="text-[10px] sm:text-[11px] font-medium text-center text-gray-600 w-16 sm:w-24 truncate tracking-wide">
                            {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}
                        </span>
                    </a>

                    @foreach($categories as $category)
                        <a href="{{ route('services.index', ['category' => $category->id]) }}"
                           {{ request('category') == $category->id ? 'data-active' : '' }}
                           class="flex flex-col items-center gap-1.5 sm:gap-2 group">
                            <div class="w-14 h-14 sm:w-22 sm:h-22 rounded-full overflow-hidden border-2
                                        {{ request('category') == $category->id ? 'border-rose-gold' : 'border-gray-200 group-hover:border-rose-gold/50' }}"
                                 style="{{ request('category') == $category->id ? 'box-shadow:0 0 0 3px color-mix(in srgb,var(--color-rose-gold) 12%,transparent)' : '' }}">
                                @if($category->image_url)
                                    <img src="{{ $category->image_url }}"
                                         alt="{{ app()->getLocale() === 'ar' ? $category->name_ar : $category->name_en }}"
                                         class="w-full h-full object-cover group-hover:scale-110"
                                         style="transition:transform 350ms ease;"
                                         loading="lazy">
                                @else
                                    <div class="w-full h-full bg-beige flex items-center justify-center">
                                        <svg class="w-5 h-5 sm:w-7 sm:h-7 text-rose-gold/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <span class="text-[10px] sm:text-[11px] font-medium text-center text-gray-600 w-16 sm:w-24 truncate tracking-wide">
                                {{ app()->getLocale() === 'ar' ? $category->name_ar : $category->name_en }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            <button id="scroll-right"
                    class="absolute inset-e-0 top-1/2 -translate-y-1/2 z-10 w-9 h-9 bg-white rounded-full
                           items-center justify-center hover:bg-rose-gold hover:text-white
                           hidden md:flex opacity-100"
                    style="box-shadow:0 2px 10px color-mix(in srgb,var(--color-rose-gold) 15%,transparent); transition:opacity 250ms,background 250ms,color 250ms;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Section header: count + view toggle --}}
        <div class="flex items-center justify-between mb-6" x-data>
            <p class="text-sm text-gray-500">
                @if($services->total())
                    <span class="font-semibold" style="color:var(--color-rose-gold);">{{ $services->total() }}</span>
                    {{ app()->getLocale() === 'ar' ? ' خدمة متاحة' : ' services available' }}
                @endif
            </p>

            {{-- Grid / List toggle --}}
            <div class="flex items-center gap-0.5 p-1 rounded-lg" style="background:#f3f4f6;">
                {{-- Grid icon --}}
                <button @click="$store.serviceView.set('grid')"
                        :class="$store.serviceView.mode === 'grid'
                            ? 'bg-rose-gold text-white shadow-sm'
                            : 'text-gray-400 hover:text-gray-600'"
                        class="w-8 h-8 flex items-center justify-center rounded-md transition-all duration-200"
                        :title="'{{ app()->getLocale() === 'ar' ? 'عرض شبكي' : 'Grid view' }}'">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                        <rect x="1" y="1" width="6" height="6" rx="1.2"/>
                        <rect x="9" y="1" width="6" height="6" rx="1.2"/>
                        <rect x="1" y="9" width="6" height="6" rx="1.2"/>
                        <rect x="9" y="9" width="6" height="6" rx="1.2"/>
                    </svg>
                </button>

                {{-- List icon --}}
                <button @click="$store.serviceView.set('list')"
                        :class="$store.serviceView.mode === 'list'
                            ? 'bg-rose-gold text-white shadow-sm'
                            : 'text-gray-400 hover:text-gray-600'"
                        class="w-8 h-8 flex items-center justify-center rounded-md transition-all duration-200"
                        :title="'{{ app()->getLocale() === 'ar' ? 'عرض قائمة' : 'List view' }}'">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                        <rect x="1"   y="1.5"  width="14" height="3" rx="1"/>
                        <rect x="1"   y="6.5"  width="14" height="3" rx="1"/>
                        <rect x="1"   y="11.5" width="14" height="3" rx="1"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Services container: switches between grid and list --}}
        @if($services->count())
            <div x-data
                 :class="$store.serviceView.mode === 'grid'
                     ? 'grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5 items-stretch'
                     : 'flex flex-col divide-y divide-rose-gold/10'"
                 class="transition-all duration-300">
                @foreach($services as $service)
                    @livewire('service-card', ['service' => $service], key($service->id))
                @endforeach
            </div>

            <div class="mt-10">{{ $services->withQueryString()->links() }}</div>

        @else
            {{-- Empty state --}}
            <div class="text-center py-24">
                <div class="text-6xl mb-4 select-none" style="color:var(--color-rose-gold); opacity:0.18;">✿</div>
                <p class="text-gray-400 text-sm">{{ __('web.no_services') }}</p>
            </div>
        @endif

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const bar       = document.getElementById('categories-bar');
        const leftBtn   = document.getElementById('scroll-left');
        const rightBtn  = document.getElementById('scroll-right');
        const amount    = 280;

        leftBtn?.addEventListener('click',  () => bar.scrollBy({ left: -amount, behavior: 'smooth' }));
        rightBtn?.addEventListener('click', () => bar.scrollBy({ left:  amount, behavior: 'smooth' }));

        function updateArrows() {
            if (!leftBtn || !rightBtn) return;
            leftBtn.style.opacity  = bar.scrollLeft > 10 ? '1' : '0.3';
            rightBtn.style.opacity = bar.scrollLeft < bar.scrollWidth - bar.clientWidth - 10 ? '1' : '0.3';
        }

        bar?.addEventListener('scroll', updateArrows);
        updateArrows();

        // Scroll the active category into the center of the bar on page load
        const activeLink = bar?.querySelector('a[data-active]');
        if (activeLink && bar) {
            const barCenter  = bar.clientWidth / 2;
            const linkCenter = activeLink.offsetLeft + activeLink.offsetWidth / 2;
            bar.scrollLeft   = linkCenter - barCenter;
            updateArrows();
        }
    });
    </script>

</x-layouts.app>
