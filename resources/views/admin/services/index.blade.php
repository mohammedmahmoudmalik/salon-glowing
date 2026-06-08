<x-layouts.admin :title="__('web.admin_services')">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('web.admin_services') }}</h1>
        <a href="{{ route('admin.services.create') }}"
           class="bg-rose-gold text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-rose-gold-dark transition">
            {{ __('web.add_new') }}
        </a>
    </div>

    {{-- Filters --}}
    <div class="flex gap-2 flex-wrap mb-4">
        <input
            id="svc-search"
            type="text"
            value="{{ $search }}"
            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث باسم الخدمة...' : 'Search services...' }}"
            class="flex-1 min-w-48 border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                   focus:outline-none focus:border-rose-gold focus:ring-1 focus:ring-rose-gold">

        <select
            id="svc-category"
            class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-600
                   focus:outline-none focus:border-rose-gold focus:ring-1 focus:ring-rose-gold">
            <option value="">
                {{ app()->getLocale() === 'ar' ? 'كل الفئات' : 'All Categories' }}
            </option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected($categoryId == $cat->id)>
                    {{ app()->getLocale() === 'ar' ? $cat->name_ar : $cat->name_en }}
                </option>
            @endforeach
        </select>
    </div>

    <div id="svc-results">
        @include('admin.services._table')
    </div>

    <script>
        (function () {
            const searchInput = document.getElementById('svc-search');
            const categorySelect = document.getElementById('svc-category');
            const baseUrl = '{{ route('admin.services.index') }}';
            let debounceTimer;

            function fetchResults() {
                const params = new URLSearchParams();
                const search = searchInput.value.trim();
                const category = categorySelect.value;
                if (search) params.set('search', search);
                if (category) params.set('category', category);

                const url = params.toString() ? baseUrl + '?' + params.toString() : baseUrl;

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (res) { return res.text(); })
                    .then(function (html) {
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const newResults = doc.getElementById('svc-results');
                        if (newResults) {
                            document.getElementById('svc-results').innerHTML = newResults.innerHTML;
                        }
                        history.replaceState(null, '', url);
                    });
            }

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchResults, 400);
            });

            categorySelect.addEventListener('change', fetchResults);
        })();
    </script>

</x-layouts.admin>
