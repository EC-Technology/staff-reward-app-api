<?php

namespace App\Http\Controllers\Api;

use App\Enum\ErrorCode;
use App\Enum\StatusCode;
use App\Http\Controllers\ApiController;
use App\Models\ActivationCode;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductRatingController extends ApiController {

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    /**
     * @throws ValidationException
     */
    public function rateVoucherUse(Request $request): JsonResponse {

        $rules = [
            'voucher_use_id' => 'required',
            'rating' => 'required|between:0.00,5.00',
            'comment'
        ];

        $postData = Validator::make($request->post(), $rules)->validate();

        //TODO:: check voucher use exist

        $productRating = ProductRating::query()->create([
            'user_id' => $request->user()->id,
            'product_id' => 1, // TODO:: from voucher use
            'voucher_use_id' => $postData['voucher_use_id'],
            'rating' => $postData['rating'],
            'user_comment' => empty($postData['comment']) ? null : $postData['comment'],
            'process_status' => ProductRating::PROCESS_STATUS_PENDING
        ]);

        return $this->successResponse($productRating);
    }


}
