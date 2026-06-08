<x-layouts.admin :title="__('web.admin_dashboard')">

    <h1 class="text-2xl font-bold text-gray-800 mb-8">{{ __('web.admin_dashboard') }}</h1>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 gap-4 mb-8 max-w-sm">
        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-gray-500">
                    {{ app()->getLocale() === 'ar' ? 'الفئات' : 'Categories' }}
                </span>
                <span class="w-8 h-8 rounded-lg text-rose-gold bg-beige flex items-center justify-center text-sm">◑</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $totalCategories }}</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-gray-500">
                    {{ app()->getLocale() === 'ar' ? 'الخدمات' : 'Services' }}
                </span>
                <span class="w-8 h-8 rounded-lg text-blue-600 bg-blue-50 flex items-center justify-center text-sm">◆</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $totalServices }}</p>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 max-w-sm">
        <h2 class="text-base font-semibold text-gray-700 mb-4">
            {{ app()->getLocale() === 'ar' ? 'روابط سريعة' : 'Quick Links' }}
        </h2>
        <div class="space-y-2">
            @php
                $links = [
                    ['route' => 'admin.settings.show', 'icon' => '⚙', 'label_ar' => 'الإعدادات والألوان', 'label_en' => 'Settings & Branding'],
                    ['route' => 'admin.setup.index',   'icon' => '⬆', 'label_ar' => 'استيراد الخدمات',    'label_en' => 'Import Services'],
                    ['route' => 'admin.demo.index',    'icon' => '⚗', 'label_ar' => 'بيانات تجريبية',     'label_en' => 'Demo Data'],
                    ['route' => 'admin.profile.show',  'icon' => '◉', 'label_ar' => 'الملف الشخصي',       'label_en' => 'My Profile'],
                ];
            @endphp
            @foreach($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600
                          hover:bg-beige hover:text-rose-gold transition">
                    <span class="text-base shrink-0">{{ $link['icon'] }}</span>
                    {{ app()->getLocale() === 'ar' ? $link['label_ar'] : $link['label_en'] }}
                </a>
            @endforeach
        </div>
    </div>

</x-layouts.admin>
