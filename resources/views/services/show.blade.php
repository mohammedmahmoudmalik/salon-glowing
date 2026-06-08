<x-layouts.app :title="app()->getLocale() === 'ar' ? $service->name_ar : $service->name_en">

    {{-- Breadcrumb --}}
    <div class="border-b border-gray-100/80 bg-white/60">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
            <a href="{{ route('services.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-gray-400 hover:text-rose-gold tracking-wide">
                <svg class="w-3.5 h-3.5 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('web.nav_services') }}
            </a>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

            {{-- Image --}}
            <div>
                @if($service->image_url)
                    <div class="overflow-hidden rounded-2xl"
                         style="box-shadow:0 8px 32px color-mix(in srgb,var(--color-rose-gold) 14%,transparent);">
                        <img src="{{ $service->image_url }}"
                             alt="{{ app()->getLocale() === 'ar' ? $service->name_ar : $service->name_en }}"
                             loading="lazy"
                             class="w-full aspect-square object-cover">
                    </div>
                @else
                    <div class="w-full aspect-square rounded-2xl flex items-center justify-center"
                         style="background:linear-gradient(135deg, var(--color-beige), var(--color-soft-pink-light));
                                box-shadow:0 8px 32px color-mix(in srgb,var(--color-rose-gold) 10%,transparent);">
                        <svg class="w-20 h-20 text-rose-gold/20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2a5 5 0 100 10A5 5 0 0012 2zm0 12c-5.33 0-8 2.67-8 4v2h16v-2c0-1.33-2.67-4-8-4z"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex flex-col gap-5">

                {{-- Category --}}
                @if($service->category)
                    <span class="text-[11px] tracking-[0.2em] uppercase font-semibold text-rose-gold/70">
                        {{ app()->getLocale() === 'ar' ? $service->category->name_ar : $service->category->name_en }}
                    </span>
                @endif

                {{-- Name --}}
                <h1 class="text-3xl sm:text-4xl font-bold text-salon-text leading-tight -mt-2">
                    {{ app()->getLocale() === 'ar' ? $service->name_ar : $service->name_en }}
                </h1>

                {{-- Description --}}
                @if($service->description_ar || $service->description_en)
                    <p class="text-gray-500 leading-relaxed text-[15px]">
                        {{ app()->getLocale() === 'ar' ? $service->description_ar : $service->description_en }}
                    </p>
                @endif

                {{-- Price card --}}
                <div class="rounded-2xl p-5"
                     style="background:linear-gradient(135deg,var(--color-beige),color-mix(in srgb,var(--color-beige) 30%,white)); border:1px solid color-mix(in srgb,var(--color-rose-gold) 12%,transparent);">
                    @if($activeOffer && $finalPrice < $service->price)
                        <div class="flex items-baseline gap-3 mb-1">
                            <span class="text-3xl font-bold text-rose-gold">
                                {{ number_format($finalPrice, 0) }} {{ currency() }}
                            </span>
                            <span class="text-sm text-gray-400 line-through">
                                {{ number_format($service->price, 0) }} {{ currency() }}
                            </span>
                        </div>
                        <p class="text-[11px] text-rose-gold font-medium tracking-wide">
                            {{ app()->getLocale() === 'ar' ? $activeOffer->title_ar : $activeOffer->title_en }}
                        </p>
                    @else
                        <span class="text-3xl font-bold text-rose-gold">
                            {{ number_format($service->price, 0) }} {{ currency() }}
                        </span>
                    @endif
                    <div class="flex items-center gap-1.5 mt-2 text-[12px] text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('web.duration_minutes', ['min' => $service->duration_minutes]) }}
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-3">
                    @php $inCart = app(\App\Services\CartService::class)->has($service->id); @endphp

                    @if($inCart)
                        <div class="flex gap-3">
                            <form action="{{ route('cart.remove', $service->id) }}" method="POST" class="flex-1">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-full py-3.5 rounded-full font-semibold text-sm bg-rose-gold text-white hover:bg-rose-gold-dark tracking-wide"
                                        style="box-shadow:0 4px 14px color-mix(in srgb,var(--color-rose-gold) 35%,transparent);">
                                    ✓ {{ __('web.in_cart') }}
                                </button>
                            </form>
                            <a href="{{ route('cart.index') }}"
                               class="flex-1 text-center py-3.5 rounded-full border border-rose-gold text-rose-gold font-semibold text-sm hover:bg-soft-pink/20 tracking-wide">
                                {{ __('web.view_cart') }}
                            </a>
                        </div>
                    @else
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="service_id" value="{{ $service->id }}">
                            <button type="submit"
                                    class="w-full py-3.5 rounded-full border-2 border-rose-gold text-rose-gold font-semibold text-sm hover:bg-rose-gold hover:text-white tracking-wide">
                                {{ __('web.add_to_cart') }}
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        </div>
    </div>

</x-layouts.app>

