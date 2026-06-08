<div class="space-y-7">

    {{-- Error banner --}}
    @if($errorMessage)
        <div class="flex items-start gap-3 bg-red-50 border border-red-100 text-red-600 text-sm px-4 py-3.5 rounded-xl">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $errorMessage }}</span>
        </div>
    @endif

    {{-- ── Step 1: Services ─────────────────────────────── --}}
    @if($fromCart)
        {{-- Read-only cart summary --}}
        <div>
            <p class="text-[11px] tracking-[0.18em] uppercase font-semibold text-rose-gold/70 mb-3">
                {{ __('web.cart_title') }}
            </p>
            <div class="rounded-xl divide-y divide-beige-dark overflow-hidden"
                 style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 12%,transparent); background:var(--color-beige);">
                @foreach($selectedServices as $svc)
                    <div class="flex justify-between items-center px-4 py-3 gap-4">
                        <span class="text-[14px] text-gray-700 flex-1">
                            {{ app()->getLocale() === 'ar' ? $svc->name_ar : $svc->name_en }}
                        </span>
                        <span class="text-[13px] text-gray-400 shrink-0">
                            {{ number_format($svc->price, 0) }} {{ currency() }}
                            · {{ __('web.duration_minutes', ['min' => $svc->duration_minutes]) }}
                        </span>
                        <button type="button"
                                wire:click="removeService({{ $svc->id }})"
                                wire:confirm="{{ __('web.confirm_delete') }}"
                                class="shrink-0 w-6 h-6 flex items-center justify-center rounded-full text-gray-300 hover:text-rose-gold hover:bg-rose-gold/8">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
            <div class="flex gap-6 text-[13px] pt-3 px-1">
                <span class="text-gray-500">
                    {{ __('web.total_duration') }}: <strong class="text-gray-700">{{ $totalDuration }} {{ __('web.min') }}</strong>
                </span>
                <span class="text-gray-500">
                    {{ __('web.total_price') }}: <strong class="text-rose-gold">{{ number_format($totalPrice, 0) }} {{ currency() }}</strong>
                </span>
            </div>
        </div>
    @else
        <div>
            <p class="text-[11px] tracking-[0.18em] uppercase font-semibold text-rose-gold/70 mb-3">
                {{ __('web.select_service') }} <span class="text-rose-gold">*</span>
            </p>

            @php
                $grouped = $allServices->groupBy(fn($s) => app()->getLocale() === 'ar'
                    ? $s->category?->name_ar
                    : $s->category?->name_en);
            @endphp

            <div class="space-y-5">
                @foreach($grouped as $categoryName => $categoryServices)
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2.5 px-1">
                            {{ $categoryName }}
                        </p>
                        <div class="space-y-2">
                            @foreach($categoryServices as $svc)
                                <label class="flex items-center gap-3.5 px-4 py-3 rounded-xl border cursor-pointer"
                                       style="{{ in_array($svc->id, $serviceIds)
                                           ? 'border-color:var(--color-rose-gold); background:color-mix(in srgb,var(--color-rose-gold) 5%,transparent);'
                                           : 'border-color:#e5e7eb; background:white;' }}
                                          transition:border-color 200ms,background 200ms;">
                                    <input type="checkbox"
                                           wire:model.live="serviceIds"
                                           value="{{ $svc->id }}"
                                           class="rounded border-gray-300 text-rose-gold focus:ring-rose-gold shrink-0">
                                    <span class="flex-1 text-[14px] text-gray-700">
                                        {{ app()->getLocale() === 'ar' ? $svc->name_ar : $svc->name_en }}
                                    </span>
                                    <span class="text-[12px] text-gray-400 shrink-0">
                                        {{ number_format($svc->price, 0) }} {{ currency() }}
                                        · {{ __('web.duration_minutes', ['min' => $svc->duration_minutes]) }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @error('serviceIds')
                <p class="text-red-500 text-[12px] mt-2">{{ $message }}</p>
            @enderror

            @if($selectedServices->isNotEmpty())
                <div class="flex gap-6 text-[13px] pt-4 px-1">
                    <span class="text-gray-500">
                        {{ __('web.total_duration') }}: <strong class="text-gray-700">{{ $totalDuration }} {{ __('web.min') }}</strong>
                    </span>
                    <span class="text-gray-500">
                        {{ __('web.total_price') }}: <strong class="text-rose-gold">{{ number_format($totalPrice, 0) }} {{ currency() }}</strong>
                    </span>
                </div>
            @endif
        </div>
    @endif

    {{-- ── Step 2: Date (shown after services selected) ── --}}
    @if($serviceIds)
        <div>
            <label class="block text-[11px] tracking-[0.18em] uppercase font-semibold text-rose-gold/70 mb-3">
                {{ __('web.select_date') }} <span class="text-rose-gold">*</span>
            </label>
            <input type="date"
                   wire:model.live="bookingDate"
                   min="{{ now()->toDateString() }}"
                   class="salon-input">
            @error('bookingDate')
                <p class="text-red-500 text-[12px] mt-1.5">{{ $message }}</p>
            @enderror
        </div>
    @endif

    {{-- ── Step 3: Time slots ───────────────────────────── --}}
    @if($bookingDate && $serviceIds)
        <div>
            <label class="block text-[11px] tracking-[0.18em] uppercase font-semibold text-rose-gold/70 mb-3">
                {{ __('web.select_time') }} <span class="text-rose-gold">*</span>
            </label>

            <div wire:loading class="flex items-center gap-2.5 text-[13px] text-gray-400 py-2">
                <svg class="animate-spin w-4 h-4 text-rose-gold" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span>{{ app()->getLocale() === 'ar' ? 'جاري التحميل...' : 'Loading slots...' }}</span>
            </div>

            <div wire:loading.remove>
                @if(empty($availableSlots))
                    <p class="text-[13px] text-gray-400 italic py-2">{{ __('web.no_slots_available') }}</p>
                @else
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        @foreach($availableSlots as $slot)
                            <button type="button"
                                    wire:click="selectSlot('{{ $slot }}')"
                                    class="py-2.5 px-3 text-[13px] rounded-xl border font-medium tracking-wide"
                                    style="{{ $startTime === $slot
                                        ? 'background:var(--color-rose-gold); color:white; border-color:var(--color-rose-gold); box-shadow:0 2px 8px color-mix(in srgb,var(--color-rose-gold) 30%,transparent);'
                                        : 'background:white; color:#4b5563; border-color:#e5e7eb;' }}
                                       transition:background 200ms,color 200ms,border-color 200ms,box-shadow 200ms;">
                                {{ $slot }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            @error('startTime')
                <p class="text-red-500 text-[12px] mt-1.5">{{ $message }}</p>
            @enderror
        </div>
    @endif

    {{-- ── Step 4: Notes + Summary + Submit ────────────── --}}
    @if($startTime)
        {{-- Notes --}}
        <div>
            <label class="block text-[11px] tracking-[0.18em] uppercase font-semibold text-rose-gold/70 mb-3">
                {{ __('web.notes') }}
            </label>
            <textarea wire:model="notes" rows="3"
                      class="salon-input resize-none"
                      placeholder="{{ __('web.notes') }}"></textarea>
        </div>

        {{-- Summary card --}}
        <div class="rounded-2xl p-5 space-y-3"
             style="background:linear-gradient(135deg,var(--color-beige),color-mix(in srgb,var(--color-beige) 30%,white)); border:1px solid color-mix(in srgb,var(--color-rose-gold) 12%,transparent);">
            <p class="text-[11px] tracking-[0.18em] uppercase font-bold text-rose-gold/70 mb-1">
                {{ __('web.booking_summary') }}
            </p>

            @foreach($selectedServices as $svc)
                <div class="flex justify-between text-[14px]">
                    <span class="text-gray-600">{{ app()->getLocale() === 'ar' ? $svc->name_ar : $svc->name_en }}</span>
                    <span class="font-medium text-gray-800">{{ number_format($svc->price, 0) }} {{ currency() }}</span>
                </div>
            @endforeach

            <div class="flex justify-between text-[14px]">
                <span class="text-gray-500">{{ __('web.date') }}</span>
                <span class="font-medium text-gray-800">{{ $bookingDate }}</span>
            </div>

            <div class="flex justify-between text-[14px]">
                <span class="text-gray-500">{{ __('web.time') }}</span>
                <span class="font-medium text-gray-800">
                    {{ $startTime }}@if($estimatedEndTime) → {{ $estimatedEndTime }}@endif
                </span>
            </div>

            <div class="flex justify-between text-[14px] pt-3 border-t border-beige-dark">
                <span class="text-gray-500">{{ __('web.total_duration') }}</span>
                <span class="font-medium text-gray-800">{{ $totalDuration }} {{ __('web.min') }}</span>
            </div>

            <div class="flex justify-between font-bold text-base pt-1">
                <span class="text-salon-text">{{ __('web.total_price') }}</span>
                <span class="text-rose-gold">{{ number_format($totalPrice, 0) }} {{ currency() }}</span>
            </div>
        </div>

        {{-- Submit --}}
        <button type="button"
                wire:click="submit"
                wire:loading.attr="disabled"
                class="w-full bg-rose-gold text-white font-semibold py-4 rounded-full hover:bg-rose-gold-dark
                       disabled:opacity-60 disabled:cursor-not-allowed tracking-wide text-sm"
                style="box-shadow:0 6px 20px color-mix(in srgb,var(--color-rose-gold) 38%,transparent);">
            <span wire:loading.remove>{{ __('web.confirm_booking') }}</span>
            <span wire:loading class="flex items-center justify-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ app()->getLocale() === 'ar' ? 'جاري الحجز...' : 'Booking...' }}
            </span>
        </button>
    @endif

</div>

