<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\AuthController;
use App\Http\Controllers\Website\CartController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\OrderController;
use App\Http\Controllers\Website\AccountController;
use App\Http\Controllers\Website\ProductController;
use App\Http\Controllers\Website\CheckoutController;
use App\Http\Controllers\Website\AddressController;
use App\Http\Controllers\Website\WalletController;
use App\Http\Controllers\Website\PaymentController;
use App\Http\Controllers\Website\RefundOrderController;
use App\Http\Controllers\Website\StaticPageController;
use App\Http\Controllers\Payments\PaymobController;

Route::group(['middleware' => ['web', 'HtmlMinifier']], function () {

    // Website home route
    Route::get('/home', [HomeController::class, 'index'])->name('website.home');
   Route::get('/', [HomeController::class, 'index'])->name('website.main');
    // Product routes
    Route::get('/offers', [ProductController::class, 'offers'])->name('website.offers');
    Route::get('/latest-products', [ProductController::class, 'latest'])->name('website.latest');
    Route::get('/brand/{id}', [ProductController::class, 'brand'])->name('website.brand');
    Route::get('/category/{slug}', [ProductController::class, 'category'])->name('website.category');
    Route::get('/product/{slug}', [ProductController::class, 'show'])->name('website.product.show');
    Route::get('/search', [ProductController::class, 'search'])->name('website.product.search');



    // Guest routes (not authenticated)
    Route::group(['middleware' => 'website.guest'], function () {
        // Login routes

        
//  Route::get('/', function () {
//            return redirect()->route('website.home');
//});
        Route::get('/login', function () {
            return view('website.auth.login');
        })->name('website.login');
        Route::post('/login', [AuthController::class, 'login'])->name('website.login.submit');

        // Register routes
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('website.register');
        Route::post('/register', [AuthController::class, 'register'])->name('website.register.submit');

        // Register OTP verification routes
        Route::get('/register-otp', function () {
            return view('website.auth.register_otp');
        })->name('website.register_otp');
        Route::post('/register-otp', [AuthController::class, 'verifyRegistrationOTP'])->name('website.register_otp.submit');
        Route::post('/register-otp/resend', [AuthController::class, 'resendRegistrationOTP'])->name('website.register_otp.resend');

        // Register success page
        Route::get('/register-success', function () {
            return view('website.auth.register_sucess');
        })->name('website.register_sucess');

        // Forgot password routes
        Route::get('/forget-password', function () {
            return view('website.auth.password_forget');
        })->name('website.password_forget');
        Route::post('/forget-password', [AuthController::class, 'forgotPassword'])->name('website.password_forget.submit');

        // Password reset OTP verification routes
        Route::get('/password-otp', function () {
            return view('website.auth.password_otp');
        })->name('website.password_otp');
        Route::post('/password-otp', [AuthController::class, 'verifyPasswordOTP'])->name('website.password_otp.submit');
        Route::post('/password-otp/resend', [AuthController::class, 'resendPasswordOTP'])->name('website.password_otp.resend');

        // Password reset routes
        Route::get('/password-reset', function () {
            return view('website.auth.password_reset');
        })->name('website.password_reset');
        Route::post('/password-reset', [AuthController::class, 'resetPassword'])->name('website.password_reset.submit');
    });

    // Authenticated routes
    Route::group(['middleware' => 'auth:web'], function () {
        // Logout route
        Route::post('/logout', [AuthController::class, 'logout'])->name('website.logout');

        // Cart routes (website) - DB only, requires auth
        Route::get('/cart', [CartController::class, 'index'])->name('website.cart.index');
        Route::post('/cart/add', [CartController::class, 'add'])->name('website.cart.add');
        Route::put('/cart/update', [CartController::class, 'update'])->name('website.cart.update');
        Route::delete('/cart/remove', [CartController::class, 'remove'])->name('website.cart.remove');
        Route::get('/cart/summary', [CartController::class, 'summary'])->name('website.cart.summary');

        // Checkout routes
        Route::post('/checkout/prepare', [CheckoutController::class, 'prepare'])->name('website.checkout.prepare');
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('website.checkout');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('website.checkout.store');
        Route::post('/checkout/calculate', [CheckoutController::class, 'calculate'])->name('website.checkout.calculate');
        Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('website.checkout.success');
        // Apply coupon and wallet deduction (website)
        Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('website.checkout.apply-coupon');
        Route::post('/checkout/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('website.checkout.remove-coupon');
        Route::post('/checkout/apply-wallet-deduction', [CheckoutController::class, 'applyWalletDeduction'])->name('website.checkout.apply-wallet-deduction');
        Route::post('/checkout/remove-wallet-deduction', [CheckoutController::class, 'removeWalletDeduction'])->name('website.checkout.remove-wallet-deduction');
        // Account routes
        Route::get('/account', [AccountController::class, 'index'])->name('website.account');
        Route::post('/account', [AccountController::class, 'update'])->name('website.account.update');
        Route::delete('/account/delete', [AccountController::class, 'deleteAccount'])->name('website.account.delete');

        // Password routes (website)
        Route::get('/account/password', [AccountController::class, 'password'])->name('website.password');
        Route::post('/account/password', [AccountController::class, 'updatePassword'])->name('website.password.update');

        // Favourites page (website)
        Route::get('/account/favourits', [OrderController::class, 'favourits'])->name('website.favourits');
        Route::post('/account/favourits', [OrderController::class, 'addFavourite'])->name('website.favourites.add');
        Route::delete('/account/favourits/{product}', [OrderController::class, 'removeFavourite'])->name('website.favourites.remove');
        Route::get('/account/favourits/ids', [OrderController::class, 'favouritesIds'])->name('website.favourites.ids');

        Route::get('/account/orders' ,[OrderController::class , 'index'])->name('website.orders');
        Route::get('/account/orders/{order}', [OrderController::class, 'show'])->name('website.orders.show');
        Route::post('/account/orders/{order}/report', [OrderController::class, 'report'])->name('website.orders.report');
        Route::post('/account/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('website.orders.cancel');
        Route::get('/account/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('website.orders.invoice');
        Route::post('/account/orders/refund', [OrderController::class, 'requestRefund'])->name('website.orders.refund');

        // Refund Orders routes (website)
        Route::get('/account/refunds', [RefundOrderController::class, 'index'])->name('website.refunds.index');
        Route::get('/account/refunds/{order}', [RefundOrderController::class, 'show'])->name('website.refunds.show');

        // Address Book routes (website)
        Route::get('/account/addresses', [AddressController::class, 'index'])->name('website.addresses.index');
        Route::get('/account/addresses/create', [AddressController::class, 'create'])->name('website.addresses.create');
        Route::post('/account/addresses', [AddressController::class, 'store'])->name('website.addresses.store');
        Route::get('/account/addresses/{address}/edit', [AddressController::class, 'edit'])->name('website.addresses.edit');
        Route::put('/account/addresses/{address}', [AddressController::class, 'update'])->name('website.addresses.update');
        Route::delete('/account/addresses/{address}', [AddressController::class, 'destroy'])->name('website.addresses.destroy');

        // Wallet routes (website)
        Route::get('/account/wallet', [WalletController::class, 'index'])->name('website.wallet.index');
        Route::post('/account/wallet/withdraw', [WalletController::class, 'createWithdrawRequest'])->name('website.wallet.withdraw');
        Route::post('/account/wallet/recharge', [WalletController::class, 'createRechargeRequest'])->name('website.wallet.recharge');

        // Notifications (website)
        Route::get('/account/notifications', [AccountController::class, 'notifications'])->name('website.notifications');
        Route::get('/account/notifications/{id}/go', [AccountController::class, 'notificationGo'])->name('website.notifications.go');
        Route::post('/account/notifications/delete', [AccountController::class, 'deleteNotifications'])->name('website.notifications.delete');

        // Device token (website)
        Route::post('/account/device-token', [AccountController::class, 'storeDeviceToken'])->name('website.device-token.store');
    });

    // Unified Paymob payment routes (webhook + callback + success/error views)
    Route::any('/payments/paymob/webhook', [PaymobController::class, 'webhook'])
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
        ->name('payments.paymob.webhook');
    Route::get('/payments/paymob/callback', [PaymobController::class, 'callback'])->name('payments.paymob.callback');
    Route::view('/payment/success', 'payment.success')->name('payment.success');
    Route::view('/payment/error', 'payment.fail')->name('payment.error');

    // Static pages routes (accessible to all)
    Route::get('/about', [StaticPageController::class, 'about'])->name('website.about');
    Route::get('/faq', [StaticPageController::class, 'faq'])->name('website.faq');
    Route::get('/terms', [StaticPageController::class, 'terms'])->name('website.terms');
    Route::get('/privacy', [StaticPageController::class, 'privacy'])->name('website.privacy');
    Route::get('/returns', [StaticPageController::class, 'returns'])->name('website.returns');
    Route::get('/contact', [StaticPageController::class, 'contact'])->name('website.contact');
    Route::post('/contact', [StaticPageController::class, 'submitContact'])->name('website.contact.submit');
});

