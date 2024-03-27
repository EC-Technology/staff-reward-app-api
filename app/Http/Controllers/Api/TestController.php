<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TestController extends ApiController {
    /**
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse {

        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $postData = Validator::make($request->post(), $rules)->validate();
        return $this->successResponse([
            'access_token' => 'd181187f-92d8-445d-8d5e-06b9efd25287',
            'expiry_time' => time() + 24 * 60 * 60
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse {

        $rules = [
            'email' => 'required|email',
            'password' => 'required',
            'password_confirm' => 'required',
            'invitation_code' => 'required'
        ];

        $postData = Validator::make($request->post(), $rules)->validate();

        return $this->successResponse([]);
    }

    /**
     * @throws ValidationException
     */
    public function forgetPassword(Request $request): JsonResponse {
        $rules = [
            'email' => 'required|email'
        ];

        $postData = Validator::make($request->post(), $rules)->validate();

        return $this->successResponse([]);
    }

    /**
     * @throws ValidationException
     */
    public function resetPassword(Request $request): JsonResponse {
        $rules = [
            'email' => 'required|email',
            'verification_code' => 'required'
        ];

        $postData = Validator::make($request->post(), $rules)->validate();

        return $this->successResponse([]);
    }

    public function logout(): JsonResponse {
        return $this->successResponse([]);
    }

    public function userProfile(): JsonResponse {
        return $this->successResponse([
            'email' => 'elvis.chan@staffcoinhk.com',
            'user_name' => 'elvis.chan',
            'icon_url' => null,
        ]);
    }

    public function getBalance(): JsonResponse {
        return $this->successResponse([
            [
               'balance' => 2400,
               'expiry_time' => 1735660799
            ],
            [
                'balance' => 1200,
                'expiry_time' => 1704038399
            ]
        ]);
    }

    public function getProducts(): JsonResponse {
        $jsonData = Storage::disk('local')->get('sample-product.json');
        $productData = json_decode($jsonData);
        return $this->successResponse($productData);
    }

    public function getMainCategory(): JsonResponse {
        $jsonData = Storage::disk('local')->get('sample-main-category.json');
        $categoryData = json_decode($jsonData);
        return $this->successResponse($categoryData);
    }

    public function getCoinTransactions(): JsonResponse {
        $jsonData = Storage::disk('local')->get('sample-coin-transaction-record.json');
        $transactionData = json_decode($jsonData);
        return $this->successResponse($transactionData);
    }

    public function getBanners(): JsonResponse {
        return $this->successResponse([
            ['id' => 1, 'order' => 1, 'image_url' => 'https://cdn-dev.staffcoinhk.com/images/banner/health-banner-v5.png'],
//            ['id' => 2, 'order' => 2, 'image_url' => 'https://cdn-dev.staffcoinhk.com/images/banner/activity_banner_2.jpeg'],
//            ['id' => 3, 'order' => 3, 'image_url' => 'https://cdn-dev.staffcoinhk.com/images/banner/activity_banner_3.jpeg']
        ]);
    }

    public function getVouchers(): JsonResponse {
        $voucherJsonData = Storage::disk('local')->get('sample-voucher.json');
        $productJsonData = Storage::disk('local')->get('sample-product.json');

        $voucherData = json_decode($voucherJsonData, TRUE);
        $productData = json_decode($productJsonData, TRUE);

        foreach ($voucherData as &$voucher) {
            foreach($productData as $product) {
                if ($voucher['product_id'] == $product['id']) {
                    $voucher['product'] = $product;
                }
            }
        }

        return $this->successResponse($voucherData);
    }

    public function getVoucherDetail(Request $request): JsonResponse {
        $voucherId = $request->get('voucher_id');

        $voucherJsonData = Storage::disk('local')->get('sample-voucher.json');
        $productJsonData = Storage::disk('local')->get('sample-product.json');

        $voucherData = json_decode($voucherJsonData, TRUE);
        $productData = json_decode($productJsonData, TRUE);

        $result = [];

        foreach ($voucherData as &$voucher) {
            if ($voucher['id'] != $voucherId) {
                continue;
            }

            $result = $voucher;

            foreach($productData as $product) {
                if ($result['product_id'] == $product['id']) {
                    $result['product'] = $product;
                }
            }
        }

        return $this->successResponse($result);
    }

    /**
     * @throws ValidationException
     */
    public function getProductDetail(Request $request): JsonResponse {
        $productId = $request['product_id'];

        if (empty($productId)) {
            throw ValidationException::withMessages(['product_id is required']);
        }

        $productJsonData = Storage::disk('local')->get('sample-product.json');
        $productData = json_decode($productJsonData, TRUE);

        foreach ($productData as $product) {
            if ($product['id'] == $productId) {
                return $this->successResponse($product);
            }
        }

        return $this->successResponse([]);
    }




    public function getVoucherUseHistory(Request $request): JsonResponse {
        //TODO



        return $this->successResponse([]);
    }

    public function getProductGroup(Request $request): JsonResponse {
        $displayLocation = $request['display_location'];
        if (empty($displayLocation)) {
            return $this->errorResponse('display_location is required');
        }

        $groupJsonData = Storage::disk('local')->get('sample-product-group.json');
        $groupProductJsonData = Storage::disk('local')->get('sample-product-group-product.json');
        $productJsonData = Storage::disk('local')->get('sample-product.json');

        $groupData = json_decode($groupJsonData, TRUE);
        $groupProductData = json_decode($groupProductJsonData, TRUE);
        $productData = json_decode($productJsonData, TRUE);

        $groups = [];
        foreach ($groupData as $group) {
            if ($group['display_location'] == $displayLocation) {
                $groups[] = $group;
            }
        }

        foreach ($groups as &$group) {
            $group['products'] = [];
            foreach ($groupProductData as $groupProduct) {
                if ($group['id'] == $groupProduct['product_group_id']) {

                    foreach ($productData as &$product) {
                        if ($product['id'] == $groupProduct['product_id']) {
                            $product['sort_order'] = $groupProduct['sort_order'];
                            $group['products'][] = $product;
                        }
                    }

                }
            }
        }


        return $this->successResponse($groups);
    }
}
