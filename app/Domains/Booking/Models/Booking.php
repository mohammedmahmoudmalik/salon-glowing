<?php

namespace App\Domains\Booking\Models;

use App\Domains\Auth\Models\Customer;
use App\Domains\Auth\Models\User;
use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Review\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'customer_id', 'booking_date', 'start_time', 'end_time',
        'status', 'buffer_minutes', 'total_price',
        'confirmed_at', 'cancelled_at', 'cancelled_by_user_id', 'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'status' => BookingStatus::class,
            'total_price' => 'decimal:2',
            'buffer_minutes' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }
}
