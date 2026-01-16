<?php
namespace App\Http\Requests\Api\Auth\Provider;

use App\Http\Requests\Api\BaseApiRequest;
use Illuminate\Http\Request;

class UpdateProfileRequest extends BaseApiRequest
{

    public function __construct(Request $request)
    {
        if (isset($request['phone'])) {
            $request['phone'] = fixPhone($request['phone']);
        }
        if (isset($request['country_code'])) {
            $request['country_code'] = fixPhone($request['country_code']);
        }
    }

    public function rules()
    {
        return [
            // User fields
            'name'                   => 'sometimes|min:3|max:50',
            'email'                  => 'sometimes|nullable|email|max:50|unique:users,email,' . auth()->id() . ',id,deleted_at,NULL',
            'city_id'                => 'sometimes|exists:cities,id',
            'region_id'                 => 'sometimes|exists:regions,id',
            'gender' => 'sometimes|in:male,female',
            // Provider fields
            'commercial_name'           => 'sometimes|array',
            'commercial_name.ar'        => 'sometimes|string|max:100',
            'commercial_name.en'        => 'nullable|string|max:100',
            'salon_type'                => 'sometimes|string|in:salon,beauty_center',
            'residence_type'            => 'sometimes|string|in:individual,professional',
            'nationality'               => 'sometimes|string|in:saudi,other',
            'logo'                      => 'sometimes|image',
            'lat'                       => 'sometimes|numeric',
            'lng'                       => 'sometimes|numeric',
            'commercial_register_no' => 'sometimes|digits:10',
            'commercial_register_image' => 'sometimes|image',

            'salon_images'              => 'sometimes|nullable|array|max:5',
      'salon_images.*'            => 'image',

            'sponsor_name'              => 'sometimes|string|max:100',
            'sponsor_phone'             => 'sometimes|string|max:20|phone:SA',
            'institution_name'          => 'sometimes|string|min:6|max:100',
            'residence_image'           => 'sometimes|image',
            'in_home'                   => 'sometimes|boolean',
            'in_salon'                  => 'sometimes|boolean',
            'home_fees'                 => 'sometimes|numeric',
            
            // Bank account fields
            'holder_name'               => 'sometimes|string|max:100',
            'bank_name'                 => 'sometimes|string|max:100',
            'account_number'            => 'sometimes|string|max:50',
            'iban'                      => 'sometimes|string|max:50',
        ];
    }
}
