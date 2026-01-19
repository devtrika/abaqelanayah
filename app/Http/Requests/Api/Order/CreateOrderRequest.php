<?php

namespace App\Http\Requests\Api\Order;

use App\Http\Requests\Api\BaseApiRequest;
use App\Enums\BookingType;
use App\Enums\DeliveryType;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'address_id' => 'nullable|integer|exists:addresses,id',
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
            'delivery_type' => ['required', 'in:immediate,scheduled'],
            'schedule_date' => 'nullable|date|required_if:delivery_type,scheduled',
            'schedule_time' => 'nullable|date_format:H:i|required_if:delivery_type,scheduled',
            'order_type' => ['required', 'in:ordinary,gift'],
            'phone' => ['nullable','phone:SA','exclude_if:order_type,gift','required_without:address_id'],
            'address_name' => [
                'nullable','exclude_if:order_type,gift',
                'required_without:address_id',
                'min:3',
                'regex:/[\p{L}]/u',
                'regex:/^[\p{L}\p{N} ]+$/u',
            ],
            'recipient_name' => [
                'exclude_if:order_type,gift',
                'required_without:address_id',
                'min:3',
                'regex:/[\p{L}]/u',
                'regex:/^[\p{L}\p{N} ]+$/u',
            ],
            'city_id' => ['nullable','exclude_if:order_type,gift','required_without:address_id','exists:cities,id'],
            'districts_id' => ['nullable','exclude_if:order_type,gift','required_without:address_id','exists:districts,id'],
            'country_code' => ['nullable','exclude_if:order_type,gift','required_without:address_id','numeric','digits_between:2,5'],
            'latitude' => [
                'nullable','exclude_if:order_type,gift','required_without:address_id',
                'numeric',
                'between:-90,90'
            ],
            'longitude' => [
                'nullable','exclude_if:order_type,gift','required_without:address_id',
                'numeric',
                'between:-180,180'
            ],
            // Fields required when order_type is gift
            'reciver_name' => ['required_if:order_type,gift','min:3','regex:/[\p{L}]/u','regex:/^[\p{L}\p{N} ]+$/u'],
            'reciver_phone' => ['required_if:order_type,gift','phone:SA'],
            'gift_address_name' => ['required_if:order_type,gift','min:3','regex:/[\p{L}]/u','regex:/^[\p{L}\p{N} ]+$/u'],
               'gift_latitude'  => ['required_if:order_type,gift', 'numeric', 'between:-90,90'],
                 'gift_longitude' => ['required_if:order_type,gift', 'numeric', 'between:-180,180'],

            'message' => ['nullable','string','max:255'],
            'whatsapp' => ['nullable','in:0,1','required_if:order_type,gift'],
            'hide_sender' => ['nullable','in:0,1','required_if:order_type,gift'],
            'description' =>'nullable|string|max:255'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'address_id.required_if' => 'Address is required for home delivery.',
            'address_id.exists' => 'Selected address is invalid.',
            'payment_method_id.required' => __('apis.payment_method_required'),
            'payment_method_id.exists' => 'Selected payment method is invalid.',
            'delivery_type.enum' => 'Delivery type is invalid.',
            'latitude.required_without' => __('apis.latitude_required_without_address'),
            'latitude.numeric' => 'Latitude must be a valid number.',
            'latitude.between' => 'Latitude must be between -90 and 90 degrees.',
            'longitude.required_without' => __('apis.longitude_required_without_address'),
            'longitude.numeric' => 'Longitude must be a valid number.',
            'longitude.between' => 'Longitude must be between -180 and 180 degrees.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();
    
        // Normalize coordinates from alternative keys
        if (isset($data['lat']) && !isset($data['latitude'])) {
            $this->merge(['latitude' => $data['lat']]);
        }
        if (isset($data['lng']) && !isset($data['longitude'])) {
            $this->merge(['longitude' => $data['lng']]);
        }
    
        // Normalize country_code to digits only
        if (isset($data['country_code'])) {
            $this->merge(['country_code' => preg_replace('/\D+/', '', (string) $data['country_code'])]);
        }
    }
}
