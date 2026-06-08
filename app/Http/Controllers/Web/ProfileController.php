<?php

namespace App\Http\Controllers\Web;

use App\Domains\Auth\Actions\UpdateProfileAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('profile.show', ['user' => auth()->user()]);
    }

    public function update(Request $request, UpdateProfileAction $action): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:users,email,'.auth()->id().'|max:100',
            'phone' => 'nullable|string|unique:users,phone,'.auth()->id().'|max:20',
            'avatar' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'nullable|string',
        ]);

        $action->execute(auth()->user(), $data);

        return back()->with('success', __('messages.profile_updated'));
    }
}
