<x-layouts.admin :title="__('web.customers')">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            {{ __('web.customers') }}
        </h1>
    </div>
    @livewire('admin.customer-table')
</x-layouts.admin>
