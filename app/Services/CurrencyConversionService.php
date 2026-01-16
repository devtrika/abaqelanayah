<?php

namespace App\Services;

class CurrencyConversionService
{
    /**
     * Exchange rates (approximate, should be updated regularly)
     * Base: 1 KWD = X SAR
     */
    private const EXCHANGE_RATES = [
        'KWD_TO_SAR' => 12.35, // 1 KWD = 12.35 SAR (approximate)
        'SAR_TO_KWD' => 0.081,  // 1 SAR = 0.081 KWD (approximate)
    ];

    /**
     * Convert amount from KWD to SAR
     *
     * @param float $kwdAmount
     * @return float
     */
    public static function kwdToSar($kwdAmount)
    {
        return round($kwdAmount * self::EXCHANGE_RATES['KWD_TO_SAR'], 2);
    }

    /**
     * Convert amount from SAR to KWD
     *
     * @param float $sarAmount
     * @return float
     */
    public static function sarToKwd($sarAmount)
    {
        return round($sarAmount * self::EXCHANGE_RATES['SAR_TO_KWD'], 3);
    }

    /**
     * Convert MyFatoorah response values from KWD to SAR
     *
     * @param array $responseData
     * @return array
     */
    public static function convertMyfatoorahResponseToSar(array $responseData)
    {
        // Convert main invoice values
        if (isset($responseData['InvoiceValue'])) {
            $responseData['InvoiceValue'] = self::kwdToSar($responseData['InvoiceValue']);
        }

        if (isset($responseData['DueDeposit'])) {
            $responseData['DueDeposit'] = self::kwdToSar($responseData['DueDeposit']);
        }

        // Convert invoice items
        if (isset($responseData['InvoiceItems']) && is_array($responseData['InvoiceItems'])) {
            foreach ($responseData['InvoiceItems'] as &$item) {
                if (isset($item['UnitPrice'])) {
                    $item['UnitPrice'] = self::kwdToSar($item['UnitPrice']);
                }
            }
        }

        // Convert transaction values
        if (isset($responseData['InvoiceTransactions']) && is_array($responseData['InvoiceTransactions'])) {
            foreach ($responseData['InvoiceTransactions'] as &$transaction) {
                if (isset($transaction['TransationValue'])) {
                    $transaction['TransationValue'] = self::kwdToSar($transaction['TransationValue']);
                }
                if (isset($transaction['CustomerServiceCharge'])) {
                    $transaction['CustomerServiceCharge'] = self::kwdToSar($transaction['CustomerServiceCharge']);
                }
                if (isset($transaction['TotalServiceCharge'])) {
                    $transaction['TotalServiceCharge'] = self::kwdToSar($transaction['TotalServiceCharge']);
                }
                if (isset($transaction['DueValue'])) {
                    $transaction['DueValue'] = self::kwdToSar($transaction['DueValue']);
                }
                if (isset($transaction['PaidCurrencyValue'])) {
                    $transaction['PaidCurrencyValue'] = self::kwdToSar($transaction['PaidCurrencyValue']);
                }
                if (isset($transaction['VatAmount'])) {
                    $transaction['VatAmount'] = self::kwdToSar($transaction['VatAmount']);
                }
                
                // Update currency fields
                $transaction['PaidCurrency'] = 'SAR';
                $transaction['Currency'] = 'SAR';
            }
        }

        // Update display value
        if (isset($responseData['InvoiceDisplayValue'])) {
            $sarValue = self::kwdToSar($responseData['InvoiceValue'] ?? 0);
            $responseData['InvoiceDisplayValue'] = number_format($sarValue, 3) . ' SAR';
        }

        return $responseData;
    }

    /**
     * Get current exchange rate from KWD to SAR
     *
     * @return float
     */
    public static function getKwdToSarRate()
    {
        return self::EXCHANGE_RATES['KWD_TO_SAR'];
    }

    /**
     * Get current exchange rate from SAR to KWD
     *
     * @return float
     */
    public static function getSarToKwdRate()
    {
        return self::EXCHANGE_RATES['SAR_TO_KWD'];
    }
}
