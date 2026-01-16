<?php
namespace App\Http\Controllers\Api;

use App\Facades\Responder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Coupon\checkCouponRequest;
use App\Http\Resources\Api\Client\BranchResource;
use App\Http\Resources\Api\ProblemResource;
use App\Http\Resources\Api\Setting\CancelReaonResource;
use App\Http\Resources\Api\Settings\CategoryResource;
use App\Http\Resources\Api\Settings\CityResource;
use App\Http\Resources\Api\Settings\CountryResource;
use App\Http\Resources\Api\Settings\CountryWithCitiesResource;
use App\Http\Resources\Api\Settings\CountryWithRegionsResource;
use App\Http\Resources\Api\Settings\DeliveryPeriodResource;
use App\Http\Resources\Api\Settings\DistrictResource;
use App\Http\Resources\Api\Settings\FqsResource;
use App\Http\Resources\Api\Settings\ImageResource;
use App\Http\Resources\Api\Settings\IntroResource;
use App\Http\Resources\Api\Settings\OrderStatusResource;
use App\Http\Resources\Api\Settings\PaymentMethodResource;
use App\Http\Resources\Api\Settings\RegionResource;
use App\Http\Resources\Api\Settings\RegionWithCitiesResource;
use App\Http\Resources\Api\Settings\SocialResource;
use App\Http\Resources\Api\VideoResource;
use App\Http\Resources\RefundReasonResource;
use App\Models\BlogCategory;
use App\Models\Branch;
use App\Models\CancelReason;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\DeliveryPeriod;
use App\Models\District;
use App\Models\Fqs;
use App\Models\Image;
use App\Models\Intro;
use App\Models\OrderStatusEnum;
use App\Models\PaymentMethod;
use App\Models\Problem;
use App\Models\ProductCategory;
use App\Models\RefundReason;
use App\Models\Region;
use App\Models\ShortVideo;
use App\Models\SiteSetting;
use App\Models\Social;
use App\Services\CouponService;
use App\Services\SettingService;

class SettingController extends Controller
{

    public function settings()
    {
        $data = SettingService::appInformations(SiteSetting::pluck('value', 'key'));
        return Responder::success($data);
    }

    public function about()
    {
        $about = SiteSetting::where(['key' => 'about_' . lang()])->first()->value;
        return Responder::success($about);
    }

    public function terms()
    {
        $terms = SiteSetting::where(['key' => 'terms_' . lang()])->first()->value;
        return Responder::success($terms);
    }

    public function privacy()
    {
        $privacy = SiteSetting::where(['key' => 'privacy_' . lang()])->first()->value;
        return Responder::success($privacy);
    }

        public function cancelpolicy()
    {
        $cancelpolicy = SiteSetting::where(['key' => 'cancel_policy_' . lang()])->first()->value;
        return Responder::success($cancelpolicy);
    }

    public function intros()
    {
        $intros = IntroResource::collection(Intro::latest()->get());
        return Responder::success($intros);
    }

    public function fqss()
    {
        $fqss = FqsResource::collection(Fqs::latest()->get());
        return Responder::success($fqss);
    }

    public function socials()
    {
        $contacts = SiteSetting::whereIn('key', ['email', 'phone', 'whatsapp'])->get(['id', 'key', 'value']);
        $socials  = SocialResource::collection(Social::latest()->get());
        return Responder::success(['contacts' => $contacts, 'socials' => $socials]);
    }

   public function images()
{
    $locale = app()->getLocale();
    $imageKey = $locale === 'ar' ? 'image_ar' : 'image_en';

    $ad = Image::where('is_active', 1)
        ->latest()
        ->get();

  

    return Responder::success(ImageResource::collection($ad));
}

    public function categories($id = null)
    {
        if (is_null($id)) {
            $categories = Category::where('is_active', 1)
                ->whereNull('parent_id')
                ->whereHas('children')
                ->latest()
                ->get();
        } else {
            $categories = Category::where('is_active', 1)
                ->where('parent_id', $id)
                ->latest()
                ->get();
        }

        return Responder::success(CategoryResource::collection($categories));
    }

    public function ProductCategories($id = null)
    {
        $categories = CategoryResource::collection(ProductCategory::where('is_active', 1)->latest()->get());
        return Responder::success($categories);
    }

    public function BlogCategories($id = null)
    {
        $categories = CategoryResource::collection(BlogCategory::where('is_active', 1)->latest()->get());
        return Responder::success($categories);
    }

