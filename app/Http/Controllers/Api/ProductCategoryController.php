<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class ProductCategoryController extends ApiController {

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    public function getCategory(): JsonResponse
    {
        $categories = Category::query()
            ->where('status', Category::STATUS_ACTIVE)
            ->get();

        return $this->successResponse($categories);
    }

}
