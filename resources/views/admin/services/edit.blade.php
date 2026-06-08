<x-layouts.admin :title="__('web.edit')">

    {{-- Page header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.services.index') }}"
           onclick="if(window.history.length > 1){ event.preventDefault(); window.history.back(); }"
           class="text-gray-400 hover:text-rose-gold">← {{ __('web.back') }}</a>
        <h1 class="text-2xl font-bold text-gray-800">{{ __('web.admin_services') }} — {{ __('web.edit') }}</h1>
    </div>

    <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-5">
                @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- ── Left: form sections (2/3) ── --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Names --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">{{ __('web.name_ar') }} / {{ __('web.name_en') }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ __('web.name_ar') }} <span class="text-rose-gold">*</span>
                            </label>
                            <input type="text" name="name_ar" value="{{ old('name_ar', $service->name_ar) }}" required dir="rtl"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ __('web.name_en') }} <span class="text-rose-gold">*</span>
                            </label>
                            <input type="text" name="name_en" value="{{ old('name_en', $service->name_en) }}" required
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                        </div>
                    </div>
                </div>

                {{-- Category / Price / Duration --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">{{ __('web.category') }} · {{ __('web.price') }} · {{ __('web.duration_minutes', ['min' => '']) }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ __('web.category') }} <span class="text-rose-gold">*</span>
                            </label>
                            <select name="service_category_id" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition bg-white">
                                <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الفئة' : 'Select Category' }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('service_category_id', $service->service_category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'ar' ? $cat->name_ar : $cat->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ __('web.price') }} <span class="text-rose-gold">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="price" value="{{ old('price', $service->price) }}" step="0.01" min="0" required
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition pe-12">
                                <span class="absolute inset-y-0 inset-e-3 flex items-center text-xs text-gray-400 pointer-events-none font-medium">{{ currency() }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('web.duration_minutes', ['min' => '']) }}</label>
                            <div class="relative">
                                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes) }}" min="5" required
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition pe-10">
                                <span class="absolute inset-y-0 inset-e-3 flex items-center text-xs text-gray-400 pointer-events-none">{{ app()->getLocale() === 'ar' ? 'د' : 'm' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Descriptions --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">{{ __('web.description_ar') }} / {{ __('web.description_en') }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('web.description_ar') }}</label>
                            <textarea name="description_ar" rows="4" placeholder="الوصف بالعربية" dir="rtl"
                                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold resize-none transition">{{ old('description_ar', $service->description_ar) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('web.description_en') }}</label>
                            <textarea name="description_en" rows="4" placeholder="Description in English"
                                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold resize-none transition">{{ old('description_en', $service->description_en) }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Right: image + status + actions (1/3) ── --}}
            <div class="space-y-5">

                {{-- Image upload --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">{{ __('web.image') }}</p>

                    @if($service->image_url)
                        <img id="img-preview" src="{{ $service->image_url }}" alt="{{ $service->name_en }}"
                             class="w-full aspect-video object-cover rounded-xl border border-gray-100 mb-3">
                        <label class="flex items-center gap-2 mb-4 cursor-pointer group">
                            <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-rose-gold focus:ring-rose-gold">
                            <span class="text-sm text-red-500 group-hover:text-red-600 transition">{{ __('messages.remove_image') }}</span>
                        </label>
                    @else
                        <div id="img-placeholder" class="w-full aspect-video bg-beige rounded-xl flex items-center justify-center mb-3 border border-dashed border-gray-200">
                            <div class="text-center">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-xs text-gray-400">{{ app()->getLocale() === 'ar' ? 'لا توجد صورة' : 'No image' }}</p>
                            </div>
                        </div>
                        <img id="img-preview" src="" alt="" class="hidden w-full aspect-video object-cover rounded-xl border border-gray-100 mb-3">
                    @endif

                    <label for="service-image-input"
                           class="flex flex-col items-center gap-2 w-full border-2 border-dashed border-gray-200 rounded-xl px-4 py-5 cursor-pointer hover:border-rose-gold/40 hover:bg-beige/30 transition group">
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-rose-gold/50 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span class="text-xs text-gray-400 group-hover:text-rose-gold/60 transition text-center">
                            {{ app()->getLocale() === 'ar' ? 'اضغط لرفع صورة جديدة' : 'Click to upload image' }}
                        </span>
                        <span id="file-name" class="text-xs text-rose-gold font-medium hidden"></span>
                        <input id="service-image-input" type="file" name="image" accept="image/*" class="hidden">
                    </label>
                </div>

                {{-- Status toggle --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">{{ __('web.is_active') }}</p>
                    <label class="flex items-center justify-between cursor-pointer" for="is_active">
                        <span class="text-sm text-gray-600">{{ __('web.is_active') }}</span>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 rounded-full cursor-pointer
                                        peer-checked:bg-rose-gold transition-colors duration-200
                                        after:content-[''] after:absolute after:top-0.5 after:start-0.5
                                        after:w-5 after:h-5 after:bg-white after:rounded-full after:shadow-sm
                                        after:transition-transform after:duration-200
                                        peer-checked:after:translate-x-5
                                        rtl:peer-checked:after:-translate-x-5">
                            </div>
                        </div>
                    </label>
                </div>

                {{-- Action buttons --}}
                <div class="flex flex-col gap-3">
                    <button type="submit"
                            class="w-full bg-rose-gold text-white font-semibold px-6 py-3 rounded-xl hover:bg-rose-gold-dark transition text-sm">
                        {{ __('web.save') }}
                    </button>
                    <a href="{{ route('admin.services.index') }}"
                       class="w-full text-center border border-gray-200 text-gray-500 px-6 py-3 rounded-xl hover:border-rose-gold hover:text-rose-gold transition text-sm">
                        {{ __('web.back') }}
                    </a>
                </div>

            </div>
        </div>
    </form>

    <script>
        (function () {
            const input = document.getElementById('service-image-input');
            if (!input) return;
            input.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;
                const label = document.getElementById('file-name');
                if (label) { label.textContent = file.name; label.classList.remove('hidden'); }
                const preview = document.getElementById('img-preview');
                const placeholder = document.getElementById('img-placeholder');
                if (preview) {
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove('hidden');
                }
                if (placeholder) placeholder.classList.add('hidden');
            });
        })();
    </script>

</x-layouts.admin>

