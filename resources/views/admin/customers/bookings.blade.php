<x-layouts.admin :title="$customer->user->name">
    <div class="flex items-center gap-3 mb-6 flex-wrap">
        <a href="{{ route('admin.customers.index') }}"
           class="text-gray-400 hover:text-rose-gold text-sm shrink-0">
            ← {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
            {{ $customer->user->name }}
        </h1>
        @if($customer->user->phone)
            <span class="text-sm text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                {{ $customer->user->phone }}
            </span>
        @endif
    </div>

    {{-- إحصائية سريعة --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">
                {{ $bookings->total() }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                {{ app()->getLocale() === 'ar' ? 'إجمالي الحجوزات' : 'Total Bookings' }}
            </p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-rose-gold">
                {{ number_format($bookings->sum('total_price'), 0) }}
                {{ currency() }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                {{ app()->getLocale() === 'ar' ? 'إجمالي المبالغ' : 'Total Amount' }}
            </p>
        </div>
    </div>

    {{-- Desktop table --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-right px-4 py-3 text-xs font-medium text-gray-500">
                        {{ app()->getLocale() === 'ar' ? 'الخدمات' : 'Services' }}
                    </th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-gray-500">
                        {{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}
                    </th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-gray-500">
                        {{ app()->getLocale() === 'ar' ? 'الوقت' : 'Time' }}
                    </th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-gray-500">
                        {{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}
                    </th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-gray-500">
                        {{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}
                    </th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-gray-500">
                        {{ app()->getLocale() === 'ar' ? 'تفاصيل' : 'Details' }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bookings as $booking)
                @php
                    $statusColors = [
                        'pending'   => 'bg-yellow-100 text-yellow-700',
                        'confirmed' => 'bg-blue-100 text-blue-700',
                        'completed' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-500',
                        'no_show'   => 'bg-gray-100 text-gray-600',
                    ];
                    $statusLabels = [
                        'pending'   => app()->getLocale() === 'ar' ? 'قيد الانتظار' : 'Pending',
                        'confirmed' => app()->getLocale() === 'ar' ? 'مؤكد'         : 'Confirmed',
                        'completed' => app()->getLocale() === 'ar' ? 'مكتمل'        : 'Completed',
                        'cancelled' => app()->getLocale() === 'ar' ? 'ملغى'         : 'Cancelled',
                        'no_show'   => app()->getLocale() === 'ar' ? 'لم تحضر'     : 'No Show',
                    ];
                    $status = $booking->status->value;
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-sm text-gray-700">
                        @foreach($booking->items as $item)
                            <p>{{ app()->getLocale() === 'ar'
                                ? $item->service->name_ar
                                : $item->service->name_en }}
                            </p>
                        @endforeach
                    </td>
                    <td class="px-4 py-3 text-sm text-center text-gray-600">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') }}
                    </td>
                    <td class="px-4 py-3 text-sm text-center text-gray-600">
                        {{ substr($booking->start_time, 0, 5) }}
                    </td>
                    <td class="px-4 py-3 text-sm text-center font-semibold text-rose-gold">
                        {{ number_format($booking->total_price, 0) }}
                        {{ currency() }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $statusLabels[$status] ?? $status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.bookings.show', $booking) }}?from_customer={{ $customer->id }}"
                           class="text-xs text-blue-500 hover:text-blue-700">
                            {{ app()->getLocale() === 'ar' ? 'تفاصيل' : 'Details' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                        {{ app()->getLocale() === 'ar' ? 'لا توجد حجوزات' : 'No bookings found' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($bookings->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>

    {{-- Mobile cards --}}
    <div class="md:hidden space-y-3">
        @forelse($bookings as $booking)
            @php
                $statusColors = [
                    'pending'   => 'bg-yellow-100 text-yellow-700',
                    'confirmed' => 'bg-blue-100 text-blue-700',
                    'completed' => 'bg-green-100 text-green-700',
                    'cancelled' => 'bg-red-100 text-red-500',
                    'no_show'   => 'bg-gray-100 text-gray-600',
                ];
                $statusLabels = [
                    'pending'   => app()->getLocale() === 'ar' ? 'قيد الانتظار' : 'Pending',
                    'confirmed' => app()->getLocale() === 'ar' ? 'مؤكد'         : 'Confirmed',
                    'completed' => app()->getLocale() === 'ar' ? 'مكتمل'        : 'Completed',
                    'cancelled' => app()->getLocale() === 'ar' ? 'ملغى'         : 'Cancelled',
                    'no_show'   => app()->getLocale() === 'ar' ? 'لم تحضر'     : 'No Show',
                ];
                $status = $booking->status->value;
            @endphp
            <div class="bg-white rounded-2xl shadow-sm p-4">
                {{-- Header: services + status --}}
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="text-sm text-gray-700 min-w-0">
                        @foreach($booking->items as $item)
                            <p class="font-medium truncate">{{ app()->getLocale() === 'ar' ? $item->service->name_ar : $item->service->name_en }}</p>
                        @endforeach
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full font-medium shrink-0 {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $statusLabels[$status] ?? $status }}
                    </span>
                </div>

                {{-- Details --}}
                <div class="text-xs text-gray-500 space-y-1.5 mb-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</span>
                        <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">{{ app()->getLocale() === 'ar' ? 'الوقت' : 'Time' }}</span>
                        <span class="font-medium text-gray-700">{{ substr($booking->start_time, 0, 5) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</span>
                        <span class="font-bold text-rose-gold">{{ number_format($booking->total_price, 0) }} {{ currency() }}</span>
                    </div>
                </div>

                {{-- Action --}}
                <div class="pt-2 border-t border-gray-50">
                    <a href="{{ route('admin.bookings.show', $booking) }}?from_customer={{ $customer->id }}"
                       class="text-xs px-3 py-1.5 bg-gray-50 text-gray-600 rounded-lg hover:bg-rose-50 hover:text-rose-gold transition border border-gray-200 inline-block">
                        {{ app()->getLocale() === 'ar' ? 'تفاصيل' : 'Details' }}
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-400 text-sm">
                {{ app()->getLocale() === 'ar' ? 'لا توجد حجوزات' : 'No bookings found' }}
            </div>
        @endforelse
        @if($bookings->hasPages())
            <div class="bg-white rounded-2xl shadow-sm px-4 py-3">{{ $bookings->links() }}</div>
        @endif
    </div>
</x-layouts.admin>

