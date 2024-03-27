<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\MerchantApiController;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class GuestController extends MerchantApiController {

    public function getMerchantData(Request $request, $code): JsonResponse
    {
        if (empty($code)) {
            return $this->errorResponse('code is required');
        }

        $merchant = Merchant::query()
            ->where('code', $code)
            ->where('status', Merchant::STATUS_ACTIVE)
            ->first();

        if (empty($merchant)) {
            return $this->objectNotExist('code');
        }

        unset($merchant->id, $merchant->code, $merchant->status, $merchant->created_time, $merchant->updated_time);

        return $this->successResponse($merchant);
    }


}
