<x-layouts.app :title="__('web.booking_title')">

    {{-- Page banner --}}
    <div class="py-10 text-center border-b border-rose-gold/10"
         style="background:linear-gradient(135deg,var(--color-beige) 0%,color-mix(in srgb,var(--color-beige) 30%,white) 60%,color-mix(in srgb,var(--color-soft-pink) 10%,transparent) 100%);">
        <p class="text-[11px] tracking-[0.22em] uppercase text-rose-gold font-medium mb-3 opacity-70">
            {{ app()->getLocale() === 'ar' ? 'احجزي موعدك' : 'Reserve Your Time' }}
        </p>
        <h1 class="text-3xl sm:text-4xl font-bold text-salon-text">{{ __('web.booking_title') }}</h1>
    </div>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-14">

        @guest
            <div class="bg-white rounded-2xl p-10 text-center"
                 style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 10%,transparent); box-shadow:0 4px 24px color-mix(in srgb,var(--color-rose-gold) 8%,transparent);">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-5"
                     style="background:color-mix(in srgb,var(--color-rose-gold) 8%,transparent);">
                    <svg class="w-8 h-8 text-rose-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <p class="text-gray-500 mb-6 text-[15px] leading-relaxed">{{ __('messages.unauthenticated') }}</p>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 bg-rose-gold text-white font-semibold px-8 py-3 rounded-full hover:bg-rose-gold-dark text-sm tracking-wide"
                   style="box-shadow:0 4px 14px color-mix(in srgb,var(--color-rose-gold) 35%,transparent);">
                    {{ __('web.sign_in') }}
                </a>
            </div>
        @else
            <div class="bg-white rounded-2xl p-6 sm:p-8"
                 style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 10%,transparent); box-shadow:0 4px 24px color-mix(in srgb,var(--color-rose-gold) 8%,transparent);">
                @livewire('booking-form', ['serviceId' => $serviceId ? (int)$serviceId : null])
            </div>
        @endguest
    </div>

</x-layouts.app>
