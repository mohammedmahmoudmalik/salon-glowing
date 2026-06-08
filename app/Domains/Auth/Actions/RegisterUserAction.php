<?php

namespace App\Domains\Auth\Actions;

use App\Domains\Auth\Models\Customer;
use App\Domains\Auth\Models\User;
use Illuminate\Support\Facades\DB;

class RegisterUserAction
{
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'],
                'is_active' => true,
            ]);

            $user->assignRole('customer');

            Customer::create(['user_id' => $user->id]);

            activity()
                ->performedOn($user)
                ->causedBy($user)
                ->withProperties(['role' => 'customer'])
                ->log('user_registered');

            return $user;
        });
    }
}
