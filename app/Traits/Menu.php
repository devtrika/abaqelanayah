<?php

namespace App\Traits;

use App\Models\RefundReason;

trait Menu {
  public function home() {

    $menu = [


        
    [
        'name'  => __('admin.clients'),
        'count' => \App\Models\User::where('type', 'client')->count(),
        'icon'  => 'icon-users',
        'url'   => url('admin/users?type=client'),
    ],
    [
        'name'  => __('admin.deliveries'),
        'count' => \App\Models\User::where('type', 'delivery')->count(),
        'icon'  => 'icon-truck',
        'url'   => url('admin/users?type=delivery'),
    ],


      

   
    [
        'name'  => __('admin.pending_orders'),
        'count' => \App\Models\Order::where('status', 'pending')->count(),
        'icon'  => 'icon-clock',
        'url'   => url('admin/orders?status=pending'),
    ],
    [
        'name'  => __('admin.new_orders'),
        'count' => \App\Models\Order::where('status', 'new')->count(),
        'icon'  => 'icon-plus',
        'url'   => url('admin/orders?status=new'),
    ],
    [
        'name'  => __('admin.out_for_delivery_orders'),
        'count' => \App\Models\Order::where('status', 'out-for-delivery')->count(),
        'icon'  => 'icon-send',
        'url'   => url('admin/orders?status=out-for-delivery'),
    ],
    [
        'name'  => __('admin.confirmed_orders'),
        'count' => \App\Models\Order::where('status', 'confirmed')->count(),
        'icon'  => 'icon-check',
        'url'   => url('admin/orders?status=confirmed'),
    ],
    [
        'name'  => __('admin.processing_orders'),
        'count' => \App\Models\Order::where('status', 'processing')->count(),
        'icon'  => 'icon-settings',
        'url'   => url('admin/orders?status=processing'),
    ],
    [
        'name'  => __('admin.delivered_orders'),
        'count' => \App\Models\Order::where('status', 'delivered')->count(),
        'icon'  => 'icon-truck',
        'url'   => url('admin/orders?status=delivered'),
    ],
    [
        'name'  => __('admin.problem_orders'),
        'count' => \App\Models\Order::where('status', 'problem')->count(),
        'icon'  => 'icon-alert-triangle',
        'url'   => url('admin/orders?status=problem'),
    ],
    [
        'name'  => __('admin.cancelled_orders'),
        'count' => \App\Models\Order::where('status', 'cancelled')->count(),
        'icon'  => 'icon-x-circle',
        'url'   => url('admin/orders?status=cancelled'),
    ],
    [
        'name'  => __('admin.request_refund_orders'),
        'count' => \App\Models\Order::where('refund_status', 'request_refund')->count(),
        'icon'  => 'icon-refresh-cw',
        'url'   => url('admin/orders?status=request_refund'),
    ],
    [
        'name'  => __('admin.refunded_orders'),
        'count' => \App\Models\Order::where('refund_status', 'refunded')->count(),
        'icon'  => 'icon-rotate-ccw',
        'url'   => url('admin/orders?status=refunded'),
    ],
    [
        'name'  => __('admin.request_rejected_orders'),
        'count' => \App\Models\Order::where('refund_status', 'request_rejected')->count(),
        'icon'  => 'icon-x',
        'url'   => url('admin/orders?status=request_rejected'),
    ],

    [
    'name'  => __('admin.order_ratings'),
    'count' => \App\Models\OrderRating::count(),
    'icon'  => 'icon-star',
    'url'   => url('admin/orderrates'),
],
[
    'name'  => __('admin.total_products'),
    'count' => \App\Models\Product::count(),
    'icon'  => 'icon-box',
    'url'   => url('admin/products'),
],

[
    'name'  => __('admin.active_product_categories'),
    'count' => \App\Models\Category::where('is_active', 1)->count(),
    'icon'  => 'icon-check',
    'url'   => url('admin/product-categories?is_active=1'),
],
[
    'name'  => __('admin.inactive_product_categories'),
    'count' => \App\Models\Category::where('is_active', 0)->count(),
    'icon'  => 'icon-x',
    'url'   => url('admin/product-categories?is_active=0'),
],

[
    'name'  => __('admin.categories'),
    'count' => \App\Models\Category::count(),
    'icon'  => 'icon-x',
    'url'   => url('admin/categories'),
],

[
    'name'  => __('admin.coupons'),
    'count' => \App\Models\Coupon::count(),
    'icon'  => 'icon-x',
    'url'   => url('admin/coupons'),
],


[
    'name'  => __('admin.total_banners'),
    'count' => \App\Models\Image::count(),
    'icon'  => 'icon-image',
    'url'   => url('admin/images'),
],
[
    'name'  => __('admin.active_banners'),
    'count' => \App\Models\Image::where('is_active', 1)->count(),
    'icon'  => 'icon-check',
    'url'   => url('admin/images?is_active=1'),
],
[
    'name'  => __('admin.inactive_banners'),
    'count' => \App\Models\Image::where('is_active', 0)->count(),
    'icon'  => 'icon-x',
    'url'   => url('admin/images?is_active=0'),
],
// [
//     'name'  => __('admin.total_problems'),
//     'count' => \App\Models\Problem::count(),
//     'icon'  => 'icon-alert-triangle',
//     'url'   => url('admin/problems'),
// ],
[
    'name'  => __('admin.total_countries'),
    'count' => \App\Models\Country::count(),
    'icon'  => 'icon-globe',
    'url'   => url('admin/countries'),
],
[
    'name'  => __('admin.total_regions'),
    'count' => \App\Models\Region::count(),
    'icon'  => 'icon-map',
    'url'   => url('admin/regions'),
],
[
    'name'  => __('admin.total_cities'),
    'count' => \App\Models\City::count(),
    'icon'  => 'icon-home',
    'url'   => url('admin/cities'),
],


[
    'name'  => __('admin.total_district'),
    'count' => \App\Models\District::count(),
    'icon'  => 'icon-home',
    'url'   => url('admin/districts'),
],

[
    'name'  => __('admin.total_complaints'),
    'count' => \App\Models\ContactUs::count(),
    'icon'  => 'icon-alert-circle',
    'url'   => url('admin/all-complaints'),
],
[
    'name'  => __('admin.total_faqs'),
    'count' => \App\Models\Fqs::count(),
    'icon'  => 'icon-help-circle',
    'url'   => url('admin/fqs'),
],


[
    'name'  => __('admin.cancelreasons'),
    'count' => \App\Models\CancelReason::count(),
    'icon'  => 'icon-alert-circle',
    'url'   => url('admin/cancelreasons'),
],


[
    'name'  => __('admin.refundreasons'),
    'count' => RefundReason::count(),
    'icon'  => 'icon-alert-circle',
    'url'   => url('admin/refundreasons'),
],
























    //   [
    //     'name'  => __('admin.services'),
    //     'count' => \App\Models\Service::count(),
    //     'icon'  => 'icon-users',
    //     'url'   => url('admin/services'),
    //   ],

    //   [
    //     'name'  => __('admin.product-categories'),
    //     'count' => \App\Models\ProductCategory::count(),
    //     'icon'  => 'icon-users',
    //     'url'   => url('admin/products'),
    //   },
      
    //   [
    //     'name'  => __('admin.products'),
    //     'count' => \App\Models\Product::count(),
    //     'icon'  => 'icon-users',
    //     'url'   => url('admin/products'),
    //   ],

    //   [
    //     'name'  => __('admin.orders_only_services'),
    //     'count' => \App\Models\Order::whereHas('items', function($q) {
    //         $q->where('item_type', 'App\\Models\\Service');
    //     })->whereDoesntHave('items', function($q) {
    //         $q->where('item_type', 'App\\Models\\Product');
    //     })->sum('total'),
    //     'icon'  => 'icon-list',
    //   ],
    //   [
    //     'name'  => __('admin.orders_only_products'),
    //     'count' => \App\Models\Order::whereHas('items', function($q) {
    //         $q->where('item_type', 'App\\Models\\Product');
    //     })->sum('total'),
    //     'icon'  => 'icon-list',
    //   ],
    //   [
    //     'name'  => __('admin.total_orders'),
    //     'count' => \App\Models\Order::sum('total'),
    //     'icon'  => 'icon-list',
    //   ],

    //   [
    //     'name'  => __('admin.platform_commission'),
    //     'count' => \App\Models\Order::sum('platform_commission'),
    //     'icon'  => 'icon-list',
    //   ],

    //   [
    //     'name'  => __('admin.delivery_fees'),
    //     'count' => \App\Models\Order::sum('delivery_fee'),
    //     'icon'  => 'icon-list',
    //   ],

    //   [
    //     'name'  => __('admin.cancel_fees'),
    //     'count' => \App\Models\Order::sum('cancel_fees'),
    //     'icon'  => 'icon-list',
    //   ],

    //   [
    //     'name'  => __('admin.booking_fees'),
    //     'count' => \App\Models\Order::sum('booking_fee'),
    //     'icon'  => 'icon-list',
    //   ],

    //   [
    //     'name'  => __('admin.total_discounts'),
    //     'count' => \App\Models\Order::sum('discount_amount'),
    //     'icon'  => 'icon-list',
    //   ],

    //   [
    //     'name'  => __('admin.total_revenue'),
    //     'count' =>\App\Models\Order::sum('platform_commission') + \App\Models\Order::sum('delivery_fee') +\App\Models\Order::sum('cancel_fees'),
    //     'icon'  => 'icon-list',
    //   ],

    //   [
    //     'name'  => __('admin.currnet_provider_share'),
    //     'count' =>\App\Models\Provider::sum('wallet_balance'),
    //     'icon'  => 'icon-list',
    //   ],

    //   [
    //     'name'  => __('admin.total_provider_share'),
    //     'count' => \App\Models\Provider::sum('wallet_balance') + \App\Models\Provider::sum('withdrawable_balance'),
    //     'icon'  => 'icon-list',
    //   ],

    //   // 6. Total clients
    //   [
    //     'name'  => __('admin.total_clients'),
    //     'count' => \App\Models\User::where('type', 'client')->count(),
    //     'icon'  => 'icon-users',
    //   ],
    //   // 17. Total client account deletion requests
    //   [
    //     'name'  => __('admin.total_client_deletion_requests'),
    //     'count' => \App\Models\AccountDeletionRequest::whereHas('user', function($q) {
    //         $q->where('type', 'client');
    //     })->count(),
    //     'icon'  => 'icon-users',
    //   ],
    //   // 18. Total blocked clients
    //   [
    //     'name'  => __('admin.total_blocked_clients'),
    //     'count' => \App\Models\User::where('type', 'client')->where('status', 'blocked')->count(),
    //     'icon'  => 'icon-users',
    //   ],
    //   [
    //     'name'  => __('admin.total_deleted_clients'),
    //     'count' => \App\Models\User::withTrashed()->where('type', 'client')->whereNotNull('deleted_at')->count(),
    //     'icon'  => 'icon-users',
    // ],
    
    //   // 19. Total service orders
    //   [
    //     'name'  => __('admin.total_service_orders'),
    //     'count' => \App\Models\OrderItem::where('item_type', 'App\\Models\\Service')->count(),
    //     'icon'  => 'icon-list',
    //   ],
    //   // 20. Total provider registration requests
    //   [
    //     'name'  => __('admin.total_provider_registration_requests'),
    //     'count' => \App\Models\Provider::where('status' , 'pending')->count(),
    //     'icon'  => 'icon-users',
    //   ],
    //   // 21. Total provider account deletion requests
    //   [
    //     'name'  => __('admin.total_provider_deletion_requests'),
    //     'count' => \App\Models\AccountDeletionRequest::whereHas('user', function($q) {
    //         $q->where('type', 'provider');
    //     })->count(),
    //     'icon'  => 'icon-users',
    //   ],

    //   [
    //     'name'  => __('admin.total_deleted_providers'),
    //     'count' => \App\Models\Provider::withTrashed()->whereNotNull('deleted_at')->count(),
    //     'icon'  => 'icon-users',
    //   ],
    //   // 22. Total blocked providers
    //   [
    //     'name'  => __('admin.total_blocked_providers'),
    //     'count' => \App\Models\Provider::where('status', 'blocked')->count(),
    //     'icon'  => 'icon-users',
    //   ],
    //   // 23. Total active providers
    //   [
    //     'name'  => __('admin.total_active_providers'),
    //     'count' => \App\Models\Provider::where('status', 'accepted')->count(),
    //     'icon'  => 'icon-users',
    //   },
     
     
    //   [
    //     'name'  => __('admin.providers'),
    //     'count' => \App\Models\Provider::count(),
    //     'icon'  => 'icon-users',
    //   ],
    //   // 24. Total bookings
    //   [
    //     'name'  => __('admin.total_bookings'),
    //     'count' => \App\Models\Order::count(),
    //     'icon'  => 'icon-list',
    //   ],
    //   // 25. Total bookings pending payment confirmation
    //   [
    //     'name'  => __('admin.total_bookings_pending_payment_confirmation'),
    //     'count' => \App\Models\Order::where('current_status', 'pending_payment')->count(),
    //     'icon'  => 'icon-list',
    //   ],
    //   // 26. Total bookings under processing
    //   [
    //     'name'  => __('admin.total_bookings_processing'),
    //     'count' => \App\Models\Order::where('current_status', 'processing')->count(),
    //     'icon'  => 'icon-list',
    //   ],
    //   // 27. Total ongoing bookings
    //   [
    //     'name'  => __('admin.total_bookings_ongoing'),
    //     'count' => \App\Models\Order::where('current_status', 'ongoing')->count(),
    //     'icon'  => 'icon-list',
    //   ],
    //   // 28. Total cancelled bookings
    //   [
    //     'name'  => __('admin.total_bookings_cancelled'),
    //     'count' => \App\Models\Order::where('current_status', 'cancelled')->count(),
    //     'icon'  => 'icon-list',
    //   ],
    //   // 29. Total completed bookings
    //   [
    //     'name'  => __('admin.total_bookings_completed'),
    //     'count' => \App\Models\Order::where('current_status', 'completed')->count(),
    //     'icon'  => 'icon-list',
    //   ],
    //   // 30. Total failed bookings
    //   [
    //     'name'  => __('admin.total_bookings_failed'),
    //     'count' => \App\Models\Order::where('current_status', 'failed')->count(),
    //     'icon'  => 'icon-list',
    //   ],  
    //   [
    //     'name'  => __('admin.total_consultation_messages'),
    //     'count' => \App\Models\ConsultationMessage::distinct('client_id')->count('user_id'),
    //     'icon'  => 'icon-list',
    //   ],
    //   // 32. Total unread contact us messages
    //   [
    //     'name'  => __('admin.total_unread_contactus_messages'),
    //     'count' => \App\Models\ContactUs::where('is_read', 0)->count(),
    //     'icon'  => 'icon-list',
    //   ],
    ];

    return $menu;
  }

  

}
