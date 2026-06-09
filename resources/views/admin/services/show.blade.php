<x-layouts.admin :title="__('web.service_details')">

    @php
        $isRtl = app()->getLocale() === 'ar';
    @endphp

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.services.index') }}"
               onclick="if(window.history.length > 1){ event.preventDefault(); window.history.back(); }"
               class="w-8 h-8 flex items-center justify-center rounded-lg bg-white shadow-sm text-gray-400 hover:text-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="{{ $isRtl ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7' }}"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">{{ __('web.service_details') }}</h1>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $isRtl ? $service->name_ar : $service->name_en }}
                </p>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full text-sm font-medium
            {{ $service->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
            {{ $service->is_active ? __('web.active') : __('web.inactive') }}
        </span>
    </div>

    {{-- Main grid --}}
    <div class="grid grid-cols-12 gap-6 items-start">

        {{-- Left: main content (8 cols) --}}
        <div class="col-span-12 lg:col-span-8 space-y-6">

            {{-- Basic Info --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">
                    {{ __('web.service_details') }}
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">{{ __('web.category') }}</p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $isRtl ? ($service->category?->name_ar ?? '—') : ($service->category?->name_en ?? '—') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">{{ __('web.price') }}</p>
                        <p class="text-sm font-bold text-rose-gold">
                            {{ number_format($service->price, 0) }} {{ currency() }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">{{ __('web.duration') }}</p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $service->duration_minutes }} {{ __('web.min') }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6 pt-5 border-t border-gray-50">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">{{ __('web.name_ar') }}</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $service->name_ar }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">{{ __('web.name_en') }}</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $service->name_en }}</p>
                    </div>
                </div>

                @if($service->description_ar || $service->description_en)
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6 pt-5 border-t border-gray-50">
                        @if($service->description_ar)
                            <div>
                                <p class="text-xs text-gray-400 mb-1">{{ __('web.description_ar') }}</p>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $service->description_ar }}</p>
                            </div>
                        @endif
                        @if($service->description_en)
                            <div>
                                <p class="text-xs text-gray-400 mb-1">{{ __('web.description_en') }}</p>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $service->description_en }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Active Offers --}}
            @if($service->offers->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">
                        {{ __('web.admin_offers') }}
                    </p>
                    <div class="divide-y divide-gray-50">
                        @foreach($service->offers as $offer)
                            <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                                <span class="text-sm text-gray-700 font-medium">
                                    {{ $isRtl ? $offer->name_ar : $offer->name_en }}
                                </span>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                    {{ $offer->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $offer->is_active ? __('web.active') : __('web.inactive') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- Right: sidebar (4 cols) --}}
        <div class="col-span-12 lg:col-span-4 space-y-6">

            {{-- Image --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">
                    {{ __('web.image') }}
                </p>
                @if($service->image_url)
                    <img src="{{ $service->image_url }}"
                         alt="{{ $isRtl ? $service->name_ar : $service->name_en }}"
                         class="w-full rounded-xl object-cover aspect-video">
                @else
                    <div class="w-full rounded-xl bg-gray-50 aspect-video flex items-center justify-center text-gray-300 text-sm">
                        {{ $isRtl ? 'لا توجد صورة' : 'No image' }}
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">
                    {{ __('web.actions') }}
                </p>
                <div class="space-y-2">
                    <a href="{{ route('admin.services.edit', $service) }}"
                       class="block w-full text-center bg-rose-gold text-white py-2.5 rounded-xl text-sm font-medium hover:opacity-90 transition">
                        {{ __('web.edit') }}
                    </a>
                    @can('delete', $service)
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                              onsubmit="return confirm('{{ __('web.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-full bg-red-50 text-red-500 py-2.5 rounded-xl text-sm font-medium hover:bg-red-100 transition">
                                {{ __('web.delete') }}
                            </button>
                        </form>
                    @endcan
                </div>
            </div>

        </div>
    </div>

</x-layouts.admin>

