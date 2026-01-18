<?php
use Illuminate\Support\Facades\App;

use App\Models\Seo;
use App\Models\SiteSetting;

function seo($key){
    return Seo::where('key' , $key)->first() ;
}

function appInformations(){
    $result = SiteSetting::pluck('value', 'key');
    return $result;
}


function convert2english( $string )
{
    $newNumbers = range( 0, 9 );
    $arabic     = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );
    $string     = str_replace( $arabic, $newNumbers, $string );
    return $string;
}

function fixPhone( $string = null )
{
    if(!$string){
      return null;
    }

    $result = convert2english($string);
    $result = ltrim($result, '00');
    $result = ltrim($result, '0');
    $result = ltrim($result, '+');
    return $result;
}

function Translate($text,$lang){

    // Short-circuit if empty text or same language
    if (!is_string($text) || $text === '' || $lang === 'ar') {
        return is_string($text) ? $text : '';
    }
$api='';
   // $api  = 'trnsl.1.1.20190807T134850Z.8bb6a23ccc48e664.a19f759906f9bb12508c3f0db1c742f281aa8468';

    try {
        $endpoint = 'https://translate.yandex.net/api/v1.5/tr.json/translate?key=' . $api
            . '&lang=ar-' . urlencode($lang) . '&text=' . urlencode($text);

        $response = @file_get_contents($endpoint);
        if ($response === false) {
            // Network or API failure; return original text
            return $text;
        }

        $json = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) {
            return $text;
        }

        // Yandex returns `text` as array of translated strings
        if (isset($json['text'])) {
            if (is_array($json['text']) && isset($json['text'][0]) && $json['text'][0] !== '') {
                return (string) $json['text'][0];
            }
            if (is_string($json['text']) && $json['text'] !== '') {
                // Fallback if API changed shape and returns a string
                return (string) $json['text'];
            }
        }

        return $text;
    } catch (\Throwable $e) {
        // Silently fallback on any unexpected error
        return $text;
    }
}


function generateReferralCode($length = 8)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $code;
}


function getYoutubeVideoId( $youtubeUrl )
{
    preg_match( "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/",
        $youtubeUrl, $videoId );
    return $youtubeVideoId = isset( $videoId[ 1 ] ) ? $videoId[ 1 ] : "";
}

function toggleBooleanView($model, $url, $switch = 'is_active' , $open = 1 , $close = 0)
{
    $path = parse_url($url, PHP_URL_PATH);
    $path = trim($path, '/');
    $pathComponents = explode('/', $path);
     $switch = $pathComponents[4] ;

    return view('components.admin.toggle-boolean-view', compact('model', 'url', 'switch','open','close'))->render();
}

function toggleBoolean($model, $name = 'is_active' , $open = 1 , $close = 0)
{
    if ($model->$name == $open) {
        $model->$name = $close;
        $model->save();
        return true;
    } elseif($model->$name == $close) {
        $model->$name = $open;
        $model->save();
        return true;
    }else{
        $model->$name = $close;
        $model->save();
        return false;
    }

    return true;
}

function lang(){
    return App() -> getLocale();
}

function generateRandomCode(){
    return '12345';
    return rand(1111,4444);
}

if (!function_exists('languages')) {
  function languages() {
    $setting = SiteSetting::where(['key'=>'locales'])->get()->first()??[];
    return json_decode($setting->value??['ar', 'en']);
  }
}

function generatePaddedRandomCode($length = 7)
{
    $max = pow(10, $length) - 1;
    $random = random_int(0, $max);
    return str_pad($random, $length, '0', STR_PAD_LEFT);
}

if (!function_exists('defaultLang')) {
  function defaultLang() {
    return SiteSetting::where(['key'=>'default_locale'])->get()->first()->value??'ar';
  }
}

if (!function_exists('getSiteSetting')) {
    function getSiteSetting($key, $default = null) {
        $setting = SiteSetting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}

if (!function_exists('categoriesTree')) {
    function categoriesTree($categories, $margin = 0,$selected_cat_id=null){
        foreach ($categories as $category) {
            $selected = $category->id == $selected_cat_id? 'selected':'';
            echo '<option value="'.$category->id.'"  '.$selected.'>'.str_repeat('ـــ ', $margin).$category->name .'</option>';
    
            if (count($category->childes)) {
                categoriesTree($category->childes, $margin + 1,$selected_cat_id);
            }
        }
    }
}

/**
 * Check if the authenticated admin has permission for a specific route
 *
 * @param string $routeName The route name to check permission for
 * @return bool
 */
if (!function_exists('adminCan')) {
    function adminCan($routeName)
    {
        // Super admin (role_id = 1) has all permissions
        if (auth()->guard('admin')->check() && auth()->guard('admin')->user()->role_id == 1) {
            return true;
        }

        // Check if admin is authenticated
        if (!auth()->guard('admin')->check()) {
            return false;
        }

        // Get admin's permissions
        $permissions = \App\Models\Permission::where('role_id', auth()->guard('admin')->user()->role_id)
            ->pluck('permission')
            ->toArray();

        return in_array($routeName, $permissions);
    }
}
