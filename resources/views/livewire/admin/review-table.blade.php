<div>
    {{-- Filters --}}
    <div class="flex gap-3 mb-6">
        <input type="text" wire:model.live.debounce.400ms="search"
               placeholder="{{ __('web.search') }}"
               class="border border-gray-200 rounded-lg px-4 py-2 text-sm flex-1 focus:outline-none focus:border-rose-gold">
        <select wire:model.live="filter"
                class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-rose-gold">
            <option value="">{{ __('web.all_statuses') }}</option>
            <option value="visible">{{ __('web.active') }}</option>
            <option value="hidden">{{ __('web.inactive') }}</option>
        </select>
    </div>

    {{-- Desktop table --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-beige text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-start">{{ __('web.customer') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.service') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.review_rating') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.review_comment') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.status') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-gray-50 transition {{ $review->is_hidden ? 'opacity-50' : '' }}">
                            <td class="px-4 py-3 font-medium">{{ $review->customer?->user?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $review->service
                                   ? (app()->getLocale() === 'ar' ? $review->service->name_ar : $review->service->name_en)
                                   : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $review->comment ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $review->is_hidden ? 'bg-gray-100 text-gray-500' : 'bg-green-50 text-green-700' }}">
                                    {{ $review->is_hidden ? __('web.inactive') : __('web.active') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button wire:click="toggleHide({{ $review->id }})"
                                            class="text-xs px-2 py-1 rounded-lg border border-gray-200 hover:border-rose-gold hover:text-rose-gold transition">
                                        {{ $review->is_hidden ? __('web.show') : __('web.hide') }}
                                    </button>
                                    <button wire:click="delete({{ $review->id }})"
                                            wire:confirm="{{ __('web.confirm_delete') }}"
                                            class="text-xs px-2 py-1 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition">
                                        {{ __('web.delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">{{ __('web.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="px-4 py-3 border-t border-gray-50">{{ $reviews->links() }}</div>
        @endif
    </div>

    {{-- Mobile cards --}}
    <div class="md:hidden space-y-3">
        @forelse($reviews as $review)
            <div class="bg-white rounded-2xl shadow-sm p-4 {{ $review->is_hidden ? 'opacity-60' : '' }}">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 text-sm truncate">{{ $review->customer?->user?->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 truncate">
                            {{ $review->service
                               ? (app()->getLocale() === 'ar' ? $review->service->name_ar : $review->service->name_en)
                               : '—' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <div class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $review->is_hidden ? 'bg-gray-100 text-gray-500' : 'bg-green-50 text-green-700' }}">
                            {{ $review->is_hidden ? __('web.inactive') : __('web.active') }}
                        </span>
                    </div>
                </div>
                @if($review->comment)
                    <p class="text-sm text-gray-600 mb-3 leading-relaxed">{{ $review->comment }}</p>
                @endif
                <div class="flex gap-2 flex-wrap pt-2 border-t border-gray-50">
                    <button wire:click="toggleHide({{ $review->id }})"
                            class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 hover:border-rose-gold hover:text-rose-gold transition">
                        {{ $review->is_hidden ? __('web.show') : __('web.hide') }}
                    </button>
                    <button wire:click="delete({{ $review->id }})"
                            wire:confirm="{{ __('web.confirm_delete') }}"
                            class="text-xs px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition">
                        {{ __('web.delete') }}
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-400 text-sm">{{ __('web.no_data') }}</div>
        @endforelse

        @if($reviews->hasPages())
            <div class="bg-white rounded-2xl shadow-sm px-4 py-3">{{ $reviews->links() }}</div>
        @endif
    </div>
</div>
