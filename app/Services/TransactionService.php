<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionService
{
    /**
     * Create a transaction for wallet deduction (refund on order cancel)
     */
    public function createWalletDepositTransaction($userId, $amount, $orderNumber)
    {
        $noteAr = "تم إيداع مبلغ {$amount} ريال قيمة إلغاء طلب رقم {$orderNumber}";
        $noteEn = "An amount of {$amount} SAR has been deposited for cancellation of order #{$orderNumber}";
        return $this->createTransaction([
            'user_id' => $userId,
            'amount' => $amount,
            'type' => 'wallet-addons',
            'note' => json_encode(['ar' => $noteAr, 'en' => $noteEn], JSON_UNESCAPED_UNICODE),
            'reference' => $orderNumber,
        ]);
    }

    /**
     * Create a transaction for loyalty points deduction
     */
    public function createLoyaltyPointsDeductionTransaction($userId, $points)
    {
        $noteAr = "تم خصم {$points} نقطة قيمة إتمام طلب رقم {$this->generateTransactionId()}";
        $noteEn = "{$points} points have been deducted for completing order #{$this->generateTransactionId()}";
        return $this->createTransaction([
            'user_id' => $userId,
            'amount' => $points,
            'type' => 'loyalty_spent',
            'note' => json_encode(['ar' => $noteAr, 'en' => $noteEn], JSON_UNESCAPED_UNICODE),
            'reference' => $this->generateTransactionId(),
        ]);
    }

    /**
     * Create a transaction for loyalty points reward
     */
    public function createLoyaltyPointsRewardTransaction($userId, $points, $orderNumber)
    {
        $noteAr = "مكافأة إتمام طلب رقم {$orderNumber}";
        $noteEn = "Reward for completing order #{$orderNumber}";
        return $this->createTransaction([
            'user_id' => $userId,
            'amount' => $points,
            'type' => 'loyalty_reward',
            'note' => json_encode(['ar' => $noteAr, 'en' => $noteEn], JSON_UNESCAPED_UNICODE),
            'reference' => $orderNumber,
        ]);
    }

    /**
     * Store a new transaction
     *
     * @param array $data
     * @return Transaction
     * @throws Exception
     */
    public function createTransaction(array $data): Transaction
    {
        try {
            DB::beginTransaction();
            // Generate transaction ID if not provided
            if (!isset($data['transaction_id'])) {
                $data['transaction_id'] = $this->generateTransactionId();
            }
            // Create the transaction
            $transaction = Transaction::create($data);

            DB::commit();

            return $transaction;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Generate unique transaction ID
     *
     * @return string
     */
    public function generateTransactionId(): string
    {
          return '#' . str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);

    }
    
    /**
     * Create a transaction for wallet deduction (refund on order cancel)
     */
    public function createWalletRefundTransaction($userId, $amount, $orderNumber)
    {
        $noteAr = "تم إيداع مبلغ {$amount} ريال قيمة إلغاء طلب رقم {$orderNumber}";
        $noteEn = "An amount of {$amount} SAR has been deposited for cancellation of order #{$orderNumber}";
        return $this->createTransaction([
            'user_id' => $userId,
            'amount' => $amount,
            'type' => 'wallet-addons',
            'status' => 'accepted',
            'note' => json_encode(['ar' => $noteAr, 'en' => $noteEn], JSON_UNESCAPED_UNICODE),
            'reference' => $orderNumber,
        ]);
    }

    public function createWalletPaymentTransaction($userId, $amount, $order)
    {
        $noteAr = "تم خصم مبلغ {$amount} ريال قيمة طلب رقم {$order->order_number}";
        $noteEn = "An amount of {$amount} SAR has been deducted for order #{$order->order_number}";
        return $this->createTransaction([
            'user_id' => $userId,
            'amount' => $amount,
            'type' => 'wallet-payment',
            'status' => 'approved',
            'note' => json_encode(['ar' => $noteAr, 'en' => $noteEn], JSON_UNESCAPED_UNICODE),
            'reference' => $this->generateTransactionId(),
            'order_id' => $order->id,
        ]);
    }

    /**
     * Create a transaction for refund after delivery pickup
     * 
     * @param int $userId
     * @param float $amount
     * @param \App\Models\Order $order
     * @return Transaction
     */
    public function createRefundTransaction($userId, $amount, $order)
    {
        $refundNumber = $order->refund_number ?? $order->order_number;
        $noteAr = "تم استرداد مبلغ {$amount} ريال لطلب الاسترجاع رقم {$refundNumber}";
        $noteEn = "An amount of {$amount} SAR has been refunded for refund order #{$refundNumber}";
        
        return $this->createTransaction([
            'user_id' => $userId,
            'amount' => $amount,
            'type' => 'wallet-addons',
            'note' => json_encode(['ar' => $noteAr, 'en' => $noteEn], JSON_UNESCAPED_UNICODE),
            'reference' => $refundNumber,
            'order_id' => $order->id,
        ]);
    }

    public function createWithdrawRequest($data)
    {

         $data['user_id'] = auth()->id();
        $data['reference'] = $this->generateTransactionId();
        $data['type'] = 'wallet-withdraw';
        $data['transfer_reference'] =$this->generateTransactionId();
        // Keep the validated amount from the request instead of overriding it
        // $data['amount'] is already set from the validated request
        $data['status'] = 'pending';
        $data['note'] = $data['note'] ??json_encode(['ar' => 'طلب سحب رصيد', 'en' => 'Withdraw Request'], JSON_UNESCAPED_UNICODE);

        $transaction = $this->createTransaction($data);

        // Notify all admins with a link to the admin transactions page and transaction id
        try {
            $admins = \App\Models\Admin::all();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NotifyAdmin([
                    'title' => __('admin.withdraw_request') ?? 'Withdraw Request',
                    'body' => __('admin.new_withdraw_request_from_user', ['user' => auth()->user()->name ?? auth()->id()]) ?? 'New withdraw request',
                    'type' => 'wallet_withdraw',
                    // admin UI link to transactions page (could be routed later to show specific transaction)
                    'link' => url('/admin/transactions'),
                    'transaction_id' => $transaction->id,
                ]));
            }
        } catch (\Exception $e) {
            // Don't fail transaction creation if notification fails; just log
            \Log::warning('Failed to notify admins for withdraw request', ['error' => $e->getMessage(), 'transaction_id' => $transaction->id]);
        }

        return $transaction;
    }

    
}