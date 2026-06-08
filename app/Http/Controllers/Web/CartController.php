<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index(): View
    {
        return view('cart.index', [
            'items' => $this->cart->items(),
            'total' => $this->cart->total(),
            'totalDuration' => $this->cart->totalDuration(),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate(['service_id' => 'required|integer|exists:services,id']);

        $this->cart->add((int) $request->service_id);

        return back()->with('success', __('web.cart_added'));
    }

    public function remove(int $serviceId): RedirectResponse
    {
        $this->cart->remove($serviceId);

        return back()->with('success', __('web.cart_removed'));
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return back();
    }
}
