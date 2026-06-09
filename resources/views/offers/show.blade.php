<x-layouts.app :title="app()->getLocale() === 'ar' ? $offer->title_ar : $offer->title_en">

    {{-- Breadcrumb --}}
    <div class="border-b border-gray-100/80 bg-white/60">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
            <a href="{{ route('offers.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-gray-400 hover:text-rose-gold tracking-wide">
                <svg class="w-3.5 h-3.5 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('web.back') }}
            </a>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start mb-16">

            {{-- Image --}}
            <div>
                @if($offer->image_url)
                    <div class="overflow-hidden rounded-2xl"
                         style="box-shadow:0 8px 32px color-mix(in srgb,var(--color-rose-gold) 14%,transparent);">
                        <img src="{{ $offer->image_url }}"
                             alt="{{ app()->getLocale() === 'ar' ? $offer->title_ar : $offer->title_en }}"
                             loading="lazy"
                             class="w-full aspect-video object-cover">
                    </div>
                @else
                    <div class="w-full aspect-video rounded-2xl flex items-center justify-center"
                         style="background:linear-gradient(135deg,var(--color-beige),var(--color-soft-pink-light));
                                box-shadow:0 8px 32px color-mix(in srgb,var(--color-rose-gold) 10%,transparent);">
                        <svg class="w-16 h-16 text-rose-gold/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Offer info --}}
            <div class="flex flex-col gap-5">
                {{-- Discount badge --}}
                <span class="inline-flex items-center self-start text-sm font-bold px-4 py-2 rounded-full"
                      style="background:color-mix(in srgb,var(--color-rose-gold) 8%,transparent); color:var(--color-rose-gold); letter-spacing:0.04em;">
                    @if($offer->discount_type->value === 'percentage')
                        {{ number_format($offer->discount_value, 0) }}% {{ __('web.discount') }}
                    @else
                        {{ number_format($offer->discount_value, 0) }} {{ currency() }} {{ __('web.discount') }}
                    @endif
                </span>

                <h1 class="text-3xl sm:text-4xl font-bold text-salon-text leading-tight -mt-1">
                    {{ app()->getLocale() === 'ar' ? $offer->title_ar : $offer->title_en }}
                </h1>

                @php $desc = app()->getLocale() === 'ar' ? $offer->description_ar : $offer->description_en; @endphp
                @if($desc)
                    <p class="text-gray-500 leading-relaxed text-[15px]">{{ $desc }}</p>
                @endif

                @if($offer->ends_at)
                    <div class="flex items-center gap-2.5 text-[13px] text-gray-400 py-3 px-4 rounded-xl"
                         style="background:color-mix(in srgb,var(--color-rose-gold) 4%,transparent); border:1px solid color-mix(in srgb,var(--color-rose-gold) 10%,transparent);">
                        <svg class="w-4 h-4 text-rose-gold/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ __('web.offer_expires') }}: <strong class="text-gray-600">{{ $offer->ends_at->translatedFormat('d F Y') }}</strong>
                    </div>
                @endif

                <a href="{{ route('booking.create') }}"
                   class="self-start inline-flex items-center gap-2 bg-rose-gold text-white font-semibold px-8 py-3.5 rounded-full hover:bg-rose-gold-dark text-sm tracking-wide mt-2"
                   style="box-shadow:0 6px 20px color-mix(in srgb,var(--color-rose-gold) 38%,transparent);">
                    {{ app()->getLocale() === 'ar' ? 'احجزي الآن' : 'Book Now' }}
                </a>
            </div>
        </div>

        {{-- Included services --}}
        @if($servicesWithPrices->count())
            <div>
                <div class="flex items-center gap-4 mb-8">
                    <span class="h-px flex-1 bg-linear-to-r from-transparent to-rose-gold/20"></span>
                    <h2 class="text-xl sm:text-2xl font-bold text-salon-text">{{ __('web.included_services') }}</h2>
                    <span class="h-px flex-1 bg-linear-to-l from-transparent to-rose-gold/20"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($servicesWithPrices as $item)
                        @php $service = $item['service']; $finalPrice = $item['finalPrice']; @endphp
                        <div class="bg-white rounded-2xl p-4 flex gap-4 card-lift"
                             style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 8%,transparent); box-shadow:0 2px 10px color-mix(in srgb,var(--color-rose-gold) 5%,transparent);">

                            {{-- Thumbnail --}}
                            <div class="shrink-0">
                                @if($service->image_url)
                                    <img src="{{ $service->image_url }}"
                                         alt="{{ app()->getLocale() === 'ar' ? $service->name_ar : $service->name_en }}"
                                         class="w-16 h-16 rounded-xl object-cover">
                                @else
                                    <div class="w-16 h-16 rounded-xl flex items-center justify-center"
                                         style="background:linear-gradient(135deg,var(--color-beige),var(--color-soft-pink-light));">
                                        <svg class="w-6 h-6 text-rose-gold/25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M12 2a5 5 0 100 10A5 5 0 0012 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                @if($service->category)
                                    <p class="text-[10px] tracking-[0.15em] uppercase font-semibold text-rose-gold/60 mb-0.5">
                                        {{ app()->getLocale() === 'ar' ? $service->category->name_ar : $service->category->name_en }}
                                    </p>
                                @endif
                                <h3 class="text-[14px] font-bold text-salon-text truncate">
                                    {{ app()->getLocale() === 'ar' ? $service->name_ar : $service->name_en }}
                                </h3>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    {{ __('web.duration_minutes', ['min' => $service->duration_minutes]) }}
                                </p>
                                <div class="flex items-baseline gap-2 mt-2">
                                    <span class="text-base font-bold text-rose-gold">
                                        {{ number_format($finalPrice, 0) }} {{ currency() }}
                                    </span>
                                    @if($finalPrice < $service->price)
                                        <span class="text-[11px] text-gray-400 line-through">
                                            {{ number_format($service->price, 0) }} {{ currency() }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Cart button --}}
                            <div class="shrink-0 flex items-end">
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                                    <button type="submit"
                                            class="text-[12px] font-semibold border border-rose-gold text-rose-gold px-3.5 py-2 rounded-lg hover:bg-rose-gold hover:text-white whitespace-nowrap tracking-wide">
                                        {{ __('web.add_to_cart') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

</x-layouts.app>

