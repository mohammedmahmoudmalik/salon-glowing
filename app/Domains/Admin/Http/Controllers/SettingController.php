<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Admin\Http\Requests\UpdateSettingsRequest;
use App\Domains\Admin\Models\Setting;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class SettingController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        Gate::authorize('view', Setting::class);

        return $this->success($this->currentSettings());
    }

    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        Gate::authorize('update', Setting::class);

        foreach ($request->validated() as $key => $value) {
            Setting::set($key, $value);
        }

        activity()->log('settings_updated');

        return $this->success($this->currentSettings(), __('messages.record_updated'));
    }

    private function currentSettings(): array
    {
        return [
            'salon_working_hours' => Setting::get('salon_working_hours'),
            'booking_buffer_minutes' => Setting::get('booking_buffer_minutes'),
        ];
    }
}
