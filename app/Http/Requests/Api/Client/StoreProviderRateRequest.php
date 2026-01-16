<?php

namespace App\Http\Requests\Api\Client;

use App\Http\Requests\Api\BaseApiRequest;

class StoreProviderRateRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'provider_id' => 'required|exists:providers,id',
            'rate' => 'required|numeric|min:1|max:5',
            'body' => 'nullable|string|max:1000',
            'images' => 'sometimes|nullable|array|max:5',
            'images.*' => 'file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv,flv,webm|max:10240',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'provider_id.required' => __('apis.provider_id_required'),
            'provider_id.exists' => __('apis.provider_not_found'),
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
