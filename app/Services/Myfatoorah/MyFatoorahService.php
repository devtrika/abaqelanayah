<?php

namespace App\Services\Myfatoorah;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MyFatoorahService
{
    protected $baseUrl;
    protected $apiKey;
    protected $headers;

    public function __construct()
    {
        $config = config('myfatoorah');
        $this->apiKey = $config['api_key'];
        $isTest = $config['test_mode'] ?? true;
        $this->baseUrl = $isTest 
            ? 'https://apitest.myfatoorah.com' 
            : 'https://api.myfatoorah.com';
            
        $this->headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Get available payment gateways
     */
    public function getVendorGateways($invoiceAmount = 0, $currencyIso = 'SAR')
    {
        $data = [
            'InvoiceAmount' => $invoiceAmount,
            'CurrencyIso'   => $currencyIso,
        ];

        $response = $this->sendRequest('v2/InitiatePayment', $data);
        
        return $response['Data']['PaymentMethods'] ?? [];
    }

    /**
     * Create invoice and get payment URL
     * Maps to SendPayment endpoint
     */
    public function sendPayment($data)
    {
        $data['NotificationOption'] = 'Lnk';
        
        $response = $this->sendRequest('v2/SendPayment', $data);
        
        return [
            'invoiceURL' => $response['Data']['InvoiceURL'],
            'invoiceId'  => $response['Data']['InvoiceId']
        ];
    }

    /**
     * Execute Payment (for direct payment methods if needed, or embedded)
     */
    public function executePayment($data)
    {
        $response = $this->sendRequest('v2/ExecutePayment', $data);

        return [
            'invoiceURL' => $response['Data']['PaymentURL'],
            'invoiceId'  => $response['Data']['InvoiceId']
        ];
    }

    /**
     * Get Payment Status
     */
    public function getPaymentStatus($key, $keyType = 'PaymentId')
    {
        $data = [
            'Key' => $key,
            'KeyType' => $keyType
        ];

        $response = $this->sendRequest('v2/GetPaymentStatus', $data);
        
        return $response['Data'] ?? null;
    }

    /**
     * Send Request to MyFatoorah API
     */
    protected function sendRequest($endpoint, $data = [])
    {
        $url = $this->baseUrl . '/' . $endpoint;
        
        // Log request (sanitize sensitive data if needed)
        Log::info("MyFatoorah Request: $endpoint", ['url' => $url, 'data_keys' => array_keys($data)]);

        $response = Http::withHeaders($this->headers)->post($url, $data);

        // Log response
        Log::info("MyFatoorah Response: $endpoint", [
            'status' => $response->status(),
            'success' => $response->successful(),
        ]);

        if ($response->failed()) {
             $errorMessage = $response->json('Message') ?? 'Unknown error';
             
             if ($validationErrors = $response->json('ValidationErrors')) {
                 $errorDetails = [];
                 foreach ($validationErrors as $error) {
                     $errorDetails[] = $error['Name'] . ': ' . $error['Error'];
                 }
                 $errorMessage .= ' (' . implode(', ', $errorDetails) . ')';
             }
             
             Log::error("MyFatoorah API Error: $endpoint", [
                 'error' => $errorMessage,
                 'response_json' => $response->json(),
                 'response_body' => $response->body()
             ]);
             
             throw new Exception("MyFatoorah API Error: " . $errorMessage);
        }

        $json = $response->json();
        
        if (isset($json['IsSuccess']) && $json['IsSuccess'] === false) {
             $msg = $json['Message'] ?? 'Operation failed';
             if (isset($json['ValidationErrors'])) {
                 $msg .= ' ' . json_encode($json['ValidationErrors']);
             }
             throw new Exception("MyFatoorah Logic Error: " . $msg);
        }

        return $json;
    }
}
