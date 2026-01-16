<?php

namespace App\Http\Requests\Admin\branches;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $branchId = $this->route('id') ?? $this->route('branch');
        
        return [
            // Basic branch information
            'name'                          => 'required|string|max:191',
            'address'                       => 'required|string|max:500',
            'email'                         => [
                'required',
                'email',
                'max:191',
                Rule::unique('branches', 'email')->ignore($branchId)
            ],
            'phone'                         => 'required|string|max:20',
            'latitude'                      => 'nullable|numeric|between:-90,90',
            'longitude'                     => 'nullable|numeric|between:-180,180',
            'polygon'                       => 'nullable|string',
            'expected_duration_normal'      => 'nullable|integer|min:1',
            'expected_duration_express'     => 'nullable|integer|min:1',
            'last_order_time_normal'        => 'nullable|date_format:H:i',
            'last_order_time_express'       => 'nullable|date_format:H:i',
            'delivery_fee_normal'           => 'nullable|numeric|min:0',
            'expected_duration'             => 'nullable|integer|min:1',
            'last_order_time'               => 'nullable',
            'status'                        => 'required|boolean',
            'managers'                      => 'required|integer|exists:admins,id',
            'deliveries'                    => 'required|array',
            'deliveries.*'                  => 'integer|exists:users,id',
            
            // Working hours validation
            'working_hours'                 => 'sometimes|nullable|array|max:7',
            'working_hours.*.day'           => 'required_with:working_hours|string|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'working_hours.*.start_time'    => 'required_with:working_hours|date_format:H:i',
            'working_hours.*.end_time'      => 'required_with:working_hours|date_format:H:i|after:working_hours.*.start_time',
            'working_hours.*.is_working'    => 'sometimes|boolean',
            
            // Delivery hours validation
            'delivery_hours'                => 'sometimes|nullable|array|max:7',
            'delivery_hours.*.day'          => 'required_with:delivery_hours|string|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'delivery_hours.*.start_time'   => 'required_with:delivery_hours|date_format:H:i',
            'delivery_hours.*.end_time'     => 'required_with:delivery_hours|date_format:H:i|after:delivery_hours.*.start_time',
            'delivery_hours.*.is_working'   => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            // Basic validation messages
            'name.required'                         => __('admin.branch_name_required'),
            'address.required'                      => __('admin.branch_address_required'),
            'email.required'                        => __('admin.email_required'),
            'email.email'                           => __('admin.email_invalid'),
            'email.unique'                          => __('admin.email_already_exists'),
            'phone.required'                        => __('admin.phone_required'),
            'delivery_type.required'                => __('admin.delivery_type_required'),
            'delivery_type.in'                      => __('admin.delivery_type_invalid'),
            'status.required'                       => __('admin.status_required'),
            
            // Manager and deliveries validation messages
            'managers.required'                     => __('admin.manager_required'),
            'managers.integer'                      => __('admin.manager_invalid'),
            'managers.exists'                       => __('admin.manager_not_found'),
            'deliveries.required'                   => __('admin.deliveries_required'),
            'deliveries.array'                      => __('admin.deliveries_must_be_array'),
            'deliveries.*.integer'                  => __('admin.delivery_invalid'),
            'deliveries.*.exists'                   => __('admin.delivery_not_found'),
            'working_hours.array'                   => __('admin.working_hours_must_be_array'),
            'working_hours.max'                     => __('admin.working_hours_max_7_days'),
            'working_hours.*.day.required_with'     => __('admin.working_day_required'),
            'working_hours.*.day.in'                => __('admin.invalid_day'),
            'working_hours.*.start_time.required_with' => __('admin.start_time_required'),
            'working_hours.*.start_time.date_format' => __('admin.start_time_format_invalid'),
            'working_hours.*.end_time.required_with' => __('admin.end_time_required'),
            'working_hours.*.end_time.date_format'  => __('admin.end_time_format_invalid'),
            'working_hours.*.end_time.after'        => __('admin.end_time_must_be_after_start_time'),
            
            // Delivery hours validation messages
            'delivery_hours.array'                  => __('admin.delivery_hours_must_be_array'),
            'delivery_hours.max'                    => __('admin.delivery_hours_max_7_days'),
            'delivery_hours.*.day.required_with'    => __('admin.delivery_day_required'),
            'delivery_hours.*.day.in'               => __('admin.invalid_day'),
            'delivery_hours.*.start_time.required_with' => __('admin.delivery_start_time_required'),
            'delivery_hours.*.start_time.date_format' => __('admin.delivery_start_time_format_invalid'),
            'delivery_hours.*.end_time.required_with' => __('admin.delivery_end_time_required'),
            'delivery_hours.*.end_time.date_format' => __('admin.delivery_end_time_format_invalid'),
            'delivery_hours.*.end_time.after'       => __('admin.delivery_end_time_must_be_after_start_time'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Set default value for is_working if not provided for working hours
        if ($this->has('working_hours')) {
            $workingHours = $this->input('working_hours');
            foreach ($workingHours as $index => $workingHour) {
                if (!isset($workingHour['is_working'])) {
                    $workingHours[$index]['is_working'] = true;
                }
            }
            $this->merge(['working_hours' => $workingHours]);
        }
        
        // Set default value for is_working if not provided for delivery hours
        if ($this->has('delivery_hours')) {
            $deliveryHours = $this->input('delivery_hours');
            foreach ($deliveryHours as $index => $deliveryHour) {
                if (!isset($deliveryHour['is_working'])) {
                    $deliveryHours[$index]['is_working'] = true;
                }
            }
            $this->merge(['delivery_hours' => $deliveryHours]);
        }
    }
}