<x-layouts.app :title="__('web.my_bookings_title')">

    {{-- Page banner --}}
    <div class="py-10 text-center border-b border-rose-gold/10"
         style="background:linear-gradient(135deg,var(--color-beige) 0%,color-mix(in srgb,var(--color-beige) 30%,white) 60%);">
        <h1 class="text-3xl sm:text-4xl font-bold text-salon-text">{{ __('web.my_bookings_title') }}</h1>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-14">

        @php
        $statusStyles = [
            'pending'   => ['pill' => 'color:#b45309; background:rgba(251,191,36,0.12); border:1px solid rgba(251,191,36,0.25);', 'dot' => '#b45309'],
            'confirmed' => ['pill' => 'color:#1d4ed8; background:rgba(59,130,246,0.10); border:1px solid rgba(59,130,246,0.20);', 'dot' => '#1d4ed8'],
            'completed' => ['pill' => 'color:#15803d; background:rgba(34,197,94,0.10); border:1px solid rgba(34,197,94,0.20);', 'dot' => '#15803d'],
            'cancelled' => ['pill' => 'color:#9ca3af; background:rgba(156,163,175,0.10); border:1px solid rgba(156,163,175,0.20);', 'dot' => '#9ca3af'],
            'no_show'   => ['pill' => 'color:#dc2626; background:rgba(239,68,68,0.10); border:1px solid rgba(239,68,68,0.20);', 'dot' => '#dc2626'],
        ];
        @endphp

        @if($bookings->count())
            <div class="space-y-4">
                @foreach($bookings as $booking)
                    @php
                        $st = $statusStyles[$booking->status->value] ?? $statusStyles['cancelled'];
                    @endphp
                    <div class="bg-white rounded-2xl p-5"
                         style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 8%,transparent); box-shadow:0 2px 12px color-mix(in srgb,var(--color-rose-gold) 5%,transparent);">

                        <div class="flex items-start justify-between flex-wrap gap-4">

                            {{-- Services + date/time --}}
                            <div class="flex-1 min-w-0">
                                @if($booking->items->isNotEmpty())
                                    <div class="space-y-0.5 mb-2">
                                        @foreach($booking->items as $item)
                                            <p class="font-semibold text-salon-text text-[15px] leading-snug">
                                                {{ app()->getLocale() === 'ar'
                                                   ? $item->service?->name_ar
                                                   : $item->service?->name_en }}
                                            </p>
                                        @endforeach
                                    </div>
                                @endif
                                <p class="text-[13px] text-gray-400 flex items-center gap-1.5 mt-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $booking->booking_date }}
                                    <span class="mx-1 text-gray-300">·</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $booking->start_time }}
                                    @if($booking->end_time) → {{ $booking->end_time }} @endif
                                </p>
                            </div>

                            {{-- Status + price --}}
                            <div class="flex flex-col items-end gap-2 shrink-0">
                                <span class="text-[11px] font-semibold tracking-wide px-3 py-1 rounded-full"
                                      style="{{ $st['pill'] }}">
                                    {{ __('web.status_' . strtolower($booking->status->value)) }}
                                </span>
                                <span class="text-base font-bold text-rose-gold">
                                    {{ number_format($booking->total_price, 0) }} {{ currency() }}
                                </span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        @if(in_array($booking->status->value, ['pending','confirmed']) || ($booking->status->value === 'completed' && !$booking->reviews()->exists()))
                            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-50">
                                @if(in_array($booking->status->value, ['pending','confirmed']))
                                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST"
                                          onsubmit="return confirm('{{ __('web.confirm_delete') }}')">
                                        @csrf
                                        <button type="submit"
                                                class="text-[12px] px-4 py-2 rounded-lg border border-gray-200 text-gray-500 hover:border-rose-gold hover:text-rose-gold tracking-wide">
                                            {{ __('web.cancel_booking') }}
                                        </button>
                                    </form>
                                    <a href="{{ route('bookings.reschedule.form', $booking) }}"
                                       class="text-[12px] px-4 py-2 rounded-lg border border-gray-200 text-gray-500 hover:border-rose-gold hover:text-rose-gold tracking-wide">
                                        {{ __('web.reschedule') }}
                                    </a>
                                @endif

                                @if($booking->status->value === 'completed' && !$booking->reviews()->exists())
                                    <a href="{{ route('bookings.review.form', $booking) }}"
                                       class="text-[12px] px-4 py-2 rounded-lg bg-rose-gold text-white hover:bg-rose-gold-dark tracking-wide"
                                       style="box-shadow:0 2px 8px color-mix(in srgb,var(--color-rose-gold) 25%,transparent);">
                                        {{ __('web.add_review') }}
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-8">{{ $bookings->links() }}</div>

        @else
            <div class="bg-white rounded-2xl py-20 text-center"
                 style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 8%,transparent); box-shadow:0 2px 12px color-mix(in srgb,var(--color-rose-gold) 5%,transparent);">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6"
                     style="background:color-mix(in srgb,var(--color-rose-gold) 6%,transparent);">
                    <svg class="w-9 h-9 text-rose-gold/25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-gray-400 text-[15px] mb-6">{{ __('web.no_bookings') }}</p>
                <a href="{{ route('services.index') }}"
                   class="inline-flex items-center gap-2 bg-rose-gold text-white font-semibold px-8 py-3 rounded-full hover:bg-rose-gold-dark text-sm tracking-wide"
                   style="box-shadow:0 4px 14px color-mix(in srgb,var(--color-rose-gold) 35%,transparent);">
                    {{ __('web.nav_services') }}
                </a>
            </div>
        @endif
    </div>

</x-layouts.app>

