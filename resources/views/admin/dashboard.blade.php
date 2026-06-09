<x-layouts.admin :title="__('web.admin_dashboard')">

    <h1 class="text-2xl font-bold text-gray-800 mb-8">{{ __('web.admin_dashboard') }}</h1>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        @php
            $cards = [
                ['label' => __('web.total_bookings'), 'value' => $stats['total_bookings'], 'icon' => '◇', 'color' => 'text-blue-600 bg-blue-50', 'url' => route('admin.bookings.index')],
                ['label' => __('web.today_bookings'), 'value' => $stats['today_bookings'], 'icon' => '◈', 'color' => 'text-rose-gold bg-beige', 'url' => route('admin.bookings.index', ['dateFrom' => today()->toDateString(), 'dateTo' => today()->toDateString()])],
                ['label' => __('web.total_customers'), 'value' => $stats['total_customers'], 'icon' => '◉', 'color' => 'text-orange-600 bg-orange-50', 'url' => route('admin.customers.index')],
                ['label' => __('web.revenue_today'), 'value' => number_format($stats['revenue_today'], 0) . ' ' . currency(), 'icon' => '◆', 'color' => 'text-green-600 bg-green-50', 'url' => null],
                ['label' => __('web.revenue_this_month'), 'value' => number_format($stats['revenue_this_month'], 0) . ' ' . currency(), 'icon' => '❋', 'color' => 'text-purple-600 bg-purple-50', 'url' => null],
            ];
        @endphp

        @foreach($cards as $card)
            @if($card['url'])
                <a href="{{ $card['url'] }}" class="bg-white rounded-2xl p-5 shadow-sm block hover:shadow-md transition-shadow duration-200 cursor-pointer">
            @else
                <div class="bg-white rounded-2xl p-5 shadow-sm">
            @endif
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-medium text-gray-500">{{ $card['label'] }}</span>
                    <span class="w-8 h-8 rounded-lg {{ $card['color'] }} flex items-center justify-center text-sm">
                        {{ $card['icon'] }}
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $card['value'] }}</p>
            @if($card['url'])
                </a>
            @else
                </div>
            @endif
        @endforeach
    </div>

    {{-- Average Rating --}}
    @if($stats['average_rating'])
        <div class="bg-white rounded-2xl p-5 shadow-sm mb-8 inline-flex items-center gap-4">
            <div>
                <p class="text-sm text-gray-500">{{ __('web.average_rating') }}</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['average_rating'] }}</p>
            </div>
            <div class="flex gap-1">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="w-6 h-6 {{ $i <= round($stats['average_rating']) ? 'text-yellow-400' : 'text-gray-200' }}"
                         fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Services --}}
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <h2 class="text-base font-semibold text-gray-700 mb-4">{{ __('web.top_services') }}</h2>
            <div class="space-y-3">
                @foreach($stats['top_services'] as $svc)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">
                            {{ app()->getLocale() === 'ar' ? $svc['name_ar'] : $svc['name_en'] }}
                        </span>
                        <span class="text-sm font-semibold text-rose-gold">{{ $svc['bookings_count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</x-layouts.admin>

