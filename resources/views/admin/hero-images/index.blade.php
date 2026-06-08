<x-layouts.admin :title="__('web.hero_images')">

    <div class="max-w-4xl">

        {{-- Page header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ __('web.hero_images') }}</h1>
                <p class="text-sm text-gray-400 mt-0.5">{{ __('web.hero_images_subtitle') }}</p>
            </div>
        </div>

        {{-- Upload form --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">{{ __('web.hero_images_upload') }}</h2>

            <form action="{{ route('admin.hero-images.store') }}" method="POST"
                  enctype="multipart/form-data"
                  class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
                @csrf

                <div class="flex-1 w-full">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('web.hero_images_constraints') }}
                    </label>
                    <input type="file"
                           name="image"
                           accept="image/jpeg,image/png,image/webp"
                           required
                           class="block w-full text-sm text-gray-500
                                  file:me-4 file:py-2 file:px-4
                                  file:rounded-xl file:border-0
                                  file:bg-rose-gold/10 file:text-rose-gold
                                  file:font-medium file:cursor-pointer
                                  hover:file:bg-rose-gold/20">
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="shrink-0 bg-rose-gold text-white font-semibold px-6 py-2.5
                               rounded-xl hover:bg-rose-gold-dark transition text-sm">
                    {{ __('web.hero_images_upload_btn') }}
                </button>
            </form>
        </div>

        {{-- Current images --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-700">
                    {{ __('web.hero_images_current') }}
                    <span class="ms-1 text-sm font-normal text-gray-400">({{ $images->count() }})</span>
                </h2>
                @if($images->count() > 1)
                    <p class="text-xs text-gray-400 flex items-center gap-1">
                        <span>⠿</span> {{ __('web.hero_images_drag_hint') }}
                    </p>
                @endif
            </div>

            @if($images->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <p class="text-4xl mb-3">🖼</p>
                    <p class="text-sm">{{ __('web.hero_images_empty') }}</p>
                </div>
            @else
                <div id="hero-sortable"
                     class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">

                    @foreach($images as $image)
                        <div class="hero-card relative rounded-xl overflow-hidden border border-gray-100
                                    select-none cursor-grab active:cursor-grabbing
                                    transition-shadow hover:shadow-md"
                             data-id="{{ $image->id }}"
                             draggable="true">

                            {{-- Thumbnail --}}
                            <div class="aspect-video bg-gray-100 relative overflow-hidden">
                                <img src="{{ $image->url }}"
                                     alt="{{ __('web.hero_images_slide') }} {{ $image->order }}"
                                     class="w-full h-full object-cover pointer-events-none">

                                {{-- Inactive dimmer --}}
                                @unless($image->is_active)
                                    <div class="absolute inset-0 bg-white/65 flex items-center justify-center">
                                        <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">
                                            {{ __('web.hero_images_inactive') }}
                                        </span>
                                    </div>
                                @endunless

                                {{-- Order badge --}}
                                <span data-order-badge
                                      class="absolute top-1.5 start-1.5 bg-black/50 text-white
                                             text-[10px] font-bold px-1.5 py-0.5 rounded leading-none">
                                    #{{ $image->order }}
                                </span>
                            </div>

                            {{-- Action bar --}}
                            <div class="flex items-center justify-between gap-1 p-2 bg-white">

                                {{-- Toggle active --}}
                                <form action="{{ route('admin.hero-images.toggle', $image) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            title="{{ $image->is_active ? __('web.hero_images_deactivate') : __('web.hero_images_activate') }}"
                                            class="text-xs px-2 py-1 rounded-lg font-medium transition
                                                   {{ $image->is_active
                                                        ? 'bg-green-50 text-green-600 hover:bg-green-100'
                                                        : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                        {{ $image->is_active ? __('web.on') : __('web.off') }}
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form action="{{ route('admin.hero-images.destroy', $image) }}"
                                      method="POST"
                                      onsubmit="return confirm('{{ __('web.hero_images_confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="{{ __('web.delete') }}"
                                            class="text-xs px-2 py-1 rounded-lg font-medium
                                                   bg-red-50 text-red-500 hover:bg-red-100 transition">
                                        ✕
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach

                </div>
            @endif
        </div>

    </div>

    <script>
    (function () {
        'use strict';

        var grid = document.getElementById('hero-sortable');
        if (!grid) return;

        var dragging   = null;
        var csrf       = document.querySelector('meta[name="csrf-token"]').content;
        var reorderUrl = '{{ route("admin.hero-images.reorder") }}';

        /* ── Drag events ─────────────────────────────────────── */
        grid.addEventListener('dragstart', function (e) {
            var card = e.target.closest('.hero-card');
            if (!card) return;
            dragging = card;
            requestAnimationFrame(function () {
                card.style.opacity = '0.45';
            });
        });

        grid.addEventListener('dragend', function () {
            if (dragging) {
                dragging.style.opacity = '1';
                dragging = null;
            }
            rebadge();
            saveOrder();
        });

        grid.addEventListener('dragover', function (e) {
            e.preventDefault();
            var target = e.target.closest('.hero-card');
            if (!target || target === dragging) return;

            var rect = target.getBoundingClientRect();
            var after = e.clientX > rect.left + rect.width / 2;
            grid.insertBefore(dragging, after ? target.nextSibling : target);
        });

        /* ── Update #N badges after each reorder ─────────────── */
        function rebadge() {
            grid.querySelectorAll('.hero-card').forEach(function (card, i) {
                var badge = card.querySelector('[data-order-badge]');
                if (badge) badge.textContent = '#' + (i + 1);
            });
        }

        /* ── Persist new order via AJAX ─────────────────────── */
        function saveOrder() {
            var ids = Array.from(grid.querySelectorAll('.hero-card')).map(function (c) {
                return parseInt(c.dataset.id, 10);
            });

            fetch(reorderUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ ids: ids }),
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.ok) console.error('Reorder failed', data);
            })
            .catch(function () {
                alert('{{ __("web.hero_images_reorder_failed") }}');
            });
        }
    }());
    </script>

</x-layouts.admin>
