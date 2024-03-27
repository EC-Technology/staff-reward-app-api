<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\MerchantApiController;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class ProductController extends MerchantApiController {

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    public function createProduct(Request $request): JsonResponse
    {
        $postData = $request->post();

        $file = $request->file('image_file');

        if (empty($file)) {
            $this->errorResponse("image_file is required");
        }

        $fileName = Str::uuid() . '.' . $file->extension();
        Storage::disk('s3')->put('images/product/' . $fileName, $file->getContent());

        $url = Storage::disk('s3')->url("images/product/$fileName");

        $product = new Product();
        $product['status'] = $postData['status'];
        $product['merchant_id'] = $postData['merchant_id'];
        $product['category_id'] = $request->post('category_id');
        $product['sku'] = $postData['sku'];
        $product['name'] = $postData['name'];
        $product['description'] = $postData['description'];
        $product['original_price'] = $postData['original_price'];
        $product['discounted_price'] = $postData['discounted_price'];
        $product['image_url'] = $url;
        $product['start_time'] = empty($postData['start_time']) ? NULL : Carbon::createFromTimestamp($postData['start_time']);
        $product['end_time'] = empty($postData['end_time']) ? NULL : Carbon::createFromTimestamp($postData['end_time']);
        $product['display_start_time'] = empty($postData['display_start_time']) ? NULL : Carbon::createFromTimestamp($postData['display_start_time']);
        $product['display_end_time'] = empty($postData['display_end_time']) ? NULL : Carbon::createFromTimestamp($postData['display_end_time']);

        $product->save();

        return $this->successResponse($product);
    }

}
