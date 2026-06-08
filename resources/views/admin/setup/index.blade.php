<x-layouts.admin :title="__('web.setup_services')">

    <div class="max-w-4xl">

        {{-- Page header --}}
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <h1 class="text-2xl font-bold text-gray-800">{{ __('web.setup_services') }}</h1>

            <form action="{{ route('admin.setup.clear') }}" method="POST"
                  x-data
                  @submit.prevent="if(confirm('{{ __('web.setup_clear_confirm') }}')) $el.submit()">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 border border-red-200 text-red-500 text-sm font-medium px-4 py-2 rounded-xl hover:bg-red-50 hover:border-red-400 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ __('web.setup_clear_all') }}
                </button>
            </form>
        </div>

        {{-- Tabs --}}
        <div x-data="{ tab: 'manual' }">

            {{-- Tab buttons --}}
            <div class="flex gap-1 bg-gray-100 p-1 rounded-xl mb-6 w-fit">
                <button type="button"
                        @click="tab = 'manual'"
                        :class="tab === 'manual' ? 'bg-white text-rose-gold shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="px-5 py-2 rounded-lg text-sm font-medium transition">
                    {{ __('web.setup_quick_entry') }}
                </button>
                <button type="button"
                        @click="tab = 'csv'"
                        :class="tab === 'csv' ? 'bg-white text-rose-gold shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="px-5 py-2 rounded-lg text-sm font-medium transition">
                    {{ __('web.setup_csv_import') }}
                </button>
            </div>

            {{-- Tab: Manual entry --}}
            <div x-show="tab === 'manual'" x-cloak>
                <livewire:service-quick-setup />
            </div>

            {{-- Tab: CSV Import --}}
            <div x-show="tab === 'csv'" x-cloak class="space-y-4">

                {{-- Download template --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-700 mb-1">
                        {{ __('web.setup_download_template') }}
                    </h2>
                    <p class="text-xs text-gray-400 mb-4">{{ __('web.setup_csv_hint') }}</p>
                    <a href="{{ route('admin.setup.template') }}"
                       class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 text-sm px-4 py-2 rounded-xl hover:border-rose-gold hover:text-rose-gold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        {{ __('web.setup_download_template') }}
                    </a>
                </div>

                {{-- Upload CSV --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-700 mb-4">{{ __('web.setup_upload_csv') }}</h2>

                    @if($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl space-y-1">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('admin.setup.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center gap-3 flex-wrap">
                            <input type="file"
                                   name="csv_file"
                                   accept=".csv,.txt"
                                   class="text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-beige file:text-rose-gold hover:file:bg-beige-dark">
                            <button type="submit"
                                    class="bg-rose-gold text-white text-sm font-semibold px-5 py-2 rounded-xl hover:bg-rose-gold-dark transition">
                                {{ __('web.setup_import_btn') }}
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        {{-- Export JSON fixture — دائماً ظاهر بغض النظر عن التبويبة النشطة --}}
        <div class="mt-4 bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-1">{{ __('web.setup_export_fixture') }}</h2>
            <p class="text-xs text-gray-400 mb-4">{{ __('web.setup_export_hint') }}</p>
            <form action="{{ route('admin.setup.export') }}" method="POST">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 text-sm px-4 py-2 rounded-xl hover:border-rose-gold hover:text-rose-gold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    {{ __('web.setup_export_fixture') }}
                </button>
            </form>
        </div>

    </div>

</x-layouts.admin>
