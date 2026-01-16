<?php
namespace App\Services;

use Illuminate\Support\Str;
use App\Models\ReferralLink;
use App\Models\UserAffiliateData;

class AfilliateService
{
    public function __construct(protected UserAffiliateData $DataModel , protected ReferralLink $referralLink)
    {}

    public function store(array $data)
    {
        $userId          = auth()->id();
        $data['user_id'] = $userId;
        return $this->DataModel->updateOrCreate(
            ['user_id' => $userId],
            $data                   
        );
    }

    public function getData()
    {
        return $this->DataModel->where('user_id', auth()->id())->first();
    }

  public function createLink(array $data)
{
    $userId = auth()->id();

    // Determine the model class based on 'type'
    $referrableModel = match ($data['type']) {
        'product' => \App\Models\Product::class,
        'service' => \App\Models\Service::class,
        default   => throw new \InvalidArgumentException('Invalid referrable type.'),
    };

    // Optional: Validate if the referrable item exists
    $referrable = $referrableModel::findOrFail($data['referrable_id']);

    // Generate referral code if not provided
    $referralCode = generateReferralCode();

    // Build referral URL
    $url = url()->to('/') . "/redirect/{$data['type']}/{$referrable->id}?ref={$referralCode}";

    // Create the referral link
    return $this->referralLink->create([
        'user_id'         => $userId,
        'referrable_id'   => $referrable->id,
        'referrable_type' => $referrableModel,
        'referral_code'   => $referralCode,
        'url'             => $url,
    ]);
}

public function getLinks()
{
    return $this->referralLink->with('referrable')->where('user_id',auth()->id())->get();
}
}
