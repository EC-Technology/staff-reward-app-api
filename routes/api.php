<?php

use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\Internal\AuthController;
use App\Http\Controllers\Api\Internal\MerchantController;
use App\Http\Controllers\Api\Internal\ProductController;
use App\Http\Controllers\Api\Internal\VoucherController;
use App\Http\Controllers\Api\Merchant\GuestController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductGroupController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\RewardTokenController;
use App\Http\Controllers\Api\UserController;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('/register-with-activation-code', [\App\Http\Controllers\Api\AuthController::class, 'registerWithActivationCode']);
    Route::post('/login-with-username-password', [\App\Http\Controllers\Api\AuthController::class,  'loginWithUsernamePassword']);
    Route::post('/login-with-email-password', [\App\Http\Controllers\Api\AuthController::class, 'loginWithEmailPassword']);
    Route::middleware('auth:sanctum')->post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->post('/update-password', [\App\Http\Controllers\Api\AuthController::class, 'updatePassword']);

});

Route::prefix('profile')->group(function () {
    Route::get('/', [UserController::class, 'getProfile']);
    Route::put('/', [UserController::class, 'updateProfile']);
});

Route::prefix('product-category')->group(function () {
    Route::get('/', [ProductCategoryController::class, 'getCategory']);
});

Route::prefix('product')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\ProductController::class, 'queryProducts']);
    Route::get('/detail/{productId}', [\App\Http\Controllers\Api\ProductController::class, 'queryProductDetails']);
});

Route::prefix('product-rating')->group(function () {

});

Route::prefix('product-group')->group(function () {
   Route::get('/', [ProductGroupController::class,  'queryProductGroups']);
});

Route::prefix('banner')->group(function () {
    Route::get('/', [BannerController::class, 'queryBanners']);
});

Route::prefix('reward-token')->group(function () {
    Route::post('/transaction', [RewardTokenController::class, 'queryTokenTransactions']);
    Route::post('/balance', [RewardTokenController::class, 'queryBalance']);
});

Route::prefix('voucher')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\VoucherController::class, 'queryVouchers']);
    Route::get('/{voucherId}/detail', [\App\Http\Controllers\Api\VoucherController::class, 'queryVoucherDetail']);
    Route::post('/redeem', [\App\Http\Controllers\Api\VoucherController::class, 'redeemVoucher']);
});

Route::prefix('internal')->group(function() {
    Route::middleware('auth:sanctum')->post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::post('/merchant/create', [MerchantController::class, 'create']);
    Route::post('/merchant-user/create', [MerchantController::class, 'createMerchantUser']);
    Route::post('/product/create', [ProductController::class, 'createProduct']);
    Route::post('/voucher/add-external', [VoucherController::class, 'addExternal']);
});

Route::prefix('merchant')->group(function() {

   Route::get('/guest/merchant/{code}', [GuestController::class, 'getMerchantData']);

   Route::post('/auth/login', [\App\Http\Controllers\Api\Merchant\AuthController::class, 'login'])->name('login'); // TODO
   Route::middleware('auth:sanctum')->post('/auth/logout', [\App\Http\Controllers\Api\Merchant\AuthController::class, 'logout']);

   Route::post('/voucher', [\App\Http\Controllers\Api\Merchant\VoucherController::class, 'getVoucher']);
   Route::post('/voucher/use', [\App\Http\Controllers\Api\Merchant\VoucherController::class, 'useVoucher']);
   Route::get('voucher/use-history', [\App\Http\Controllers\Api\Merchant\VoucherController::class, 'queryUseHistories']);
});

Route::prefix('test')->group(function() {
    Route::post('/auth/login-with-email-password', [TestController::class, 'login']);
    Route::post('/auth/register-with-activation-code', [TestController::class, 'register']);
    Route::post('/auth/forget-password-with-email', [TestController::class, 'forgetPassword']);
    Route::post('/auth/reset-password-with-email-code', [TestController::class, 'forgetPassword']);
    Route::post('/auth/logout', [TestController::class, 'logout']);

    Route::get('/profile', [TestController::class, 'userProfile']);

    Route::get('/coin/balance', [TestController::class, 'getBalance']);
    Route::get('/coin/transaction-record', [TestController::class, 'getCoinTransactions']);

    Route::get('/product', [TestController::class, 'getProducts']);
    Route::get('/product/group', [TestController::class, 'getProductGroup']);

    Route::get('/product-category/main', [TestController::class, 'getMainCategory']);

    Route::get('/banner', [TestController::class, 'getBanners']);

    Route::get('/voucher', [TestController::class, 'getVouchers']);

    Route::get('/voucher/detail', [TestController::class, 'getVoucherDetail']);

    //TODO
    Route::get('/product/detail', [TestController::class, 'getProductDetail']);

    //TODO
    Route::get('/voucher/use-history', [TestController::class, 'getVoucherUseHistory']);
});
