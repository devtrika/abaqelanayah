<?php
namespace  App\Services;

class SettingService {

   public static function appInformations($app_info)
    {
       // Get image settings with media relations
       $imageSettings = \App\Models\SiteSetting::whereIn('key', [
           'logo', 'fav_icon', 'default_user', 'intro_loader', 'intro_logo',
           'about_image_1', 'about_image_2', 'login_background', 'profile_cover', 'no_data'
       ])->with('media')->get()->keyBy('key');

       $data                        = [
           'is_production'              =>$app_info['is_production'],
           'name_ar'                    =>$app_info['name_ar'],
           'name_en'                    =>$app_info['name_en'],
           'email'                      =>$app_info['email'],
           'phone'                      =>$app_info['phone'],
           'whatsapp'                   =>$app_info['whatsapp'],

           'logo'                       => self::getImageUrl($imageSettings, 'logo', $app_info['logo'] ?? ''),
           'fav_icon'                   => self::getImageUrl($imageSettings, 'fav_icon', $app_info['fav_icon'] ?? ''),
           'no_data_icon'               => $app_info['no_data_icon'] ?? '',
           'default_user'               => self::getImageUrl($imageSettings, 'default_user', $app_info['default_user'] ?? '', 'users'),
           'profile_cover'              => self::getImageUrl($imageSettings, 'profile_cover', $app_info['profile_cover'] ?? ''),
           'login_background'           => self::getImageUrl($imageSettings, 'login_background', $app_info['login_background'] ?? ''),
           'intro_logo'                 => self::getImageUrl($imageSettings, 'intro_logo', $app_info['intro_logo'] ?? ''),
           'intro_loader'               => self::getImageUrl($imageSettings, 'intro_loader', $app_info['intro_loader'] ?? ''),
           'intro_name'                 =>$app_info['intro_name_'.lang()],
           'intro_name_ar'              =>$app_info['intro_name_ar'],
           'intro_name_en'              =>$app_info['intro_name_en'],
           'intro_about'                =>$app_info['intro_about_'.lang()],
           'intro_about_ar'             =>$app_info['intro_about_ar'],
           'intro_about_en'             =>$app_info['intro_about_en'],

           'about_image_2'              => self::getImageUrl($imageSettings, 'about_image_2', $app_info['about_image_2'] ?? ''),
           'about_image_1'              => self::getImageUrl($imageSettings, 'about_image_1', $app_info['about_image_1'] ?? ''),
           'services_text_ar'           =>$app_info['services_text_ar'],
           'services_text_en'           =>$app_info['services_text_en'],
           'services_text'              =>$app_info['services_text_'.lang()],
           'how_work_text_ar'           =>$app_info['how_work_text_ar'],
           'how_work_text_en'           =>$app_info['how_work_text_en'],
           'how_work_text'              =>$app_info['how_work_text_'.lang()],
           'fqs_text_ar'                =>$app_info['fqs_text_ar'],
           'fqs_text_en'                =>$app_info['fqs_text_en'],
           'fqs_text'                   =>$app_info['fqs_text_'.lang()],
           'parteners_text_ar'          =>$app_info['parteners_text_ar'],
           'parteners_text_en'          =>$app_info['parteners_text_en'],
           'parteners_text'             =>$app_info['parteners_text_'.lang()],
           'contact_text_ar'            =>$app_info['contact_text_ar'],
           'contact_text_en'            =>$app_info['contact_text_en'],
           'contact_text'               =>$app_info['contact_text_'.lang()],
           'intro_email'                =>$app_info['intro_email'],
           'intro_phone'                =>$app_info['intro_phone'],
           'intro_address'              =>$app_info['intro_address'],
           'color'                      =>$app_info['color'],
           'buttons_color'              =>$app_info['buttons_color'],
           'hover_color'                =>$app_info['hover_color'],
           'intro_meta_description'     =>$app_info['intro_meta_description'],
           'intro_meta_keywords'        =>$app_info['intro_meta_keywords'],

           'smtp_user_name'             =>$app_info['smtp_user_name'],
           'smtp_password'              =>$app_info['smtp_password'],
           'smtp_mail_from'             =>$app_info['smtp_mail_from'],
           'smtp_sender_name'           =>$app_info['smtp_sender_name'],
           'smtp_port'                  =>$app_info['smtp_port'],
           'smtp_host'                  =>$app_info['smtp_host'],
           'smtp_encryption'            =>$app_info['smtp_encryption'],
           'min_wallet_deduction'            =>$app_info['min_wallet_deduction'],
           'min_refund_balance'            =>$app_info['min_refund_balance'],



           'firebase_key'               =>$app_info['firebase_key'],
           'firebase_sender_id'         =>$app_info['firebase_sender_id'],

           'google_places'              =>$app_info['google_places'],
           'google_analytics'           =>$app_info['google_analytics'],
           'live_chat'                  =>$app_info['live_chat'],
           'default_locale'             =>$app_info['default_locale'],
           'locales'                    =>$app_info['locales'],
           'rtl_locales'                =>$app_info['rtl_locales'],
           'default_country'            =>$app_info['default_country'],
           'countries'                  =>$app_info['countries'],
           'default_currency'           =>$app_info['default_currency'],
           'currencies'                 =>$app_info['currencies'],
           'vat_amount'                 =>$app_info['vat_amount'] ?? 15,
           'registeration_availability' =>$app_info['registeration_availability'] ?? 15,

           // Loyalty Points Settings
           'loyalty_points_enabled'     =>$app_info['loyalty_points_enabled'] ?? 1,
           'loyalty_points_earn_rate'   =>$app_info['loyalty_points_earn_rate'] ?? 1,
           'loyalty_points_redeem_rate' =>$app_info['loyalty_points_redeem_rate'] ?? 1,
           'loyalty_points_min_redeem'  =>$app_info['loyalty_points_min_redeem'] ?? 10,
           'loyalty_points_max_redeem_percentage' =>$app_info['loyalty_points_max_redeem_percentage'] ?? 50,

           // Fee Settings
           'delivery_fee'        => $app_info['delivery_fee'] ?? 0,
           'delivery_per_km_fee' => $app_info['delivery_per_km_fee'] ?? 0,
           'delivery_distance_threshold' => $app_info['delivery_distance_threshold'] ?? 0,
           'express_delivery_fee'       => $app_info['express_delivery_fee'] ?? 0,
        // commision fees
        'salon_comission'        => $app_info['salon_comission'] ?? 0,

        'product_referral_commission'        => $app_info['product_referral_commission'] ?? 0,
        'service_referral_commission'        => $app_info['service_referral_commission'] ?? 0,

        'comission_withdrawal_fee'   => $app_info['comission_withdrawal_fee'] ?? 0,

                   'app_store_link'             =>$app_info['app_store_link'] ?? '',
           'google_play_link'           =>$app_info['google_play_link'] ?? '',
           'returns_en'           =>$app_info['returns_en'] ?? '',
           'returns_ar'           =>$app_info['returns_ar'] ?? '',

           'scheduled_delivery_fee'           =>$app_info['scheduled_delivery_fee'] ?? '',
           'gift_fee'           =>$app_info['gift_fee'] ?? '',

           'ordinary_delivery_fee'           =>$app_info['ordinary_delivery_fee'] ?? '',




        ];
        foreach(languages() as $lang){
            $data['about_'.$lang]   = $app_info['about_'.$lang]??'';
            $data['terms_'.$lang]   = $app_info['terms_'.$lang]??'';
            $data['privacy_'.$lang] = $app_info['privacy_'.$lang]??'';
            $data['cancel_policy_'.$lang] = $app_info['cancel_policy_'.$lang]??'';
        }
        return $data;
    }

    /**
     * Get image URL from media library or fallback to old path
     */
    private static function getImageUrl($imageSettings, $key, $fallbackValue, $directory = 'settings')
    {
        // Check if we have a media library URL
        if (isset($imageSettings[$key]) && $imageSettings[$key]->getFirstMediaUrl($key)) {
            return $imageSettings[$key]->getFirstMediaUrl($key);
        }

        // Fallback to old file path if value exists
        if (!empty($fallbackValue)) {
            return "/storage/images/{$directory}/{$fallbackValue}";
        }

        // Return default fallback
        $defaultPath = $directory === 'users' ? 'users/default.png' : 'settings/default.png';
        return "/storage/images/{$defaultPath}";
    }

}
