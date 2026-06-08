<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (! Auth::attempt([$field => $login, 'password' => $request->password], $request->boolean('remember'))) {
            return back()->withErrors(['login' => __('messages.invalid_credentials')])->withInput(['login' => $login]);
        }

        $user = Auth::user();
        if (! $user->is_active) {
            Auth::logout();

            return back()->withErrors(['login' => __('messages.account_inactive')]);
        }

        $request->session()->regenerate();

        if ($user->hasAnyRole(['admin', 'receptionist'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('owner')) {
            return redirect()->route('admin.owner.dashboard');
        }

        return redirect()->intended(route('home'));
    }
}
