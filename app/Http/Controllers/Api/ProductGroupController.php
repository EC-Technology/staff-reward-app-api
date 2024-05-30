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
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductGroupController extends ApiController {

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    public function queryProductGroups(Request $request): JsonResponse {
        $isIncludeProductInfo = $request->input('include_product_info');
        $displayLocation = $request->input('display_location');




        $query = ProductGroup::query();

        if (!$displayLocation) {
            $query->where('display_location', $displayLocation);
        }

        $productGroups = $query->get();



        if (!empty($isIncludeProductInfo) && $isIncludeProductInfo == 1) {
            foreach($productGroups as $productGroup) {
                $productGroup->products = [];

                $productGroupProducts = ProductGroupProduct::query()->where('product_group_id', $productGroup->id)->get();


                if (!empty($productGroupProducts)) {
                    $productIds = $productGroupProducts->pluck('product_id');
                    $productGroup->products = Product::query()->whereIn('id', $productIds)->get();
                }

            }
        }



        return $this->successResponse($productGroups);
    }


}
