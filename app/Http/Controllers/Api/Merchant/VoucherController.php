<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Enum\ErrorCode;
use App\Enum\StatusCode;
use App\Http\Controllers\MerchantApiController;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\VoucherUseHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class VoucherController extends MerchantApiController {

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    public function getVoucher(Request $request): JsonResponse
    {
        $postData = $request->post();
        $merchantId = $request->user()->merchant_id;

        if (empty($postData['code'])) {
            return $this->errorResponse('code is required');
        }

        $voucher = Voucher::query()
            ->where('code', $postData['code'])
            ->where('merchant_id', $merchantId)
            ->first();

        if (empty($voucher)) {
            return $this->errorResponse();
        }

        if ($voucher->status == Voucher::STATUS_USED) {
            return $this->errorResponse('voucher used', StatusCode::HTTP_BAD_REQUEST, ErrorCode::VOUCHER_USED);
        }

        if ($voucher->status != Voucher::STATUS_ASSIGNED) {
            return $this->errorResponse();
        }

        if (!empty($voucher->use_expiry_time)) {
            if (strtotime($voucher->use_expiry_time) < time()) {
                return $this->errorResponse('voucher expired', StatusCode::HTTP_BAD_REQUEST, ErrorCode::VOUCHER_EXPIRED);
            }
        }

        $product = Product::query()
            ->where('merchant_id', $merchantId)
            ->where('id', $voucher->product_id)
            ->first();

        if (empty($product)) {
            return $this->configError();
        }

        unset($voucher->code_external);

        return $this->successResponse([
            'voucher' => $voucher,
            'product' => $product
        ]);
    }

    public function useVoucher(Request $request): JsonResponse
    {
        $postData = $request->post();
        $user = $request->user();

        if (empty($postData['code'])) {
            return $this->errorResponse('code is required');
        }

        $voucher = Voucher::query()
            ->where('code', $postData['code'])
            ->where('merchant_id', $user->merchant_id)
            ->where('status', Voucher::STATUS_ASSIGNED)
            ->first();

        if (empty($voucher)) {
            return $this->objectNotExist('voucher');
        }

        $usedTime = now();

        $history = new VoucherUseHistory();
        $history['merchant_id'] = $user->merchant_id;
        $history['merchant_user_id'] = $user->id;
        $history['product_id'] = $voucher->product_id;
        $history['voucher_id'] = $voucher->id;
        $history['reference_number'] = $request->post('reference_number');
        $history['internal_remarks'] = $request->post('internal_remarks');
        $history['status'] = VoucherUseHistory::STATUS_ACTIVE;
        $history['used_time'] = $usedTime;

        if (!$history->save()) {
            return $this->errorResponse('cannot create history record', StatusCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        $voucher->status = Voucher::STATUS_USED;
        $voucher->used_time = $usedTime;
        if (!$voucher->save()) {
            $history->delete();
            return $this->errorResponse('cannot update voucher record', StatusCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->successResponse($history);
    }

    public function queryUseHistories(Request $request): JsonResponse
    {
        $user = $request->user();

        //TODO:: more filter
        $histories = VoucherUseHistory::query()
            ->where('merchant_id', $user->merchant_id)
            ->where('status', VoucherUseHistory::STATUS_ACTIVE)
            ->orderBy('used_time', 'desc')
            ->get();

        $productIds = [];
        $voucherIds = [];

        foreach ($histories as $history) {
            $productIds[] = $history->product_id;
            $voucherIds[] = $history->voucher_id;
        }

        $productIds = array_unique($productIds);
        $voucherIds = array_unique($voucherIds);

        $productIdName = [];
        $voucherIdReferenceCode = [];
        if (!empty($productIds)) {
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->get();

            $vouchers = Voucher::query()
                ->whereIn('id', $voucherIds)
                ->get();



            foreach ($products as $product) {
                $productIdName[$product->id] = $product->name;
            }

            foreach ($vouchers as $voucher) {
                $voucherIdReferenceCode[$voucher->id] = $voucher->code_reference;
            }
        }

        foreach ($histories as $history) {
            $history->product_name = $productIdName[$history->product_id];
            $history->voucher_code_reference = $voucherIdReferenceCode[$history->voucher_id];
        }

        return $this->successResponse($histories);

    }

}
