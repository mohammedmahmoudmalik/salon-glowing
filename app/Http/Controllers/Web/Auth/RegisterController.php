<?php

namespace App\Http\Controllers\Web\Auth;

use App\Domains\Auth\Actions\RegisterUserAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function show(): View
    {
        return view('auth.register');
    }

    public function store(Request $request, RegisterUserAction $action): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:users,email|max:100',
            'phone' => 'nullable|string|unique:users,phone|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (empty($data['email']) && empty($data['phone'])) {
            return back()->withErrors(['email' => __('messages.validation_error')])->withInput();
        }

        $user = $action->execute($data);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', __('messages.register_success'));
    }
}
