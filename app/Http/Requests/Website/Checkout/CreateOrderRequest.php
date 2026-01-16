<?php

namespace App\Http\Requests\Website\Checkout;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        // Normalize coordinate field names to match API (latitude/longitude)
        $lat = $this->input('lat');
        $lng = $this->input('lng');
        $latitude = $this->input('latitude');
        $longitude = $this->input('longitude');

        // Accept both gift_latitude/gift_longitude and gift_lat/gift_lng
        $giftLat = $this->input('gift_latitude') ?? $this->input('gift_lat');
        $giftLng = $this->input('gift_longitude') ?? $this->input('gift_lng');

        $cc = $this->input('country_code');
        $normalizedCc = is_string($cc) ? preg_replace('/\D+/', '', $cc) : $cc;

        // Ensure definitive order_type and delivery_type are set from the incoming request (NOT session)
        $orderType = $this->input('order_type');
        $deliveryType = $this->input('delivery_type');

        $this->merge([
            'order_type' => $orderType ?? 'ordinary',
            'delivery_type' => $deliveryType ?? 'immediate',
            'latitude' => $latitude ?? $lat,
            'longitude' => $longitude ?? $lng,
            'gift_latitude' => $giftLat,
            'gift_longitude' => $giftLng,
            'country_code' => $normalizedCc,
            'schedule_time' => $this->input('schedule_time') ? trim((string) $this->input('schedule_time')) : null,
        ]);

        // Pull temporary payload from session as a fallback to ensure continuity (EXCLUDING order_type)
        $session = session('checkout.temp', []);
        if (!empty($session)) {
            $keys = [
                // EXCLUDE 'order_type' to avoid stale gift flag affecting ordinary orders
                'delivery_type','address_id','address_name','recipient_name','phone',
                'country_code','city_id','districts_id','description','latitude','longitude','lat','lng',
                'reciver_name','reciver_phone','gift_city_id','gift_districts_id','gift_address_name','gift_latitude','gift_longitude',
                'message','whatsapp','hide_sender','branch_id'
            ];
            $merge = [];
            foreach ($keys as $key) {
                if ($this->input($key) === null && array_key_exists($key, $session)) {
                    $merge[$key] = $session[$key];
                }
            }
            if (!empty($merge)) {
                $this->merge($merge);
            }
        }
    }

    public function rules(): array
    {
        $rules = [
            // Core
            'order_type' => ['required', Rule::in(['ordinary','gift'])],
            'delivery_type' => ['required', Rule::in(['immediate','scheduled'])],
            'schedule_date' => ['nullable','date','required_if:delivery_type,scheduled'],
            'schedule_time' => ['nullable','date_format:H:i','required_if:delivery_type,scheduled'],

            // Address selection (no hard requirements on ordinary address fields here)
            'address_option' => ['nullable', Rule::in(['existing','new'])],
            'address_id' => ['nullable','integer','exists:addresses,id'],
    
            // Ordinary order: relax requirements; only coordinates are mandatory when no address_id
            'phone' => ['nullable','phone:SA','exclude_if:order_type,gift'],
            'address_name' => ['nullable','exclude_if:order_type,gift','min:3','regex:/[\p{L}]/u','regex:/^[\p{L}\p{N} ]+$/u'],
            'recipient_name' => ['nullable','exclude_if:order_type,gift','min:3','regex:/[\p{L}]/u','regex:/^[\p{L}\p{N} ]+$/u'],
            'city_id' => ['nullable','exclude_if:order_type,gift','exists:cities,id'],
            'districts_id' => ['nullable','exclude_if:order_type,gift','exists:districts,id'],
            'country_code' => ['nullable','exclude_if:order_type,gift','numeric','digits_between:2,5'],
            'latitude' => ['nullable','exclude_if:order_type,gift','required_without:address_id','numeric','between:-90,90'],
            'longitude' => ['nullable','exclude_if:order_type,gift','required_without:address_id','numeric','between:-180,180'],

            // Gift fields
            'reciver_name' => ['exclude_unless:order_type,gift','required_if:order_type,gift','min:3','regex:/[\p{L}]/u','regex:/^[\p{L}\p{N} ]+$/u'],
            'reciver_phone' => ['exclude_unless:order_type,gift','required_if:order_type,gift','phone:SA'],
            'gift_city_id' => ['exclude_unless:order_type,gift','required_if:order_type,gift','exists:cities,id'],
            'gift_districts_id' => ['exclude_unless:order_type,gift','required_if:order_type,gift','exists:districts,id'],
            'gift_address_name' => ['exclude_unless:order_type,gift','required_if:order_type,gift','min:3','regex:/[\p{L}]/u','regex:/^[\p{L}\p{N} ]+$/u'],
            'gift_latitude' => ['exclude_unless:order_type,gift','required_if:order_type,gift','numeric','between:-90,90'],
            'gift_longitude' => ['exclude_unless:order_type,gift','required_if:order_type,gift','numeric','between:-180,180'],
            'message' => ['nullable','string','max:255'],
            'whatsapp' => ['exclude_unless:order_type,gift','nullable','in:0,1','required_if:order_type,gift'],
            'hide_sender' => ['exclude_unless:order_type,gift','nullable','in:0,1','required_if:order_type,gift'],

            // Payment
            'payment_method_id' => [
                'required','integer',
                Rule::exists('payment_methods','id')->where('is_active', 1),
            ],
            'notes' => ['nullable','string','max:500'],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            // Core fields
            'order_type.required' => 'نوع الطلب مطلوب',
            'order_type.in' => 'نوع الطلب غير صالح',
            'delivery_type.required' => 'نوع التوصيل مطلوب',
            'delivery_type.in' => 'نوع التوصيل غير صالح',
            'schedule_date.required_if' => 'تاريخ التوصيل مطلوب عند اختيار التوصيل المجدول',
            'schedule_date.date' => 'تاريخ التوصيل غير صالح',
            'schedule_time.required_if' => 'وقت التوصيل مطلوب عند اختيار التوصيل المجدول',
            'schedule_time.date_format' => 'صيغة وقت التوصيل غير صالحة',

            // Address fields
            'address_option.in' => 'خيار العنوان غير صالح',
            'address_id.integer' => 'معرف العنوان غير صالح',
            'address_id.exists' => 'العنوان المحدد غير موجود',

            // Ordinary order fields
            'phone.phone' => 'رقم الهاتف غير صالح',
            'address_name.min' => 'اسم العنوان يجب أن يكون 3 أحرف على الأقل',
            'address_name.regex' => 'اسم العنوان يجب أن يحتوي على أحرف فقط',
            'recipient_name.min' => 'اسم المستلم يجب أن يكون 3 أحرف على الأقل',
            'recipient_name.regex' => 'اسم المستلم يجب أن يحتوي على أحرف فقط',
            'city_id.exists' => 'المدينة المحددة غير موجودة',
            'districts_id.exists' => 'الحي المحدد غير موجود',
            'country_code.numeric' => 'كود الدولة يجب أن يكون رقماً',
            'country_code.digits_between' => 'كود الدولة غير صالح',
            'latitude.required_without' => 'يجب تحديد موقع التوصيل',
            'latitude.numeric' => 'خط العرض يجب أن يكون رقماً',
            'latitude.between' => 'خط العرض غير صالح',
            'longitude.required_without' => 'يجب تحديد موقع التوصيل',
            'longitude.numeric' => 'خط الطول يجب أن يكون رقماً',
            'longitude.between' => 'خط الطول غير صالح',

            // Gift order fields
            'reciver_name.required_if' => 'اسم المستلم مطلوب للطلبات الهدايا',
            'reciver_name.min' => 'اسم المستلم يجب أن يكون 3 أحرف على الأقل',
            'reciver_name.regex' => 'اسم المستلم يجب أن يحتوي على أحرف فقط',
            'reciver_phone.required_if' => 'رقم هاتف المستلم مطلوب للطلبات الهدايا',
            'reciver_phone.phone' => 'رقم هاتف المستلم غير صالح',
            'gift_city_id.required_if' => 'المدينة مطلوبة للطلبات الهدايا',
            'gift_city_id.exists' => 'المدينة المحددة غير موجودة',
            'gift_districts_id.required_if' => 'الحي مطلوب للطلبات الهدايا',
            'gift_districts_id.exists' => 'الحي المحدد غير موجود',
            'gift_address_name.required_if' => 'عنوان الهدية مطلوب',
            'gift_address_name.min' => 'عنوان الهدية يجب أن يكون 3 أحرف على الأقل',
            'gift_address_name.regex' => 'عنوان الهدية يجب أن يحتوي على أحرف فقط',
            'gift_latitude.required_if' => 'يجب تحديد موقع توصيل الهدية',
            'gift_latitude.numeric' => 'خط العرض غير صالح',
            'gift_latitude.between' => 'خط العرض غير صالح',
            'gift_longitude.required_if' => 'يجب تحديد موقع توصيل الهدية',
            'gift_longitude.numeric' => 'خط الطول غير صالح',
            'gift_longitude.between' => 'خط الطول غير صالح',
            'message.max' => 'رسالة الهدية يجب ألا تتجاوز 255 حرف',
            'whatsapp.required_if' => 'يجب تحديد إرسال الرسالة عبر واتساب',
            'whatsapp.in' => 'قيمة واتساب غير صالحة',
            'hide_sender.required_if' => 'يجب تحديد إخفاء اسم المرسل',
            'hide_sender.in' => 'قيمة إخفاء المرسل غير صالحة',

            // Payment
            'payment_method_id.required' => 'طريقة الدفع مطلوبة',
            'payment_method_id.integer' => 'طريقة الدفع غير صالحة',
            'payment_method_id.exists' => 'طريقة الدفع المحددة غير متاحة',

            // Notes
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 500 حرف',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $user = $this->user();
            if (!$user) return;

            // Ensure the chosen address belongs to the authenticated user
            $addressId = (int) $this->input('address_id');
            if ($addressId) {
                $owns = $user->addresses()->where('id', $addressId)->exists();
                if (!$owns) {
                    $v->errors()->add('address_id', 'العنوان المحدد غير صالح');
                }
            }

            // Single consolidated requirement for ordinary orders: either address_id or coordinates
            $orderType = $this->input('order_type', 'ordinary');
            $lat = $this->input('latitude');
            $lng = $this->input('longitude');
            if ($orderType !== 'gift' && !$addressId && (empty($lat) || empty($lng))) {
                $v->errors()->add('address_id', 'يجب تحديد عنوان محفوظ أو إحداثيات الموقع');
            }
        });
    }
}

