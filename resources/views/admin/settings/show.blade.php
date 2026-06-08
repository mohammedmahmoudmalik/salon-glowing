<x-layouts.admin :title="__('web.settings_title')">
    @php $isAr = app()->getLocale() === 'ar'; @endphp

    <div class="max-w-3xl space-y-6">

        {{-- Page header --}}
        <h1 class="text-2xl font-bold text-gray-800">{{ __('web.settings_title') }}</h1>

        {{-- ── 1. Working Hours (admin only) ─────────────────────── --}}
        @if(auth()->user()->hasRole('admin'))
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-4">
                    @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

                {{-- Section header --}}
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-beige flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-rose-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">{{ __('web.working_hours') }}</h2>
                        <p class="text-xs text-gray-400 mt-0.5">{{ app()->getLocale() === 'ar' ? 'أوقات وأيام العمل' : 'Operating times and days' }}</p>
                    </div>
                </div>

                <div class="px-6 py-5 space-y-5">

                    {{-- Open / Close times --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('web.open_time') }}</label>
                            <input type="time" name="open"
                                   value="{{ old('open', $salonHours['open'] ?? '09:00') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('web.close_time') }}</label>
                            <input type="time" name="close"
                                   value="{{ old('close', $salonHours['close'] ?? '21:00') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                        </div>
                    </div>

                    {{-- Working days --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ __('web.working_days') }}</label>
                        @php
                            $days = [
                                0 => __('web.day_sun'),
                                1 => __('web.day_mon'),
                                2 => __('web.day_tue'),
                                3 => __('web.day_wed'),
                                4 => __('web.day_thu'),
                                5 => __('web.day_fri'),
                                6 => __('web.day_sat'),
                            ];
                            $currentDays = old('working_days', $salonHours['working_days'] ?? [0, 1, 2, 3, 4]);
                        @endphp
                        <div class="grid grid-cols-4 sm:grid-cols-7 gap-2">
                            @foreach($days as $value => $label)
                                <label class="cursor-pointer select-none">
                                    <input type="checkbox" name="working_days[]" value="{{ $value }}"
                                           {{ in_array($value, $currentDays) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="flex items-center justify-center h-10 rounded-xl text-xs font-semibold border border-gray-200 text-gray-500 transition
                                                peer-checked:bg-rose-gold peer-checked:text-white peer-checked:border-rose-gold
                                                hover:border-rose-gold/40 hover:text-rose-gold">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Buffer minutes --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('web.buffer_minutes') }}</label>
                        <div class="flex items-center gap-3">
                            <div class="relative w-36">
                                <input type="number" name="booking_buffer_minutes"
                                       value="{{ old('booking_buffer_minutes', $buffer) }}"
                                       min="0" max="60"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition pe-10">
                                <span class="absolute inset-y-0 inset-e-3 flex items-center text-xs text-gray-400 pointer-events-none">
                                    {{ app()->getLocale() === 'ar' ? 'د' : 'm' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400">{{ app()->getLocale() === 'ar' ? 'فترة الراحة بين الحجوزات' : 'Break between bookings' }}</p>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50/60 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                            class="bg-rose-gold text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-rose-gold-dark transition">
                        {{ __('web.save_changes') }}
                    </button>
                </div>
            </div>
        </form>
        @endif

        {{-- ── 2. Country / Currency (owner only) ─────────────── --}}
        @if(auth()->user()->hasRole('owner'))
        <form action="{{ route('admin.settings.country') }}" method="POST">
            @csrf
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-beige flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-rose-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 004 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">{{ __('web.country') }}</h2>
                        <p class="text-xs text-gray-400 mt-0.5">{{ __('web.country_hint') }}</p>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach(['SA' => __('web.country_sa'), 'AE' => __('web.country_ae'), 'EG' => __('web.country_eg')] as $code => $label)
                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition
                                          {{ $salonCountry === $code ? 'border-rose-gold bg-beige' : 'border-gray-100 hover:border-rose-gold/40' }}">
                                <input type="radio" name="salon_country" value="{{ $code }}"
                                       {{ $salonCountry === $code ? 'checked' : '' }}
                                       class="accent-rose-gold">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $label }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ match($code) { 'SA' => 'ر.س / SAR', 'AE' => 'د.إ / AED', 'EG' => 'ج.م / EGP' } }}
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50/60 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                            class="bg-rose-gold text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-rose-gold-dark transition">
                        {{ __('web.save_country') }}
                    </button>
                </div>
            </div>
        </form>
        @endif

        {{-- ── 3. Branding (owner only) ──────────────────────────── --}}
        @if(auth()->user()->hasRole('owner'))
        @php
            $colorDefs = [
                [
                    'key'         => 'primary_color',
                    'name_ar'     => 'لون الأزرار والروابط',
                    'name_en'     => 'Buttons & Links',
                    'desc_ar'     => 'الأزرار الرئيسية والروابط النشطة',
                    'desc_en'     => 'Main buttons and active links',
                    'placeholder' => '#B76E79',
                ],
                [
                    'key'         => 'primary_color_light',
                    'name_ar'     => 'الأزرار عند التمرير',
                    'name_en'     => 'Button Hover (Light)',
                    'desc_ar'     => 'لون الزر عند مرور المؤشر فوقه',
                    'desc_en'     => 'Button color on mouse hover',
                    'placeholder' => '#c98a93',
                ],
                [
                    'key'         => 'primary_color_dark',
                    'name_ar'     => 'الأزرار عند الضغط',
                    'name_en'     => 'Button Pressed (Dark)',
                    'desc_ar'     => 'لون الزر لحظة الضغط عليه',
                    'desc_en'     => 'Button color when clicked',
                    'placeholder' => '#9a5a64',
                ],
                [
                    'key'         => 'soft_pink_color',
                    'name_ar'     => 'لون تدرج الأقسام',
                    'name_en'     => 'Section Gradient',
                    'desc_ar'     => 'التدرج الملوّن في خلفيات الأقسام',
                    'desc_en'     => 'Colored gradient behind page sections',
                    'placeholder' => '#F2A7BB',
                ],
                [
                    'key'         => 'soft_pink_light_color',
                    'name_ar'     => 'تدرج الأقسام الفاتح',
                    'name_en'     => 'Section Gradient (Light)',
                    'desc_ar'     => 'النسخة الأفتح من تدرج الخلفية',
                    'desc_en'     => 'Lighter variant of the section gradient',
                    'placeholder' => '#f7c8d5',
                ],
                [
                    'key'         => 'secondary_color',
                    'name_ar'     => 'لون خلفية الصفحة',
                    'name_en'     => 'Page Background',
                    'desc_ar'     => 'خلفية الصفحة والفوتر والشريط السفلي',
                    'desc_en'     => 'Page background, footer, bottom bar',
                    'placeholder' => '#F5F0E8',
                ],
                [
                    'key'         => 'secondary_color_dark',
                    'name_ar'     => 'لون الحدود والفواصل',
                    'name_en'     => 'Borders & Dividers',
                    'desc_ar'     => 'الخطوط الفاصلة بين عناصر الصفحة',
                    'desc_en'     => 'Lines separating page elements',
                    'placeholder' => '#E8E0CC',
                ],
                [
                    'key'         => 'salon_text_color',
                    'name_ar'     => 'لون النصوص الرئيسية',
                    'name_en'     => 'Main Text Color',
                    'desc_ar'     => 'لون المحتوى والنصوص في الصفحات',
                    'desc_en'     => 'Body text and content across pages',
                    'placeholder' => '#3d2b2f',
                ],
            ];
        @endphp

        <form action="{{ route('admin.settings.branding') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

                {{-- Section header --}}
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-beige flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-rose-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">{{ $isAr ? 'هوية الصالون' : 'Salon Branding' }}</h2>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $isAr ? 'الشعار والاسم وجميع ألوان الموقع' : 'Logo, name and all site colors' }}</p>
                    </div>
                </div>

                <div class="px-6 py-5 space-y-6">

                    {{-- Salon name --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                            {{ $isAr ? 'اسم الصالون' : 'Salon Name' }}
                        </label>
                        <input type="text" name="salon_name"
                               value="{{ old('salon_name', $salonName) }}"
                               maxlength="100"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                    </div>

                    {{-- Logo upload --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                            {{ $isAr ? 'شعار الصالون' : 'Salon Logo' }}
                        </label>
                        <div class="flex items-center gap-4">
                            @if($logoPath)
                                <div class="flex flex-col items-center gap-1.5">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($logoPath) }}"
                                         alt="{{ $isAr ? 'الشعار الحالي' : 'Current logo' }}"
                                         class="h-12 w-auto object-contain rounded-lg border border-gray-100 bg-gray-50 p-1">
                                    <button type="button"
                                            onclick="deleteLogo()"
                                            class="flex items-center gap-1 text-xs font-medium text-red-500 hover:text-red-700 border border-red-200 hover:border-red-400 hover:bg-red-50 rounded-lg px-2.5 py-1 transition">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        {{ $isAr ? 'حذف الشعار' : 'Delete Logo' }}
                                    </button>
                                </div>
                            @else
                                <div class="h-12 w-16 flex items-center justify-center rounded-lg border border-gray-100 bg-gray-50">
                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a4 4 0 004 4z"/>
                                    </svg>
                                </div>
                            @endif
                            <label for="logo-input" class="cursor-pointer flex-1">
                                <span id="logo-file-label"
                                      class="flex items-center gap-2 border border-dashed border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-400 hover:border-rose-gold/40 hover:text-rose-gold/70 transition cursor-pointer">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    {{ $isAr ? 'اختر شعاراً...' : 'Choose logo...' }}
                                </span>
                                <input id="logo-input" type="file" name="logo"
                                       accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml"
                                       class="hidden">
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">{{ $isAr ? 'JPG، PNG، WEBP أو SVG — حتى 2 ميجابايت' : 'JPG, PNG, WEBP or SVG — up to 2 MB' }}</p>
                        @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- ── All 8 Colors ──────────────────────────────── --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">
                            {{ $isAr ? 'ألوان الموقع' : 'Site Colors' }}
                        </p>
                        {{-- Tip: button colors --}}
                        <div class="flex items-start gap-2.5 mb-4 px-3 py-2.5 rounded-xl"
                             style="background:color-mix(in srgb,var(--color-rose-gold) 6%,transparent); border:1px solid color-mix(in srgb,var(--color-rose-gold) 15%,transparent);">
                            <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" style="color:var(--color-rose-gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-[11px] leading-relaxed" style="color:var(--color-rose-gold);">
                                {{ $isAr
                                    ? 'ألوان الأزرار الثلاثة (الرئيسي، التمرير، الضغط) يجب أن تكون من نفس اللون بنسب فاتحة وداكنة — مثلاً إذا اخترتِ أزرق للأزرار الرئيسي، اختاري أزرق فاتح للتمرير وأزرق داكن للضغط.'
                                    : 'The three button colors (main, hover, pressed) should be the same hue in light and dark shades — e.g. if you pick blue for the main button, choose a lighter blue for hover and a darker blue for pressed.'
                                }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($colorDefs as $cd)
                            @php
                                $val = old($cd['key'], $colors[$cd['key']]);
                                $pickerId = 'cp-' . str_replace('_', '-', $cd['key']);
                                $textId   = 'ct-' . str_replace('_', '-', $cd['key']);
                            @endphp
                            <div class="flex flex-col gap-2 p-3 border border-gray-100 rounded-xl hover:border-rose-gold/20 transition">
                                {{-- Swatch preview --}}
                                <div class="h-10 w-full rounded-lg border border-gray-200"
                                     id="{{ $pickerId }}-swatch"
                                     style="background-color: {{ $val }};"></div>
                                {{-- Name --}}
                                <div>
                                    <p class="text-xs font-semibold text-gray-700 leading-tight">
                                        {{ $isAr ? $cd['name_ar'] : $cd['name_en'] }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 mt-0.5 leading-tight">
                                        {{ $isAr ? $cd['desc_ar'] : $cd['desc_en'] }}
                                    </p>
                                </div>
                                {{-- Picker + hex --}}
                                <div class="flex items-center gap-1.5">
                                    <input type="color"
                                           id="{{ $pickerId }}"
                                           value="{{ $val }}"
                                           class="w-8 h-8 rounded-lg border border-gray-200 cursor-pointer p-0.5 shrink-0">
                                    <input type="text"
                                           name="{{ $cd['key'] }}"
                                           id="{{ $textId }}"
                                           value="{{ $val }}"
                                           maxlength="7"
                                           placeholder="{{ $cd['placeholder'] }}"
                                           class="flex-1 min-w-0 border border-gray-200 rounded-lg px-2 py-1.5 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                                </div>
                                @error($cd['key'])
                                    <p class="text-red-500 text-[10px]">{{ $message }}</p>
                                @enderror
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50/60 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                            class="bg-rose-gold text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-rose-gold-dark transition">
                        {{ __('web.save_changes') }}
                    </button>
                </div>
            </div>
        </form>
        @endif

        {{-- ── 3. Hero Text (owner only) ─────────────────────────── --}}
        @if(auth()->user()->hasRole('owner'))
        <form action="{{ route('admin.settings.hero') }}" method="POST">
            @csrf

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

                {{-- Section header --}}
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-beige flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-rose-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">{{ app()->getLocale() === 'ar' ? 'نصوص الصفحة الرئيسية' : 'Hero Section Text' }}</h2>
                        <p class="text-xs text-gray-400 mt-0.5">{{ app()->getLocale() === 'ar' ? 'العنوان والوصف وزر الدعوة للعمل' : 'Title, subtitle and call-to-action button' }}</p>
                    </div>
                </div>

                <div class="px-6 py-5 space-y-5">

                    {{-- Title --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                {{ app()->getLocale() === 'ar' ? 'العنوان — عربي' : 'Title — Arabic' }}
                            </label>
                            <input type="text" name="hero_title_ar"
                                   value="{{ old('hero_title_ar', $heroTitleAr) }}"
                                   maxlength="200" dir="rtl"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                {{ app()->getLocale() === 'ar' ? 'العنوان — إنجليزي' : 'Title — English' }}
                            </label>
                            <input type="text" name="hero_title_en"
                                   value="{{ old('hero_title_en', $heroTitleEn) }}"
                                   maxlength="200" dir="ltr"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                        </div>
                    </div>

                    {{-- Subtitle --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                {{ app()->getLocale() === 'ar' ? 'الوصف — عربي' : 'Subtitle — Arabic' }}
                            </label>
                            <textarea name="hero_subtitle_ar" rows="3"
                                      maxlength="500" dir="rtl"
                                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition resize-none">{{ old('hero_subtitle_ar', $heroSubtitleAr) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                {{ app()->getLocale() === 'ar' ? 'الوصف — إنجليزي' : 'Subtitle — English' }}
                            </label>
                            <textarea name="hero_subtitle_en" rows="3"
                                      maxlength="500" dir="ltr"
                                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition resize-none">{{ old('hero_subtitle_en', $heroSubtitleEn) }}</textarea>
                        </div>
                    </div>

                    {{-- CTA button text --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                {{ app()->getLocale() === 'ar' ? 'زر الدعوة — عربي' : 'CTA Button — Arabic' }}
                            </label>
                            <input type="text" name="hero_cta_ar"
                                   value="{{ old('hero_cta_ar', $heroCtaAr) }}"
                                   maxlength="100" dir="rtl"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                {{ app()->getLocale() === 'ar' ? 'زر الدعوة — إنجليزي' : 'CTA Button — English' }}
                            </label>
                            <input type="text" name="hero_cta_en"
                                   value="{{ old('hero_cta_en', $heroCtaEn) }}"
                                   maxlength="100" dir="ltr"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50/60 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                            class="bg-rose-gold text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-rose-gold-dark transition">
                        {{ __('web.save_changes') }}
                    </button>
                </div>
            </div>
        </form>
        @endif

        {{-- ── 4. Social Links (admin only) ──────────────────────── --}}
        @if(auth()->user()->hasRole('admin'))
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

            {{-- Section header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-beige flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-rose-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">{{ __('web.social_links') }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ app()->getLocale() === 'ar' ? 'فعّل الرابط ليظهر في الموقع' : 'Enable a link to show it on the site' }}</p>
                </div>
            </div>

            <form action="{{ route('admin.settings.social') }}" method="POST">
                @csrf

                @php
                    $socials = [
                        'social_whatsapp'  => ['label' => __('web.social_whatsapp'),  'placeholder' => 'https://wa.me/966...', 'svg' => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>'],
                        'social_instagram' => ['label' => __('web.social_instagram'), 'placeholder' => 'https://instagram.com/...', 'svg' => '<svg class="w-5 h-5" viewBox="0 0 24 24"><defs><linearGradient id="ig-s" x1="0%" y1="100%" x2="100%" y2="0%"><stop offset="0%" style="stop-color:#f09433"/><stop offset="50%" style="stop-color:#dc2743"/><stop offset="100%" style="stop-color:#bc1888"/></linearGradient></defs><path fill="url(#ig-s)" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>'],
                        'social_snapchat'  => ['label' => __('web.social_snapchat'),  'placeholder' => 'https://snapchat.com/add/...', 'svg' => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="#FFFC00" style="background:#222;border-radius:5px;padding:1px"><path d="M12.206.793c.99 0 4.347.276 5.93 3.821.529 1.193.403 3.219.299 4.847l-.003.06c-.012.18-.022.345-.03.51.075.045.203.09.401.09.3-.016.659-.12 1.033-.301.165-.088.344-.104.464-.104.182 0 .359.029.509.09.45.149.734.479.734.838.015.449-.39.839-1.213 1.168-.089.029-.209.075-.344.119-.45.135-1.139.36-1.333.81-.09.224-.061.524.12.868l.015.015c.06.136 1.526 3.468 4.791 4.013.255.044.435.27.42.509 0 .075-.015.149-.045.225-.24.569-1.273.988-3.146 1.271-.059.091-.12.375-.164.57-.029.179-.074.36-.134.553-.076.271-.27.405-.555.405h-.03c-.135 0-.313-.031-.538-.074-.36-.075-.765-.135-1.273-.135-.3 0-.599.015-.913.074-.6.104-1.123.464-1.723.884-.853.599-1.826 1.288-3.294 1.288-.06 0-.119-.015-.018-.015h-.06c-1.469 0-2.427-.675-3.279-1.288-.599-.42-1.107-.779-1.707-.884-.314-.045-.629-.074-.928-.074-.54 0-.958.089-1.272.149-.211.043-.391.074-.54.074-.374 0-.523-.224-.583-.42-.061-.192-.09-.389-.135-.567-.046-.181-.105-.494-.166-.57-1.918-.222-2.95-.642-3.189-1.226-.031-.063-.052-.15-.055-.225-.015-.243.165-.465.42-.509 3.264-.54 4.73-3.87 4.791-4.013l.016-.029c.18-.345.224-.645.119-.869-.195-.434-.884-.658-1.332-.809-.121-.029-.24-.074-.346-.119-1.107-.435-1.257-.93-1.197-1.273.09-.479.674-.793 1.168-.793.139 0 .293.029.479.09.405.195.748.3 1.05.3.224 0 .378-.06.479-.105l-.031-.569c-.098-1.626-.225-3.651.301-4.845C7.748 1.07 11.111.793 12.101.793h.105z"/></svg>'],
                        'social_tiktok'    => ['label' => __('web.social_tiktok'),    'placeholder' => 'https://tiktok.com/@...', 'svg' => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="#000000"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.78a4.85 4.85 0 01-1.01-.09z"/></svg>'],
                        'social_twitter'   => ['label' => __('web.social_twitter'),   'placeholder' => 'https://x.com/...', 'svg' => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="#000000"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>'],
                        'social_facebook'  => ['label' => __('web.social_facebook'),  'placeholder' => 'https://facebook.com/...', 'svg' => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'],
                        'google_maps_url'  => ['label' => __('web.google_maps_url'),  'placeholder' => 'https://maps.google.com/...', 'svg' => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="#EA4335"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>'],
                    ];
                @endphp

                <div class="divide-y divide-gray-50">
                    @foreach($socials as $key => $social)
                        @php
                            $value = \App\Domains\Admin\Models\Setting::get($key, ['url' => '', 'is_active' => false]);
                        @endphp
                        <div class="px-6 py-3.5 flex items-center gap-3">
                            {{-- Icon --}}
                            <span class="w-8 h-8 shrink-0 flex items-center justify-center rounded-lg bg-gray-50">
                                {!! $social['svg'] !!}
                            </span>
                            {{-- Label --}}
                            <span class="text-sm text-gray-600 w-20 shrink-0 hidden sm:block">{{ $social['label'] }}</span>
                            {{-- URL input --}}
                            <input type="url"
                                   name="social[{{ $key }}][url]"
                                   value="{{ $value['url'] ?? '' }}"
                                   placeholder="{{ $social['placeholder'] }}"
                                   class="flex-1 min-w-0 border border-gray-200 rounded-xl px-3 py-2 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                            {{-- Toggle --}}
                            <label class="shrink-0 cursor-pointer" title="{{ __('web.social_active') }}">
                                <input type="checkbox"
                                       name="social[{{ $key }}][is_active]"
                                       value="1"
                                       {{ ($value['is_active'] ?? false) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="relative w-9 h-5 bg-gray-200 rounded-full cursor-pointer
                                            peer-checked:bg-rose-gold transition-colors duration-200
                                            after:content-[''] after:absolute after:top-0.5 after:inset-s-0.5
                                            after:w-4 after:h-4 after:bg-white after:rounded-full after:shadow-sm
                                            after:transition-transform after:duration-200
                                            peer-checked:after:translate-x-4
                                            rtl:peer-checked:after:-translate-x-4
                                            peer-disabled:opacity-40 peer-disabled:cursor-not-allowed">
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 py-4 bg-gray-50/60 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                            class="bg-rose-gold text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-rose-gold-dark transition">
                        {{ __('web.save_links') }}
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- ── 5. Hero Images (admin only) ───────────────────────── --}}
        @if(auth()->user()->hasRole('admin'))
        <div id="hero-images-section" class="bg-white rounded-2xl shadow-sm overflow-hidden">

            {{-- Section header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-beige flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-rose-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">{{ __('web.hero_images') }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ __('web.hero_images_subtitle') }}</p>
                </div>
            </div>

            <div class="px-6 py-5">

                {{-- Upload form --}}
                <form action="{{ route('admin.hero-images.store') }}" method="POST"
                      enctype="multipart/form-data" class="mb-5">
                    @csrf
                    <label for="hero-image-input"
                           class="flex items-center gap-4 w-full border-2 border-dashed border-gray-200 rounded-xl px-5 py-4 cursor-pointer hover:border-rose-gold/40 hover:bg-beige/30 transition group mb-3">
                        <svg class="w-6 h-6 text-gray-300 group-hover:text-rose-gold/50 shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <span id="hero-file-label" class="block text-sm text-gray-500 group-hover:text-rose-gold/70 transition truncate">
                                {{ app()->getLocale() === 'ar' ? 'اضغط لاختيار صورة...' : 'Click to choose an image...' }}
                            </span>
                            <span class="block text-xs text-gray-300 mt-0.5">{{ __('web.hero_images_constraints') }}</span>
                        </div>
                        <input id="hero-image-input" type="file" name="image"
                               accept="image/jpeg,image/png,image/webp" required class="hidden">
                    </label>
                    @error('image')
                        <p class="text-red-500 text-xs mb-3">{{ $message }}</p>
                    @enderror
                    <button type="submit"
                            class="bg-rose-gold text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-rose-gold-dark transition">
                        {{ __('web.hero_images_upload_btn') }}
                    </button>
                </form>

                {{-- Current images --}}
                @if($images->isEmpty())
                    <div class="text-center py-10 text-gray-400 border border-dashed border-gray-200 rounded-xl">
                        <p class="text-3xl mb-2">🖼</p>
                        <p class="text-sm">{{ __('web.hero_images_empty') }}</p>
                    </div>
                @else
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs text-gray-400">
                            {{ $images->count() }} {{ app()->getLocale() === 'ar' ? 'صورة' : 'images' }}
                        </span>
                        @if($images->count() > 1)
                            <p class="text-xs text-gray-400 flex items-center gap-1">
                                <span>⠿</span> {{ __('web.hero_images_drag_hint') }}
                            </p>
                        @endif
                    </div>
                    <div id="hero-sortable" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($images as $image)
                            <div class="hero-card relative rounded-xl overflow-hidden border border-gray-100
                                        select-none cursor-grab active:cursor-grabbing transition-shadow hover:shadow-md"
                                 data-id="{{ $image->id }}" draggable="true">
                                <div class="aspect-video bg-gray-100 relative overflow-hidden">
                                    <img src="{{ $image->url }}"
                                         alt="{{ __('web.hero_images_slide') }} {{ $image->order }}"
                                         class="w-full h-full object-cover pointer-events-none">
                                    @unless($image->is_active)
                                        <div class="absolute inset-0 bg-white/65 flex items-center justify-center">
                                            <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">
                                                {{ __('web.hero_images_inactive') }}
                                            </span>
                                        </div>
                                    @endunless
                                    <span data-order-badge
                                          class="absolute top-1.5 inset-s-1.5 bg-black/50 text-white text-[10px] font-bold px-1.5 py-0.5 rounded leading-none">
                                        #{{ $image->order }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-1 p-2 bg-white">
                                    <form action="{{ route('admin.hero-images.toggle', $image) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="text-xs px-2 py-1 rounded-lg font-medium transition
                                                       {{ $image->is_active ? 'bg-green-50 text-green-600 hover:bg-green-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                            {{ $image->is_active ? __('web.on') : __('web.off') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.hero-images.destroy', $image) }}" method="POST"
                                          onsubmit="return confirm('{{ __('web.hero_images_confirm_delete') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-xs px-2 py-1 rounded-lg font-medium bg-red-50 text-red-500 hover:bg-red-100 transition">
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
        @endif

        {{-- ── 6. Tour Video (admin only) ────────────────────────── --}}
        @if(auth()->user()->hasRole('admin'))
        <div id="tour-video-section" class="bg-white rounded-2xl shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-beige flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-rose-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">{{ $isAr ? 'فيديو جولة الصالون' : 'Salon Tour Video' }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $isAr ? 'يُعرض خلفيةً في الهيرو وفي قسم "قصتنا"' : 'Used as hero background and in the story section' }}</p>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="flex flex-wrap gap-6 items-start">

                    {{-- ── Video preview column ──────────────────────── --}}
                    <div class="flex flex-col items-center gap-3" style="width:176px; max-width:100%; flex-shrink:0;">
                        @if($tourVideoPath)
                            <video src="{{ Storage::url($tourVideoPath) }}"
                                   controls preload="metadata"
                                   class="rounded-2xl border border-gray-100 bg-gray-900 block"
                                   style="width:176px; max-width:100%; aspect-ratio:9/16; object-fit:contain;">
                            </video>
                            <div class="flex gap-2">
                                <form action="{{ route('admin.settings.tour-video.delete') }}" method="POST"
                                      onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من حذف فيديو الجولة؟' : 'Delete the tour video?' }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="flex items-center gap-1.5 text-xs font-medium text-red-500 hover:text-red-700 border border-red-200 hover:border-red-400 hover:bg-red-50 rounded-lg px-3 py-1.5 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        {{ $isAr ? 'حذف' : 'Delete' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.settings.tour-video') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <label for="tour-video-input"
                                           class="flex items-center gap-1.5 text-xs font-medium text-gray-600 hover:text-rose-gold border border-gray-200 hover:border-rose-gold/40 rounded-lg px-3 py-1.5 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        {{ $isAr ? 'استبدال' : 'Replace' }}
                                        <input id="tour-video-input" type="file" name="tour_video"
                                               accept="video/mp4,video/webm,video/ogg,video/quicktime"
                                               class="hidden" onchange="this.form.submit()">
                                    </label>
                                    @error('tour_video')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </form>
                            </div>
                        @else
                            <div class="rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 flex flex-col items-center justify-center gap-2 text-gray-300"
                                 style="width:176px; max-width:100%; aspect-ratio:9/16;">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-[11px] text-center px-2 leading-tight">{{ $isAr ? 'لم يُرفع فيديو بعد' : 'No video yet' }}</span>
                            </div>
                            <form action="{{ route('admin.settings.tour-video') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <label for="tour-video-input-new"
                                       class="flex items-center gap-1.5 text-xs font-semibold text-white rounded-lg px-3 py-1.5 transition cursor-pointer"
                                       style="background:var(--color-rose-gold);">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    {{ $isAr ? 'رفع فيديو' : 'Upload' }}
                                    <input id="tour-video-input-new" type="file" name="tour_video"
                                           accept="video/mp4,video/webm,video/ogg,video/quicktime"
                                           class="hidden" onchange="this.form.submit()">
                                </label>
                                @error('tour_video')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </form>
                        @endif
                    </div>

                    {{-- ── Settings column ───────────────────────────── --}}
                    <div class="flex-1 space-y-5" style="min-width:0;">

                        <form action="{{ route('admin.settings.tour-video-texts') }}" method="POST">
                                @csrf
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">
                                    {{ $isAr ? 'نصوص قسم الجولة' : 'Tour Section Texts' }}
                                </p>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                                {{ $isAr ? 'اسم التاب — عربي' : 'Tab Name — Arabic' }}
                                            </label>
                                            <input type="text" name="tour_video_tab_ar"
                                                   value="{{ old('tour_video_tab_ar', $tourVideoTabAr) }}"
                                                   maxlength="60" dir="rtl"
                                                   placeholder="{{ $isAr ? 'مثال: جولة الصالون' : 'e.g. Salon Tour' }}"
                                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                                {{ $isAr ? 'اسم التاب — إنجليزي' : 'Tab Name — English' }}
                                            </label>
                                            <input type="text" name="tour_video_tab_en"
                                                   value="{{ old('tour_video_tab_en', $tourVideoTabEn) }}"
                                                   maxlength="60" dir="ltr"
                                                   placeholder="e.g. Salon Tour"
                                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                                {{ $isAr ? 'العنوان — عربي' : 'Title — Arabic' }}
                                            </label>
                                            <input type="text" name="tour_video_title_ar"
                                                   value="{{ old('tour_video_title_ar', $tourVideoTitleAr) }}"
                                                   maxlength="120" dir="rtl"
                                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                                {{ $isAr ? 'العنوان — إنجليزي' : 'Title — English' }}
                                            </label>
                                            <input type="text" name="tour_video_title_en"
                                                   value="{{ old('tour_video_title_en', $tourVideoTitleEn) }}"
                                                   maxlength="120" dir="ltr"
                                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                                {{ $isAr ? 'الوصف — عربي' : 'Description — Arabic' }}
                                            </label>
                                            <textarea name="tour_video_desc_ar" rows="3"
                                                      maxlength="400" dir="rtl"
                                                      class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition resize-none">{{ old('tour_video_desc_ar', $tourVideoDescAr) }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                                {{ $isAr ? 'الوصف — إنجليزي' : 'Description — English' }}
                                            </label>
                                            <textarea name="tour_video_desc_en" rows="3"
                                                      maxlength="400" dir="ltr"
                                                      class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition resize-none">{{ old('tour_video_desc_en', $tourVideoDescEn) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end mt-4">
                                    <button type="submit"
                                            class="bg-rose-gold text-white text-sm font-semibold px-5 py-2 rounded-xl hover:bg-rose-gold-dark transition">
                                        {{ __('web.save_changes') }}
                                    </button>
                                </div>
                            </form>

                    </div>{{-- end settings column --}}
                </div>
            </div>
        </div>
        @endif

        {{-- ── 7. Owner Video (admin only) ───────────────────────── --}}
        @if(auth()->user()->hasRole('admin'))
        <div id="owner-video-section" class="bg-white rounded-2xl shadow-sm overflow-hidden">

            {{-- Section header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-beige flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-rose-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">{{ $isAr ? 'فيديو المالكة' : 'Owner Story Video' }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $isAr ? 'يظهر في قسم "قصتنا" في الصفحة الرئيسية' : 'Shown in the "Our Story" section on the homepage' }}</p>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="flex flex-wrap gap-6 items-start">

                    {{-- ── Video preview column ──────────────────────── --}}
                    <div class="flex flex-col items-center gap-3" style="width:176px; max-width:100%; flex-shrink:0;">
                        @if($ownerVideoPath)
                            <video src="{{ Storage::url($ownerVideoPath) }}"
                                   controls preload="metadata"
                                   class="rounded-2xl border border-gray-100 bg-gray-900 block"
                                   style="width:176px; max-width:100%; aspect-ratio:9/16; object-fit:contain;">
                            </video>
                            <div class="flex gap-2">
                                <form action="{{ route('admin.settings.owner-video.delete') }}" method="POST"
                                      onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من حذف الفيديو؟' : 'Delete the owner video?' }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="flex items-center gap-1.5 text-xs font-medium text-red-500 hover:text-red-700 border border-red-200 hover:border-red-400 hover:bg-red-50 rounded-lg px-3 py-1.5 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        {{ $isAr ? 'حذف' : 'Delete' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.settings.owner-video') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <label for="owner-video-input"
                                           class="flex items-center gap-1.5 text-xs font-medium text-gray-600 hover:text-rose-gold border border-gray-200 hover:border-rose-gold/40 rounded-lg px-3 py-1.5 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        {{ $isAr ? 'استبدال' : 'Replace' }}
                                        <input id="owner-video-input" type="file" name="owner_video"
                                               accept="video/mp4,video/webm,video/ogg,video/quicktime"
                                               class="hidden" onchange="this.form.submit()">
                                    </label>
                                    @error('owner_video')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </form>
                            </div>
                        @else
                            <div class="rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 flex flex-col items-center justify-center gap-2 text-gray-300"
                                 style="width:176px; max-width:100%; aspect-ratio:9/16;">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                </svg>
                                <span class="text-[11px] text-center px-2 leading-tight">{{ $isAr ? 'لم يُرفع فيديو بعد' : 'No video yet' }}</span>
                            </div>
                            <form action="{{ route('admin.settings.owner-video') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <label for="owner-video-input-new"
                                       class="flex items-center gap-1.5 text-xs font-semibold text-white rounded-lg px-3 py-1.5 transition cursor-pointer"
                                       style="background:var(--color-rose-gold);">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    {{ $isAr ? 'رفع فيديو' : 'Upload' }}
                                    <input id="owner-video-input-new" type="file" name="owner_video"
                                           accept="video/mp4,video/webm,video/ogg,video/quicktime"
                                           class="hidden" onchange="this.form.submit()">
                                </label>
                                @error('owner_video')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </form>
                        @endif
                    </div>

                    {{-- ── Settings column ───────────────────────────── --}}
                    <div class="flex-1 space-y-5" style="min-width:0;">

                        <form action="{{ route('admin.settings.owner-video-texts') }}" method="POST">
                            @csrf
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">
                                {{ $isAr ? 'نصوص قسم المالكة' : 'Owner Section Texts' }}
                            </p>
                            <div class="space-y-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                                {{ $isAr ? 'اسم التاب — عربي' : 'Tab Name — Arabic' }}
                                            </label>
                                            <input type="text" name="owner_video_tab_ar"
                                                   value="{{ old('owner_video_tab_ar', $ownerVideoTabAr) }}"
                                                   maxlength="60" dir="rtl"
                                                   placeholder="{{ $isAr ? 'مثال: كلمة المالكة' : 'e.g. Owner Story' }}"
                                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                                {{ $isAr ? 'اسم التاب — إنجليزي' : 'Tab Name — English' }}
                                            </label>
                                            <input type="text" name="owner_video_tab_en"
                                                   value="{{ old('owner_video_tab_en', $ownerVideoTabEn) }}"
                                                   maxlength="60" dir="ltr"
                                                   placeholder="e.g. Owner Story"
                                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                                {{ $isAr ? 'العنوان — عربي' : 'Title — Arabic' }}
                                            </label>
                                            <input type="text" name="owner_video_title_ar"
                                                   value="{{ old('owner_video_title_ar', $ownerVideoTitleAr) }}"
                                                   maxlength="120" dir="rtl"
                                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                                {{ $isAr ? 'العنوان — إنجليزي' : 'Title — English' }}
                                            </label>
                                            <input type="text" name="owner_video_title_en"
                                                   value="{{ old('owner_video_title_en', $ownerVideoTitleEn) }}"
                                                   maxlength="120" dir="ltr"
                                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                                {{ $isAr ? 'الوصف — عربي' : 'Description — Arabic' }}
                                            </label>
                                            <textarea name="owner_video_desc_ar" rows="3"
                                                      maxlength="400" dir="rtl"
                                                      class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition resize-none">{{ old('owner_video_desc_ar', $ownerVideoDescAr) }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                                {{ $isAr ? 'الوصف — إنجليزي' : 'Description — English' }}
                                            </label>
                                            <textarea name="owner_video_desc_en" rows="3"
                                                      maxlength="400" dir="ltr"
                                                      class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition resize-none">{{ old('owner_video_desc_en', $ownerVideoDescEn) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end mt-4">
                                    <button type="submit"
                                            class="bg-rose-gold text-white text-sm font-semibold px-5 py-2 rounded-xl hover:bg-rose-gold-dark transition">
                                        {{ __('web.save_changes') }}
                                    </button>
                                </div>
                            </form>

                    </div>{{-- end settings column --}}
                </div>
            </div>
        </div>
        @endif

    </div>{{-- end max-w-3xl --}}

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // Logo file picker — show filename
        (function () {
            var input = document.getElementById('logo-input');
            var lbl   = document.getElementById('logo-file-label');
            if (!input || !lbl) return;
            var def = lbl.textContent.trim();
            input.addEventListener('change', function () {
                lbl.textContent = this.files[0] ? this.files[0].name : def;
            });
        }());

        // Color pickers ↔ text inputs ↔ swatch sync + live CSS-variable preview
        (function () {
            var cssVarMap = {
                'primary-color':       '--color-rose-gold',
                'primary-color-light': '--color-rose-gold-light',
                'primary-color-dark':  '--color-rose-gold-dark',
                'soft-pink-color':     '--color-soft-pink',
                'soft-pink-light-color': '--color-soft-pink-light',
                'secondary-color':     '--color-beige',
                'secondary-color-dark':'--color-beige-dark',
                'salon-text-color':    '--color-salon-text',
            };
            Object.keys(cssVarMap).forEach(function (key) {
                var picker  = document.getElementById('cp-' + key);
                var text    = document.getElementById('ct-' + key);
                var swatch  = document.getElementById('cp-' + key + '-swatch');
                var cssVar  = cssVarMap[key];
                if (!picker || !text) return;
                function syncAll(hex) {
                    picker.value = hex;
                    text.value   = hex;
                    if (swatch) swatch.style.backgroundColor = hex;
                    document.documentElement.style.setProperty(cssVar, hex);
                }
                picker.addEventListener('input', function () { syncAll(picker.value); });
                text.addEventListener('input', function () {
                    if (/^#[0-9A-Fa-f]{6}$/.test(text.value)) syncAll(text.value);
                    else if (swatch) swatch.style.backgroundColor = text.value;
                });
            });
        }());

        // Social: disable toggle when URL is empty
        document.querySelectorAll('input[type="checkbox"][name*="social"]').forEach(function (checkbox) {
            var row      = checkbox.closest('.px-6');
            var urlInput = row ? row.querySelector('input[type="url"]') : null;
            if (!urlInput) return;

            function sync() {
                var empty = urlInput.value.trim() === '';
                checkbox.disabled = empty;
                if (empty) checkbox.checked = false;
            }
            sync();
            urlInput.addEventListener('input', sync);
            checkbox.addEventListener('change', function () {
                if (urlInput.value.trim() === '') {
                    checkbox.checked = false;
                    alert('{{ app()->getLocale() === "ar" ? "يجب إضافة رابط قبل التفعيل" : "Please add a URL before activating" }}');
                }
            });
        });

        // Hero image file picker — show filename
        (function () {
            var input = document.getElementById('hero-image-input');
            var lbl   = document.getElementById('hero-file-label');
            if (!input || !lbl) return;
            var def = lbl.textContent.trim();
            input.addEventListener('change', function () {
                lbl.textContent = this.files[0] ? this.files[0].name : def;
            });
        }());

        // Tour video file picker — show filename
        (function () {
            var input = document.getElementById('tour-video-input');
            var lbl   = document.getElementById('tour-video-label');
            if (!input || !lbl) return;
            var def = lbl.textContent.trim();
            input.addEventListener('change', function () {
                lbl.textContent = this.files[0] ? this.files[0].name : def;
            });
        }());

        // Hero images drag-and-drop reorder
        (function () {
            var grid = document.getElementById('hero-sortable');
            if (!grid) return;
            var dragging   = null;
            var csrf       = document.querySelector('meta[name="csrf-token"]').content;
            var reorderUrl = '{{ route("admin.hero-images.reorder") }}';

            grid.addEventListener('dragstart', function (e) {
                var card = e.target.closest('.hero-card');
                if (!card) return;
                dragging = card;
                requestAnimationFrame(function () { card.style.opacity = '0.45'; });
            });
            grid.addEventListener('dragend', function () {
                if (dragging) { dragging.style.opacity = '1'; dragging = null; }
                rebadge(); saveOrder();
            });
            grid.addEventListener('dragover', function (e) {
                e.preventDefault();
                var target = e.target.closest('.hero-card');
                if (!target || target === dragging) return;
                var rect  = target.getBoundingClientRect();
                grid.insertBefore(dragging, e.clientX > rect.left + rect.width / 2 ? target.nextSibling : target);
            });

            function rebadge() {
                grid.querySelectorAll('.hero-card').forEach(function (c, i) {
                    var b = c.querySelector('[data-order-badge]');
                    if (b) b.textContent = '#' + (i + 1);
                });
            }
            function saveOrder() {
                var ids = Array.from(grid.querySelectorAll('.hero-card')).map(function (c) {
                    return parseInt(c.dataset.id, 10);
                });
                fetch(reorderUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ ids: ids }),
                })
                .then(function (r) { return r.json(); })
                .then(function (d) { if (!d.ok) console.error('Reorder failed', d); })
                .catch(function () { alert('{{ __("web.hero_images_reorder_failed") }}'); });
            }
        }());
    });
    </script>

    {{-- Standalone form for logo deletion (outside branding form to avoid nesting) --}}
    <form id="delete-logo-form" method="POST" action="{{ route('admin.settings.logo.delete') }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    <script>
        function deleteLogo() {
            var msg = '{{ $isAr ? 'هل أنت متأكد من حذف الشعار؟' : 'Are you sure you want to delete the logo?' }}';
            if (confirm(msg)) {
                document.getElementById('delete-logo-form').submit();
            }
        }
    </script>

</x-layouts.admin>
