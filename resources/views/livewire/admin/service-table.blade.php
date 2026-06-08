<div>
    {{-- Filters --}}
    <div class="flex gap-2 flex-wrap mb-4">

        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="{{ $locale === 'ar' ? 'ابحث باسم الخدمة...' : 'Search services...' }}"
            class="flex-1 min-w-48 border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                   focus:outline-none focus:border-rose-gold focus:ring-1 focus:ring-rose-gold">

        <select
            wire:model.live="category"
            class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-600
                   focus:outline-none focus:border-rose-gold focus:ring-1 focus:ring-rose-gold">
            <option value="">
                {{ $locale === 'ar' ? 'كل الفئات' : 'All Categories' }}
            </option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">
                    {{ $locale === 'ar' ? $cat->name_ar : $cat->name_en }}
                </option>
            @endforeach
        </select>


    </div>

    {{-- Result count --}}
    <p class="text-sm text-gray-400 mb-3">
        {{ $services->total() }}
        {{ $locale === 'ar' ? 'خدمة' : 'services' }}
        @if($search || $category)
            <span class="text-rose-gold">
                ({{ $locale === 'ar' ? 'مفلترة' : 'filtered' }})
            </span>
        @endif
    </p>

    {{-- Desktop table --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-beige text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-start">{{ $locale === 'ar' ? __('web.name_ar') : __('web.name_en') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.category') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.price') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.duration') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.is_active') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($services as $service)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">
                                {{ $locale === 'ar' ? $service->name_ar : $service->name_en }}
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $locale === 'ar' ? ($service->category?->name_ar ?? '—') : ($service->category?->name_en ?? '—') }}
                            </td>
                            <td class="px-4 py-3">{{ number_format($service->price, 0) }} {{ currency() }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $service->duration_minutes }} {{ __('web.min') }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                    {{ $service->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $service->is_active ? __('web.active') : __('web.inactive') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.services.show', $service) }}"
                                       class="text-xs text-blue-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg transition">{{ __('web.view_details') }}</a>
                                    <a href="{{ route('admin.services.edit', $service) }}"
                                       class="text-xs text-amber-600 bg-amber-50 hover:bg-amber-100 px-2.5 py-1 rounded-lg transition">{{ __('web.edit') }}</a>
                                    @can('delete', $service)
                                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                                              onsubmit="return confirm('{{ __('web.confirm_delete') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="text-xs text-red-500 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded-lg transition">{{ __('web.delete') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                                @if($search || $category)
                                    <p class="text-base mb-2">
                                        {{ $locale === 'ar' ? 'لا توجد نتائج تطابق الفلتر المحدد' : 'No results match the selected filter' }}
                                    </p>
                                    <button wire:click="$set('search', ''); $set('category', '')"
                                            class="text-rose-gold text-sm hover:underline">
                                        {{ $locale === 'ar' ? 'عرض كل الخدمات' : 'Show all services' }}
                                    </button>
                                @else
                                    {{ __('web.no_data') }}
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($services->hasPages())
            <div class="px-4 py-3 border-t border-gray-50">{{ $services->links() }}</div>
        @endif
    </div>

    {{-- Mobile cards --}}
    <div class="md:hidden space-y-3">
        @forelse($services as $service)
            <div class="bg-white rounded-2xl shadow-sm p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">
                            {{ $locale === 'ar' ? $service->name_ar : $service->name_en }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $locale === 'ar' ? ($service->category?->name_ar ?? '—') : ($service->category?->name_en ?? '—') }}
                        </p>
                    </div>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full shrink-0 ms-2
                        {{ $service->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $service->is_active ? __('web.active') : __('web.inactive') }}
                    </span>
                </div>
                <div class="text-xs text-gray-500 space-y-1.5 mb-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">{{ __('web.price') }}</span>
                        <span class="font-bold text-rose-gold">{{ number_format($service->price, 0) }} {{ currency() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">{{ __('web.duration') }}</span>
                        <span class="font-medium text-gray-700">{{ $service->duration_minutes }} {{ __('web.min') }}</span>
                    </div>
                </div>
                <div class="flex gap-2 flex-wrap pt-2 border-t border-gray-50">
                    <a href="{{ route('admin.services.show', $service) }}"
                       class="text-xs text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">{{ __('web.view_details') }}</a>
                    <a href="{{ route('admin.services.edit', $service) }}"
                       class="text-xs text-amber-600 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-lg transition">{{ __('web.edit') }}</a>
                    @can('delete', $service)
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                              onsubmit="return confirm('{{ __('web.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs text-red-500 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">{{ __('web.delete') }}</button>
                        </form>
                    @endcan
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-400 text-sm">
                @if($search || $category)
                    <p class="mb-2">{{ $locale === 'ar' ? 'لا توجد نتائج تطابق الفلتر المحدد' : 'No results match the selected filter' }}</p>
                    <button wire:click="$set('search', ''); $set('category', '')"
                            class="text-rose-gold text-sm hover:underline">
                        {{ $locale === 'ar' ? 'عرض كل الخدمات' : 'Show all services' }}
                    </button>
                @else
                    {{ __('web.no_data') }}
                @endif
            </div>
        @endforelse
        @if($services->hasPages())
            <div class="bg-white rounded-2xl shadow-sm px-4 py-3">{{ $services->links() }}</div>
        @endif
    </div>
</div>

