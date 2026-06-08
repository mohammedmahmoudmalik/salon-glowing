<?php

namespace App\Domains\Auth\Http\Controllers;

use App\Domains\Auth\Actions\UpdateProfileAction;
use App\Domains\Auth\Http\Requests\UpdateProfileRequest;
use App\Domains\Auth\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponse;

    public function show(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()));
    }

    public function update(UpdateProfileRequest $request, UpdateProfileAction $action): JsonResponse
    {
        $user = $action->execute($request->user(), $request->validated());

        return $this->success(new UserResource($user), __('messages.profile_updated'));
    }
}
