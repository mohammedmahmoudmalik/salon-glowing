<x-layouts.app :title="__('web.cart_title')">

    {{-- Page banner --}}
    <div class="py-10 text-center border-b border-rose-gold/10"
         style="background:linear-gradient(135deg,var(--color-beige) 0%,color-mix(in srgb,var(--color-beige) 30%,white) 60%);">
        <h1 class="text-3xl sm:text-4xl font-bold text-salon-text">{{ __('web.cart_title') }}</h1>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-14">

        @if($items->isNotEmpty())

            {{-- Items list --}}
            <div class="space-y-3 mb-8">
                @foreach($items as $item)
                    <div class="bg-white rounded-2xl p-4 flex items-center gap-4"
                         style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 8%,transparent); box-shadow:0 2px 10px color-mix(in srgb,var(--color-rose-gold) 5%,transparent);">

                        {{-- Thumbnail --}}
                        @if($item->image_url)
                            <img src="{{ $item->image_url }}"
                                 alt="{{ app()->getLocale() === 'ar' ? $item->name_ar : $item->name_en }}"
                                 class="w-16 h-16 rounded-xl object-cover shrink-0">
                        @else
                            <div class="w-16 h-16 rounded-xl shrink-0 flex items-center justify-center"
                                 style="background:linear-gradient(135deg,var(--color-beige),var(--color-soft-pink-light));">
                                <svg class="w-6 h-6 text-rose-gold/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M12 2a5 5 0 100 10A5 5 0 0012 2z"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            @if($item->category)
                                <p class="text-[10px] tracking-[0.15em] uppercase font-semibold text-rose-gold/60 mb-0.5">
                                    {{ app()->getLocale() === 'ar' ? $item->category->name_ar : $item->category->name_en }}
                                </p>
                            @endif
                            <p class="font-semibold text-salon-text leading-snug truncate text-[15px]">
                                {{ app()->getLocale() === 'ar' ? $item->name_ar : $item->name_en }}
                            </p>
                            <p class="text-[11px] text-gray-400 mt-0.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ __('web.duration_minutes', ['min' => $item->duration_minutes]) }}
                            </p>
                        </div>

                        {{-- Price + remove --}}
                        <div class="flex flex-col items-end gap-2 shrink-0">
                            <span class="text-base font-bold text-rose-gold">
                                {{ number_format($item->price, 0) }} {{ currency() }}
                            </span>
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-[11px] text-gray-400 hover:text-rose-gold tracking-wide">
                                    {{ __('web.remove') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Summary + CTA --}}
            <div class="bg-white rounded-2xl p-6"
                 style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 10%,transparent); box-shadow:0 4px 20px color-mix(in srgb,var(--color-rose-gold) 7%,transparent);">

                <div class="flex flex-wrap justify-between text-[13px] text-gray-500 mb-3 gap-2">
                    <span>{{ __('web.cart_services_count', ['count' => $items->count()]) }}</span>
                    <span>
                        {{ __('web.total_duration') }}: <strong class="text-gray-700">{{ $totalDuration }} {{ __('web.min') }}</strong>
                    </span>
                </div>

                <div class="flex justify-between items-center border-t border-gray-100 pt-4 mb-6">
                    <span class="text-base font-semibold text-salon-text">{{ __('web.total_price') }}</span>
                    <span class="text-2xl font-bold text-rose-gold">{{ number_format($total, 0) }} {{ currency() }}</span>
                </div>

                <a href="{{ route('booking.create') }}"
                   class="block w-full text-center bg-rose-gold text-white font-semibold py-4 rounded-full hover:bg-rose-gold-dark tracking-wide text-sm"
                   style="box-shadow:0 6px 20px color-mix(in srgb,var(--color-rose-gold) 35%,transparent);">
                    {{ __('web.cart_checkout') }}
                </a>

                <div class="flex items-center justify-between mt-4">
                    <a href="{{ route('services.index') }}"
                       class="text-[13px] text-gray-400 hover:text-rose-gold tracking-wide">
                        ← {{ __('web.cart_continue_shopping') }}
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-[13px] text-gray-400 hover:text-rose-gold tracking-wide">
                            {{ __('web.cart_clear') }}
                        </button>
                    </form>
                </div>
            </div>

        @else
            {{-- Empty state --}}
            <div class="bg-white rounded-2xl py-20 text-center"
                 style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 8%,transparent); box-shadow:0 2px 12px color-mix(in srgb,var(--color-rose-gold) 5%,transparent);">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6"
                     style="background:color-mix(in srgb,var(--color-rose-gold) 6%,transparent);">
                    <svg class="w-9 h-9 text-rose-gold/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <p class="text-gray-400 text-[15px] mb-6">{{ __('web.cart_empty') }}</p>
                <a href="{{ route('services.index') }}"
                   class="inline-flex items-center gap-2 bg-rose-gold text-white font-semibold px-8 py-3 rounded-full hover:bg-rose-gold-dark text-sm tracking-wide"
                   style="box-shadow:0 4px 14px color-mix(in srgb,var(--color-rose-gold) 35%,transparent);">
                    {{ __('web.nav_services') }}
                </a>
            </div>
        @endif
    </div>

</x-layouts.app>

