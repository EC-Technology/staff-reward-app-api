<?php

namespace App\Http\Controllers\Api;

use App\Enum\ErrorCode;
use App\Enum\StatusCode;
use App\Http\Controllers\ApiController;
use App\Models\ActivationCode;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductController extends ApiController {

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    public function queryProducts(Request $request): JsonResponse {
        $categoryId = $request->input('category_id');

        $query = Product::query();

        if (!empty($categoryId)) {
            $query->where(['category_id' => $categoryId]);
        }

        $products = $query->get();

        return $this->successResponse($products);

    }

    public function queryProductDetails(Request $request, $productId): JsonResponse {

        $product = Product::query()->where('id', $productId)->first();

        return $this->successResponse($product);
    }


}
