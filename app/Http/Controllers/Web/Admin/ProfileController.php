<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domains\Auth\Actions\UpdateProfileAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('admin.profile.show', ['user' => auth()->user()]);
    }

    public function update(Request $request, UpdateProfileAction $action): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:users,email,'.auth()->id().'|max:100',
            'phone' => 'nullable|string|unique:users,phone,'.auth()->id().'|max:20',
            'avatar' => 'nullable|image|max:2048',
            'remove_avatar' => 'nullable|boolean',
        ]);

        $user = auth()->user();

        if ($request->boolean('remove_avatar') && $user->avatar) {
            Storage::disk(config('filesystems.default'))->delete($user->avatar);
            $user->update(['avatar' => null]);
            unset($data['avatar']);
        }

        $action->execute($user, $data);

        activity()->log('admin_profile_updated');

        return back()->with('success', __('messages.profile_updated'));
    }

    public function updatePassword(Request $request, UpdateProfileAction $action): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $action->execute(auth()->user(), $data);

        return back()->with('success', __('messages.profile_updated'));
    }
}
