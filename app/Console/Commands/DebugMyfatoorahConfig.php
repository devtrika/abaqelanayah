<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WalletRechargeService;
use App\Services\Myfatoorah\OrderPaymentService;

class DebugMyfatoorahConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:myfatoorah-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug MyFatoorah API configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== MyFatoorah Configuration Debug ===');
        
        // Check environment variables
        $this->info("\n1. Environment Variables:");
        $this->line("MYFATOORAH_API_KEY: " . (env('MYFATOORAH_API_KEY') ? substr(env('MYFATOORAH_API_KEY'), 0, 20) . '...' : 'NOT SET'));
        $this->line("MYFATOORAH_TOKEN: " . (env('MYFATOORAH_TOKEN') ? substr(env('MYFATOORAH_TOKEN'), 0, 20) . '...' : 'NOT SET'));
        $this->line("MYFATOORAH_TEST_MODE: " . (env('MYFATOORAH_TEST_MODE', 'not set')));
        
        // Check config values
        $this->info("\n2. Config Values:");
        $myfatoorahConfig = config('myfatoorah');
        $this->line("config('myfatoorah.api_key'): " . (isset($myfatoorahConfig['api_key']) ? substr($myfatoorahConfig['api_key'], 0, 20) . '...' : 'NOT SET'));
        $this->line("config('myfatoorah.test_mode'): " . ($myfatoorahConfig['test_mode'] ?? 'NOT SET'));
        
        $servicesConfig = config('services.myfatoorah');
        $this->line("config('services.myfatoorah.token'): " . (isset($servicesConfig['token']) ? substr($servicesConfig['token'], 0, 20) . '...' : 'NOT SET'));
        $this->line("config('services.myfatoorah.test_mode'): " . ($servicesConfig['test_mode'] ?? 'NOT SET'));
        
        // Check if API keys match
        $this->info("\n3. API Key Comparison:");
        $myfatoorahKey = $myfatoorahConfig['api_key'] ?? '';
        $servicesKey = $servicesConfig['token'] ?? '';
        
        if ($myfatoorahKey === $servicesKey) {
            $this->info("✅ API keys MATCH");
        } else {
            $this->error("❌ API keys DO NOT MATCH");
            $this->line("myfatoorah config: " . substr($myfatoorahKey, 0, 30) . '...');
            $this->line("services config:   " . substr($servicesKey, 0, 30) . '...');
        }
        
        // Test service configurations
        $this->info("\n4. Service Configurations:");
        try {
            $walletService = app(WalletRechargeService::class);
            $this->info("✅ WalletRechargeService instantiated successfully");
        } catch (\Exception $e) {
            $this->error("❌ WalletRechargeService failed: " . $e->getMessage());
        }
        
        try {
            $orderService = app(OrderPaymentService::class);
            $this->info("✅ OrderPaymentService instantiated successfully");
        } catch (\Exception $e) {
            $this->error("❌ OrderPaymentService failed: " . $e->getMessage());
        }
        
        $this->info("\n=== Debug Complete ===");
        
        return 0;
    }
}
