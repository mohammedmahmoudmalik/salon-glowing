<x-layouts.admin :title="__('web.booking_details')">

    @php
        $statusColors = [
            'pending'   => 'bg-yellow-100 text-yellow-700',
            'confirmed' => 'bg-blue-100 text-blue-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
            'no_show'   => 'bg-gray-100 text-gray-600',
        ];
        $badgeColor = $statusColors[$booking->status->value] ?? 'bg-gray-100 text-gray-600';
        $review = $booking->reviews->first();
        $isRtl = app()->getLocale() === 'ar';
    @endphp

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ request('from_customer') ? route('admin.customers.bookings', request('from_customer')) : route('admin.bookings.index') }}"
               class="w-8 h-8 flex items-center justify-center rounded-lg bg-white shadow-sm text-gray-400 hover:text-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="{{ $isRtl ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7' }}"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">{{ __('web.booking_details') }}</h1>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('web.booking_number') }}{{ $booking->id }}</p>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $badgeColor }}">
            {{ __('web.status_' . $booking->status->value) }}
        </span>
    </div>

    {{-- Main grid --}}
    <div class="grid grid-cols-12 gap-6 items-start">

        {{-- Left: main content (8 cols) --}}
        <div class="col-span-12 lg:col-span-8 space-y-6">

            {{-- Booking Info --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">
                    {{ __('web.booking_details') }}
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">{{ __('web.date') }}</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $booking->booking_date->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">{{ __('web.time') }}</p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ substr($booking->start_time, 0, 5) }}
                            @if($booking->end_time)
                                <span class="text-gray-400 font-normal">→ {{ substr($booking->end_time, 0, 5) }}</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">{{ __('web.total_price') }}</p>
                        <p class="text-sm font-bold text-rose-gold">{{ number_format($booking->total_price, 0) }} {{ currency() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">{{ __('web.created_at') }}</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $booking->created_at->format('Y-m-d') }}</p>
                    </div>
                </div>
                @if($booking->customer?->user)
                    <div class="mt-5 space-y-3">
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                            <span class="text-sm text-gray-500">{{ __('web.customer') }}</span>
                            <span class="text-sm font-medium text-gray-800">{{ $booking->customer->user->name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">{{ __('web.phone') }}</span>
                            <span class="text-sm font-medium text-gray-800">{{ $booking->customer->user->phone ?? '—' }}</span>
                        </div>
                    </div>
                @endif
                @if($booking->notes)
                    <div class="mt-5 pt-5 border-t border-gray-50">
                        <p class="text-xs text-gray-400 mb-1">{{ __('web.notes') }}</p>
                        <p class="text-sm text-gray-700">{{ $booking->notes }}</p>
                    </div>
                @endif
                @if($booking->cancellation_reason)
                    <div class="mt-5 pt-5 border-t border-gray-50">
                        <p class="text-xs text-red-400 mb-1">{{ __('web.cancel') }}</p>
                        <p class="text-sm text-red-600">{{ $booking->cancellation_reason }}</p>
                    </div>
                @endif
            </div>

            {{-- Services Table --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">
                    {{ __('web.services_list') }}
                </p>
                <div class="overflow-x-auto -mx-2">
                    <table class="w-full min-w-95 px-2">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-start pb-3 text-xs text-gray-500 font-medium px-2">{{ __('web.services') }}</th>
                                <th class="text-start pb-3 text-xs text-gray-500 font-medium px-2 hidden sm:table-cell">{{ __('web.category') }}</th>
                                <th class="text-center pb-3 text-xs text-gray-500 font-medium px-2">{{ __('web.total_duration') }}</th>
                                <th class="text-end pb-3 text-xs text-gray-500 font-medium px-2">{{ __('web.price') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->items as $item)
                                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                    <td class="py-3 text-sm font-medium text-gray-800 px-2">
                                        {{ $isRtl ? $item->service?->name_ar : $item->service?->name_en }}
                                        <p class="text-xs text-gray-400 sm:hidden mt-0.5">
                                            {{ $isRtl
                                                ? ($item->service?->category?->name_ar ?? '—')
                                                : ($item->service?->category?->name_en ?? '—') }}
                                        </p>
                                    </td>
                                    <td class="py-3 text-sm text-gray-500 px-2 hidden sm:table-cell">
                                        {{ $isRtl
                                            ? ($item->service?->category?->name_ar ?? '—')
                                            : ($item->service?->category?->name_en ?? '—') }}
                                    </td>
                                    <td class="py-3 text-sm text-center text-gray-600 px-2">
                                        {{ $item->duration_minutes }} {{ __('web.min') }}
                                    </td>
                                    <td class="py-3 text-sm font-semibold text-rose-gold text-end px-2">
                                        {{ number_format($item->price, 0) }} {{ currency() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-100">
                                <td class="pt-4 text-sm text-gray-500 font-medium px-2">{{ __('web.total_price') }}</td>
                                <td class="pt-4 text-sm font-semibold text-center text-gray-700 px-2 hidden sm:table-cell">
                                    {{ $booking->items->sum('duration_minutes') }} {{ __('web.min') }}
                                </td>
                                <td class="pt-4 text-sm font-semibold text-center text-gray-700 px-2 sm:hidden">
                                    {{ $booking->items->sum('duration_minutes') }} {{ __('web.min') }}
                                </td>
                                <td class="pt-4 text-sm font-bold text-rose-gold text-end px-2">
                                    {{ number_format($booking->total_price, 0) }} {{ currency() }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Review --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">
                    {{ __('web.review_info') }}
                </p>
                @if($review)
                    <div class="flex items-start gap-4">
                        <div class="flex text-yellow-400 text-xl shrink-0 leading-none">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $review->rating ? '★' : '☆' }}
                            @endfor
                        </div>
                        <div class="flex-1">
                            @if($review->comment)
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $review->comment }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-2">{{ $review->created_at->format('Y-m-d') }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">{{ __('web.no_review') }}</p>
                @endif
            </div>

        </div>

        {{-- Right: sidebar (4 cols) --}}
        <div class="col-span-12 lg:col-span-4 space-y-6">

            {{-- Actions --}}
            @if(in_array($booking->status->value, ['pending', 'confirmed']))
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">
                        {{ __('web.actions') }}
                    </p>
                    <div class="space-y-2">

                        @if($booking->status->value === 'pending')
                            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit"
                                        class="w-full bg-blue-500 text-white py-2.5 rounded-xl text-sm font-medium hover:bg-blue-600 transition">
                                    ✓ {{ __('web.confirm') }}
                                </button>
                            </form>
                        @endif

                        @if($booking->status->value === 'confirmed')
                            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit"
                                        class="w-full bg-green-500 text-white py-2.5 rounded-xl text-sm font-medium hover:bg-green-600 transition">
                                    ✓ {{ __('web.complete') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="no_show">
                                <button type="submit"
                                        class="w-full bg-gray-100 text-gray-600 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-200 transition">
                                    {{ __('web.no_show') }}
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}"
                              onsubmit="return confirm('{{ __('web.confirm_delete') }}')">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-red-50 text-red-500 py-2.5 rounded-xl text-sm font-medium hover:bg-red-100 transition">
                                ✕ {{ __('web.cancel') }}
                            </button>
                        </form>

                    </div>
                </div>
            @endif

        </div>
    </div>

</x-layouts.admin>

