<x-layouts.admin :title="__('web.admin_bookings')">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('web.admin_bookings') }}</h1>
    </div>

    @livewire('admin.booking-table')

</x-layouts.admin>
