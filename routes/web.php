<?php

use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\OrderRateController;
use App\Http\Controllers\TestNotificationController;

Route::group([
    'prefix'     => 'admin',
    'namespace'  => 'Admin',
    'as'         => 'admin.',
    'middleware' => ['web-cors', 'admin-lang', 'admin', 'check-role'],
], function () {

    Route::post('/orderrates/{id}/publish-video', [OrderRateController::class, 'publishVideo'])->name('orderrates.publishVideo');


    Route::get('/lang/{lang}', 'AuthController@SetLanguage')->withoutMiddleware(['admin', 'check-role']);

    Route::get('login', 'AuthController@showLoginForm')->name('show.login')->middleware('guest:admin')->withoutMiddleware(['admin', 'check-role']);
    Route::post('login', 'AuthController@login')->name('login')->withoutMiddleware(['admin', 'check-role']);
    Route::get('logout', 'AuthController@logout')->name('logout')->withoutMiddleware(['admin', 'check-role']);

    Route::get('forget-password', 'AuthController@showForgetPasswordForm')->name('show.forget-password')->withoutMiddleware(['admin', 'check-role']);
    Route::post('forget-password', 'AuthController@forgetPassword')->name('forget-password')->withoutMiddleware(['admin', 'check-role']);

    Route::get('resert-password/{admin}', 'AuthController@showResetPasswordForm')->name('show.reset-password')->withoutMiddleware(['admin', 'check-role']);
    Route::post('reset-password', 'AuthController@resetPassword')->name('reset-password')->withoutMiddleware(['admin', 'check-role']);

    Route::post('getCities', 'CityController@getCities')->name('getCities');

    Route::get('user_complaints/{id}', [
        'uses'  => 'ClientController@showfinancial',
        'as'    => 'user_complaints.show',
        'title' => 'the_resolution_of_complaining_or_proposal',
    ]);

    Route::get('user_orders/{id}', [
        'uses'  => 'ClientController@showorders',
        'as'    => 'user_orders.show',
        'title' => 'orders',
    ]);

    Route::group(['middleware' => ['admin', 'check-role', 'admin-lang']], function () {

        Route::get('dashboard', [
            'uses'      => 'HomeController@dashboard',
            'as'        => 'dashboard',
            'icon'      => '<i class="feather icon-home"></i>',
            'title'     => 'main_page',
            'sub_route' => false,
            'type'      => 'parent',
        ]);
        /*------------ start Of profile----------*/
        Route::get('profile', [
            'uses'      => 'HomeController@profile',
            'as'        => 'profile',
            'title'     => 'profile',
            'icon'      => '<i class="feather icon-users"></i>',

            'sub_route' => false,
            'type'      => 'parent',
            'child'     => ['profile.update_password', 'profile.update'],
        ]);

        Route::put('profile-update', [
            'uses'  => 'HomeController@updateProfile',
            'as'    => 'profile.update',
            'title' => 'update_profile',
        ]);
        Route::put('profile-update-password', [
            'uses'  => 'HomeController@updatePassword',
            'as'    => 'profile.update_password',
            'title' => 'update_password',
        ]);
        /*------------ end Of profile----------*/

        /*------------ start Of Dashboard----------*/

        /*------------ end Of dashboard ----------*/

        /*------------ start Of intro site  ----------*/
        Route::get('intro-site', [
            'as'        => 'intro_site',
            'icon'      => '<i class="feather icon-map"></i>',
            'title'     => 'introductory_site',
            'type'      => 'parent',
            'sub_route' => true,
            'child'     => [
                'intro_settings.index', 'introsliders.show', 'introsliders.index', 'introsliders.store', 'introsliders.update', 'introsliders.delete', 'introsliders.deleteAll', 'introsliders.create', 'introsliders.edit',
                'introservices.show', 'introservices.index', 'introservices.create', 'introservices.store', 'introservices.edit', 'introservices.update', 'introservices.delete', 'introservices.deleteAll',
                    'fqs.index', 'fqs.show', 'fqs.create', 'fqs.store', 'fqs.edit', 'fqs.update', 'fqs.delete', 'fqs.deleteAll',
                'socials.index', 'socials.show', 'socials.create', 'socials.store', 'socials.show', 'socials.update', 'socials.edit', 'socials.delete', 'socials.deleteAll',
                                    'all_complaints', 'complaints.delete', 'complaints.deleteAll', 'complaints.show', 'complaint.replay',


            ],
        ]);

        Route::get('intro-settings', [
            'uses'  => 'IntroSetting@index',
            'as'    => 'intro_settings.index',
            'title' => 'introductory_site_setting',
            'icon'  => '<i class="feather icon-settings"></i>',

        ]);

        /*------------ start Of introsliders ----------*/
        Route::get('introsliders', [
            'uses'  => 'IntroSliderController@index',
            'as'    => 'introsliders.index',
            'title' => 'insolder',
            'icon'  => '<i class="feather icon-image"></i>',
        ]);

        # introsliders update
        Route::get('introsliders/{id}/Show', [
            'uses'  => 'IntroSliderController@show',
            'as'    => 'introsliders.show',
            'title' => 'view_of_banner_page',
        ]);

        # socials store
        Route::get('introsliders/create', [
            'uses'  => 'IntroSliderController@create',
            'as'    => 'introsliders.create',
            'title' => 'add_of_banner_page',
        ]);

        # introsliders store
        Route::post('introsliders/store', [
            'uses'  => 'IntroSliderController@store',
            'as'    => 'introsliders.store',
            'title' => 'add_a_banner',
        ]);

        # socials update
        Route::get('introsliders/{id}/edit', [
            'uses'  => 'IntroSliderController@edit',
            'as'    => 'introsliders.edit',
            'title' => 'edit_of_banner_page',
        ]);

        # introsliders update
        Route::put('introsliders/{id}', [
            'uses'  => 'IntroSliderController@update',
            'as'    => 'introsliders.update',
            'title' => 'modification_of_banner',
        ]);

        # introsliders delete
        Route::delete('introsliders/{id}', [
            'uses'  => 'IntroSliderController@destroy',
            'as'    => 'introsliders.delete',
            'title' => 'delete_a_banner',
        ]);

        #delete all introsliders
        Route::post('delete-all-introsliders', [
            'uses'  => 'IntroSliderController@destroyAll',
            'as'    => 'introsliders.deleteAll',
            'title' => 'delete_multible_banner',
        ]);
        /*------------ end Of introsliders ----------*/

        /*------------ start Of introservices ----------*/
        Route::get('introservices', [
            'uses'  => 'IntroServiceController@index',
            'as'    => 'introservices.index',
            'title' => 'our_services',
            'icon'  => '<i class="la la-map"></i>',
        ]);
        # introservices update
        Route::get('introservices/{id}/Show', [
            'uses'  => 'IntroServiceController@show',
            'as'    => 'introservices.show',
            'title' => 'view_services',
        ]);
        # socials store
        Route::get('introservices/create', [
            'uses'  => 'IntroServiceController@create',
            'as'    => 'introservices.create',
            'title' => 'add_services',
        ]);
        # introservices store
        Route::post('introservices/store', [
            'uses'  => 'IntroServiceController@store',
            'as'    => 'introservices.store',
            'title' => 'add_services',
        ]);

        # socials update
        Route::get('introservices/{id}/edit', [
            'uses'  => 'IntroServiceController@edit',
            'as'    => 'introservices.edit',
            'title' => 'edit_services',
        ]);

        # introservices update
        Route::put('introservices/{id}', [
            'uses'  => 'IntroServiceController@update',
            'as'    => 'introservices.update',
            'title' => 'edit_services',
        ]);

        # introservices delete
        Route::delete('introservices/{id}', [
            'uses'  => 'IntroServiceController@destroy',
            'as'    => 'introservices.delete',
            'title' => 'delete_services',
        ]);

        #delete all introservices
        Route::post('delete-all-introservices', [
            'uses'  => 'IntroServiceController@destroyAll',
            'as'    => 'introservices.deleteAll',
            'title' => 'delete_multible_services',
        ]);
        /*------------ end Of introservices ----------*/

        /*------------ start Of introfqscategories ----------*/
        Route::get('introfqscategories', [
            'uses'  => 'IntroFqsCategoryController@index',
            'as'    => 'introfqscategories.index',
            'title' => 'Common-questions_sections',
            'icon'  => '<i class="la la-list"></i>',
        ]);
        # socials store
        Route::get('introfqscategories/create', [
            'uses'  => 'IntroFqsCategoryController@create',
            'as'    => 'introfqscategories.create',
            'title' => ' صفحة اضاjfgjfgjfفة قسم',
        ]);
        # introfqscategories store
        Route::post('introfqscategories/store', [
            'uses'  => 'IntroFqsCategoryController@store',
            'as'    => 'introfqscategories.store',
            'title' => 'add_secjtrjtrjtrjtrjtrtion',
        ]);
        # introfqscategories update
        Route::get('introfqscategories/{id}/edit', [
            'uses'  => 'IntroFqsCategoryController@edit',
            'as'    => 'introfqscategories.edit',
            'title' => 'edit_section_page',
        ]);
        # introfqscategories update
        Route::put('introfqscategories/{id}', [
            'uses'  => 'IntroFqsCategoryController@update',
            'as'    => 'introfqscategories.update',
            'title' => 'edit_section',
        ]);

        # introfqscategories update
        Route::get('introfqscategories/{id}/Show', [
            'uses'  => 'IntroFqsCategoryController@show',
            'as'    => 'introfqscategories.show',
            'title' => 'view_section_page',
        ]);

        # introfqscategories delete
        Route::delete('introfqscategories/{id}', [
            'uses'  => 'IntroFqsCategoryController@destroy',
            'as'    => 'introfqscategories.delete',
            'title' => 'delete_section',
        ]);

        #delete all introfqscategories
        Route::post('delete-all-introfqscategories', [
            'uses'  => 'IntroFqsCategoryController@destroyAll',
            'as'    => 'introfqscategories.deleteAll',
            'title' => 'delete_multible_section ',
        ]);
        /*------------ end Of introfqscategories ----------*/

        /*------------ start Of introfqs ----------*/
        Route::get('introfqs', [
            'uses'  => 'IntroFqsController@index',
            'as'    => 'introfqs.index',
            'title' => 'questions_sections',
            'icon'  => '<i class="la la-bullhorn"></i>',
        ]);

        # socials store
        Route::get('introfqs/create', [
            'uses'  => 'IntroFqsController@create',
            'as'    => 'introfqs.create',
            'title' => 'add_question',
        ]);

        # introfqs store
        Route::post('introfqs/store', [
            'uses'  => 'IntroFqsController@store',
            'as'    => 'introfqs.store',
            'title' => 'add_question',
        ]);
        # introfqscategories update
        Route::get('introfqs/{id}/edit', [
            'uses'  => 'IntroFqsController@edit',
            'as'    => 'introfqs.edit',
            'title' => 'edit_question',
        ]);
        # introfqscategories update
        Route::get('introfqs/{id}/Show', [
            'uses'  => 'IntroFqsController@show',
            'as'    => 'introfqs.show',
            'title' => 'view_question',
        ]);

        # introfqs update
        Route::put('introfqs/{id}', [
            'uses'  => 'IntroFqsController@update',
            'as'    => 'introfqs.update',
            'title' => 'edit_question',
        ]);

        # introfqs delete
        Route::delete('introfqs/{id}', [
            'uses'  => 'IntroFqsController@destroy',
            'as'    => 'introfqs.delete',
            'title' => 'delete_question',
        ]);

        #delete all introfqs
        Route::post('delete-all-introfqs', [
            'uses'  => 'IntroFqsController@destroyAll',
            'as'    => 'introfqs.deleteAll',
            'title' => 'delete_multible_question',
        ]);
        /*------------ end Of introfqs ----------*/

        /*------------ start Of introparteners ----------*/
        Route::get('introparteners', [
            'uses'  => 'IntroPartenerController@index',
            'as'    => 'introparteners.index',
            'title' => 'Success_Partners',
            'icon'  => '<i class="la la-list"></i>',
        ]);

        # introparteners update
        Route::get('introparteners/{id}/Show', [
            'uses'  => 'IntroPartenerController@show',
            'as'    => 'introparteners.show',
            'title' => 'view_partner_success',
        ]);

        # socials store
        Route::get('introparteners/create', [
            'uses'  => 'IntroPartenerController@create',
            'as'    => 'introparteners.create',
            'title' => 'add_partner',
        ]);

        # introparteners store
        Route::post('introparteners/store', [
            'uses'  => 'IntroPartenerController@store',
            'as'    => 'introparteners.store',
            'title' => 'add_partner',
        ]);

        # introparteners update
        Route::get('introparteners/{id}/edit', [
            'uses'  => 'IntroPartenerController@edit',
            'as'    => 'introparteners.edit',
            'title' => 'edit_partner',
        ]);

        # introparteners update
        Route::put('introparteners/{id}', [
            'uses'  => 'IntroPartenerController@update',
            'as'    => 'introparteners.update',
            'title' => 'edit_partner',
        ]);

        # introparteners delete
        Route::delete('introparteners/{id}', [
            'uses'  => 'IntroPartenerController@destroy',
            'as'    => 'introparteners.delete',
            'title' => 'delete_partner',
        ]);

        #delete all introparteners
        Route::post('delete-all-introparteners', [
            'uses'  => 'IntroPartenerController@destroyAll',
            'as'    => 'introparteners.deleteAll',
            'title' => 'delete_multible_partner',
        ]);
        /*------------ end Of introparteners ----------*/

        /*------------ start Of intromessages ----------*/
        Route::get('intromessages', [
            'uses'  => 'IntroMessagesController@index',
            'as'    => 'intromessages.index',
            'title' => 'Customer_messages',
            'icon'  => '<i class="la la-envelope-square"></i>',
        ]);

        # socials update
        Route::get('intromessages/{id}', [
            'uses'  => 'IntroMessagesController@show',
            'as'    => 'intromessages.show',
            'title' => 'view_message',
        ]);

        # intromessages delete
        Route::delete('intromessages/{id}', [
            'uses'  => 'IntroMessagesController@destroy',
            'as'    => 'intromessages.delete',
            'title' => 'delete_message',
        ]);

        #delete all intromessages
        Route::post('delete-all-intromessages', [
            'uses'  => 'IntroMessagesController@destroyAll',
            'as'    => 'intromessages.deleteAll',
            'title' => 'delete_multible_message',
        ]);
        /*------------ end Of intromessages ----------*/


        /*------------ end Of intro site ----------*/

        /*------------ start Of users Controller ----------*/

        Route::get('all-users', [
            'as'        => 'intro_site',
            'icon'      => '<i class="feather icon-users"></i>',
            'title'     => 'users',
            'type'      => 'parent',
            'sub_route' => true,
            'child'     => [
                'clients.index', 'deliveries.index','clients.show', 'clients.changeStatus', 'clients.store', 'clients.update', 'clients.delete', 'clients.notify', 'clients.deleteAll', 'clients.create', 'clients.edit', 'clients.importFile', 'clients.updateBalance',
                'admins.index', 'admins.block', 'admins.store', 'admins.update', 'admins.edit', 'admins.delete', 'admins.deleteAll', 'admins.create', 'admins.edit', 'admins.notifications', 'admins.notifications.delete', 'admins.show',
                'account-deletion-requests.index', 'account-deletion-requests.show', 'account-deletion-requests.approve', 'account-deletion-requests.reject',
 'deliveries.show', 'deliveries.changeStatus', 'deliveries.store', 'deliveries.update', 'deliveries.delete', 'deliveries.notify', 'deliveries.deleteAll', 'deliveries.create', 'deliveries.edit', 'deliveries.importFile','clients.activate'
            ],
        ]);

        Route::get('clients', [
            'uses'  => 'ClientController@index',
            'as'    => 'clients.index',
            'icon'  => '<i class="feather icon-users"></i>',
            'title' => 'clients',
            // 'type'  => 'parent',
            // 'child' => ['clients.show', 'clients.block', 'clients.store', 'clients.update', 'clients.delete', 'clients.notify', 'clients.deleteAll', 'clients.create', 'clients.edit','clients.importFile','clients.updateBalance'],
        ]);

        # clients store
        Route::get('clients/create', [
            'uses'  => 'ClientController@create',
            'as'    => 'clients.create', 'clients.edit',
            'title' => 'add_client',
        ]);

        Route::get('points-reports', [
            'uses'  => 'ClientController@loyalityPoints',
            'icon'  => '<i class="feather icon-users"></i>',
            'as'    => 'points-reports.index',
            'title' => 'Loyality_points-reports',
        ]);

        Route::post('clients/block', [
            'uses'  => 'ClientController@block',
            'as'    => 'clients.block',
            'title' => 'block_client',
        ])->withoutMiddleware('check-role');

        Route::post('clients/activate', [
            'uses'  => 'ClientController@activate',
            'as'    => 'clients.activate',
            'title' => 'activate_client',
        ])->withoutMiddleware('check-role');


        # clients update
        Route::get('clients/{id}/edit', [
            'uses'  => 'ClientController@edit',
            'as'    => 'clients.edit',
            'title' => 'edit_client',
        ]);
        #store
        Route::post('clients/store', [
            'uses'  => 'ClientController@store',
            'as'    => 'clients.store',
            'title' => 'add_client',
        ]);
        #change status
        Route::post('clients/change-status', [
            'uses'  => 'ClientController@changeStatus',
            'as'    => 'clients.changeStatus',
            'title' => 'change_client_status',
        ]);

        #update
        Route::put('clients/{id}', [
            'uses'  => 'ClientController@update',
            'as'    => 'clients.update',
            'title' => 'edit_client',
        ]);

        #add or deduct balance
        Route::post('clients/update-balance', [
            'uses'  => 'ClientController@updateBalance',
            'as'    => 'clients.updateBalance',
            'title' => 'update_balance',
        ]);
        Route::get('clients/{id}/show', [
            'uses'  => 'ClientController@show',
            'as'    => 'clients.show',
            'title' => 'view_user',
        ]);

        #delete
        Route::delete('clients/{id}', [
            'uses'  => 'ClientController@destroy',
            'as'    => 'clients.delete',
            'title' => 'delete_user',
        ]);

        #delete
        Route::post('delete-all-clients', [
            'uses'  => 'ClientController@destroyAll',
            'as'    => 'clients.deleteAll',
            'title' => 'delete_multible_user',
        ]);

        #notify
        Route::post('admins/clients/notify', [
            'uses'  => 'ClientController@notify',
            'as'    => 'clients.notify',
            'title' => 'Send_user_notification',
        ]);
        #import
        Route::post('clients/importFile', [
            'uses'  => 'ClientController@importFile',
            'as'    => 'clients.importFile',
            'title' => 'importfile',
        ]);
        /************ #Clients ************/



          // Delivery routes
        Route::get('deliveries', [
            'uses'  => 'DeliveryController@index',
            'as'    => 'deliveries.index',
            'icon'  => '<i class="feather icon-users"></i>',
            'title' => 'deliveries',
        ]);

        Route::get('deliveries/create', [
            'uses'  => 'DeliveryController@create',
            'as'    => 'deliveries.create',
            'title' => 'add_delivery',
        ]);

        Route::post('deliveries/store', [
            'uses'  => 'DeliveryController@store',
            'as'    => 'deliveries.store',
            'title' => 'add_delivery',
        ]);

        Route::get('deliveries/{id}/edit', [
            'uses'  => 'DeliveryController@edit',
            'as'    => 'deliveries.edit',
            'title' => 'edit_delivery',
        ]);

        Route::put('deliveries/{id}', [
            'uses'  => 'DeliveryController@update',
            'as'    => 'deliveries.update',
            'title' => 'edit_delivery',
        ]);

        Route::get('deliveries/{id}/show', [
            'uses'  => 'DeliveryController@show',
            'as'    => 'deliveries.show',
            'title' => 'view_delivery',
        ]);
           Route::post('deliveries/change-status', [
            'uses'  => 'DeliveryController@changeStatus',
            'as'    => 'deliveries.changeStatus',
            'title' => 'change_client_status',
        ]);

        Route::delete('deliveries/{id}', [
            'uses'  => 'DeliveryController@destroy',
            'as'    => 'deliveries.delete',
            'title' => 'delete_delivery',
        ]);

        Route::post('delete-all-deliveries', [
            'uses'  => 'DeliveryController@destroyAll',
            'as'    => 'deliveries.deleteAll',
            'title' => 'delete_multible_delivery',
        ]);

               #notify
        Route::post('admins/deliveries/notify', [
            'uses'  => 'DeliveryController@notify',
            'as'    => 'deliveries.notify',
            'title' => 'Send_user_notification',
        ]);
        #import
        Route::post('deliveries/importFile', [
            'uses'  => 'DeliveryController@importFile',
            'as'    => 'deliveries.importFile',
            'title' => 'importfile',
        ]);


        // Account Deletion Requests
        Route::get('account-deletion-requests', [
            'uses'  => 'AccountDeletionRequestController@index',
            'as'    => 'account-deletion-requests.index',
            'icon'  => '<i class="feather icon-trash-2"></i>',
            'title' => 'account_deletion_requests',
        ]);

        Route::get('account-deletion-requests/{id}', [
            'uses'  => 'AccountDeletionRequestController@show',
            'as'    => 'account-deletion-requests.show',
            'title' => 'view_deletion_request',
        ]);

        Route::post('account-deletion-requests/{id}/approve', [
            'uses'  => 'AccountDeletionRequestController@approve',
            'as'    => 'account-deletion-requests.approve',
            'title' => 'approve_deletion_request',
        ]);

        Route::post('account-deletion-requests/{id}/reject', [
            'uses'  => 'AccountDeletionRequestController@reject',
            'as'    => 'account-deletion-requests.reject',
            'title' => 'reject_deletion_request',
        ]);

        Route::get('admins', [
            'uses'  => 'AdminController@index',
            'as'    => 'admins.index',
            'title' => 'admins',
            'icon'  => '<i class="feather icon-users"></i>',
            // 'type'  => 'parent',
            // 'child' => [
            //     'admins.block', 'admins.index', 'admins.store', 'admins.update', 'admins.edit',
            //     'admins.delete', 'admins.deleteAll', 'admins.create', 'admins.edit', 'admins.notifications',
            //     'admins.notifications.delete', 'admins.show',
            // ],
        ]);

        # admins store
        Route::get('show-notifications', [
            'uses'  => 'AdminController@notifications',
            'as'    => 'admins.notifications',
            'title' => 'notification_page',
        ]);

        #block
        Route::post('admins/block', [
            'uses'  => 'AdminController@block',
            'as'    => 'admins.block',
            'title' => 'block_admin',
        ]);

        # admins store
        Route::post('delete-notifications', [
            'uses'  => 'AdminController@deleteNotifications',
            'as'    => 'admins.notifications.delete',
            'title' => 'delete_notification',
        ]);

        # admins store
        Route::get('admins/create', [
            'uses'  => 'AdminController@create',
            'as'    => 'admins.create',
            'title' => 'add_admin',
        ]);

        #store
        Route::post('admins/store', [
            'uses'  => 'AdminController@store',
            'as'    => 'admins.store',
            'title' => 'add_admin',
        ]);

        # admins update
        Route::get('admins/{id}/edit', [
            'uses'  => 'AdminController@edit',
            'as'    => 'admins.edit',
            'title' => 'edit_admin',
        ]);
        #update
        Route::put('admins/{id}', [
            'uses'  => 'AdminController@update',
            'as'    => 'admins.update',
            'title' => 'edit_admin',
        ]);

        Route::get('admins/{id}/show', [
            'uses'  => 'AdminController@show',
            'as'    => 'admins.show',
            'title' => 'view_admin',
        ]);

        #delete
        Route::delete('admins/{id}', [
            'uses'  => 'AdminController@destroy',
            'as'    => 'admins.delete',
            'title' => 'delete_admin',
        ]);

        #delete
        Route::post('delete-all-admins', [
            'uses'  => 'AdminController@destroyAll',
            'as'    => 'admins.deleteAll',
            'title' => 'delete_multible_admin',
        ]);

        /************ #Admins ************/
        Route::get('orders-management', [
            'as'        => 'orders.management',
            'title'     => 'orders',
            'icon'      => '<i class="feather icon-shopping-cart"></i>',
            'type'      => 'parent',
            'sub_route' => true,
            'child'     => [
                'orders.index', 'orders.show',
                 'orderrates.show','orderrates.deleteAll','orderrates.index', 'orderrates.updateStatus',
                 'cancel_request_orders.index' , 'cancel_request_orders.show' , 'cancel_request_orders.accept' , 'cancel_request_orders.reject',
                 'refund_orders.index' , 'refund_orders.show' , 'refund_orders.accept' , 'refund_orders.refuse','problem_orders.index'
            ],
        ]);

        /*------------ start Of products management ----------*/
        Route::get('products-management', [
            'as'        => 'products.management',
            'title'     => 'products_management',
            'icon'      => '<i class="feather icon-package"></i>',
            'type'      => 'parent',
            'sub_route' => true,
            'child'     => [
                'products.index', 'products.create', 'products.store', 'products.edit', 'products.update', 'products.show', 'products.delete', 'products.deleteAll', 'products.toggleStatus',
                // 'product-categories.index', 'product-categories.create', 'product-categories.store', 'product-categories.edit', 'product-categories.update', 'product-categories.show', 'product-categories.delete', 'product-categories.deleteAll'
                'categories.index',
                'categories.create','categories.store','categories.edit','categories.update','categories.show','categories.delete','categories.deleteAll','transactions.index','transactions.show','transactions.accept','transactions.reject'
            ],
        ]);

        /*------------ start Of products ----------*/
        Route::get('products', [
            'uses'      => 'ProductController@index',
            'as'        => 'products.index',
            'title'     => 'products',
            'icon'      => '<i class="feather icon-package"></i>',
        ]);


Route::post('products/delete-image/{media}', [
    'uses'  => 'ProductController@deleteImage',
    'as'    => 'products.delete_image',
    'title' => 'delete_product_image',
]);

        # products store
        Route::get('products/create', [
            'uses'  => 'ProductController@create',
            'as'    => 'products.create',
            'title' => 'add_product_page',
        ]);

        # products store
        Route::post('products/store', [
            'uses'  => 'ProductController@store',
            'as'    => 'products.store',
            'title' => 'add_product',
        ]);

        # products update
        Route::get('products/{id}/edit', [
            'uses'  => 'ProductController@edit',
            'as'    => 'products.edit',
            'title' => 'update_product_page',
        ]);

        # products update
        Route::put('products/{id}', [
            'uses'  => 'ProductController@update',
            'as'    => 'products.update',
            'title' => 'update_product',
        ]);

        # products show
        Route::get('products/{id}/Show', [
            'uses'  => 'ProductController@show',
            'as'    => 'products.show',
            'title' => 'show_product_page',
        ]);

        # products delete
        Route::delete('products/{id}', [
            'uses'  => 'ProductController@destroy',
            'as'    => 'products.delete',
            'title' => 'delete_product',
        ]);
        #delete all products
        Route::post('delete-all-products', [
            'uses'  => 'ProductController@deleteAll',
            'as'    => 'products.deleteAll',
            'title' => 'delete_group_of_products',
        ]);

        # toggle product status
        Route::post('products/toggle-status', [
            'uses'  => 'ProductController@toggleStatus',
            'as'    => 'products.toggleStatus',
            'title' => 'toggle_product_status',
        ]);

                /*------------ start Of categories ----------*/
        Route::get('categories', [
            'uses'      => 'CategoryController@index',
            'as'        => 'categories.index',
            'title'     => 'categories',
            'icon'      => '<i class="feather icon-grid"></i>',
        ]);

        # categories store
        Route::get('categories/create', [
            'uses'  => 'CategoryController@create',
            'as'    => 'categories.create',
            'title' => 'add_category_page',
        ]);

        # categories store
        Route::post('categories/store', [
            'uses'  => 'CategoryController@store',
            'as'    => 'categories.store',
            'title' => 'add_category',
        ]);

        # categories update
        Route::get('categories/{id}/edit', [
            'uses'  => 'CategoryController@edit',
            'as'    => 'categories.edit',
            'title' => 'update_category_page',
        ]);

        # categories update
        Route::put('categories/{id}', [
            'uses'  => 'CategoryController@update',
            'as'    => 'categories.update',
            'title' => 'update_category',
        ]);

        # categories show
        Route::get('categories/{id}/show', [
            'uses'  => 'CategoryController@show',
            'as'    => 'categories.show',
            'title' => 'show_category_page',
        ]);

        # categories delete
        Route::delete('categories/{id}', [
            'uses'  => 'CategoryController@destroy',
            'as'    => 'categories.delete',
            'title' => 'delete_category',
        ]);
        #delete all categories
        Route::post('delete-all-categories', [
            'uses'  => 'CategoryController@destroyAll',
            'as'    => 'categories.deleteAll',
            'title' => 'delete_group_of_categories',
        ]);
        /*------------ end Of categories ----------*/
        /*------------ start Of categories ----------*/
        Route::get('categories', [
            'uses'  => 'CategoryController@index',
            'as'    => 'categories.index',
            'title' => 'categories',
            'icon'  => '<i class="feather icon-grid"></i>',
        ]);
        /*------------ end Of categories ----------*/
        /*------------ end Of products ----------*/

 Route::get('transactions', [
            'uses'      => 'TransactionController@index',
            'as'        => 'transactions.index',
            'title'     => 'transactions',
            'icon'      => '<i class="feather icon-grid"></i>',
        ]);
           Route::get('transactions/{id}/show', [
            'uses'  => 'TransactionController@show',
            'as'    => 'transactions.show',
            'title' => 'show_category_page',
        ]);

            // Accept / Reject transaction (withdraw) requests
            Route::post('transactions/{id}/accept', [
                'uses'  => 'TransactionController@accept',
                'as'    => 'transactions.accept',
                'title' => 'accept_transaction',
            ]);

            Route::post('transactions/{id}/reject', [
                'uses'  => 'TransactionController@reject',
                'as'    => 'transactions.reject',
                'title' => 'reject_transaction',
            ]);


        /*------------ start Of notifications ----------*/
        Route::get('marketing', [
            'as'        => 'marketing',
            'icon'      => '<i class="feather icon-flag"></i>',
            'title'     => 'marketing',
            'type'      => 'parent',
            'sub_route' => true,
            'child'     => [
                'coupons.index', 'coupons.orders','coupons.show', 'coupons.create', 'coupons.store', 'coupons.edit', 'coupons.update', 'coupons.delete', 'coupons.deleteAll', 'coupons.renew',
                'images.index', 'images.show', 'images.create', 'images.store', 'images.edit', 'images.update', 'images.delete', 'images.deleteAll',
                'branches.index','branches.create', 'branches.store','branches.edit', 'branches.update', 'branches.show', 'branches.delete'  ,'branches.deleteAll' ,
                // 'seos.index','seos.show', 'seos.create', 'seos.edit', 'seos.index', 'seos.store', 'seos.update', 'seos.delete', 'seos.deleteAll',
            ],
        ]);


    /*------------ start Of orderrates ----------*/
        Route::get('orderrates', [
            'uses'      => 'OrderRateController@index',
            'as'        => 'orderrates.index',
            'title'     => 'orderrates',
            'icon'      => '<i class="feather icon-image"></i>',
            'sub_route' => false,
        ]);



        # orderrates show
        Route::get('orderrates/{id}/Show', [
            'uses'  => 'OrderRateController@show',
            'as'    => 'orderrates.show',
            'title' => 'show_orderrate_page'
        ]);

        # orderrates status update
        Route::post('orderrates/{id}/status', [
            'uses'  => 'OrderRateController@updateStatus',
            'as'    => 'orderrates.updateStatus',
            'title' => 'update_orderrate_status'
        ]);


        #delete all orderrates
        Route::post('delete-all-orderrates', [
            'uses'  => 'OrderRateController@destroyAll',
            'as'    => 'orderrates.deleteAll',
            'title' => 'delete_group_of_orderrates'
        ]);



    // /*------------ start Of rates ----------*/
    //     Route::get('rates', [
    //         'uses'      => 'RateController@index',
    //         'as'        => 'rates.index',
    //         'title'     => 'rates',
    //         'icon'      => '<i class="feather icon-image"></i>',
    //         'type'      => 'parent',
    //         'sub_route' => false,
    //         'child'     => [ 'rates.show', 'rates.delete', 'rates.deleteAll', 'rates.updateStatus', 'rates.publishVideo']
    //     ]);

    //     # rates store
    //     Route::get('rates/create', [
    //         'uses'  => 'RateController@create',
    //         'as'    => 'rates.create',
    //         'title' => 'add_rate_page'
    //     ]);


    //     # rates store
    //     Route::post('rates/store', [
    //         'uses'  => 'RateController@store',
    //         'as'    => 'rates.store',
    //         'title' => 'add_rate'
    //     ]);

    //     # rates update
    //     Route::get('rates/{id}/edit', [
    //         'uses'  => 'RateController@edit',
    //         'as'    => 'rates.edit',
    //         'title' => 'update_rate_page'
    //     ]);

    //     # rates update
    //     Route::put('rates/{id}', [
    //         'uses'  => 'RateController@update',
    //         'as'    => 'rates.update',
    //         'title' => 'update_rate'
    //     ]);

    //     # rates show
    //     Route::get('rates/{id}/Show', [
    //         'uses'  => 'RateController@show',
    //         'as'    => 'rates.show',
    //         'title' => 'show_rate_page'
    //     ]);

    //     # rates status update
    //     Route::post('rates/{id}/update-status', [
    //         'uses'  => 'RateController@updateStatus',
    //         'as'    => 'rates.updateStatus',
    //         'title' => 'update_rate_status'
    //     ]);

    //     # rates publish video
    //     Route::post('rates/{id}/publish-video', [
    //         'uses'  => 'RateController@publishVideo',
    //         'as'    => 'rates.publishVideo',
    //         'title' => 'publish_rate_video'
    //     ]);

    //     # rates delete
    //     Route::delete('rates/{id}', [
    //         'uses'  => 'RateController@destroy',
    //         'as'    => 'rates.delete',
    //         'title' => 'delete_rate'
    //     ]);
    //     #delete all rates
    //     Route::post('delete-all-rates', [
    //         'uses'  => 'RateController@destroyAll',
    //         'as'    => 'rates.deleteAll',
    //         'title' => 'delete_group_of_rates'
    //     ]);
    // /*------------ end Of rates ----------*/

    /*------------ start Of problems ----------*/
        Route::get('problems', [
            'uses'      => 'ProblemController@index',
            'as'        => 'problems.index',
            'title'     => 'problems',
            'icon'  => '<i class="feather icon-alert-circle"></i>',
        ]);

        # problems store
        Route::get('problems/create', [
            'uses'  => 'ProblemController@create',
            'as'    => 'problems.create',
            'title' => 'add_problem_page'
        ]);


        # problems store
        Route::post('problems/store', [
            'uses'  => 'ProblemController@store',
            'as'    => 'problems.store',
            'title' => 'add_problem'
        ]);

        # problems update
        Route::get('problems/{id}/edit', [
            'uses'  => 'ProblemController@edit',
            'as'    => 'problems.edit',
            'title' => 'update_problem_page'
        ]);

        # problems update
        Route::put('problems/{id}', [
            'uses'  => 'ProblemController@update',
            'as'    => 'problems.update',
            'title' => 'update_problem'
        ]);

        # problems show
        Route::get('problems/{id}/Show', [
            'uses'  => 'ProblemController@show',
            'as'    => 'problems.show',
            'title' => 'show_problem_page'
        ]);

        # problems delete
        Route::delete('problems/{id}', [
            'uses'  => 'ProblemController@destroy',
            'as'    => 'problems.delete',
            'title' => 'delete_problem'
        ]);
        #delete all problems
        Route::post('delete-all-problems', [
            'uses'  => 'ProblemController@destroyAll',
            'as'    => 'problems.deleteAll',
            'title' => 'delete_group_of_problems'
        ]);
    /*------------ end Of problems ----------*/

    /*------------ start Of problems ----------*/
        Route::get('problems', [
            'uses'      => 'ProblemController@index',
            'as'        => 'problems.index',
            'title'     => 'problems',
            'icon'      => '<i class="feather icon-circle"></i>',

        ]);

        # problems store
        Route::get('problems/create', [
            'uses'  => 'ProblemController@create',
            'as'    => 'problems.create',
            'title' => 'add_problem_page'
        ]);


        # problems store
        Route::post('problems/store', [
            'uses'  => 'ProblemController@store',
            'as'    => 'problems.store',
            'title' => 'add_problem'
        ]);

        # problems update
        Route::get('problems/{id}/edit', [
            'uses'  => 'ProblemController@edit',
            'as'    => 'problems.edit',
            'title' => 'update_problem_page'
        ]);

        # problems update
        Route::put('problems/{id}', [
            'uses'  => 'ProblemController@update',
            'as'    => 'problems.update',
            'title' => 'update_problem'
        ]);

        # problems show
        Route::get('problems/{id}/Show', [
            'uses'  => 'ProblemController@show',
            'as'    => 'problems.show',
            'title' => 'show_problem_page'
        ]);

        # problems delete
        Route::delete('problems/{id}', [
            'uses'  => 'ProblemController@destroy',
            'as'    => 'problems.delete',
            'title' => 'delete_problem'
        ]);
        #delete all problems
        Route::post('delete-all-problems', [
            'uses'  => 'ProblemController@destroyAll',
            'as'    => 'problems.deleteAll',
            'title' => 'delete_group_of_problems'
        ]);
    /*------------ end Of problems ----------*/

    /*------------ start Of brands ----------*/
        Route::get('brands', [
            'uses'      => 'BrandController@index',
            'as'        => 'brands.index',
            'title'     => 'brands',
            'icon'      => '<i class="feather icon-image"></i>',
            // 'type'      => 'parent',
            // 'sub_route' => false,
            // 'child'     => ['brands.create', 'brands.store','brands.edit', 'brands.update', 'brands.show', 'brands.delete'  ,'brands.deleteAll' ,]
        ]);

        # brands store
        Route::get('brands/create', [
            'uses'  => 'BrandController@create',
            'as'    => 'brands.create',
            'title' => 'add_brand_page'
        ]);


        # brands store
        Route::post('brands/store', [
            'uses'  => 'BrandController@store',
            'as'    => 'brands.store',
            'title' => 'add_brand'
        ]);

        # brands update
        Route::get('brands/{id}/edit', [
            'uses'  => 'BrandController@edit',
            'as'    => 'brands.edit',
            'title' => 'update_brand_page'
        ]);

        # brands update
        Route::put('brands/{id}', [
            'uses'  => 'BrandController@update',
            'as'    => 'brands.update',
            'title' => 'update_brand'
        ]);

        # brands show
        Route::get('brands/{id}/Show', [
            'uses'  => 'BrandController@show',
            'as'    => 'brands.show',
            'title' => 'show_brand_page'
        ]);

        # brands delete
        Route::delete('brands/{id}', [
            'uses'  => 'BrandController@destroy',
            'as'    => 'brands.delete',
            'title' => 'delete_brand'
        ]);
        #delete all brands
        Route::post('delete-all-brands', [
            'uses'  => 'BrandController@destroyAll',
            'as'    => 'brands.deleteAll',
            'title' => 'delete_group_of_brands'
        ]);
    /*------------ end Of brands ----------*/
    #new_routes_here











    });

        Route::get('notifications', [
            'uses'      => 'NotificationController@index',
            'as'        => 'notifications.index',
            'title'     => 'notifications',
            'icon'      => '<i class="ficon feather icon-bell"></i>',
            'sub_route' => true,
            'child'     => ['notifications.send'],
        ]);

        # coupons store
        Route::post('send-notifications', [
            'uses'  => 'NotificationController@sendNotifications',
            'as'    => 'notifications.send',
            'title' => 'send_notification_email_to_client',
        ]);
        /*------------ end Of notifications ----------*/
        /*------------ start Of coupons ----------*/
        Route::get('coupons', [
            'uses'      => 'CouponController@index',
            'as'        => 'coupons.index',
            'title'     => 'coupons',
            'icon'      => '<i class="fa fa-gift"></i>',
            'sub_route' => true,
            'child'     => ['coupons.show', 'coupons.create', 'coupons.store', 'coupons.edit', 'coupons.update', 'coupons.delete', 'coupons.deleteAll', 'coupons.renew'],
        ]);

        Route::get('coupons/{id}/show', [
            'uses'  => 'CouponController@show',
            'as'    => 'coupons.show',
            'title' => 'view_coupons',
        ]);

        # coupons store
        Route::get('coupons/create', [
            'uses'  => 'CouponController@create',
            'as'    => 'coupons.create',
            'title' => 'add_coupons',
        ]);

        # coupons store
        Route::post('coupons/store', [
            'uses'  => 'CouponController@store',
            'as'    => 'coupons.store',
            'title' => 'add_coupons',
        ]);

        # coupons update
        Route::get('coupons/{id}/edit', [
            'uses'  => 'CouponController@edit',
            'as'    => 'coupons.edit',
            'title' => 'edit_coupons',
        ]);

        # coupons update
        Route::put('coupons/{id}', [
            'uses'  => 'CouponController@update',
            'as'    => 'coupons.update',
            'title' => 'edit_coupons',
        ]);

        Route::get('coupons/{coupon}/orders', [
            'uses'  => 'CouponController@orders',
            'as'    => 'coupons.orders',
            'title' => 'orders',
        ]);

        # renew coupon
        Route::post('coupons/renew', [
            'uses'  => 'CouponController@renew',
            'as'    => 'coupons.renew',
            'title' => 'update_coupon_status',
        ]);

        # coupons delete
        Route::delete('coupons/{id}', [
            'uses'  => 'CouponController@destroy',
            'as'    => 'coupons.delete',
            'title' => 'delete_coupons',
        ]);
        #delete all coupons
        Route::post('delete-all-coupons', [
            'uses'  => 'CouponController@destroyAll',
            'as'    => 'coupons.deleteAll',
            'title' => 'delete_multible_coupons',
        ]);
        /*------------ end Of coupons ----------*/

        /*------------ start Of images ----------*/
        Route::get('images', [
            'uses'  => 'ImageController@index',
            'as'    => 'images.index',
            'title' => 'advertising_banners',
            'icon'  => '<i class="feather icon-image"></i>',
            // 'type'      => 'parent',
            // 'sub_route' => false,
            // 'child'     => ['images.show', 'images.create', 'images.store', 'images.edit', 'images.update', 'images.delete', 'images.deleteAll'],
        ]);
        Route::get('images/{id}/show', [
            'uses'  => 'ImageController@show',
            'as'    => 'images.show',
            'title' => 'view_of_banner',
        ]);
        # images store
        Route::get('images/create', [
            'uses'  => 'ImageController@create',
            'as'    => 'images.create',
            'title' => 'add_a_banner',
        ]);

        # images store
        Route::post('images/store', [
            'uses'  => 'ImageController@store',
            'as'    => 'images.store',
            'title' => 'add_a_banner',
        ]);

        # images update
        Route::get('images/{id}/edit', [
            'uses'  => 'ImageController@edit',
            'as'    => 'images.edit',
            'title' => 'modification_of_banner',
        ]);

        # images update
        Route::put('images/{id}', [
            'uses'  => 'ImageController@update',
            'as'    => 'images.update',
            'title' => 'modification_of_banner',
        ]);

        # images delete
        Route::delete('images/{id}', [
            'uses'  => 'ImageController@destroy',
            'as'    => 'images.delete',
            'title' => 'delete_a_banner',
        ]);
        #delete all images
        Route::post('delete-all-images', [
            'uses'  => 'ImageController@destroyAll',
            'as'    => 'images.deleteAll',
            'title' => 'delete_multible_banner',
        ]);
        /*------------ end Of images ----------*/

        /*------------ start Of socials ----------*/
        Route::get('socials', [
            'uses'  => 'SocialController@index',
            'as'    => 'socials.index',
            'title' => 'socials',
            'icon'  => '<i class="feather icon-message-circle"></i>',
            // 'type'      => 'parent',
            // 'sub_route' => false,
            // 'child'     => ['socials.show', 'socials.create', 'socials.store', 'socials.show', 'socials.update', 'socials.edit', 'socials.delete', 'socials.deleteAll'],
        ]);
        # socials update
        Route::get('socials/{id}/Show', [
            'uses'  => 'SocialController@show',
            'as'    => 'socials.show',
            'title' => 'view_socials',
        ]);
        # socials store
        Route::get('socials/create', [
            'uses'  => 'SocialController@create',
            'as'    => 'socials.create',
            'title' => 'add_socials',
        ]);

        # socials store
        Route::post('socials', [
            'uses'  => 'SocialController@store',
            'as'    => 'socials.store',
            'title' => 'add_socials',
        ]);
        # socials update
        Route::get('socials/{id}/edit', [
            'uses'  => 'SocialController@edit',
            'as'    => 'socials.edit',
            'title' => 'edit_socials',
        ]);
        # socials update
        Route::put('socials/{id}', [
            'uses'  => 'SocialController@update',
            'as'    => 'socials.update',
            'title' => 'edit_socials',
        ]);

        # socials delete
        Route::delete('socials/{id}', [
            'uses'  => 'SocialController@destroy',
            'as'    => 'socials.delete',
            'title' => 'delete_socials',
        ]);

        #delete all socials
        Route::post('delete-all-socials', [
            'uses'  => 'SocialController@destroyAll',
            'as'    => 'socials.deleteAll',
            'title' => 'delete_multible_socials',
        ]);
        /*------------ end Of socials ----------*/
        /*------------ start Of intros ----------*/
        // Route::get('intros', [
        //     'uses'  => 'IntroController@index',
        //     'as'    => 'intros.index',
        //     'title' => 'definition_pages',
        //     'icon'  => '<i class="feather icon-loader"></i>',
        //     // 'type'      => 'parent',
        //     // 'sub_route' => false,
        //     // 'child'     => ['intros.show', 'intros.create', 'intros.store', 'intros.edit', 'intros.update', 'intros.delete', 'intros.deleteAll'],
        // ]);

        # intros update
        Route::get('intros/{id}/Show', [
            'uses'  => 'IntroController@show',
            'as'    => 'intros.show',
            'title' => 'view_a_profile_page',
        ]);

        # intros store
        Route::get('intros/create', [
            'uses'  => 'IntroController@create',
            'as'    => 'intros.create',
            'title' => 'add_a_profile_page',
        ]);

        # intros store
        Route::post('intros/store', [
            'uses'  => 'IntroController@store',
            'as'    => 'intros.store',
            'title' => 'add_a_profile_page',
        ]);

        # intros update
        Route::get('intros/{id}/edit', [
            'uses'  => 'IntroController@edit',
            'as'    => 'intros.edit',
            'title' => 'edit_a_profile_page',
        ]);

        # intros update
        Route::put('intros/{id}', [
            'uses'  => 'IntroController@update',
            'as'    => 'intros.update',
            'title' => 'edit_a_profile_page',
        ]);

        # intros delete
        Route::delete('intros/{id}', [
            'uses'  => 'IntroController@destroy',
            'as'    => 'intros.delete',
            'title' => 'delete_a_profile_page',
        ]);
        #delete all intros
        Route::post('delete-all-intros', [
            'uses'  => 'IntroController@destroyAll',
            'as'    => 'intros.deleteAll',
            'title' => 'delete_amultible_profile_page',
        ]);
        /*------------ end Of intros ----------*/

        /*------------ start Of seos ----------*/
        // Route::get('seos', [
        //     'uses'  => 'SeoController@index',
        //     'as'    => 'seos.index',
        //     'title' => 'seo',
        //     'icon'  => '<i class="feather icon-list"></i>',
        //     // 'type'  => 'parent',
        //     // 'child' => [
        //     //     'seos.show', 'seos.create', 'seos.edit', 'seos.index', 'seos.store', 'seos.update', 'seos.delete', 'seos.deleteAll',
        //     // ],
        // ]);
        // # seos update
        // Route::get('seos/{id}/Show', [
        //     'uses'  => 'SeoController@show',
        //     'as'    => 'seos.show',
        //     'title' => 'view_seo',
        // ]);

        // # seos store
        // Route::get('seos/create', [
        //     'uses'  => 'SeoController@create',
        //     'as'    => 'seos.create',
        //     'title' => 'add_seo',
        // ]);

        // # seos update
        // Route::get('seos/{id}/edit', [
        //     'uses'  => 'SeoController@edit',
        //     'as'    => 'seos.edit',
        //     'title' => 'edit_seo',
        // ]);

        // #store
        // Route::post('seos/store', [
        //     'uses'  => 'SeoController@store',
        //     'as'    => 'seos.store',
        //     'title' => 'add_seo',
        // ]);

        // #update
        // Route::put('seos/{id}', [
        //     'uses'  => 'SeoController@update',
        //     'as'    => 'seos.update',
        //     'title' => 'edit_seo',
        // ]);

        // #deletّe
        // Route::delete('seos/{id}', [
        //     'uses'  => 'SeoController@destroy',
        //     'as'    => 'seos.delete',
        //     'title' => 'delete_seo',
        // ]);
        // #delete
        // Route::post('delete-all-seos', [
        //     'uses'  => 'SeoController@destroyAll',
        //     'as'    => 'seos.deleteAll',
        //     'title' => 'delete_multible_seo',
        // ]);
        // /*------------ end Of seos ----------*/

        /*------------ start Of statistics ----------*/
        // Route::get('statistics', [
        //     'uses'  => 'StatisticsController@index',
        //     'as'    => 'statistics.index',
        //     'title' => 'Statistics',
        //     'icon'  => '<i class="feather icon-activity"></i>',
        //     // 'type'  => 'parent',
        //     // 'child' => [
        //     //     'statistics.index',
        //     // ],
        // ]);
        /*------------ end Of statistics ----------*/
        /*------------ start Of countries ----------*/
        Route::get('countries-cities', [
            'as'        => 'countries_cities',
            'icon'      => '<i class="fa fa-map-marker"></i>',
            'title'     => 'countries_cities',
            'type'      => 'parent',
            'sub_route' => true,
            'child'     => [
                 'countries.index','countries.show', 'countries.create', 'countries.store', 'countries.edit', 'countries.update', 'countries.delete', 'countries.deleteAll',
                'regions.index', 'regions.create', 'regions.store', 'regions.edit', 'regions.update', 'regions.show', 'regions.delete', 'regions.deleteAll',
                'cities.index', 'cities.create', 'cities.store', 'cities.edit', 'cities.show', 'cities.update', 'cities.delete', 'cities.deleteAll', 'districts.index', 'districts.create', 'districts.store', 'districts.edit', 'districts.update', 'districts.show', 'districts.delete', 'districts.deleteAll',
            ],
        ]);

        Route::get('countries', [
            'uses'  => 'CountryController@index',
            'as'    => 'countries.index',
            'title' => 'countries',
            'icon'  => '<i class="feather icon-flag"></i>',
            // 'type'      => 'parent',
            // 'sub_route' => false,
            // 'child'     => ['countries.show', 'countries.create', 'countries.store', 'countries.edit', 'countries.update', 'countries.delete', 'countries.deleteAll'],
        ]);

        Route::get('countries/{id}/show', [
            'uses'  => 'CountryController@show',
            'as'    => 'countries.show',
            'title' => 'view_country',
        ]);

        # countries store
        Route::get('countries/create', [
            'uses'  => 'CountryController@create',
            'as'    => 'countries.create',
            'title' => 'add_country',
        ]);

        # countries store
        Route::post('countries/store', [
            'uses'  => 'CountryController@store',
            'as'    => 'countries.store',
            'title' => 'add_country',
        ]);

        # countries update
        Route::get('countries/{id}/edit', [
            'uses'  => 'CountryController@edit',
            'as'    => 'countries.edit',
            'title' => 'edit_country',
        ]);

        # countries update
        Route::put('countries/{id}', [
            'uses'  => 'CountryController@update',
            'as'    => 'countries.update',
            'title' => 'edit_country',
        ]);

        # countries delete
        Route::delete('countries/{id}', [
            'uses'  => 'CountryController@destroy',
            'as'    => 'countries.delete',
            'title' => 'delete_country',
        ]);
        #delete all countries
        Route::post('delete-all-countries', [
            'uses'  => 'CountryController@destroyAll',
            'as'    => 'countries.deleteAll',
            'title' => 'delete_multible_country',
        ]);
        /*------------ end Of countries ----------*/

        /*------------ start Of regions ----------*/
        Route::get('regions', [
            'uses'  => 'RegionController@index',
            'as'    => 'regions.index',
            'title' => 'regions',
            'icon'  => '<i class="fa fa-map-marker"></i>',
            // 'type'      => 'parent',
            // 'sub_route' => false,
            // 'child'     => ['regions.create', 'regions.store', 'regions.edit', 'regions.update', 'regions.show', 'regions.delete', 'regions.deleteAll'],
        ]);

        # regions store
        Route::get('regions/create', [
            'uses'  => 'RegionController@create',
            'as'    => 'regions.create',
            'title' => 'add_region_page',
        ]);

        # regions store
        Route::post('regions/store', [
            'uses'  => 'RegionController@store',
            'as'    => 'regions.store',
            'title' => 'add_region',
        ]);

        # regions update
        Route::get('regions/{id}/edit', [
            'uses'  => 'RegionController@edit',
            'as'    => 'regions.edit',
            'title' => 'update_region_page',
        ]);

        # regions update
        Route::put('regions/{id}', [
            'uses'  => 'RegionController@update',
            'as'    => 'regions.update',
            'title' => 'update_region',
        ]);

        # regions show
        Route::get('regions/{id}/Show', [
            'uses'  => 'RegionController@show',
            'as'    => 'regions.show',
            'title' => 'show_region_page',
        ]);

        # regions delete
        Route::delete('regions/{id}', [
            'uses'  => 'RegionController@destroy',
            'as'    => 'regions.delete',
            'title' => 'delete_region',
        ]);
        #delete all regions
        Route::post('delete-all-regions', [
            'uses'  => 'RegionController@destroyAll',
            'as'    => 'regions.deleteAll',
            'title' => 'delete_group_of_regions',
        ]);
/*------------ end Of regions ----------*/

        /*------------ start Of cities ----------*/
        Route::get('cities', [
            'uses'  => 'CityController@index',
            'as'    => 'cities.index',
            'title' => 'cities',
            'icon'  => '<i class="feather icon-globe"></i>',
            // 'type'      => 'parent',
            // 'sub_route' => false,
            // 'child'     => ['cities.create', 'cities.store', 'cities.edit', 'cities.show', 'cities.update', 'cities.delete', 'cities.deleteAll'],
        ]);

        # cities store
        Route::get('cities/create', [
            'uses'  => 'CityController@create',
            'as'    => 'cities.create',
            'title' => 'add_city',
        ]);

        # cities store
        Route::post('cities/store', [
            'uses'  => 'CityController@store',
            'as'    => 'cities.store',
            'title' => 'add_city',
        ]);

        # cities update
        Route::get('cities/{id}/edit', [
            'uses'  => 'CityController@edit',
            'as'    => 'cities.edit',
            'title' => 'edit_city',
        ]);

        # cities update
        Route::put('cities/{id}', [
            'uses'  => 'CityController@update',
            'as'    => 'cities.update',
            'title' => 'edit_city',
        ]);

        Route::get('cities/{id}/show', [
            'uses'  => 'CityController@show',
            'as'    => 'cities.show',
            'title' => 'view_city',
        ]);

        # cities delete
        Route::delete('cities/{id}', [
            'uses'  => 'CityController@destroy',
            'as'    => 'cities.delete',
            'title' => 'delete_city',
        ]);
        #delete all cities
        Route::post('delete-all-cities', [
            'uses'  => 'CityController@destroyAll',
            'as'    => 'cities.deleteAll',
            'title' => 'delete_multible_city',
        ]);
                /*------------ start Of districts ----------*/
                Route::get('districts', [
                    'uses'  => 'DistrictController@index',
                    'as'    => 'districts.index',
                    'title' => 'districts',
                    'icon'  => '<i class="feather icon-map-pin"></i>',
                ]);

                # districts store
                Route::get('districts/create', [
                    'uses'  => 'DistrictController@create',
                    'as'    => 'districts.create',
                    'title' => 'add_district',
                ]);

                # districts store
                Route::post('districts/store', [
                    'uses'  => 'DistrictController@store',
                    'as'    => 'districts.store',
                    'title' => 'add_district',
                ]);

                # districts update
                Route::get('districts/{id}/edit', [
                    'uses'  => 'DistrictController@edit',
                    'as'    => 'districts.edit',
                    'title' => 'edit_district',
                ]);

                # districts update
                Route::put('districts/{id}', [
                    'uses'  => 'DistrictController@update',
                    'as'    => 'districts.update',
                    'title' => 'edit_district',
                ]);

                Route::get('districts/{id}/show', [
                    'uses'  => 'DistrictController@show',
                    'as'    => 'districts.show',
                    'title' => 'view_district',
                ]);

                # districts delete
                Route::delete('districts/{id}', [
                    'uses'  => 'DistrictController@destroy',
                    'as'    => 'districts.delete',
                    'title' => 'delete_district',
                ]);
                #delete all districts
                Route::post('delete-all-districts', [
                    'uses'  => 'DistrictController@destroyAll',
                    'as'    => 'districts.deleteAll',
                    'title' => 'delete_multible_district',
                ]);
                /*------------ end Of districts ----------*/
        /*------------ end Of cities ----------*/
             /*------------ start Of Settings----------*/
             Route::get('all-settings', [
                'as'        => 'all_settings',
                'icon'      => '<i class="feather icon-settings"></i>',
                'title'     => 'all_settings',
                'type'      => 'parent',
                'sub_route' => true,
                'child'     => [
                    'roles.index', 'roles.create', 'roles.store', 'roles.edit', 'roles.update', 'roles.delete',
                    'settings.index', 'settings.update', 'settings.message.all', 'settings.message.one', 'settings.send_email',
                    'paymentmethods.index', 'paymentmethods.show', 'paymentmethods.delete', 'paymentmethods.deleteAll',
                    'paymentmethods.edit','paymentmethods.update','problems.index','problems.create', 'problems.store','problems.edit', 'problems.update', 'problems.show', 'problems.delete'  ,'problems.deleteAll',
                    'cancelreasons.index', 'cancelreasons.create', 'cancelreasons.store','cancelreasons.edit', 'cancelreasons.update', 'cancelreasons.show', 'cancelreasons.delete'  ,'cancelreasons.deleteAll' ,
                    'refundreasons.index', 'refundreasons.create', 'refundreasons.store','refundreasons.edit', 'refundreasons.update', 'refundreasons.show', 'refundreasons.delete'  ,'refundreasons.deleteAll' ,
                    'brands.create', 'brands.store','brands.edit', 'brands.update', 'brands.show', 'brands.delete'  ,'brands.deleteAll' ,'brands.index',

                ],
            ]);

        /*------------ start Of fqs ----------*/
        Route::get('fqs', [
            'uses'  => 'FqsController@index',
            'as'    => 'fqs.index',
            'title' => 'questions_sections',
            'icon'  => '<i class="feather icon-alert-circle"></i>',
            // 'type'      => 'parent',
            // 'sub_route' => false,
            // 'child'     => ['fqs.show', 'fqs.create', 'fqs.store', 'fqs.edit', 'fqs.update', 'fqs.delete', 'fqs.deleteAll'],
        ]);

        Route::get('fqs/{id}/show', [
            'uses'  => 'FqsController@show',
            'as'    => 'fqs.show',
            'title' => 'view_question',
        ]);

        # fqs store
        Route::get('fqs/create', [
            'uses'  => 'FqsController@create',
            'as'    => 'fqs.create',
            'title' => 'add_question',
        ]);

        # fqs store
        Route::post('fqs/store', [
            'uses'  => 'FqsController@store',
            'as'    => 'fqs.store',
            'title' => 'add_question',
        ]);

        # fqs update
        Route::get('fqs/{id}/edit', [
            'uses'  => 'FqsController@edit',
            'as'    => 'fqs.edit',
            'title' => 'edit_question',
        ]);

        # fqs update
        Route::put('fqs/{id}', [
            'uses'  => 'FqsController@update',
            'as'    => 'fqs.update',
            'title' => 'edit_question',
        ]);

        # fqs delete
        Route::delete('fqs/{id}', [
            'uses'  => 'FqsController@destroy',
            'as'    => 'fqs.delete',
            'title' => 'delete_question',
        ]);
        #delete all fqs
        Route::post('delete-all-fqs', [
            'uses'  => 'FqsController@destroyAll',
            'as'    => 'fqs.deleteAll',
            'title' => 'delete_multible_question',
        ]);
        /*------------ end Of fqs ----------*/
        /*------------ start Of complaints ----------*/
        Route::get('all-complaints', [
            'as'    => 'all_complaints',
            'uses'  => 'ComplaintController@index',
            'icon'  => '<i class="feather icon-mail"></i>',
            'title' => 'complaints_and_proposals',
            // 'type'      => 'parent',
            // 'sub_route' => false,
            // 'child'     => [
            //     'complaints.delete', 'complaints.deleteAll', 'complaints.show', 'complaint.replay',
            // ],
        ]);

        # complaint replay
        Route::post('complaints-replay/{id}', [
            'uses'  => 'ComplaintController@replay',
            'as'    => 'complaint.replay',
            'title' => 'the_replay_of_complaining_or_proposal',
        ]);
        # socials update
        Route::get('complaints/{id}/show', [
            'uses'  => 'ComplaintController@show',
            'as'    => 'complaints.show',
            'title' => 'the_resolution_of_complaining_or_proposal',
        ]);

        # complaints delete
        Route::delete('complaints/{id}', [
            'uses'  => 'ComplaintController@destroy',
            'as'    => 'complaints.delete',
            'title' => 'delete_complaining',
        ]);

        #delete all complaints
        Route::post('delete-all-complaints', [
            'uses'  => 'ComplaintController@destroyAll',
            'as'    => 'complaints.deleteAll',
            'title' => 'delete_multibles_complaining',
        ]);
        /*------------ end Of complaints ----------*/
        /*------------ start Of sms ----------*/
        Route::get('sms', [
            'uses'  => 'SMSController@index',
            'as'    => 'sms.index',
            'title' => 'message_packages',
            'icon'  => '<i class="feather icon-smartphone"></i>',
            // 'type'      => 'parent',
            // 'sub_route' => false,
            // 'child'     => ['sms.update', 'sms.change'],
        ]);
        # sms change
        Route::post('sms-change', [
            'uses'  => 'SMSController@change',
            'as'    => 'sms.change',
            'title' => 'message_update',
        ]);
        # sms update
        Route::put('sms/{id}', [
            'uses'  => 'SMSController@update',
            'as'    => 'sms.update',
            'title' => 'message_update',
        ]);
        /*------------ end Of sms ----------*/
        /*------------ start Of Roles----------*/
        Route::get('roles', [
            'uses'  => 'RoleController@index',
            'as'    => 'roles.index',
            'title' => 'Validities_list',
            'icon'  => '<i class="feather icon-eye"></i>',
            // 'type'  => 'parent',
            // 'child' => [
            //     'roles.index', 'roles.create', 'roles.store', 'roles.edit', 'roles.update', 'roles.delete',
            // ],
        ]);

        #add role page
        Route::get('roles/create', [
            'uses'  => 'RoleController@create',
            'as'    => 'roles.create',
            'title' => 'add_role',

        ]);

        #store role
        Route::post('roles/store', [
            'uses'  => 'RoleController@store',
            'as'    => 'roles.store',
            'title' => 'add_role',
        ]);

        #edit role page
        Route::get('roles/{id}/edit', [
            'uses'  => 'RoleController@edit',
            'as'    => 'roles.edit',
            'title' => 'edit_role',
        ]);

        #update role
        Route::put('roles/{id}', [
            'uses'  => 'RoleController@update',
            'as'    => 'roles.update',
            'title' => 'edit_role',
        ]);

        #delete role
        Route::delete('roles/{id}', [
            'uses'  => 'RoleController@destroy',
            'as'    => 'roles.delete',
            'title' => 'delete_role',
        ]);
        /*------------ end Of Roles----------*/
        /*------------ start Of reports----------*/
               /*------------ end Of reports ----------*/
        Route::get('settings', [
            'uses'  => 'SettingController@index',
            'as'    => 'settings.index',
            'title' => 'setting',
            'icon'  => '<i class="feather icon-settings"></i>',
            // 'type'  => 'parent',
            // 'child' => [
            //     'settings.index', 'settings.update', 'settings.message.all', 'settings.message.one', 'settings.send_email',
            // ],
        ]);

        #update
        Route::put('settings', [
            'uses'  => 'SettingController@update',
            'as'    => 'settings.update',
            'title' => 'edit_setting',
        ]);

        #message all
        Route::post('settings/{type}/message-all', [
            'uses'  => 'SettingController@messageAll',
            'as'    => 'settings.message.all',
            'title' => 'message_all',
        ])->where('type', 'email|sms|notification');

        #message one
        Route::post('settings/{type}/message-one', [
            'uses'  => 'SettingController@messageOne',
            'as'    => 'settings.message.one',
            'title' => 'message_one',
        ])->where('type', 'email|sms|notification');

        #send email
        Route::post('settings/send-email', [
            'uses'  => 'SettingController@sendEmail',
            'as'    => 'settings.send_email',
            'title' => 'send_email',
        ]);
        /*------------ end Of Settings ----------*/




        /*------------ start Of orderss ----------*/



        Route::get('orders', [
            'uses'      => 'OrderController@index',
            'as'        => 'orders.index',
            'title'     => 'all_orders',
            'icon'      => '<i class="feather icon-list"></i>',
        ]);

        // Status-based order filtering routes
        Route::get('orders/status/{status}', [
            'uses'  => 'OrderController@indexByStatus',
            'as'    => 'orders.byStatus',
            'title' => 'orders_by_status',
        ])->withoutMiddleware('check-role');

        // Get order counts for dashboard
        Route::get('orders/counts/all', [
            'uses'  => 'OrderController@getCounts',
            'as'    => 'orders.counts',
            'title' => 'order_counts',
        ])->withoutMiddleware('check-role');

        Route::get('orders/{id}/show', [
            'uses'  => 'OrderController@show',
            'as'    => 'orders.show',
            'title' => 'show_orders_page',
        ]);

        Route::post('orders/{id}/status', [
            'uses'  => 'OrderController@updateStatus',
            'as'    => 'orders.updateStatus',
            'title' => 'update_order_status',
        ])->withoutMiddleware('check-role');

        Route::post('orders/{id}/change-status', [
            'uses'  => 'OrderController@changeOrderStatus',
            'as'    => 'orders.changeStatus',
            'title' => 'change_order_status',
        ])->withoutMiddleware('check-role');

        /*------------ end Of orders ----------*/

        // Assign delivery user and change status
        Route::post('orders/{id}/assign-delivery', 'OrderController@assignDelivery')->name('orders.assignDelivery')->withoutMiddleware('check-role');


        /*------------ end Of orderss ----------*/
        // Route::get('reports', [
        //     'as'        => 'reports',
        //     'icon'      => '<i class="feather icon-users"></i>',
        //     'title'     => 'reports',
        //     'type'      => 'parent',
        //     'sub_route' => true,
        //     'child'     => [
        //         'revenue-reports.index' , 'wallets-reports.index' ,'payment-reports.index' , 'withdraw-requests-reports.index' , 'commission-reports.index'
        //     ],
        // ]);

        Route::get('reports/revenue-reports', [
            'uses'      => 'ReportController@RevenueReport',
            'as'        => 'revenue-reports.index',
            'title'     => 'revenue_reports',
            'icon'      => '<i class="feather icon-alert-triangle"></i>',
        ]);

        Route::get('reports/wallets', [
            'uses'      => 'ReportController@wallets',
            'as'        => 'wallets-reports.index',
            'title'     => 'wallets_reports',
            'icon'      => '<i class="feather icon-alert-triangle"></i>',
        ]);

        Route::get('reports/revenue-report/export', [
            'uses' => 'ReportController@exportRevenueReport',
            'as' => 'reports.revenue-report.export',
        ])->withoutMiddleware('check-role');


        Route::get('reports/payment-reports', [
            'uses'      => 'ReportController@PaymentReport',
            'as'        => 'payment-reports.index',
            'title'     => 'payment_reports',
            'icon'      => '<i class="feather icon-alert-triangle"></i>',
        ]);

        Route::get('reports/payment-report/export', [
            'uses' => 'ReportController@exportPaymentReport',
            'as' => 'reports.payment-report.export',
        ])->withoutMiddleware('check-role');

        Route::get('reports/payment-report/download-invoice/{orderId}', [
            'uses' => 'ReportController@downloadOrderInvoicePdf',
            'as' => 'reports.payment-report.download-invoice',
        ])->withoutMiddleware('check-role');

        Route::get('reports/withdraw-requests-reports', [
            'uses'      => 'ReportController@WithdrawRequestsReport',
            'as'        => 'withdraw-requests-reports.index',
            'title'     => 'withdraw_requests_reports',
            'icon'      => '<i class="feather icon-alert-triangle"></i>',
        ]);

        Route::get('reports/withdraw-requests-report/export', [
            'uses' => 'ReportController@exportWithdrawRequests',
            'as' => 'reports.withdraw-requests-report.export',
        ])->withoutMiddleware('check-role');

        Route::get('reports/commission-reports', [
            'uses'      => 'ReportController@CommissionReport',
            'as'        => 'commission-reports.index',
            'title'     => 'commission_reports',
            'icon'      => '<i class="feather icon-alert-triangle"></i>',
        ]);

        /*------------ start Of cancel request orders ----------*/

        Route::get('cancel-request-orders', [
            'uses'      => 'CancelRequestOrderController@index',
            'as'        => 'cancel_request_orders.index',
            'title'     => 'cancel_request_orders',
            'icon'      => '<i class="feather icon-alert-triangle"></i>',
        ])->withoutMiddleware('check-role');

        Route::get('cancel-request-orders/{id}/show', [
            'uses'  => 'CancelRequestOrderController@show',
            'as'    => 'cancel_request_orders.show',
            'title' => 'show_cancel_request_orders_page',
        ])->withoutMiddleware('check-role');

        Route::post('cancel-request-orders/{id}/accept', [
            'uses'  => 'CancelRequestOrderController@acceptCancelRequest',
            'as'    => 'cancel_request_orders.accept',
            'title' => 'accept_cancel_request',
        ])->withoutMiddleware('check-role');

        Route::post('cancel-request-orders/{id}/reject', [
            'uses'  => 'CancelRequestOrderController@rejectCancelRequest',
            'as'    => 'cancel_request_orders.reject',
            'title' => 'reject_cancel_request',
        ])->withoutMiddleware('check-role');

        /*------------ end Of cancel request orders ----------*/


                /*------------ start  problems  orders ----------*/

        Route::get('problem-orders', action: [
            'uses'      => 'ProblemOrderController@index',
            'as'        => 'problem_orders.index',
            'title'     => 'problem_orders',
            'icon'      => '<i class="feather icon-alert-triangle"></i>',
        ])->withoutMiddleware('check-role');

        Route::get('problem-orders/{id}/show', [
            'uses'  => 'ProblemOrderController@show',
            'as'    => 'problem_orders.show',
            'title' => 'show_problem_orders_page',
        ])->withoutMiddleware('check-role');

        Route::post('problem-orders/{id}/accept', [
            'uses'  => 'ProblemOrderController@acceptProblem',
            'as'    => 'problem_orders.accept',
            'title' => 'accept_problem',
        ])->withoutMiddleware('check-role');

        Route::post('problem-orders/{id}/reject', [
            'uses'  => 'ProblemOrderController@rejectProblem',
            'as'    => 'problem_orders.reject',
            'title' => 'reject_problem',
        ])->withoutMiddleware('check-role');

        /*------------ end Of problems orders ----------*/

        /*------------ start Of refund orders ----------*/

        Route::get('refund-orders', [
            'uses'      => 'RefundOrderController@index',
            'as'        => 'refund_orders.index',
            'title'     => 'refund_orders',
            'icon'      => '<i class="feather icon-rotate-ccw"></i>',
        ])->withoutMiddleware('check-role');

        Route::get('refund-orders/{id}/show', [
            'uses'  => 'RefundOrderController@show',
            'as'    => 'refund_orders.show',
            'title' => 'show_refund_orders_page',
        ])->withoutMiddleware('check-role');

        Route::post('refund-orders/{id}/accept', [
            'uses'  => 'RefundOrderController@accept',
            'as'    => 'refund_orders.accept',
            'title' => 'accept_refund_request',
        ])->withoutMiddleware('check-role');

        Route::post('refund-orders/{id}/refuse', [
            'uses'  => 'RefundOrderController@refuse',
            'as'    => 'refund_orders.refuse',
            'title' => 'refuse_refund_request',
        ])->withoutMiddleware('check-role');

        /*------------ end Of refund orders ----------*/

        /*------------ start Of paymentmethods ----------*/
        Route::get('paymentmethods', [
            'uses'      => 'PaymentMethodController@index',
            'as'        => 'paymentmethods.index',
            'title'     => 'paymentmethods',
            'icon'      => '<i class="feather icon-image"></i>',
            'sub_route' => false,
            'child'     => ['paymentmethods.create', 'paymentmethods.store', 'paymentmethods.edit', 'paymentmethods.update', 'paymentmethods.show', 'paymentmethods.delete', 'paymentmethods.deleteAll'],
        ]);

        # paymentmethods store


        # paymentmethods edit
        Route::get('paymentmethods/{id}/edit', [
            'uses'  => 'PaymentMethodController@edit',
            'as'    => 'paymentmethods.edit',
            'title' => 'edit_paymentmethod',
        ]);

        # paymentmethods update
        Route::put('paymentmethods/{id}', [
            'uses'  => 'PaymentMethodController@update',
            'as'    => 'paymentmethods.update',
            'title' => 'edit_paymentmethod',
        ]);

        # paymentmethods show
        Route::get('paymentmethods/{id}/show', [
            'uses'  => 'PaymentMethodController@show',
            'as'    => 'paymentmethods.show',
            'title' => 'show_paymentmethod_page',
        ]);

        # paymentmethods delete
        Route::delete('paymentmethods/{id}', [
            'uses'  => 'PaymentMethodController@destroy',
            'as'    => 'paymentmethods.delete',
            'title' => 'delete_paymentmethod',
        ]);
        #delete all paymentmethods
        Route::post('delete-all-paymentmethods', [
            'uses'  => 'PaymentMethodController@destroyAll',
            'as'    => 'paymentmethods.deleteAll',
            'title' => 'delete_group_of_paymentmethods',
        ]);
        /*------------ end Of paymentmethods ----------*/


        /*------------ start Of product-categories ----------*/
        Route::get('product-categories', [
            'uses'      => 'ProductCategoryController@index',
            'as'        => 'product-categories.index',
            'title'     => 'product_categories',
            'icon'      => '<i class="feather icon-grid"></i>',
        ]);

        # product-categories store
        Route::get('product-categories/create', [
            'uses'  => 'ProductCategoryController@create',
            'as'    => 'product-categories.create',
            'title' => 'add_product_category_page',
        ]);

        # product-categories store
        Route::post('product-categories/store', [
            'uses'  => 'ProductCategoryController@store',
            'as'    => 'product-categories.store',
            'title' => 'add_product_category',
        ]);

        # product-categories update
        Route::get('product-categories/{id}/edit', [
            'uses'  => 'ProductCategoryController@edit',
            'as'    => 'product-categories.edit',
            'title' => 'update_product_category_page',
        ]);

        # product-categories update
        Route::put('product-categories/{id}', [
            'uses'  => 'ProductCategoryController@update',
            'as'    => 'product-categories.update',
            'title' => 'update_product_category',
        ]);

        # product-categories show
            Route::get('product-categories/{id}/show', [
            'uses'  => 'ProductCategoryController@show',
            'as'    => 'product-categories.show',
            'title' => 'show_product_category_page',
        ]);

        # product-categories delete
        Route::delete('product-categories/{id}', [
            'uses'  => 'ProductCategoryController@destroy',
            'as'    => 'product-categories.delete',
            'title' => 'delete_product_category',
        ]);
        #delete all product-categories
        Route::post('delete-all-product-categories', [
            'uses'  => 'ProductCategoryController@destroyAll',
            'as'    => 'product-categories.deleteAll',
            'title' => 'delete_group_of_product_categories',
        ]);
        /*------------ end Of product-categories ----------*/


         /*------------ start Of cancelreasons ----------*/
        Route::get('cancelreasons', [
            'uses'      => 'CancelReasonController@index',
            'as'        => 'cancelreasons.index',
            'title'     => 'cancelreasons',
            'icon'  => '<i class="feather icon-alert-circle"></i>',
        ]);

        # cancelreasons store
        Route::get('cancelreasons/create', [
            'uses'  => 'CancelReasonController@create',
            'as'    => 'cancelreasons.create',
            'title' => 'add_cancelreason_page'
        ]);


        # cancelreasons store
        Route::post('cancelreasons/store', [
            'uses'  => 'CancelReasonController@store',
            'as'    => 'cancelreasons.store',
            'title' => 'add_cancelreason'
        ]);

        # cancelreasons update
        Route::get('cancelreasons/{id}/edit', [
            'uses'  => 'CancelReasonController@edit',
            'as'    => 'cancelreasons.edit',
            'title' => 'update_cancelreason_page'
        ]);

        # cancelreasons update
        Route::put('cancelreasons/{id}', [
            'uses'  => 'CancelReasonController@update',
            'as'    => 'cancelreasons.update',
            'title' => 'update_cancelreason'
        ]);

        # cancelreasons show
        Route::get('cancelreasons/{id}/Show', [
            'uses'  => 'CancelReasonController@show',
            'as'    => 'cancelreasons.show',
            'title' => 'show_cancelreason_page'
        ]);

        # cancelreasons delete
        Route::delete('cancelreasons/{id}', [
            'uses'  => 'CancelReasonController@destroy',
            'as'    => 'cancelreasons.delete',
            'title' => 'delete_cancelreason'
        ]);
        #delete all cancelreasons
        Route::post('delete-all-cancelreasons', [
            'uses'  => 'CancelReasonController@destroyAll',
            'as'    => 'cancelreasons.deleteAll',
            'title' => 'delete_group_of_cancelreasons'
        ]);
    /*------------ end Of cancelreasons ----------*/

      /*------------ start Of refundreasons ----------*/
        Route::get('refundreasons', [
            'uses'      => 'RefundReasonController@index',
            'as'        => 'refundreasons.index',
            'title'     => 'refundreasons',
            'icon'  => '<i class="feather icon-alert-circle"></i>',
        ]);

        # refundreasons store
        Route::get('refundreasons/create', [
            'uses'  => 'RefundReasonController@create',
            'as'    => 'refundreasons.create',
            'title' => 'add_refundreason_page'
        ]);


        # refundreasons store
        Route::post('refundreasons/store', [
            'uses'  => 'RefundReasonController@store',
            'as'    => 'refundreasons.store',
            'title' => 'add_refundreason'
        ]);

        # refundreasons update
        Route::get('refundreasons/{id}/edit', [
            'uses'  => 'RefundReasonController@edit',
            'as'    => 'refundreasons.edit',
            'title' => 'update_refundreason_page'
        ]);

        # refundreasons update
        Route::put('refundreasons/{id}', [
            'uses'  => 'RefundReasonController@update',
            'as'    => 'refundreasons.update',
            'title' => 'update_refundreason'
        ]);

        # refundreasons show
        Route::get('refundreasons/{id}/Show', [
            'uses'  => 'RefundReasonController@show',
            'as'    => 'refundreasons.show',
            'title' => 'show_refundreason_page'
        ]);

        # refundreasons delete
        Route::delete('refundreasons/{id}', [
            'uses'  => 'RefundReasonController@destroy',
            'as'    => 'refundreasons.delete',
            'title' => 'delete_refundreason'
        ]);
        #delete all refundreasons
        Route::post('delete-all-refundreasons', [
            'uses'  => 'RefundReasonController@destroyAll',
            'as'    => 'refundreasons.deleteAll',
            'title' => 'delete_group_of_refundreasons'
        ]);
    /*------------ end Of cancelreasons ----------*/



Route::get('export-loyalty-points', [
    'uses'  => 'ClientController@exportLoyaltyPoints',
    'as'    => 'loyalty.export',
    'title' => 'export_loyalty_points',
]);
    /// excel area
    Route::get(
        'export/{export}',
        'ExcelController@master'
    )->name('master-export');
    Route::post('import-items', 'ExcelController@importItems')->name('import-items');
    Route::get('{model}/toggle-boolean/{id}/{action}', 'AdminController@toggleBoolean')->name('model.active')->withoutMiddleware(['admin', 'check-role']);


    Route::post('reports/withdraw-requests/accept', [
        'uses' => 'ReportController@acceptWithdrawRequest',
        'as' => 'reports.withdraw-requests.accept',
    ])->withoutMiddleware('check-role');
    Route::post('reports/withdraw-requests/reject', [
        'uses' => 'ReportController@rejectWithdrawRequest',
        'as' => 'reports.withdraw-requests.reject',
    ])->withoutMiddleware('check-role');

    Route::get('admin/reports/commission-report/export', [
        'uses' => 'ReportController@exportCommissionReport',
        'as' => 'reports.commission-report.export',
    ])->withoutMiddleware('check-role');



});

Route::get('/', function () {
    return redirect()->route('admin.show.login');
});

Route::get('/page/{key}', [\App\Http\Controllers\Site\StaticPageController::class, 'show'])->name('static.page');

Route::get('/device-token', function () {
    return view('device-token');
})->name('device-token.test');

// Test notification page
Route::get('/test-notification', [TestNotificationController::class, 'index'])->name('test-notification');
Route::post('/test-notification/send', [TestNotificationController::class, 'send'])->name('test-notification.send');

