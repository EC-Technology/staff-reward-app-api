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

class UserController extends ApiController {

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    public function getProfile(Request $request): JsonResponse {
        return $this->successResponse($request->user());
    }

    /**
     * @throws ValidationException
     */
    public function updateProfile(Request $request): JsonResponse {
        $rules = [];

        $postData = Validator::make($request->post(), $rules)->validate();


        $user = $request->user();


        $user->update($postData);

        return $this->successResponse($user);
    }
}
