<?php

return [
    // Cart general messages
    'cart_is_empty' => 'السلة فارغة',
    'invalid_item_type' => 'نوع العنصر غير صحيح',
    
    // Product messages
    'product_not_available' => 'المنتج غير متوفر',
    'insufficient_stock' => 'المخزون غير كافي',
    
    // Service messages
    'service_not_available' => 'الخدمة غير متوفرة',
    'service_provider_unavailable' => 'مقدم الخدمة غير متوفر حالياً. يرجى المحاولة لاحقاً',
    'service_already_in_cart' => 'الخدمة موجودة بالفعل في السلة',
    'service_quantity_cannot_be_updated' => 'لا يمكن تحديث كمية الخدمة',
    
    // Coupon messages
    'invalid_coupon_code' => 'كود الخصم غير صحيح',
    'coupon_not_valid_or_usage_limit_reached' => 'الكوبون غير صالح أو وصل لحد الاستخدام المسموح',
    'coupon_only_valid_for_provider' => 'هذا الكوبون صالح فقط لـ :provider',
    'coupon_not_applicable_to_cart_services' => 'هذا الكوبون غير قابل للتطبيق على الخدمات الموجودة في سلتك',
    'minimum_order_value_required' => 'الحد الأدنى لقيمة الطلب :amount :currency مطلوب لاستخدام هذا الكوبون',
    'coupon_already_applied' => 'الكوبون مطبق بالفعل',
    'no_coupon_applied' => 'لا يوجد كوبون مطبق على السلة',
    'coupon_already_used' => 'لقد استخدمت هذا الكوبون من قبل.',
    'currency' => 'ريال',

    // Loyalty points messages
    'loyalty_points_system_disabled' => 'نظام نقاط الولاء معطل',
    'minimum_points_required' => 'الحد الأدنى :points نقطة مطلوب للاستبدال',
    'insufficient_loyalty_points' => 'نقاط الولاء غير كافية',
    'cannot_use_more_than_max_points' => 'لا يمكن استخدام أكثر من :max_points نقطة (:percentage% من إجمالي السلة)',
    
    // Cart conflict messages
    'product_different_provider_with_services_conflict' => 'إضافة هذا المنتج سيؤدي إلى إزالة جميع الخدمات الحالية من سلتك لأنها من مقدم خدمة مختلف. هل تريد المتابعة؟',
    'product_different_provider_with_products_conflict' => 'إضافة هذا المنتج سيؤدي إلى إزالة جميع المنتجات الحالية من سلتك لأنها من مقدم خدمة مختلف. هل تريد المتابعة؟',
    'service_different_provider_conflict' => 'إضافة هذه الخدمة سيؤدي إلى إزالة جميع الخدمات الحالية من سلتك لأنها من مقدم خدمة مختلف. هل تريد المتابعة؟',
    'service_with_multiple_products_conflict' => 'إضافة هذه الخدمة سيؤدي إلى إزالة جميع المنتجات الحالية من سلتك لأن الخدمات تتطلب مقدم خدمة واحد. هل تريد المتابعة؟',
    'service_different_provider_with_products_conflict' => 'إضافة هذه الخدمة سيؤدي إلى إزالة جميع المنتجات الحالية من سلتك لأنها من مقدم خدمة مختلف. هل تريد المتابعة؟',

    // General error messages
    'not_found' => 'غير موجود',
    'error_occurred' => 'حدث خطأ',
     'coupon_not_available' => 'هذا الكوبون غير متاح.',
    'coupon_not_found' => 'هذا الكوبون غير موجود',
    'coupon_usage_ended'   => 'لم يعد بالإمكان استخدام هذا الكوبون.',
    'coupon_expired'       => 'انتهت صلاحية هذا الكوبون.',
    'coupon_not_started'   => 'هذا الكوبون غير صالح بعد.',
    'invalid_coupon_data'  => 'بيانات الكوبون غير صحيحة.',
        'insufficient_wallet_balance' => 'الرصيد في المحفظة غير كافٍ',
    'maximum_wallet_deduction_exceeded' => 'تم تجاوز الحد الأقصى لخصم المحفظة وهو :amount ',
    'requested_quantity_exceeds_available' => 'هذه الكمية أكبر من الكمية المتاحة لهذا المنتج.',
];
