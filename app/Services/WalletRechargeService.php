<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Paymob\PaymobService;

class WalletRechargeService
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Initialize Paymob payment for wallet recharge
     */
    public function initializeWalletRecharge(User $user, float $amount, string $gateway = 'card', array $options = [])
    {
        try {
            // Create pending transaction first
            $transaction = $this->createPendingTransaction($user, $amount);

            $paymobService = app(PaymobService::class);

            $origin = (string) ($options['origin'] ?? 'wallet-deposit');

            Log::info('Wallet recharge initialization', [
                'user_id' => $user->id,
                'amount' => $amount,
                'transaction_id' => $transaction->id,
            ]);

            $result = $paymobService->createWalletRechargePayment($transaction, $origin);

            // Update transaction with payment information
            $transaction->update([
                'reference' => $result['merchant_order_id'],
            ]);

            return [
                'success' => true,
                'payment_url' => $result['payment_url'],
                'transaction_id' => $transaction->id,
            ];

        } catch (Exception $e) {
            Log::error('Paymob wallet recharge creation failed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create payment: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create a pending transaction for wallet recharge
     */
    private function createPendingTransaction(User $user, float $amount): Transaction
    {
        return $this->transactionService->createTransaction([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'wallet-addons',
            'status' => 'pending',
            'reference' => $this->transactionService->generateTransactionId(),
            'note' => json_encode([
                'ar' => 'شحن محفظة',
                'en' => 'Wallet Recharge'
            ], JSON_UNESCAPED_UNICODE),
        ]);
    }

    /**
     * Complete wallet recharge after successful payment
     */
    public function completeWalletRecharge(Transaction $transaction): bool
    {
        try {
            return DB::transaction(function () use ($transaction) {
                // Update transaction status
                $transaction->update([
                    'status' => 'completed',
                ]);

                // Add amount to user's wallet
                $transaction->user->increment('wallet_balance', $transaction->amount);

                Log::info('Wallet recharge completed successfully', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                    'amount' => $transaction->amount,
                ]);

                return true;
            });
        } catch (Exception $e) {
            Log::error('Failed to complete wallet recharge', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    // MyFatoorah gateway resolution removed in Paymob migration

    // Verification handled via Paymob webhook; legacy verify method removed
}
