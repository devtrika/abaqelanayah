<?php

use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\Client\BrandController;
use App\Http\Controllers\Api\Delivery\DeliveryReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\Client\CategoryController;
use App\Http\Controllers\Api\Client\ProductController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\ConsultationMessageController;
use App\Http\Controllers\Api\Delivery\OrderController;
use App\Http\Controllers\Api\Delivery\RefundOrderController;
use App\Http\Controllers\Api\UnifonicWebhookController;

Route::group([
    'namespace'  => 'Api',
    'middleware' => ['api-cors', 'api-lang'],
], function () {

    Route::group(['middleware' => ['OptionalSanctumMiddleware']], function () {
        /***************************** SettingController start *****************************/
            Route::get('settings'                    ,[SettingController::class, 'settings']);
            Route::get('about'                       ,[SettingController::class, 'about']);
            Route::get('terms'                       ,[SettingController::class, 'terms']);
            Route::get('privacy'                     ,[SettingController::class, 'privacy']);
            Route::get('intros'                      ,[SettingController::class, 'intros']);
                        Route::get('cancelpolicy'                     ,[SettingController::class, 'cancelpolicy']);


            Route::get('fqss'                        ,[SettingController::class, 'fqss']);
            Route::get('socials'                     ,[SettingController::class, 'socials']);
            Route::get('images'                      ,[SettingController::class, 'images']);
            Route::get('countries'                   ,[SettingController::class, 'countries']);
            Route::get('countries-with-cities'       ,[SettingController::class, 'countriesWithCities']);
            Route::get('countries-with-regions'      ,[SettingController::class, 'countriesWithRegions']);
            Route::get('regions'                     ,[SettingController::class, 'regions']);
            Route::get('categories/{id?}', [SettingController::class, 'categories']);

            Route::get('service-categories'                      ,[SettingController::class, 'categories']);
            Route::get('product-categories'                      ,[SettingController::class, 'ProductCategories']);
            Route::get('blog-categories'                      ,[SettingController::class, 'BlogCategories']);
            Route::get('cancel-reasons'                      ,[SettingController::class, 'cancellationReasons']);
            Route::get('problems'                      ,[SettingController::class, 'problems']);
            Route::get('brands'                      ,[BrandController::class, 'index']);
            Route::get('refund-reasons'                      ,[SettingController::class, 'refundReasons']);



            Route::get('cities'                      ,[SettingController::class, 'cities']);
            Route::get('districts',[SettingController::class, 'districts'] );
            Route::get('region/{region_id}/cities'   ,[SettingController::class, 'regionCities']);
            Route::get('city/{city_id}/districts'   ,[SettingController::class, 'districtCities']);
            Route::get('regions-with-cities'         ,[SettingController::class, 'regionsWithCities']);
            Route::get('country/{country_id}/cities' ,[SettingController::class, 'CountryCities']);
            Route::get('country/{country_id}/regions' ,[SettingController::class, 'CountryRegions']);
            Route::post('check-coupon'               ,[SettingController::class, 'checkCoupon']);
            Route::get('is-production'               ,[SettingController::class, 'isProduction']);
            Route::get('payment-methods'             ,[SettingController::class, 'paymentMethods']);
            Route::get('order-statuses'              ,[SettingController::class, 'orderStatuses']);
            Route::get('delivery-periods'            ,[SettingController::class, 'deliveryPeriods']);
            Route::get('vat-amount'            ,[SettingController::class, 'VatAmount']);
            Route::get('delivery-register'            ,[SettingController::class, 'isRegister']);
            Route::get('videos'            ,[SettingController::class, 'videos']);


Route::post('contact-us', [ContactUsController::class, 'store'])->name('client.contact-us.store');

            // Route::apiResource('products', ProductController::class);

Route::get('/regions/{id}/cities', [SettingController::class, 'getCities']);
Route::get('/cities/{id}/districts', [SettingController::class, 'getDistricts']);

            /***************************** SettingController End *****************************/


    });



Route::prefix('delivery')
    ->middleware(['auth:sanctum', 'api-cors', 'api-lang'])
    ->group(function () {

        Route::get('orders', [OrderController::class, 'index'])
            ->name('delivery.orders.index');

               Route::get('statistics', [OrderController::class, 'statistics'])
            ->name('delivery.orders.statistics');

        // Order details
        Route::get('orders/{id}', [OrderController::class, 'show'])
            ->name('delivery.orders.show');

        // Update order status
        Route::put('orders/{id}/status', [OrderController::class, 'updateStatus'])
            ->name('delivery.orders.updateStatus');


     Route::get('refund-orders', [RefundOrderController::class, 'index']);
    Route::get('refund-orders/{id}', [RefundOrderController::class, 'show']);
    Route::put('refund-orders/{id}/update-status', [RefundOrderController::class, 'updateStatus']);

                Route::patch('switch-order'                          ,[AuthController::class,       'switchAcceptOrders']);

    

    
    });
    





     Route::group(['middleware' => ['guest']], function () {
        /***************************** AuthController  Start *****************************/
          Route::post('webhooks/unifonic/whatsapp',  [UnifonicWebhookController::class, 'handle']);
          Route::post('webhooks/unifonic/voice',     [UnifonicWebhookController::class, 'handleVoice']);
          Route::post('sign-in'                      ,[AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);

            Route::patch('activate'                    ,[AuthController::class, 'activate']);
            Route::get('resend-code'                   ,[AuthController::class, 'resendCode']);
            Route::post('sign-in'                      ,[AuthController::class, 'login']);
            Route::delete('sign-out'                   ,[AuthController::class, 'logout']);
            Route::post('forget-password-send-code'    ,[AuthController::class, 'forgetPasswordSendCode']);
            Route::post('verify-password-reset-code', [AuthController::class, 'verifyPasswordResetCode']);
            Route::post('reset-password'               ,[AuthController::class, 'resetPassword']);

            Route::get('business-register', [BusinessController::class, 'check']);

            // Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

        /***************************** AuthController end *****************************/
    });


      Route::group(['middleware' => ['auth:sanctum', 'is-active']], function () {
        /***************************** AuthController  Start *****************************/
                Route::get('profile', [AuthController::class, 'getProfile']);
                Route::post('update-profile', [AuthController::class, 'updateProfile']);


            Route::patch('update-passward'                        ,[AuthController::class,       'updatePassword']);
            Route::patch('change-lang'                            ,[AuthController::class,       'changeLang']);
            Route::patch('switch-notify'                          ,[AuthController::class,       'switchNotificationStatus']);
            Route::post('change-phone-send-code'                  ,[AuthController::class        , 'changePhoneSendCode']);
            Route::post('change-phone-check-code'                 ,[AuthController::class        , 'changePhoneCheckCode']);
            Route::post('change-email-send-code'                  ,[AuthController::class        , 'changeEmailSendCode']);
            Route::post('change-email-check-code'                 ,[AuthController::class        , 'changeEmailCheckCode']);
            Route::get('notifications'                            ,[AuthController::class,       'getNotifications']);
            Route::get('count-notifications'                      ,[AuthController::class,       'countUnreadNotifications']);
            Route::get('notitications/read/{notification_id}' ,[AuthController::class,       'markNotificationAsRead']);
               Route::get('notifications/mark-as-read'                  ,[AuthController::class,       'MarkAsReadNotifications']);
            Route::delete('notifications'                         ,[AuthController::class,       'deleteNotifications']);
            // Route::get('notifications/mark-as-read'                  ,[AuthController::class,       'deleteNotifications']);
            Route::delete('delete-account'                        , [AuthController::class,  'deleteAccount']);
            Route::post('request-account-deletion'                , [AuthController::class,  'requestAccountDeletion']);
            Route::post('send-phone-update-code', [AuthController::class, 'sendPhoneUpdateCode']);
            Route::post('verify-phone-update-code', [AuthController::class, 'verifyPhoneUpdateCode']);




   

        /***************************** ChatController start *****************************/
            Route::get('create-room'                       ,[ChatController::class, 'createRoom']);
            Route::post('create-private-room'              ,[ChatController::class, 'createPrivateRoom']);
            Route::get('room-members/{room}'               ,[ChatController::class, 'getRoomMembers']);
            Route::get('join-room/{room}'                  ,[ChatController::class, 'joinRoom']);
            Route::get('leave-room/{room}'                 ,[ChatController::class, 'leaveRoom']);
            Route::get('get-room-messages/{room}'          ,[ChatController::class, 'getRoomMessages']);
            Route::get('get-room-unseen-messages/{room}'   ,[ChatController::class, 'getRoomUnseenMessages']);
            Route::get('get-rooms'                         ,[ChatController::class, 'getMyRooms']);
            Route::delete('delete-message-copy/{message}'  ,[ChatController::class, 'deleteMessageCopy']);
            Route::post('send-message/{room}'              ,[ChatController::class, 'sendMessage']);
            Route::post('upload-room-file/{room}'          ,[ChatController::class, 'uploadRoomFile']);
        /***************************** ChatController end *****************************/

    // ...existing code...

        // Consultation messages
    
        Route::get('wallet', action: [AuthController::class, 'getWalletTransactions']);
        
    
    });



// Make contact-us available for both guests and authenticated users
});

