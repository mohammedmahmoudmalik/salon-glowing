<div>
    {{-- Validation errors --}}
    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl space-y-1">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="space-y-4">
        @foreach($categories as $catIndex => $cat)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                {{-- Category header --}}
                <div class="px-5 py-4 bg-beige flex items-center gap-3">
                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                                {{ __('web.setup_category_name_ar') }}
                            </label>
                            <input type="text"
                                   wire:model="categories.{{ $catIndex }}.name_ar"
                                   placeholder="{{ __('web.setup_category_name_ar') }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition bg-white">
                            @error("categories.{$catIndex}.name_ar")
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                                {{ __('web.setup_category_name_en') }}
                            </label>
                            <input type="text"
                                   wire:model="categories.{{ $catIndex }}.name_en"
                                   placeholder="{{ __('web.setup_category_name_en') }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition bg-white">
                            @error("categories.{$catIndex}.name_en")
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <button type="button"
                            wire:click="removeCategory({{ $catIndex }})"
                            class="text-red-400 hover:text-red-600 transition shrink-0 p-1 rounded-lg hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Services list --}}
                <div class="divide-y divide-gray-50">
                    @foreach($cat['services'] as $svcIndex => $svc)
                        <div class="px-5 py-3 flex items-start gap-3">
                            <div class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div>
                                    <input type="text"
                                           wire:model="categories.{{ $catIndex }}.services.{{ $svcIndex }}.name_ar"
                                           placeholder="{{ __('web.setup_service_name_ar') }}"
                                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                                    @error("categories.{$catIndex}.services.{$svcIndex}.name_ar")
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <input type="text"
                                           wire:model="categories.{{ $catIndex }}.services.{{ $svcIndex }}.name_en"
                                           placeholder="{{ __('web.setup_service_name_en') }}"
                                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                                    @error("categories.{$catIndex}.services.{$svcIndex}.name_en")
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <input type="number"
                                           wire:model="categories.{{ $catIndex }}.services.{{ $svcIndex }}.price"
                                           placeholder="{{ __('web.setup_price') }} ({{ currency() }})"
                                           min="0" step="0.01"
                                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                                    @error("categories.{$catIndex}.services.{$svcIndex}.price")
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <input type="number"
                                           wire:model="categories.{{ $catIndex }}.services.{{ $svcIndex }}.duration_minutes"
                                           placeholder="{{ __('web.setup_duration') }}"
                                           min="5" step="5"
                                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                                    @error("categories.{$catIndex}.services.{$svcIndex}.duration_minutes")
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <button type="button"
                                    wire:click="removeService({{ $catIndex }}, {{ $svcIndex }})"
                                    class="text-gray-300 hover:text-red-400 transition shrink-0 mt-2 p-1 rounded hover:bg-red-50">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                {{-- Add service button --}}
                <div class="px-5 py-3 border-t border-gray-50">
                    <button type="button"
                            wire:click="addService({{ $catIndex }})"
                            class="text-xs text-rose-gold hover:text-rose-gold-dark font-medium flex items-center gap-1 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('web.setup_add_service') }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Bottom actions --}}
    <div class="mt-5 flex items-center gap-3 flex-wrap">
        <button type="button"
                wire:click="addCategory"
                class="border border-rose-gold text-rose-gold text-sm font-medium px-4 py-2 rounded-xl hover:bg-beige transition">
            {{ __('web.setup_add_category') }}
        </button>
        <button type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                class="bg-rose-gold text-white text-sm font-semibold px-6 py-2 rounded-xl hover:bg-rose-gold-dark transition disabled:opacity-60">
            <span wire:loading.remove wire:target="save">{{ __('web.setup_save_all') }}</span>
            <span wire:loading wire:target="save">...</span>
        </button>
    </div>
</div>
