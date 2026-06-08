<?php

namespace App\Domains\Auth\Http\Controllers;

use App\Domains\Auth\Actions\RegisterUserAction;
use App\Domains\Auth\Http\Requests\LoginRequest;
use App\Domains\Auth\Http\Requests\RegisterRequest;
use App\Domains\Auth\Http\Resources\UserResource;
use App\Domains\Auth\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request, RegisterUserAction $action): JsonResponse
    {
        $user = $action->execute($request->validated());
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->created([
            'user' => new UserResource($user),
            'token' => $token,
        ], __('messages.register_success'));
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $login = $request->input('login');

        $user = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? User::where('email', $login)->first()
            : User::where('phone', $login)->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return $this->error(__('messages.invalid_credentials'), 401);
        }

        if (! $user->is_active) {
            return $this->error(__('messages.account_inactive'), 403);
        }

        $user->tokens()->where('name', 'auth_token')->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->log('user_logged_in');

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
        ], __('messages.login_success'));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        activity()
            ->performedOn($request->user())
            ->causedBy($request->user())
            ->log('user_logged_out');

        return $this->success(null, __('messages.logout_success'));
    }
}
