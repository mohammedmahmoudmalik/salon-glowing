<?php

namespace App\Services;

use App\Domains\Service\Models\Service;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_KEY = 'salon_cart';

    public function add(int $serviceId): void
    {
        $ids = $this->ids();
        if (! in_array($serviceId, $ids)) {
            $ids[] = $serviceId;
            session([self::SESSION_KEY => $ids]);
        }
    }

    public function remove(int $serviceId): void
    {
        $ids = array_values(array_filter($this->ids(), fn ($id) => $id !== $serviceId));
        session([self::SESSION_KEY => $ids]);
    }

    public function ids(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public function items(): Collection
    {
        $ids = $this->ids();

        if (empty($ids)) {
            return collect();
        }

        return Service::active()->with('category')->whereIn('id', $ids)->get();
    }

    public function total(): float
    {
        return (float) $this->items()->sum('price');
    }

    public function totalDuration(): int
    {
        return (int) $this->items()->sum('duration_minutes');
    }

    public function count(): int
    {
        return count($this->ids());
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function has(int $serviceId): bool
    {
        return in_array($serviceId, $this->ids());
    }
}
