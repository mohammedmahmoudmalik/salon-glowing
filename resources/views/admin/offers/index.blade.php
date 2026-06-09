<x-layouts.admin :title="__('web.admin_offers')">

    <div class="flex items-center justify-between mb-6 gap-3 flex-wrap">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('web.admin_offers') }}</h1>
        <a href="{{ route('admin.offers.create') }}"
           class="bg-rose-gold text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-rose-gold-dark transition shrink-0">
            {{ __('web.add_new') }}
        </a>
    </div>

    {{-- Desktop table --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-beige text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-start">{{ __('web.title') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.discount_type') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.discount_value') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.starts_at') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.ends_at') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.is_active') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($offers as $offer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ app()->getLocale() === 'ar' ? $offer->title_ar : $offer->title_en }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $offer->discount_type?->label() }}</td>
                            <td class="px-4 py-3">
                                @if($offer->discount_type?->value === 'percentage')
                                    {{ (float) $offer->discount_value }}%
                                @else
                                    {{ (float) $offer->discount_value }} {{ currency() }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $offer->starts_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $offer->ends_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $offer->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $offer->is_active ? __('web.active') : __('web.inactive') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.offers.edit', $offer) }}"
                                       class="text-xs text-rose-gold hover:underline">{{ __('web.edit') }}</a>
                                    @can('delete', $offer)
                                        <form action="{{ route('admin.offers.destroy', $offer) }}" method="POST"
                                              onsubmit="return confirm('{{ __('web.confirm_delete') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:underline">{{ __('web.delete') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">{{ __('web.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($offers->hasPages())
            <div class="px-4 py-3 border-t">{{ $offers->links() }}</div>
        @endif
    </div>

    {{-- Mobile cards --}}
    <div class="md:hidden space-y-3">
        @forelse($offers as $offer)
            <div class="bg-white rounded-2xl shadow-sm p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ app()->getLocale() === 'ar' ? $offer->title_ar : $offer->title_en }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ app()->getLocale() === 'ar' ? $offer->title_en : $offer->title_ar }}</p>
                    </div>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full shrink-0 ms-2
                        {{ $offer->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $offer->is_active ? __('web.active') : __('web.inactive') }}
                    </span>
                </div>
                <div class="text-xs text-gray-500 space-y-1.5 mb-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">{{ __('web.discount_value') }}</span>
                        <span class="font-bold text-rose-gold">
                            {{ $offer->discount_value }}
                            {{ $offer->discount_type?->value === 'percentage' ? '%' : currency() }}
                        </span>
                    </div>
                    @if($offer->starts_at || $offer->ends_at)
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ __('web.starts_at') }}</span>
                            <span class="font-medium text-gray-700">{{ $offer->starts_at?->format('Y-m-d') ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ __('web.ends_at') }}</span>
                            <span class="font-medium text-gray-700">{{ $offer->ends_at?->format('Y-m-d') ?? '—' }}</span>
                        </div>
                    @endif
                </div>
                <div class="flex gap-2 flex-wrap pt-2 border-t border-gray-50">
                    <a href="{{ route('admin.offers.edit', $offer) }}"
                       class="text-xs text-amber-600 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-lg transition">{{ __('web.edit') }}</a>
                    @can('delete', $offer)
                        <form action="{{ route('admin.offers.destroy', $offer) }}" method="POST"
                              onsubmit="return confirm('{{ __('web.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs bg-red-50 text-red-500 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">{{ __('web.delete') }}</button>
                        </form>
                    @endcan
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-400 text-sm">{{ __('web.no_data') }}</div>
        @endforelse
        @if($offers->hasPages())
            <div class="bg-white rounded-2xl shadow-sm px-4 py-3">{{ $offers->links() }}</div>
        @endif
    </div>

</x-layouts.admin>

