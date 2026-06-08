<?php

namespace App\Livewire;

use App\Domains\Offer\Services\ActiveOfferService;
use App\Domains\Service\Models\Service;
use App\Services\CartService;
use Livewire\Component;

class ServiceCard extends Component
{
    public Service $service;

    public function addToCart(): void
    {
        $cart = app(CartService::class);
        $cart->add($this->service->id);
        $this->dispatch('cart-updated', count: $cart->count());
    }

    public function removeFromCart(): void
    {
        $cart = app(CartService::class);
        $cart->remove($this->service->id);
        $this->dispatch('cart-updated', count: $cart->count());
    }

    public function render()
    {
        $offerService = app(ActiveOfferService::class);
        $activeOffer = $offerService->getForService($this->service);
        $finalPrice = $offerService->calculateFinalPrice($this->service, $activeOffer);
        $inCart = app(CartService::class)->has($this->service->id);

        return view('livewire.service-card', [
            'activeOffer' => $activeOffer,
            'finalPrice' => $finalPrice,
            'inCart' => $inCart,
        ]);
    }
}
