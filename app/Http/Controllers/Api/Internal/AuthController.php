<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\MerchantApiController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class AuthController extends MerchantApiController {

    /**
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:user',
            'password' => 'required|min:16'
        ];

        $postData = Validator::make($request->post(), $rules)->validate();

        $user = User::query()->create([
            'name' => $postData['name'],
            'email' => $postData['email'],
            'password' => Hash::make($postData['password']),
            'role' => User::ROLE_INTERNAL_ADMIN,
            'status' => User::STATUS_ACTIVE
        ]);

        return $this->successResponse(null);
    }

    /**
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:16'
        ];

        $postData = Validator::make($request->post(), $rules)->validate();

        $user = User::query()
            ->where('email', $postData['email'])
            ->where('role', User::ROLE_INTERNAL_ADMIN)
            ->where('status', User::STATUS_ACTIVE)
            ->first();

        if (empty($user) || !Hash::check($postData['password'], $user->getAuthPassword())) {
            return $this->errorResponse('user not found');
        }

        $token = $user->createToken($request->header('User-Agent'))->plainTextToken;

        return $this->successResponse(['access_token' => $token]);
    }

}
