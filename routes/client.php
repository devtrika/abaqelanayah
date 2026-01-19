<?php

use App\Http\Controllers\Api\Client\AddressController;
use App\Http\Controllers\Api\Client\AuthController;
use App\Http\Controllers\Api\Client\CartController;
use App\Http\Controllers\Api\Client\CategoryController;
use App\Http\Controllers\Api\Client\FavoriteController;
use App\Http\Controllers\Api\Client\LoyaltyPointController;
use App\Http\Controllers\Api\Client\OrderController;
use App\Http\Controllers\Api\Client\OrderRateController;
use App\Http\Controllers\Api\Client\OrderReportController;
use App\Http\Controllers\Api\Client\ProductController;
use App\Http\Controllers\Api\Client\RateController;
use App\Http\Controllers\Api\Client\RatingController;
use App\Http\Controllers\Api\Client\WalletController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Client API Routes
|--------------------------------------------------------------------------
|
| Here are the API routes for client-side functionality including
| authentication, products, orders, cart, and other client features.
|
*/

Route::group([
    'middleware' => ['api-cors', 'api-lang'],
    'prefix'     => 'client',
], function () {

    /*
    |--------------------------------------------------------------------------
    | Authenticated Client Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['middleware' => ['auth:sanctum', 'check-client-type', 'check.user.status']], function () {

        /*
        |--------------------------------------------------------------------------
        | Profile & Authentication
        |--------------------------------------------------------------------------
        */
        Route::prefix('profile')->name('client.profile.')->group(function () {
            Route::get('/', [AuthController::class, 'getProfile'])->name('get');
            Route::post('update', [AuthController::class, 'updateProfile'])->name('update');
            Route::post('update-location', [AuthController::class, 'setLocation'])->name('update-location');
        });

        /*
        |--------------------------------------------------------------------------
        | Address Management
        |--------------------------------------------------------------------------
        */
        Route::apiResource('addresses', AddressController::class);

        /*
        |--------------------------------------------------------------------------
        | Favorites
        |--------------------------------------------------------------------------
        */
        Route::prefix('favorites')->name('client.favorites.')->group(function () {
            Route::get('/', [FavoriteController::class, 'index'])->name('index');
            Route::post('toggle', [FavoriteController::class, 'toggle'])->name('toggle');
            Route::get('check', [FavoriteController::class, 'check'])->name('check');
        });

        /*
        |--------------------------------------------------------------------------
        | Ratings & Reviews
        |--------------------------------------------------------------------------
        */
        Route::prefix('ratings')->name('client.ratings.')->group(function () {
            Route::post('/', [RatingController::class, 'store'])->name('store');
            Route::get('/', [RatingController::class, 'myRatings'])->name('my');
        });

        /*
        |--------------------------------------------------------------------------
        | Shopping Cart
        |--------------------------------------------------------------------------
        */
        Route::prefix('cart')->name('client.cart.')->group(function () {
            Route::get('/', [CartController::class, 'getCart'])->name('get');
            Route::post('add', [CartController::class, 'addToCart'])->name('add');
            Route::put('update', [CartController::class, 'updateCartItem'])->name('update');
            Route::delete('remove', [CartController::class, 'removeFromCart'])->name('remove');
            Route::delete('clear', [CartController::class, 'clearCart'])->name('clear');

            // Cart Discounts & Payments
            Route::post('apply-coupon', [CartController::class, 'applyCoupon'])->name('apply-coupon');
            Route::delete('remove-coupon', [CartController::class, 'removeCoupon'])->name('remove-coupon');
            Route::post('apply-loyalty-points', [CartController::class, 'applyLoyaltyPoints'])->name('apply-loyalty-points');
            Route::delete('remove-loyalty-points', [CartController::class, 'removeLoyaltyPoints'])->name('remove-loyalty-points');
            Route::post('apply-wallet-deduction', [CartController::class, 'applyWalletDeduction'])->name('apply-wallet-deduction');
        });

        /*
        |--------------------------------------------------------------------------
        | Order Management
        |--------------------------------------------------------------------------
        */
        Route::prefix('orders')->name('client.orders.')->group(function () {
            Route::get('/', [OrderController::class, 'getOrders'])->name('list');
            Route::get('{id}', [OrderController::class, 'getOrder'])->name('show');
            Route::post('create', [OrderController::class, 'createOrder'])->name('create');
            Route::post('cancel', [OrderController::class, 'SendCancelRequest'])->name('cancel');
            Route::post('report-problem', [OrderController::class, 'reportProblem'])->name('report-problem');
            Route::post('request-refund', [OrderController::class, 'requestRefund'])->name('request-refund');
            Route::post('calculate-delivery-fee', [OrderController::class, 'calculateDeliveryFee'])->name('calculate-delivery-fee');
            Route::get('{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('invoice.download');
            Route::get('loyality-points', [OrderController::class, 'loyalityPoints'])->name('loyality-points');


            // Payment related routes
            Route::get('payment-gateways', [OrderController::class, 'paymentGateways'])->name('payment-gateways');
            Route::get('payments', [OrderController::class, 'payments'])->name('payments');
            Route::post('payment/webhook', [\App\Http\Controllers\Api\Order\PaymentController::class, 'webhook'])->name('payment.webhook');

            // Order feedback
            Route::post('rate', [OrderRateController::class, 'store'])->name('rate');
            Route::post('report', [OrderReportController::class, 'store'])->name('report');
        });

        /*
        |--------------------------------------------------------------------------
        | Refundable Orders Management
        |--------------------------------------------------------------------------
        */
        Route::prefix('refundable-orders')->name('client.refundable_orders.')->group(function () {
            Route::get('/', [OrderController::class, 'getRefundableOrders'])->name('list');
            Route::get('{id}', [OrderController::class, 'getRefundableOrder'])->name('show');
        });

        /*
        |--------------------------------------------------------------------------
        | Wallet & Loyalty Points
        |--------------------------------------------------------------------------
        */
        Route::get('wallet', [WalletController::class, 'wallet'])->name('client.wallet');
        Route::post('wallet/withdraw', [WalletController::class, 'createWithdrawRequest'])->name('client.wallet.withdraw');
        Route::post('wallet/recharge', [WalletController::class, 'createRechargeRequest'])->name('client.wallet.recharge');
        Route::get('loyalty-points', [LoyaltyPointController::class, 'loyalityPoints'])->name('client.loyalty-points');

        /*
        |--------------------------------------------------------------------------
        | General Ratings (for providers, etc.)
        |--------------------------------------------------------------------------
        */
        Route::post('rates/store', [RateController::class, 'store'])->name('client.rates.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Optional Authentication Routes (Guest or Authenticated)
    |--------------------------------------------------------------------------
    */
    Route::group(['middleware' => ['OptionalSanctumMiddleware']], function () {
        // Products (available for both guests and authenticated users)
        Route::get('products', [ProductController::class, 'index'])->name('client.products.index');
        Route::get('products/{id}', [ProductController::class, 'show'])->name('client.products.show');
        Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
        Route::get('sub-category/{id}', [CategoryController::class, 'showSubCategory'])->name('categories.sub');
    });
});
