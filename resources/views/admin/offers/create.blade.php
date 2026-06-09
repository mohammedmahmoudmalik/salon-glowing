<x-layouts.admin :title="__('web.add_new')">
    <div class="max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.offers.index') }}" class="text-gray-400 hover:text-rose-gold">← {{ __('web.back') }}</a>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('web.admin_offers') }} — {{ __('web.add_new') }}</h1>
        </div>
        <form action="{{ route('admin.offers.store') }}" method="POST" enctype="multipart/form-data"
              class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
            @csrf
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                    @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('web.name_ar') }} *</label>
                    <input type="text" name="title_ar" value="{{ old('title_ar') }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-gold">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('web.name_en') }} *</label>
                    <input type="text" name="title_en" value="{{ old('title_en') }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-gold">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('web.discount_type') }} *</label>
                    <select name="discount_type" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-gold">
                        <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>{{ __('web.percentage') }}</option>
                        <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>{{ __('web.fixed') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('web.discount_value') }} *</label>
                    <input type="number" name="discount_value" value="{{ old('discount_value') }}" step="0.01" min="0" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-gold">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('web.starts_at') }}</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-gold">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('web.ends_at') }}</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-rose-gold">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('web.services') }}</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto p-2 border border-gray-200 rounded-xl">
                    @foreach($services as $svc)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" name="service_ids[]" value="{{ $svc->id }}"
                                   {{ in_array($svc->id, old('service_ids', [])) ? 'checked' : '' }}
                                   class="rounded text-rose-gold border-gray-300">
                            {{ app()->getLocale() === 'ar' ? $svc->name_ar : $svc->name_en }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('web.image') }}</label>
                <input type="file" name="image" accept="image/*" class="text-sm text-gray-500">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" checked class="rounded text-rose-gold">
                <label class="text-sm text-gray-700">{{ __('web.is_active') }}</label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-rose-gold text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-rose-gold-dark transition">
                    {{ __('web.save') }}
                </button>
                <a href="{{ route('admin.offers.index') }}" class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-xl hover:border-rose-gold hover:text-rose-gold transition">
                    {{ __('web.back') }}
                </a>
            </div>
        </form>
    </div>
</x-layouts.admin>
