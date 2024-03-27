<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\MerchantApiController;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\User;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class VoucherController extends MerchantApiController {

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    /**
     * @throws ValidationException
     */
    public function addExternal(Request $request): JsonResponse
    {

        $rules = [
            'product_id' => 'required',
            'external_codes' => 'required'
        ];

        $postData = Validator::make($request->post(), $rules)->validate();

        if (!is_array($postData['external_codes'])) {
            return $this->errorResponse('external_code is not array');
        }

        $product = Product::query()
            ->where('id', $postData['product_id'])
            ->first();

        if (empty($product)) {
            return $this->errorResponse('product not found');
        }

        $vouchers = [];

        foreach ($postData['external_codes'] as $codeExternal) {
            $voucher = new Voucher();
            $voucher['merchant_id'] = $product->merchant_id;
            $voucher['product_id'] = $product->id;
            $voucher['code_external'] = $codeExternal;
            $voucher['code'] = Str::uuid();
            $voucher['use_begin_time'] = $product->start_time;
            $voucher['use_expiry_time'] = $product->end_time;
            $voucher['status'] = Voucher::STATUS_ACTIVE;

            $voucher->save();

            $voucher = $voucher->refresh();

            $voucher['code_reference'] = $voucher['id'] . $voucher['merchant_id'] . Str::random(6);
            $voucher->save();

            $vouchers[] = $voucher;
        }

        return $this->successResponse($vouchers);
    }

}
