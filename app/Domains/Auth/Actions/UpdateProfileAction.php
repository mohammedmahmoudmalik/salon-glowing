<?php

namespace App\Domains\Auth\Actions;

use App\Domains\Auth\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UpdateProfileAction
{
    public function execute(User $user, array $data): User
    {
        if (isset($data['password'])) {
            if (! Hash::check($data['current_password'] ?? '', $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => [__('messages.invalid_credentials')],
                ]);
            }
        }

        unset($data['current_password']);

        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            $disk = config('filesystems.default');
            if ($user->avatar) {
                Storage::disk($disk)->delete($user->avatar);
            }
            $data['avatar'] = $data['avatar']->store('avatars', $disk);
        }

        $fillable = array_filter(
            array_intersect_key($data, array_flip(['name', 'email', 'phone', 'avatar', 'password'])),
            fn ($v) => $v !== null
        );

        $user->update($fillable);

        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->log('profile_updated');

        return $user->fresh();
    }
}
