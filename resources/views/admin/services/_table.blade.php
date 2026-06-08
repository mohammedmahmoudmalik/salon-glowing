@php
    $isAr = app()->getLocale() === 'ar';
    $hasFilter = request('search') || request('category');
@endphp

{{-- Result count --}}
<p class="text-sm text-gray-400 mb-3">
    {{ $services->total() }}
    {{ $isAr ? 'خدمة' : 'services' }}
    @if($hasFilter)
        <span class="text-rose-gold">
            ({{ $isAr ? 'مفلترة' : 'filtered' }})
        </span>
    @endif
</p>

{{-- Desktop table --}}
<div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-beige text-gray-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-start">{{ $isAr ? __('web.name_ar') : __('web.name_en') }}</th>
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
                            {{ $isAr ? $service->name_ar : $service->name_en }}
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $isAr ? ($service->category?->name_ar ?? '—') : ($service->category?->name_en ?? '—') }}
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
                            @if($hasFilter)
                                <p class="text-base mb-2">
                                    {{ $isAr ? 'لا توجد نتائج تطابق الفلتر المحدد' : 'No results match the selected filter' }}
                                </p>
                                <a href="{{ route('admin.services.index') }}"
                                   class="text-rose-gold text-sm hover:underline">
                                    {{ $isAr ? 'عرض كل الخدمات' : 'Show all services' }}
                                </a>
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
                        {{ $isAr ? $service->name_ar : $service->name_en }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $isAr ? ($service->category?->name_ar ?? '—') : ($service->category?->name_en ?? '—') }}
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
            @if($hasFilter)
                <p class="mb-2">{{ $isAr ? 'لا توجد نتائج تطابق الفلتر المحدد' : 'No results match the selected filter' }}</p>
                <a href="{{ route('admin.services.index') }}"
                   class="text-rose-gold text-sm hover:underline">
                    {{ $isAr ? 'عرض كل الخدمات' : 'Show all services' }}
                </a>
            @else
                {{ __('web.no_data') }}
            @endif
        </div>
    @endforelse
    @if($services->hasPages())
        <div class="bg-white rounded-2xl shadow-sm px-4 py-3">{{ $services->links() }}</div>
    @endif
</div>
