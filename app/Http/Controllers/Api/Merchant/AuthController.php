<?php

namespace App\Http\Controllers\Api\Merchant;

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
    public function login(Request $request): JsonResponse
    {
        $rule = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $postData = Validator::make($request->post(), $rule)->validate();

        $user = User::query()
            ->where('email', $postData['email'])
            ->where('status', User::STATUS_ACTIVE)
            ->where('role', User::ROLE_MERCHANT_STAFF)
            ->first();

        if (empty($user) || !Hash::check($postData['password'], $user->password)) {
            return $this->errorResponse();
        }

        $token = $user->createToken($request->header('User-Agent'))->plainTextToken;

        return $this->successResponse([
            'access_token' => $token,
            'name' => $user->name,
            'email' => $user->email
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(null);
    }


}
