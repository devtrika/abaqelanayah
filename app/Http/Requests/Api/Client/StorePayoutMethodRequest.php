<?php
namespace App\Http\Requests\Api\Client;

use App\Http\Requests\Api\BaseApiRequest;

class StorePayoutMethodRequest extends BaseApiRequest
{

    public function rules(): array
    {
        return [
            'beneficiary_name' => 'required|string|max:255',
            'bank_name'        => 'required|string|max:255',
            'account_number'   => 'required|string|max:255',
        ];
    }
}
