<?php

namespace App\Http\Controllers\Api;

use App\Enum\ErrorCode;
use App\Enum\StatusCode;
use App\Http\Controllers\ApiController;
use App\Models\ActivationCode;
use App\Models\Banner;
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

class BannerController extends ApiController {

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    public function queryBanners(Request $request): JsonResponse {
        $group = $request->input('group');

        if (empty($group)) {
            return $this->errorResponse('group is required');
        }

        $banners = Banner::query()->where('group', $group)->get();

        return $this->successResponse($banners);
    }


}
