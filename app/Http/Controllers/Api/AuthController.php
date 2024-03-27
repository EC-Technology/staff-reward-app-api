<?php

namespace App\Http\Controllers\Api;

use App\Enum\ErrorCode;
use App\Enum\StatusCode;
use App\Http\Controllers\ApiController;
use App\Models\ActivationCode;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiController {

    /**
     * @throws ValidationException
     */
    public function registerWithActivationCode(Request $request): JsonResponse {

        $rules = [
            'code' => 'required',
            'password' => 'required',
            'confirm_password' => 'required',
            'email' => 'email',
            'login_username' => '',
        ];

        $postData = Validator::make($request->post(), $rules)->validate();

        $activationCode = ActivationCode::query()
            ->where('code', $postData['code'])
            ->where('status', ActivationCode::STATUS_ACTIVE)
            ->first();

        if (empty($activationCode)) {
            return $this->errorResponse('activation code', StatusCode::HTTP_BAD_REQUEST, ErrorCode::OBJECT_NOT_EXIST);
        }

        if (!empty($activationCode->expiry_time) && $activationCode->expiry_time <= time()) {
            return $this->errorResponse('activation code', StatusCode::HTTP_BAD_REQUEST, ErrorCode::OBJECT_NOT_EXIST);
        }

        $company = Company::query()
            ->where('id', $activationCode->company_id)
            ->where('status', Company::STATUS_ACTIVE)
            ->first();

        if (empty($company)) {
            return $this->errorResponse('company', StatusCode::HTTP_BAD_REQUEST, ErrorCode::OBJECT_NOT_EXIST);
        }

        if (empty($activationCode->login_user_name) && empty($postData['login_username'])) {
            return $this->errorResponse('login username', StatusCode::HTTP_BAD_REQUEST, ErrorCode::REQUEST_PARAMETER_MISSING_OR_INCORRECT);
        }

        if (empty($activationCode->email) && empty($postData['email'])) {
            return $this->errorResponse('email', StatusCode::HTTP_BAD_REQUEST, ErrorCode::REQUEST_PARAMETER_MISSING_OR_INCORRECT);
        }

        $user = User::query()->create([
            'company_id' => $company->id,
            'name' => $postData['login_username'] ?: $activationCode->login_user_name ,
            'email' => $postData['email'] ?: $activationCode->email,
            'password' => Hash::make($postData['password']),
            'role' => User::ROLE_COMPANY_USER,
            'status' => User::STATUS_ACTIVE
        ]);

        $activationCode->update([
            'status' => ActivationCode::STATUS_USED,
            'company_user_id' => $user['id'],
            'used_time' => time()
        ]);

        return $this->successResponse();
    }

    /**
     * @throws ValidationException
     */
    public function loginWithUsernamePassword(Request $request): JsonResponse {

        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        $postData = Validator::make($request->post(), $rules)->validate();

        $user = User::query()
            ->where('name', $postData['username'])
            ->where('role', User::ROLE_COMPANY_USER)
            ->where('status', User::STATUS_ACTIVE)
            ->first();

        if (empty($user) || !Hash::check($postData['password'], $user->getAuthPassword())) {
            return $this->errorResponse(null, StatusCode::HTTP_UNAUTHORIZED, ErrorCode::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken($request->header('User-Agent'))->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'access_token' => $token
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function loginWithEmailPassword(Request $request): JsonResponse {

        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $postData = Validator::make($request->post(), $rules)->validate();

        $user = User::query()
            ->where('email', $postData['email'])
            ->where('role', User::ROLE_COMPANY_USER)
            ->where('status', User::STATUS_ACTIVE)
            ->first();


        if (empty($user) || !Hash::check($postData['password'], $user->getAuthPassword())) {
            return $this->errorResponse(null, StatusCode::HTTP_UNAUTHORIZED, ErrorCode::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken($request->header('User-Agent'))->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'access_token' => $token
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function updatePassword(Request $request): JsonResponse {
        $rules = [
            'password' => 'required'
        ];

        $postData = Validator::make($request->post(), $rules)->validate();

        $user = $request->user()->update([
            'password' => Hash::make($postData['password'])
        ]);

        return $this->successResponse();
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse();
    }
}
