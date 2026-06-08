<?php

use App\Domains\Admin\Models\Setting;

if (! function_exists('currency')) {
    /**
     * Returns the salon's currency symbol based on the configured country.
     * Reads from cached Settings so there is no extra DB hit per request.
     */
    function currency(): string
    {
        $country = Setting::get('salon_country', 'SA');
        $isAr    = app()->getLocale() === 'ar';

        return match ($country) {
            'AE'    => $isAr ? 'د.إ' : 'AED',
            'EG'    => $isAr ? 'ج.م' : 'EGP',
            default => $isAr ? 'ر.س' : 'SAR',
        };
    }
}
