<?php

namespace App\Domains\Offer\Enums;

enum DiscountType: string
{
    case Percentage = 'percentage';
    case Fixed = 'fixed';

    public function label(): string
    {
        return __('web.' . $this->value);
    }
}
