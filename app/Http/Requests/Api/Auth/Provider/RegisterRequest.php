<?php
namespace App\Http\Requests\Api\Auth\Provider;

use App\Http\Requests\Api\BaseApiRequest;
use App\Rules\ProviderPhoneUnique;
use Illuminate\Http\Request;

class RegisterRequest extends BaseApiRequest
{

    public function __construct(Request $request)
    {
        $request['phone']        = fixPhone($request['phone']);
        $request['country_code'] = fixPhone($request['country_code']);
    }

    public function rules()
    {
        return [
            // User fields
            'name'                      => 'required|min:6|max:30',
            'country_code'              => 'required|numeric|digits_between:2,5',
            'phone'                     => [
                'required',
                new ProviderPhoneUnique(),
                function ($attribute, $value, $fail) {
                    // Check if phone belongs to a rejected provider
                    $rejectedProvider = \App\Models\User::where('phone', $value)
                        ->where('type', 'provider')
                        ->whereHas('provider', function($query) {
                            $query->where('status', 'rejected');
                        })
                        ->first();

                    if ($rejectedProvider) {
                        $fail(__('auth.phone_rejected_cannot_reregister'));
                    }
                }
            ],
            'email'                     => 'sometimes|nullable|email|unique:users,email,NULL,id,deleted_at,NULL|max:50',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:100',
                'confirmed',
                'regex:/^[A-Za-z0-9]+$/',
            ],
            'city_id'                   => 'required|exists:cities,id',
            'region_id'                 => 'required|exists:regions,id',
            'gender' => 'required|in:male,female',

            // Provider fields
            'commercial_name'           => 'required|array',
            'commercial_name.ar'        => 'required|string|max:100',
            'commercial_name.en'        => 'nullable|string|max:100',
            'salon_type'                => 'required|string|in:salon,beauty_center',
            'residence_type'            => 'required_if:nationality,other|string|in:individual,professional',
            'nationality'               => 'required|string|in:saudi,other',
            'logo'                      => 'sometimes|image',
            'lat'                       => 'required|numeric',
            'lng'                       => 'required|numeric',
            'commercial_register_no' => 'required|digits:10',
            'commercial_register_image' => 'required|image',

            'sponsor_name'              => 'required_if:nationality,other|string|max:100',
            'sponsor_phone'             => 'required_if:nationality,other|string|max:20|phone:SA',
            'institution_name'          => 'required|string|min:6|max:100',
            'residence_image'           => 'required_if:nationality,other|image',
            'in_home'                   => 'required_if:home_fees,*,true|boolean',
            'in_salon'                  => 'sometimes|boolean',
            'home_fees'                 => 'sometimes|numeric',

            // Bank account fields
            'holder_name'               => 'required|string|max:100',
            'bank_name'                 => 'required|string|max:100',
            'account_number'            => 'required|string|max:50',
            'iban'                      => 'required|string|max:50',
        ];
    }

    public function messages() {
        return [
          'phone.phone' => __('validation.phone_format'),
          'sponsor_phone.phone' => __('validation.phone_format'),

          'password.min' => __('validation.password_min', ['attribute' => 'password', 'min' => 8]),

        ];
      }
}
