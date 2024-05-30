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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RewardTokenController extends ApiController {

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    public function queryTokenTransactions(Request $request): JsonResponse {

        $userId = $request->user()->id;

        $transactions = TokenTransaction::query()->where('user_id', $userId)->get();

        return $this->successResponse($transactions);
    }

    //TODO:: edit new balance handling
    public function queryBalance(Request $request): JsonResponse {

        $userId = $request->user()->id;

        $transactions = TokenTransaction::query()->where('user_id', $userId)->get();

        $balance = 0;

        foreach ($transactions as $transaction) {
            $balance += $transaction->value;
        }

        return $this->successResponse($balance);

    }


}
