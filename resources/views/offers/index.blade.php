<x-layouts.app :title="__('web.offers_title')">

    {{-- Page banner --}}
    <div class="py-12 text-center border-b border-rose-gold/10"
         style="background:linear-gradient(135deg,var(--color-beige) 0%,color-mix(in srgb,var(--color-beige) 30%,white) 60%,color-mix(in srgb,var(--color-soft-pink) 10%,transparent) 100%);">
        <p class="text-[11px] tracking-[0.22em] uppercase text-rose-gold font-medium mb-3 opacity-70">
            {{ app()->getLocale() === 'ar' ? 'عروض حصرية' : 'Exclusive Deals' }}
        </p>
        <h1 class="text-3xl sm:text-4xl font-bold text-salon-text">{{ __('web.offers_title') }}</h1>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

        @if($offers->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
                @foreach($offers as $offer)
                    <a href="{{ route('offers.show', $offer) }}"
                       class="group bg-white rounded-2xl overflow-hidden card-lift flex flex-col"
                       style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 8%,transparent); box-shadow:0 2px 12px color-mix(in srgb,var(--color-rose-gold) 6%,transparent);">

                        {{-- Image --}}
                        @if($offer->image_url)
                            <div>
                                <img src="{{ $offer->image_url }}"
                                     alt="{{ app()->getLocale() === 'ar' ? $offer->title_ar : $offer->title_en }}"
                                     loading="lazy"
                                     class="w-full">
                            </div>
                        @else
                            <div class="aspect-video flex items-center justify-center"
                                 style="background:linear-gradient(135deg,var(--color-beige),var(--color-soft-pink-light));">
                                <svg class="w-14 h-14 text-rose-gold/15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                        @endif

                        <div class="p-5 flex flex-col flex-1">

                            {{-- Header row --}}
                            <div class="flex items-start justify-between mb-3 gap-3">
                                <span class="inline-flex items-center text-[11px] font-bold tracking-wide px-3 py-1 rounded-full shrink-0"
                                      style="background:color-mix(in srgb,var(--color-rose-gold) 8%,transparent); color:var(--color-rose-gold);">
                                    @if($offer->discount_type->value === 'percentage')
                                        {{ number_format($offer->discount_value, 0) }}% {{ __('web.discount') }}
                                    @else
                                        -{{ number_format($offer->discount_value, 0) }} {{ currency() }}
                                    @endif
                                </span>
                                @if($offer->ends_at)
                                    <span class="text-[11px] text-gray-400 shrink-0">
                                        {{ $offer->ends_at->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Title --}}
                            <h2 class="text-base font-bold text-salon-text group-hover:text-rose-gold line-clamp-1 mb-1.5">
                                {{ app()->getLocale() === 'ar' ? $offer->title_ar : $offer->title_en }}
                            </h2>

                            {{-- Description --}}
                            @php $desc = app()->getLocale() === 'ar' ? $offer->description_ar : $offer->description_en; @endphp
                            @if($desc)
                                <p class="text-[13px] text-gray-500 line-clamp-2 mb-auto">{{ $desc }}</p>
                            @endif

                            @if($offer->services->count())
                                <p class="text-[11px] text-gray-400 mt-3 pt-3 border-t border-gray-50">
                                    {{ $offer->services->count() }} {{ __('web.included_services') }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">{{ $offers->links() }}</div>

        @else
            <div class="text-center py-24">
                <p class="text-4xl text-rose-gold/15 mb-5">✦</p>
                <p class="text-gray-400 text-[15px]">{{ __('web.no_offers') }}</p>
            </div>
        @endif
    </div>

</x-layouts.app>

