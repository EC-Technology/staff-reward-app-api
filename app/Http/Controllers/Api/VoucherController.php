<?php

namespace App\Http\Controllers\Api;

use App\Enum\ErrorCode;
use App\Enum\StatusCode;
use App\Http\Controllers\ApiController;
use App\Models\ActivationCode;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\ProductGroupProduct;
use App\Models\TokenTransaction;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class VoucherController extends ApiController {

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    public function queryVouchers(Request $request): JsonResponse {

        $userId = $request->user()->id;

        $vouchers = Voucher::query()->where('owner_user_id', $userId)->get();

        foreach ($vouchers as $voucher) {
            $voucher->product = Product::query()->where('id', $voucher->product_id)->first();
        }



        return $this->successResponse($vouchers);
    }

    public function queryVoucherDetail(Request $request, $voucherId): JsonResponse {
        $userId = $request->user()->id;

        $voucher = Voucher::query()->where('id', $voucherId)->where('owner_user_id', $userId)->first();

        if (!empty($voucher)) {
            $product = Product::query()->where('id', $voucher->product_id)->first();
            $voucher->product = $product;
        }

        return $this->successResponse($voucher);
    }

    /**
     * @throws ValidationException
     */
    public function redeemVoucher(Request $request): JsonResponse {
        $userId = $request->user()->id;

        $rules = [
            'product_id' => 'required'
        ];

        $postData = Validator::make($request->post(), $rules)->validate();

        $product = Product::query()->where('id', $postData['product_id'])->first();

        if (empty($product)) {
            return $this->errorResponse('product not exist');
        }


        //TODO:: check token balance

        $voucher = Voucher::query()->where('product_id', $postData['product_id'])->where('status', Voucher::STATUS_ACTIVE)->first();

        if (empty($voucher)) {
            return $this->errorResponse('voucher out of stock');
        }

        $voucher->update([
            'status' => Voucher::STATUS_ASSIGNED,
            'owner_user_id' => $userId,
            'assign_time' => now()
        ]);

        $transaction = TokenTransaction::query()->create([
            'user_id' => $userId,
            'type' => 2,
            'description' => $product->name_tc,
            'transaction_time' => time(),
            'value' => -($product->coin_value)
        ]);

        return $this->successResponse([
            'voucher' => $voucher,
            'transaction' => $transaction
        ]);
    }
}
