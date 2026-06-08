<div>
    {{-- بحث --}}
    <div class="mb-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="{{ $locale === 'ar' ? 'ابحث بالاسم أو رقم الهاتف...' : 'Search by name or phone...' }}"
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                   focus:outline-none focus:border-rose-gold focus:ring-1 focus:ring-rose-gold">
    </div>

    {{-- عداد --}}
    <p class="text-sm text-gray-400 mb-3">
        {{ $customers->total() }}
        {{ $locale === 'ar' ? 'عميلة' : 'customers' }}
        @if($search)
            <span class="text-rose-gold">
                ({{ $locale === 'ar' ? 'مفلترة' : 'filtered' }})
            </span>
        @endif
    </p>

    {{-- Desktop table --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-start px-4 py-3 text-xs font-medium text-gray-500">
                            {{ $locale === 'ar' ? 'الاسم' : 'Name' }}
                        </th>
                        <th class="text-start px-4 py-3 text-xs font-medium text-gray-500">
                            {{ $locale === 'ar' ? 'رقم الهاتف' : 'Phone' }}
                        </th>
                        <th class="px-4 py-3 text-xs font-medium text-gray-500 cursor-pointer select-none"
                            wire:click="sort('bookings_count')">
                            <div class="flex items-center gap-1 justify-center">
                                {{ $locale === 'ar' ? 'عدد الحجوزات' : 'Bookings' }}
                                @if($sortBy === 'bookings_count')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-gray-300">↕</span>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3 text-xs font-medium text-gray-500 cursor-pointer select-none"
                            wire:click="sort('last_booking')">
                            <div class="flex items-center gap-1 justify-center">
                                {{ $locale === 'ar' ? 'آخر حجز' : 'Last Booking' }}
                                @if($sortBy === 'last_booking')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-gray-300">↕</span>
                                @endif
                            </div>
                        </th>
                        <th class="text-center px-4 py-3 text-xs font-medium text-gray-500">
                            {{ $locale === 'ar' ? 'إجراءات' : 'Actions' }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-800">{{ $customer->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $customer->user->email ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $customer->user->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-semibold text-gray-800">{{ $customer->bookings_count ?? 0 }}</span>
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600">
                            {{ $customer->bookings_max_booking_date
                                ? \Carbon\Carbon::parse($customer->bookings_max_booking_date)->format('Y/m/d')
                                : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.customers.bookings', $customer) }}"
                               class="text-xs px-3 py-1.5 bg-gray-50 text-gray-600 rounded-lg
                                      hover:bg-rose-50 hover:text-rose-gold transition border border-gray-200">
                                {{ $locale === 'ar' ? 'عرض الحجوزات' : 'View Bookings' }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-gray-400 text-sm">
                            {{ $locale === 'ar' ? 'لا توجد عميلات' : 'No customers found' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $customers->links() }}</div>
        @endif
    </div>

    {{-- Mobile cards --}}
    <div class="md:hidden space-y-3">
        @forelse($customers as $customer)
            <div class="bg-white rounded-2xl shadow-sm p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ $customer->user->name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $customer->user->email ?? '—' }}</p>
                    </div>
                    <span class="text-xs font-bold text-rose-gold bg-rose-50 px-2 py-1 rounded-lg shrink-0 ms-2">
                        {{ $customer->bookings_count ?? 0 }}
                        {{ $locale === 'ar' ? 'حجز' : 'bookings' }}
                    </span>
                </div>
                <div class="text-xs text-gray-500 space-y-1.5 mb-3">
                    @if($customer->user->phone)
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ $locale === 'ar' ? 'الهاتف' : 'Phone' }}</span>
                            <span class="font-medium text-gray-700">{{ $customer->user->phone }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-400">{{ $locale === 'ar' ? 'آخر حجز' : 'Last Booking' }}</span>
                        <span class="font-medium text-gray-700">
                            {{ $customer->bookings_max_booking_date
                                ? \Carbon\Carbon::parse($customer->bookings_max_booking_date)->format('Y/m/d')
                                : '—' }}
                        </span>
                    </div>
                </div>
                <div class="pt-2 border-t border-gray-50">
                    <a href="{{ route('admin.customers.bookings', $customer) }}"
                       class="text-xs px-3 py-1.5 bg-gray-50 text-gray-600 rounded-lg
                              hover:bg-rose-50 hover:text-rose-gold transition border border-gray-200 inline-block">
                        {{ $locale === 'ar' ? 'عرض الحجوزات' : 'View Bookings' }}
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-400 text-sm">
                {{ $locale === 'ar' ? 'لا توجد عميلات' : 'No customers found' }}
            </div>
        @endforelse
        @if($customers->hasPages())
            <div class="bg-white rounded-2xl shadow-sm px-4 py-3">{{ $customers->links() }}</div>
        @endif
    </div>
</div>
