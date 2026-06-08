<x-layouts.admin :title="__('web.admin_categories')">

    <div class="flex items-center justify-between mb-6 gap-3 flex-wrap">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('web.admin_categories') }}</h1>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.services.index') }}"
               class="border border-gray-200 text-gray-600 text-sm px-4 py-2 rounded-lg hover:border-rose-gold hover:text-rose-gold transition">
                {{ __('web.admin_services') }}
            </a>
            <a href="{{ route('admin.categories.create') }}"
               class="bg-rose-gold text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-rose-gold-dark transition">
                {{ __('web.add_new') }}
            </a>
        </div>
    </div>

    {{-- Desktop table --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-beige text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-start">{{ __('web.name_en') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.name_ar') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.services') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.is_active') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('web.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $cat->name_en }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $cat->name_ar }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $cat->services_count }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $cat->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $cat->is_active ? __('web.active') : __('web.inactive') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.categories.edit', $cat) }}"
                                       class="text-xs text-rose-gold hover:underline">{{ __('web.edit') }}</a>
                                    <form action="{{ route('admin.categories.toggle', $cat) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs {{ $cat->is_active ? 'text-amber-500' : 'text-green-600' }} hover:underline">
                                            {{ $cat->is_active ? __('web.deactivate') : __('web.activate') }}
                                        </button>
                                    </form>
                                    @can('delete', $cat)
                                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST"
                                              onsubmit="return confirm('{{ __('web.confirm_delete') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:underline">{{ __('web.delete') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">{{ __('web.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="px-4 py-3 border-t">{{ $categories->links() }}</div>
        @endif
    </div>

    {{-- Mobile cards --}}
    <div class="md:hidden space-y-3">
        @forelse($categories as $cat)
            <div class="bg-white rounded-2xl shadow-sm p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ $cat->name_en }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $cat->name_ar }}</p>
                    </div>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full shrink-0 ms-2
                        {{ $cat->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $cat->is_active ? __('web.active') : __('web.inactive') }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mb-3">
                    {{ $cat->services_count }} {{ __('web.services') }}
                </p>
                <div class="flex gap-2 flex-wrap pt-2 border-t border-gray-50">
                    <a href="{{ route('admin.categories.edit', $cat) }}"
                       class="text-xs text-amber-600 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-lg transition">{{ __('web.edit') }}</a>
                    <form action="{{ route('admin.categories.toggle', $cat) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="text-xs px-3 py-1.5 rounded-lg transition
                                       {{ $cat->is_active ? 'bg-amber-50 text-amber-500 hover:bg-amber-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                            {{ $cat->is_active ? __('web.deactivate') : __('web.activate') }}
                        </button>
                    </form>
                    @can('delete', $cat)
                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST"
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
        @if($categories->hasPages())
            <div class="bg-white rounded-2xl shadow-sm px-4 py-3">{{ $categories->links() }}</div>
        @endif
    </div>

</x-layouts.admin>
