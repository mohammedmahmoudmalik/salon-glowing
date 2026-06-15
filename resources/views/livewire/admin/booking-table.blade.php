<div>
    {{-- فلاتر البحث --}}
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-4">

        {{-- الصف الأول: بحث + تواريخ --}}
        <div class="flex flex-wrap gap-3 mb-3">

            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ $locale === 'ar' ? 'ابحث باسم العميلة أو الجوال...' : 'Search by name or phone...' }}"
                class="flex-1 min-w-52 border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                       focus:outline-none focus:border-rose-gold focus:ring-1 focus:ring-rose-gold">

            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-400 px-1">{{ $locale === 'ar' ? 'من' : 'From' }}</label>
                <input type="date" wire:model.live="dateFrom"
                       class="border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600
                              focus:outline-none focus:border-rose-gold">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-400 px-1">{{ $locale === 'ar' ? 'إلى' : 'To' }}</label>
                <input type="date" wire:model.live="dateTo"
                       class="border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600
                              focus:outline-none focus:border-rose-gold">
            </div>

        </div>

        {{-- الصف الثاني: أزرار الحالة --}}
        <div class="flex flex-wrap gap-2">
            <span class="text-xs text-gray-400 self-center ml-1">
                {{ $locale === 'ar' ? 'الحالة:' : 'Status:' }}
            </span>
            @php
                $statusButtons = [
                    ''          => ['label' => $locale === 'ar' ? 'الكل'         : 'All',       'color' => '#1f2937'],
                    'pending'   => ['label' => $locale === 'ar' ? 'قيد الانتظار' : 'Pending',   'color' => '#f59e0b'],
                    'confirmed' => ['label' => $locale === 'ar' ? 'مؤكد'         : 'Confirmed', 'color' => '#3b82f6'],
                    'completed' => ['label' => $locale === 'ar' ? 'مكتمل'        : 'Completed', 'color' => '#22c55e'],
                    'cancelled' => ['label' => $locale === 'ar' ? 'ملغى'         : 'Cancelled', 'color' => '#f87171'],
                    'no_show'   => ['label' => $locale === 'ar' ? 'لم تحضر'      : 'No Show',   'color' => '#9ca3af'],
                ];
            @endphp
            @foreach($statusButtons as $value => $btn)
                <button
                    wire:click="filterStatus('{{ $value }}')"
                    @if($status === $value)
                        style="background:{{ $btn['color'] }}; color:#fff;"
                    @else
                        style="background:#f3f4f6; color:#6b7280;"
                    @endif
                    class="px-3 py-1.5 rounded-full text-xs font-medium transition hover:opacity-90">
                    {{ $btn['label'] }}
                </button>
            @endforeach
        </div>

        {{-- عداد النتائج --}}
        <p class="text-xs text-gray-400 mt-3">
            {{ $bookings->total() }} {{ __('web.admin_bookings') }}
            @if($search || $status || $dateFrom || $dateTo)
                <span class="text-rose-gold font-medium">({{ __('web.filtered') }})</span>
            @endif
        </p>
    </div>

    @php
        $statusColors = [
            'pending'   => 'bg-yellow-100 text-yellow-700',
            'confirmed' => 'bg-blue-100 text-blue-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-500',
            'no_show'   => 'bg-gray-100 text-gray-600',
        ];
        $statusLabels = [
            'pending'   => $locale === 'ar' ? 'قيد الانتظار' : 'Pending',
            'confirmed' => $locale === 'ar' ? 'مؤكد'          : 'Confirmed',
            'completed' => $locale === 'ar' ? 'مكتمل'         : 'Completed',
            'cancelled' => $locale === 'ar' ? 'ملغى'          : 'Cancelled',
            'no_show'   => $locale === 'ar' ? 'لم تحضر'      : 'No Show',
        ];
    @endphp

    {{-- الجدول (Desktop) --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-beige text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-start">#</th>
                        <th class="px-4 py-3 text-start">{{ __('web.customer') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.service') }}</th>
                        <th class="px-4 py-3 text-start cursor-pointer select-none hover:text-rose-gold transition"
                            wire:click="sort('booking_date')">
                            <span class="flex items-center gap-1">
                                {{ __('web.date') }}
                                @if($sortBy === 'booking_date')
                                    <span class="text-rose-gold">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-gray-300">↕</span>
                                @endif
                            </span>
                        </th>
                        <th class="px-4 py-3 text-start">{{ __('web.time') }}</th>
                        <th class="px-4 py-3 text-start cursor-pointer select-none hover:text-rose-gold transition"
                            wire:click="sort('total_price')">
                            <span class="flex items-center gap-1">
                                {{ __('web.total_price') }}
                                @if($sortBy === 'total_price')
                                    <span class="text-rose-gold">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-gray-300">↕</span>
                                @endif
                            </span>
                        </th>
                        <th class="px-4 py-3 text-start">{{ __('web.status') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($bookings as $booking)
                        @php $statusKey = $booking->status->value; @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-400">{{ $booking->id }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $booking->customer?->user?->name ?? '—' }}</p>
                                @if($booking->customer?->user?->phone)
                                    <p class="text-xs text-gray-400">{{ $booking->customer->user->phone }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $booking->items->first()?->service
                                   ? ($locale === 'ar' ? $booking->items->first()->service->name_ar : $booking->items->first()->service->name_en)
                                   : '—' }}
                                @if($booking->items->count() > 1)
                                    <span class="text-xs text-gray-400">+{{ $booking->items->count() - 1 }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ substr($booking->start_time, 0, 5) }}</td>
                            <td class="px-4 py-3 font-medium text-rose-gold">
                                {{ number_format($booking->total_price, 0) }} {{ currency() }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full font-medium {{ $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabels[$statusKey] ?? $statusKey }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('admin.bookings.show', $booking) }}"
                                       class="text-xs px-2 py-1 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition">
                                        {{ __('web.view_details') }}
                                    </a>
                                    @if($booking->status->value === 'pending')
                                        <button wire:click="changeStatus({{ $booking->id }}, 'confirmed')"
                                                class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-2 py-1 rounded-lg transition">
                                            {{ __('web.confirm') }}
                                        </button>
                                    @endif
                                    @if(in_array($booking->status->value, ['pending', 'confirmed']))
                                        <button wire:click="changeStatus({{ $booking->id }}, 'cancelled')"
                                                wire:confirm="{{ $locale === 'ar' ? 'هل تريد إلغاء الحجز؟' : 'Cancel this booking?' }}"
                                                class="text-xs bg-red-50 text-red-500 hover:bg-red-100 px-2 py-1 rounded-lg transition">
                                            {{ __('web.cancel') }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-400">{{ __('web.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())
            <div class="px-4 py-3 border-t border-gray-50">{{ $bookings->links() }}</div>
        @endif
    </div>

    {{-- الجدول (Mobile cards) --}}
    <div class="md:hidden space-y-3">
        @forelse($bookings as $booking)
            @php $statusKey = $booking->status->value; @endphp
            <div class="bg-white rounded-2xl shadow-sm p-4">
                {{-- Header: customer + status --}}
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 text-sm truncate">
                            {{ $booking->customer?->user?->name ?? '—' }}
                        </p>
                        @if($booking->customer?->user?->phone)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $booking->customer->user->phone }}</p>
                        @endif
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full font-medium shrink-0 {{ $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $statusLabels[$statusKey] ?? $statusKey }}
                    </span>
                </div>

                {{-- Details --}}
                <div class="text-xs text-gray-500 space-y-1.5 mb-3">
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-400 shrink-0">{{ __('web.service') }}</span>
                        <span class="font-medium text-gray-700 text-end">
                            {{ $booking->items->first()?->service
                               ? ($locale === 'ar' ? $booking->items->first()->service->name_ar : $booking->items->first()->service->name_en)
                               : '—' }}
                            @if($booking->items->count() > 1)
                                <span class="text-gray-400">+{{ $booking->items->count() - 1 }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">{{ __('web.date') }}</span>
                        <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">{{ __('web.time') }}</span>
                        <span class="font-medium text-gray-700">{{ substr($booking->start_time, 0, 5) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">{{ __('web.total_price') }}</span>
                        <span class="font-bold text-rose-gold">{{ number_format($booking->total_price, 0) }} {{ currency() }}</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 flex-wrap pt-2 border-t border-gray-50">
                    <a href="{{ route('admin.bookings.show', $booking) }}"
                       class="text-xs px-3 py-1.5 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition">
                        {{ __('web.view_details') }}
                    </a>
                    @if($booking->status->value === 'pending')
                        <button wire:click="changeStatus({{ $booking->id }}, 'confirmed')"
                                class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                            {{ __('web.confirm') }}
                        </button>
                    @endif
                    @if(in_array($booking->status->value, ['pending', 'confirmed']))
                        <button wire:click="changeStatus({{ $booking->id }}, 'cancelled')"
                                wire:confirm="{{ $locale === 'ar' ? 'هل تريد إلغاء الحجز؟' : 'Cancel this booking?' }}"
                                class="text-xs bg-red-50 text-red-500 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                            {{ __('web.cancel') }}
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-400 text-sm">{{ __('web.no_data') }}</div>
        @endforelse
        @if($bookings->hasPages())
            <div class="bg-white rounded-2xl shadow-sm px-4 py-3">{{ $bookings->links() }}</div>
        @endif
    </div>
</div>
