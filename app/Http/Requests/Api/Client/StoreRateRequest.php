<?php

namespace App\Http\Requests\Api\Client;

use App\Http\Requests\Api\BaseApiRequest;

class StoreRateRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'rateable_type' => 'required|in:provider,product,order',
            'rateable_id' => 'required|integer',
            'rate' => 'required|numeric|min:1|max:5',
            'body' => 'nullable|string|max:1000',
            'media' => 'sometimes|nullable|array|max:5',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,avif,mp4,mov,avi,wmv,flv,webm|max:10240',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $rateableType = $this->input('rateable_type');
            $rateableId = $this->input('rateable_id');

            if ($rateableType && $rateableId) {
                // Map rateable_type to model class
                $modelClass = match($rateableType) {
                    'provider' => 'App\\Models\\Provider',
                    'product' => 'App\\Models\\Product',
                    'order' => 'App\\Models\\Order',
                    default => null
                };

                if ($modelClass) {
                    // Check if the rateable item exists
                    if (!$modelClass::find($rateableId)) {
                        $validator->errors()->add('rateable_id', __('apis.item_not_found'));
                    }
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'rateable_type.required' => __('apis.rateable_type_required'),
            'rateable_type.in' => __('apis.invalid_rateable_type'),
            'rateable_id.required' => __('apis.rateable_id_required'),
            'rateable_id.integer' => __('apis.rateable_id_must_be_integer'),
            'rate.required' => __('apis.rate_required'),
            'rate.numeric' => __('apis.rate_must_be_numeric'),
            'rate.min' => __('apis.rate_min_value'),
            'rate.max' => __('apis.rate_max_value'),
            'body.max' => __('apis.body_max_length'),
            'images.max' => __('apis.max_files_allowed'),
            'images.*.file' => __('apis.file_must_be_valid'),
            'images.*.mimes' => __('apis.invalid_file_format'),
            'images.*.max' => __('apis.file_size_too_large'),
        ];
    }
}
