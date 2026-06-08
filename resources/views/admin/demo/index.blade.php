<x-layouts.admin :title="__('web.demo_title')">

    <div class="max-w-2xl">

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ __('web.demo_title') }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ __('web.demo_subtitle') }}</p>
        </div>

        {{-- No services warning --}}
        @if(! $hasServices)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-amber-800">{{ __('web.demo_no_services') }}</p>
                    <a href="{{ route('admin.setup.index') }}"
                       class="text-xs text-amber-700 underline hover:text-amber-900 mt-1 inline-block">
                        {{ __('web.setup_services') }} ←
                    </a>
                </div>
            </div>
        @endif

        {{-- Demo warning --}}
        <div class="bg-red-50 border border-red-100 rounded-2xl p-4 mb-6 flex items-start gap-3">
            <svg class="w-4 h-4 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-xs text-red-600">{{ __('web.demo_warning') }}</p>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.demo.generate') }}" method="POST">
            @csrf

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl space-y-1">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

                {{-- Customers --}}
                <div class="px-6 py-5 border-b border-gray-50">
                    <div class="flex items-center justify-between gap-6">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-0.5">
                                {{ __('web.demo_customers') }}
                            </label>
                            <p class="text-xs text-gray-400">{{ __('web.demo_customers_hint') }}</p>
                        </div>
                        <input type="number" name="customer_count"
                               value="{{ old('customer_count', 10) }}"
                               min="1" max="100"
                               class="w-24 text-center border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                    </div>
                </div>

                {{-- Bookings --}}
                <div class="px-6 py-5 border-b border-gray-50">
                    <div class="flex items-center justify-between gap-6">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-0.5">
                                {{ __('web.demo_bookings') }}
                            </label>
                            <p class="text-xs text-gray-400">{{ __('web.demo_bookings_hint') }}</p>
                        </div>
                        <input type="number" name="booking_count"
                               value="{{ old('booking_count', 30) }}"
                               min="1" max="500"
                               class="w-24 text-center border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                    </div>
                </div>

                {{-- Offers --}}
                <div class="px-6 py-5">
                    <div class="flex items-center justify-between gap-6">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-0.5">
                                {{ __('web.demo_offers') }}
                            </label>
                            <p class="text-xs text-gray-400">{{ __('web.demo_offers_hint') }}</p>
                        </div>
                        <input type="number" name="offer_count"
                               value="{{ old('offer_count', 3) }}"
                               min="0" max="10"
                               class="w-24 text-center border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50/60 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                            {{ ! $hasServices ? 'disabled' : '' }}
                            class="bg-rose-gold text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-rose-gold-dark transition disabled:opacity-40 disabled:cursor-not-allowed">
                        {{ __('web.demo_generate') }}
                    </button>
                </div>

            </div>
        </form>

        {{-- Danger zone: clear all demo data --}}
        <div class="mt-6 rounded-2xl overflow-hidden" style="border: 1px solid #fecaca;">
            <div class="px-6 py-4" style="background-color: #fff5f5;">
                <p class="text-sm font-bold mb-0.5" style="color: #b91c1c;">{{ __('web.demo_clear_all') }}</p>
                <p class="text-xs mb-4" style="color: #ef4444;">{{ __('web.demo_clear_hint') }}</p>

                <form action="{{ route('admin.demo.clear') }}" method="POST"
                      x-data
                      @submit.prevent="if(confirm('{{ __('web.demo_clear_confirm') }}')) $el.submit()">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 text-sm font-semibold px-5 py-2.5 rounded-xl transition"
                            style="background-color: #dc2626; color: #ffffff; cursor: pointer;"
                            onmouseover="this.style.backgroundColor='#b91c1c'"
                            onmouseout="this.style.backgroundColor='#dc2626'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ __('web.demo_clear_all') }}
                    </button>
                </form>
            </div>
        </div>

    </div>

</x-layouts.admin>
