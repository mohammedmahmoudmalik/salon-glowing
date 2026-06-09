@php
    $name         = app()->getLocale() === 'ar' ? $service->name_ar : $service->name_en;
    $desc         = app()->getLocale() === 'ar'
                    ? ($service->description_ar ?? '')
                    : ($service->description_en ?? '');
    $catName      = $service->category
                    ? (app()->getLocale() === 'ar' ? $service->category->name_ar : $service->category->name_en)
                    : null;
    $priceStr     = number_format($finalPrice, 0) . ' ' . currency();
    $origPriceStr = ($activeOffer && $finalPrice < $service->price)
                    ? number_format($service->price, 0) . ' ' . currency()
                    : null;
    $discountPct  = ($origPriceStr && $service->price > 0)
                    ? round((1 - $finalPrice / $service->price) * 100)
                    : null;
@endphp

<div>
    {{-- ════════════════════════════════════════════
         GRID VIEW CARD — vertical portrait
    ═════════════════════════════════════════════ --}}
    <div x-show="$store.serviceView.mode === 'grid'"
         class="group bg-white rounded-2xl overflow-hidden border border-gray-100
                hover:border-rose-gold/25 hover:shadow-xl
                transition-all duration-300 cursor-default flex flex-col h-full"
         style="box-shadow:0 2px 12px rgba(0,0,0,0.06);">

        {{-- Image area --}}
        <div class="relative overflow-hidden shrink-0" style="aspect-ratio:4/3;">
            @if($service->image_url)
                <img src="{{ $service->image_url }}"
                     alt="{{ $name }}"
                     loading="lazy"
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center"
                     style="background:linear-gradient(135deg,var(--color-beige) 0%,var(--color-soft-pink-light) 100%);">
                    <svg class="w-12 h-12" style="color:color-mix(in srgb,var(--color-rose-gold) 22%,transparent);"
                         viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3c-.5 0-.9.4-.9.9v1.8c0 .5.4.9.9.9s.9-.4.9-.9V3.9c0-.5-.4-.9-.9-.9zm4.2 1.7c-.4-.2-.9-.1-1.2.3l-.9 1.6c-.2.4-.1.9.3 1.2.4.2.9.1 1.2-.3l.9-1.6c.2-.4.1-.9-.3-1.2zm-8.4 0c-.4.2-.5.7-.3 1.2l.9 1.6c.2.4.7.5 1.2.3.4-.2.5-.7.3-1.2L9 4.7c-.3-.4-.8-.5-1.2-.3zM4.7 8.1c-.4.2-.5.7-.3 1.2l.9 1.6c.2.4.7.5 1.2.3.4-.2.5-.7.3-1.2l-.9-1.6c-.3-.4-.8-.5-1.2-.3zm14.6 0c-.4-.2-.9-.1-1.2.3l-.9 1.6c-.2.4-.1.9.3 1.2.4.2.9.1 1.2-.3l.9-1.6c.2-.5.1-1-.3-1.2zM12 7.5c-2.5 0-4.5 2-4.5 4.5S9.5 16.5 12 16.5s4.5-2 4.5-4.5S14.5 7.5 12 7.5zm0 7.2c-1.5 0-2.7-1.2-2.7-2.7S10.5 9.3 12 9.3s2.7 1.2 2.7 2.7-1.2 2.7-2.7 2.7zm-7.2-.9c-.5 0-.9.4-.9.9s.4.9.9.9h1.8c.5 0 .9-.4.9-.9s-.4-.9-.9-.9H4.8zm13.5 0c-.5 0-.9.4-.9.9s.4.9.9.9h1.8c.5 0 .9-.4.9-.9s-.4-.9-.9-.9h-1.8zM7.6 17.1c-.4-.2-.9-.1-1.2.3l-.9 1.6c-.2.4-.1.9.3 1.2.4.2.9.1 1.2-.3l.9-1.6c.3-.4.1-.9-.3-1.2zm8.8 0c-.4.2-.5.7-.3 1.2l.9 1.6c.2.4.7.5 1.2.3.4-.2.5-.7.3-1.2l-.9-1.6c-.3-.4-.8-.5-1.2-.3zM12 18.3c-.5 0-.9.4-.9.9v1.8c0 .5.4.9.9.9s.9-.4.9-.9v-1.8c0-.5-.4-.9-.9-.9z"/>
                    </svg>
                </div>
            @endif

            {{-- Discount badge --}}
            @if($discountPct)
                <div class="absolute top-2.5 end-2.5">
                    <span class="text-[10px] font-bold text-white px-2 py-0.5 rounded-full leading-none"
                          style="background:var(--color-rose-gold); box-shadow:0 1px 4px rgba(0,0,0,0.18);">
                        -{{ $discountPct }}%
                    </span>
                </div>
            @endif

            {{-- Category overlay at bottom of image --}}
            @if($catName)
                <div class="absolute bottom-0 inset-x-0 px-3 pt-4 pb-2"
                     style="background:linear-gradient(to top,rgba(0,0,0,0.45) 0%,transparent 100%);">
                    <span class="text-[9px] sm:text-[10px] tracking-[0.14em] uppercase font-semibold text-white/90 leading-none">
                        {{ $catName }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Content --}}
        <div class="flex flex-col flex-1 px-3.5 py-3 sm:px-4 sm:py-3.5">

            <h3 class="text-[13px] sm:text-[14px] font-semibold text-salon-text line-clamp-2 leading-snug mb-1.5">
                {{ $name }}
            </h3>

            @if($desc)
                <p class="text-[11px] sm:text-[12px] text-gray-400 leading-relaxed line-clamp-2 mb-2 flex-1">
                    {{ $desc }}
                </p>
            @else
                <div class="flex-1"></div>
            @endif

            {{-- Duration --}}
            <div class="flex items-center gap-1 mb-3">
                <svg class="w-3 h-3 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-[10px] sm:text-[11px] text-gray-400">
                    {{ __('web.duration_minutes', ['min' => $service->duration_minutes]) }}
                </span>
            </div>

            {{-- Price + CTA --}}
            <div class="flex items-center justify-between gap-2 pt-2.5 border-t border-gray-100">
                <div class="flex flex-col leading-none min-w-0 shrink-0">
                    @if($origPriceStr)
                        <span class="text-[9px] sm:text-[10px] text-gray-400 line-through mb-0.5 whitespace-nowrap">
                            {{ $origPriceStr }}
                        </span>
                    @endif
                    <span class="text-[13px] sm:text-[14px] font-bold whitespace-nowrap"
                          style="color:var(--color-rose-gold);">
                        {{ $priceStr }}
                    </span>
                </div>

                @if($inCart)
                    <button wire:click="removeFromCart"
                            class="text-[10px] sm:text-[11px] font-semibold py-1.5 px-2.5 sm:px-3 rounded-full
                                   text-white shrink-0 whitespace-nowrap transition-opacity duration-200 hover:opacity-80"
                            style="background:var(--color-rose-gold);">
                        ✓ {{ __('web.in_cart') }}
                    </button>
                @else
                    <button wire:click="addToCart"
                            class="text-[10px] sm:text-[11px] font-semibold py-1.5 px-2.5 sm:px-3 rounded-full
                                   border shrink-0 whitespace-nowrap transition-colors duration-200
                                   hover:text-white"
                            style="border-color:var(--color-rose-gold); color:var(--color-rose-gold);"
                            onmouseover="this.style.background='var(--color-rose-gold)'; this.style.color='white';"
                            onmouseout="this.style.background=''; this.style.color='var(--color-rose-gold)';">
                        + {{ __('web.add_to_cart') }}
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════
         LIST VIEW ROW
    ═════════════════════════════════════════════ --}}
    <div x-show="$store.serviceView.mode === 'list'"
         class="flex items-center gap-3 sm:gap-4 py-4 px-1 sm:px-2 w-full cursor-default
                rounded-xl transition-colors duration-200 hover:bg-rose-50/50">

        {{-- Thumbnail --}}
        <div class="shrink-0 rounded-xl overflow-hidden"
             style="width:72px; height:72px; min-width:72px;">
            @if($service->image_url)
                <img src="{{ $service->image_url }}"
                     alt="{{ $name }}"
                     loading="lazy"
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center"
                     style="background:linear-gradient(135deg,var(--color-beige),var(--color-soft-pink-light));">
                    <svg class="w-6 h-6" style="color:color-mix(in srgb,var(--color-rose-gold) 22%,transparent);"
                         viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3c-.5 0-.9.4-.9.9v1.8c0 .5.4.9.9.9s.9-.4.9-.9V3.9c0-.5-.4-.9-.9-.9zm4.2 1.7c-.4-.2-.9-.1-1.2.3l-.9 1.6c-.2.4-.1.9.3 1.2.4.2.9.1 1.2-.3l.9-1.6c.2-.4.1-.9-.3-1.2zm-8.4 0c-.4.2-.5.7-.3 1.2l.9 1.6c.2.4.7.5 1.2.3.4-.2.5-.7.3-1.2L9 4.7c-.3-.4-.8-.5-1.2-.3zM4.7 8.1c-.4.2-.5.7-.3 1.2l.9 1.6c.2.4.7.5 1.2.3.4-.2.5-.7.3-1.2l-.9-1.6c-.3-.4-.8-.5-1.2-.3zm14.6 0c-.4-.2-.9-.1-1.2.3l-.9 1.6c-.2.4-.1.9.3 1.2.4.2.9.1 1.2-.3l.9-1.6c.2-.5.1-1-.3-1.2zM12 7.5c-2.5 0-4.5 2-4.5 4.5S9.5 16.5 12 16.5s4.5-2 4.5-4.5S14.5 7.5 12 7.5zm0 7.2c-1.5 0-2.7-1.2-2.7-2.7S10.5 9.3 12 9.3s2.7 1.2 2.7 2.7-1.2 2.7-2.7 2.7zm-7.2-.9c-.5 0-.9.4-.9.9s.4.9.9.9h1.8c.5 0 .9-.4.9-.9s-.4-.9-.9-.9H4.8zm13.5 0c-.5 0-.9.4-.9.9s.4.9.9.9h1.8c.5 0 .9-.4.9-.9s-.4-.9-.9-.9h-1.8zM7.6 17.1c-.4-.2-.9-.1-1.2.3l-.9 1.6c-.2.4-.1.9.3 1.2.4.2.9.1 1.2-.3l.9-1.6c.3-.4.1-.9-.3-1.2zm8.8 0c-.4.2-.5.7-.3 1.2l.9 1.6c.2.4.7.5 1.2.3.4-.2.5-.7.3-1.2l-.9-1.6c-.3-.4-.8-.5-1.2-.3zM12 18.3c-.5 0-.9.4-.9.9v1.8c0 .5.4.9.9.9s.9-.4.9-.9v-1.8c0-.5-.4-.9-.9-.9z"/>
                    </svg>
                </div>
            @endif
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            @if($catName)
                <span class="text-[9px] sm:text-[10px] tracking-[0.15em] uppercase font-semibold block mb-0.5 truncate"
                      style="color:color-mix(in srgb,var(--color-rose-gold) 70%,transparent);">
                    {{ $catName }}
                </span>
            @endif

            <h3 class="text-[14px] sm:text-[15px] font-semibold text-salon-text leading-snug mb-1 truncate">
                {{ $name }}
            </h3>

            @if($desc)
                <p class="text-[11px] sm:text-[12px] text-gray-500 leading-relaxed line-clamp-1 mb-1.5 hidden sm:block">
                    {{ $desc }}
                </p>
            @endif

            <span class="inline-flex items-center gap-1 text-[10px] sm:text-[11px] text-gray-400">
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('web.duration_minutes', ['min' => $service->duration_minutes]) }}
            </span>
        </div>

        {{-- Price + Action --}}
        <div class="flex flex-col items-end gap-2 shrink-0">
            <div class="text-end leading-none">
                @if($origPriceStr)
                    <span class="text-[10px] text-gray-400 line-through block mb-0.5 whitespace-nowrap">
                        {{ $origPriceStr }}
                    </span>
                @endif
                <span class="text-[13px] sm:text-[14px] font-bold whitespace-nowrap"
                      style="color:var(--color-rose-gold);">
                    {{ $priceStr }}
                </span>
            </div>

            @if($inCart)
                <button wire:click="removeFromCart"
                        class="text-[10px] sm:text-[11px] font-semibold py-1.5 px-3 sm:px-4 rounded-full
                               text-white whitespace-nowrap transition-opacity duration-200 hover:opacity-80"
                        style="background:var(--color-rose-gold);">
                    ✓ {{ __('web.in_cart') }}
                </button>
            @else
                <button wire:click="addToCart"
                        class="text-[10px] sm:text-[11px] font-semibold py-1.5 px-3 sm:px-4 rounded-full
                               border whitespace-nowrap transition-colors duration-200"
                        style="border-color:var(--color-rose-gold); color:var(--color-rose-gold);"
                        onmouseover="this.style.background='var(--color-rose-gold)'; this.style.color='white';"
                        onmouseout="this.style.background=''; this.style.color='var(--color-rose-gold)';">
                    {{ __('web.add_to_cart') }}
                </button>
            @endif
        </div>
    </div>
</div>
