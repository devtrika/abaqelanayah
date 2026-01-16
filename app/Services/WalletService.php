<?php

namespace App\Services;

use App\Models\Provider;
use App\Models\WithdrawRequest;
use App\Models\WalletTransaction;

class WalletService
{
    public function createTransaction($user_id, $amount, $type, $status)
    {
        $transaction = new WalletTransaction();
        $transaction->user_id = $user_id;
        $transaction->amount = $amount;
        $transaction->type = $type;
        $transaction->status = $status;
        $transaction->reference = generatePaddedRandomCode();

        $transaction->save();

        return $transaction;
    }

    public function getTransactions($user_id)
    {
        return WalletTransaction::where('user_id', $user_id)->get();
    }

    public function createWithdrawRequest($provider_id, $amount)
    {
        $provider = Provider::find($provider_id);
        if ($provider->wallet_balance < $amount) {
            return Responder::error(__('apis.insufficient_wallet_balance'));
        }
        $withdrawRequest = new WithdrawRequest();
        $withdrawRequest->number = generatePaddedRandomCode();
        $withdrawRequest->provider_id = $provider_id;
        $withdrawRequest->amount = $amount;
        $withdrawRequest->status = 'pending';
        $withdrawRequest->save();
        // Notify all admins
        $provider = $withdrawRequest->provider;
        $providerName = $provider ? ($provider->commercial_name ?? '-') : '-';
        $admins = \App\Models\Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NotifyAdmin([
                'title' => __('admin.withdraw_request'),
                'body' => 'لديك طلب سحب جديد من ' . $providerName,
                'type' => 'withdraw_request',
            ]));
        }
    }

    public function getWithdrawRequests($provider_id)
    {
        return WithdrawRequest::where('provider_id', $provider_id)->get();
    }
}