    public function countries()
    {
        $countries = CountryResource::collection(Country::latest()->get());
        return Responder::success($countries);
    }

    public function cancellationReasons()
    {
        $reasons = CancelReaonResource::collection(CancelReason::latest()->get());
        return Responder::success($reasons);
    }

    public function problems()
    {
        $problems = ProblemResource::collection(Problem::latest()->get());
        return Responder::success($problems);
    }

        public function refundReasons(): mixed
    {
        $problems = RefundReasonResource::collection(RefundReason::latest()->get());
        return Responder::success($problems);
    }

    public function cities()
    {
        $cities = CityResource::collection(City::latest()->get());
        return Responder::success($cities);
    }

        public function districts()
    {
    $districts = DistrictResource::collection(District::where('status', 1)->latest()->get());
    return Responder::success($districts);
    }

    public function regions()
    {
        $regions = RegionResource::collection(Region::latest()->get());
        return Responder::success($regions);
    }

    public function regionCities($region_id)
    {
        $cities = CityResource::collection(resource: City::where('region_id', $region_id)->latest()->get());
        return Responder::success($cities);
    }

    public function districtCities($district_id)
    {
        $cities = DistrictResource::collection(resource: District::where('city_id', $district_id)->latest()->get());
        return Responder::success($cities);
    }

 // Get cities by region_id
    public function getCities($region_id)
    {
        $cities = City::where('region_id', $region_id)->get();
        return CityResource::collection($cities);
    }

    // Get districts by city_id
    public function getDistricts($city_id)
    {
        $districts = District::where('city_id', $city_id)->get();
        return DistrictResource::collection($districts);
    }

    public function regionsWithCities()
    {
        $regions = RegionWithCitiesResource::collection(Region::with('cities')->latest()->get());
        return Responder::succesdistrictss($regions);
    }

    public function CountryCities($country_id)
    {
        $cities = CityResource::collection(City::where('country_id', $country_id)->latest()->get());
        return Responder::success($cities);
    }

    public function CountryRegions($country_id)
    {
        $regions = RegionResource::collection(Region::where('country_id', $country_id)->latest()->get());
        return Responder::success($regions);
    }

    public function countriesWithCities()
    {
        $countries = CountryWithCitiesResource::collection(Country::with('cities')->latest()->get());
        return Responder::success($countries);
    }

    public function countriesWithRegions()
    {
        $countries = CountryWithRegionsResource::collection(Country::with('regions')->latest()->get());
        return Responder::success($countries);
    }

    public function checkCoupon(checkCouponRequest $request)
    {
        $checkCouponRes = CouponService::checkCoupon($request->coupon_num, $request->total_price);
        if ($checkCouponRes['key'] === 'success') {
            return Responder::success($checkCouponRes['data'] ?? null, ['message' => $checkCouponRes['msg']]);
        } else {
            return Responder::error($checkCouponRes['msg'], [], 400);
        }
    }
    public function isProduction()
    {
        $isProduction = SiteSetting::where(['key' => 'is_production'])->first()->value;
        return Responder::success($isProduction);
    }

    public function isRegister()
    {
        $isProduction = SiteSetting::where(['key' => 'registeration_availability'])->first()->value;
        return Responder::success($isProduction);
    }

    public function VatAmount()
    {
        $isProduction = SiteSetting::where(['key' => 'vat_amount'])->first()->value;
        return Responder::success($isProduction);
    }

    /**
     * Get all payment methods with their integer values and text values
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentMethods()
    {
        $paymentMethods = PaymentMethodResource::collection(PaymentMethod::where('is_active' , 1)->get());
        return Responder::success($paymentMethods);
    }

    /**
     * Get all order statuses with their integer values and text values
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderStatuses()
    {
        $orderStatuses = OrderStatusResource::collection(OrderStatusEnum::all());
        return Responder::success($orderStatuses);
    }

    /**
     * Get all delivery periods
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deliveryPeriods()
    {
        $deliveryPeriods = DeliveryPeriodResource::collection(DeliveryPeriod::all());
        return Responder::success($deliveryPeriods);
    }

    public function videos()
    {
        $videos = VideoResource::collection(ShortVideo::where('is_active', true)->get());
        return Responder::success($videos);
    }
    public function branches()
    {
        $branches = BranchResource::collection(Branch::where('status', 'active')->get());
        return Responder::success($branches);

    }

}
