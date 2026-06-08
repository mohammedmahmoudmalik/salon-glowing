<x-layouts.admin :title="__('web.admin_reviews')">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ __('web.admin_reviews') }}</h1>

    {{-- Desktop table --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-beige text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-start">{{ __('web.customer') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.service') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.average_rating') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.is_active') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.created_at') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $review->customer?->user?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ app()->getLocale() === 'ar' ? $review->service?->name_ar : $review->service?->name_en }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-yellow-500 font-medium">★ {{ $review->rating }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $review->is_hidden ? 'bg-gray-100 text-gray-500' : 'bg-green-50 text-green-700' }}">
                                    {{ $review->is_hidden ? __('web.inactive') : __('web.active') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $review->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @can('update', $review)
                                        <form action="{{ route('admin.reviews.hide', $review) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="text-xs {{ $review->is_hidden ? 'text-green-600 hover:underline' : 'text-gray-500 hover:underline' }}">
                                                {{ $review->is_hidden ? __('web.show') : __('web.hide') }}
                                            </button>
                                        </form>
                                    @endcan
                                    @can('delete', $review)
                                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                              onsubmit="return confirm('{{ __('web.confirm_delete') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:underline">{{ __('web.delete') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">{{ __('web.no_data') }}</td>
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
            <div class="bg-white rounded-2xl shadow-sm p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ $review->customer?->user?->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ app()->getLocale() === 'ar' ? $review->service?->name_ar : $review->service?->name_en }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 ms-2">
                        <span class="text-yellow-500 font-bold text-sm">★ {{ $review->rating }}</span>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $review->is_hidden ? 'bg-gray-100 text-gray-500' : 'bg-green-50 text-green-700' }}">
                            {{ $review->is_hidden ? __('web.inactive') : __('web.active') }}
                        </span>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mb-3">{{ $review->created_at->format('Y-m-d') }}</p>
                <div class="flex gap-2 flex-wrap pt-2 border-t border-gray-50">
                    @can('update', $review)
                        <form action="{{ route('admin.reviews.hide', $review) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button class="text-xs px-3 py-1.5 rounded-lg transition
                                {{ $review->is_hidden ? 'bg-green-50 text-green-600 hover:bg-green-100' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                                {{ $review->is_hidden ? __('web.show') : __('web.hide') }}
                            </button>
                        </form>
                    @endcan
                    @can('delete', $review)
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
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
        @if($reviews->hasPages())
            <div class="bg-white rounded-2xl shadow-sm px-4 py-3">{{ $reviews->links() }}</div>
        @endif
    </div>

</x-layouts.admin>
