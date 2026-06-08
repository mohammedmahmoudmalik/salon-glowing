<x-layouts.admin :title="__('web.edit')">

    {{-- Page header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.categories.index') }}" class="text-gray-400 hover:text-rose-gold">← {{ __('web.back') }}</a>
        <h1 class="text-2xl font-bold text-gray-800">{{ __('web.admin_categories') }} — {{ __('web.edit') }}</h1>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-5">
                @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- ── Left: form fields (2/3) ── --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Names --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">{{ __('web.name_ar') }} / {{ __('web.name_en') }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ __('web.name_ar') }} <span class="text-rose-gold">*</span>
                            </label>
                            <input type="text" name="name_ar" value="{{ old('name_ar', $category->name_ar) }}" required dir="rtl"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ __('web.name_en') }} <span class="text-rose-gold">*</span>
                            </label>
                            <input type="text" name="name_en" value="{{ old('name_en', $category->name_en) }}" required
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">{{ __('web.is_active') }}</p>
                    <label class="flex items-center justify-between cursor-pointer" for="is_active">
                        <span class="text-sm text-gray-600">{{ __('web.is_active') }}</span>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 rounded-full cursor-pointer
                                        peer-checked:bg-rose-gold transition-colors duration-200
                                        after:content-[''] after:absolute after:top-0.5 after:inset-s-0.5
                                        after:w-5 after:h-5 after:bg-white after:rounded-full after:shadow-sm
                                        after:transition-transform after:duration-200
                                        peer-checked:after:translate-x-5
                                        rtl:peer-checked:after:-translate-x-5">
                            </div>
                        </div>
                    </label>
                </div>

                {{-- Action buttons --}}
                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-rose-gold text-white font-semibold px-8 py-3 rounded-xl hover:bg-rose-gold-dark transition text-sm">
                        {{ __('web.save') }}
                    </button>
                    <a href="{{ route('admin.categories.index') }}"
                       class="border border-gray-200 text-gray-500 px-8 py-3 rounded-xl hover:border-rose-gold hover:text-rose-gold transition text-sm">
                        {{ __('web.back') }}
                    </a>
                </div>

            </div>

            {{-- ── Right: image (1/3) ── --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">{{ __('web.image') }}</p>

                @if($category->image_url)
                    <div class="flex justify-center mb-3">
                        <img id="img-preview" src="{{ $category->image_url }}" alt="{{ $category->name_en }}"
                             class="w-32 h-32 object-cover rounded-full border-4 border-beige shadow-sm">
                    </div>
                    <label class="flex items-center justify-center gap-2 mb-4 cursor-pointer group">
                        <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-rose-gold focus:ring-rose-gold">
                        <span class="text-sm text-red-500 group-hover:text-red-600 transition">{{ __('messages.remove_image') }}</span>
                    </label>
                @else
                    <div class="flex justify-center mb-3">
                        <div id="img-placeholder" class="w-32 h-32 rounded-full bg-beige border-4 border-dashed border-gray-200 flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <img id="img-preview" src="" alt="" class="hidden w-32 h-32 object-cover rounded-full border-4 border-beige shadow-sm">
                    </div>
                @endif

                <label for="category-image-input"
                       class="flex flex-col items-center gap-2 w-full border-2 border-dashed border-gray-200 rounded-xl px-4 py-5 cursor-pointer hover:border-rose-gold/40 hover:bg-beige/30 transition group">
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-rose-gold/50 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <span class="text-xs text-gray-400 group-hover:text-rose-gold/60 transition text-center">
                        {{ app()->getLocale() === 'ar' ? 'اضغط لرفع صورة جديدة' : 'Click to upload image' }}
                    </span>
                    <span id="cat-file-name" class="text-xs text-rose-gold font-medium hidden"></span>
                    <input id="category-image-input" type="file" name="image" accept="image/*" class="hidden">
                </label>
            </div>

        </div>
    </form>

    <script>
        (function () {
            const input = document.getElementById('category-image-input');
            if (!input) return;
            input.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;
                const label = document.getElementById('cat-file-name');
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
