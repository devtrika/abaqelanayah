<?php

namespace App\Http\Requests\Api\Client;

use App\Http\Requests\Api\BaseApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreReferralLinkRequest extends BaseApiRequest
{
   
    public function rules(): array
    {
        return [
            'type' => 'required|in:product,service',
            'referrable_id' => 'required|integer',
        ];
    }
}
