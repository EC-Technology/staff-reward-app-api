<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\MerchantApiController;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class MerchantController extends MerchantApiController {

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    public function create(Request $request): JsonResponse
    {
        $postData = $request->post();

        $file = $request->file('image_file');

        if (empty($file)) {
            $this->errorResponse("image_file is required");
        }

        $fileName = Str::uuid() . '.' . $file->extension();
        Storage::disk('s3')->put('images/merchant/' . $fileName, $file->getContent());

        $url = Storage::disk('s3')->url("images/merchant/$fileName");

        $merchant = new Merchant();
        $merchant['status'] = Merchant::STATUS_ACTIVE;
        $merchant['code'] = $postData['code'];
        $merchant['name'] = $postData['name'];
        $merchant['theme_color'] = $postData['theme_color'];
        $merchant['image_url'] = $url;

        $merchant->save();

        return $this->successResponse($merchant);
    }

    /**
     * @throws ValidationException
     */
    public function createMerchantUser(Request $request) : JsonResponse
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:user',
            'password' => 'required|min:8',
            'merchant_id' => 'required'
        ];

        $postData = Validator::make($request->post(), $rules)->validate();

        $merchant = Merchant::query()
            ->where('id', $postData['merchant_id'])
            ->where('status', Merchant::STATUS_ACTIVE)
            ->first();

        if (empty($merchant)) {
            return $this->errorResponse('merchant not found');
        }

        $merchantUser = new User();
        $merchantUser['status'] = User::STATUS_ACTIVE;
        $merchantUser['role'] = User::ROLE_MERCHANT_STAFF;
        $merchantUser['name'] = $postData['name'];
        $merchantUser['email'] = $postData['email'];
        $merchantUser['merchant_id'] = $merchant->id;
        $merchantUser['password'] = Hash::make($postData['password']);

        $merchantUser->save();

        return $this->successResponse(null);
    }

}
