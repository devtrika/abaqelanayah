<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\Provider;
use App\Models\Address;
use App\Models\ProviderBankAccount;
use App\Models\SiteSetting;
use App\Services\OrderService;
use App\Services\FeeCalculationService;
use App\Services\LoyaltyPointsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderCreationWithFeesTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $provider;
    protected $address;
    protected $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up fee settings
        SiteSetting::create(['key' => 'booking_fee_amount', 'value' => '10.00']);
        SiteSetting::create(['key' => 'default_home_service_fee', 'value' => '15.00']);
        SiteSetting::create(['key' => 'normal_delivery_fee', 'value' => '5.00']);
        SiteSetting::create(['key' => 'express_delivery_fee', 'value' => '15.00']);

        // Create test data
        $this->user = User::factory()->create(['wallet_balance' => 1000.00]);
        $this->provider = Provider::factory()->create(['home_fees' => 20.00]);
        $this->address = Address::factory()->create(['user_id' => $this->user->id]);

        // Create bank account for provider
        ProviderBankAccount::create([
            'provider_id' => $this->provider->id,
            'bank_name' => 'Test Bank',
            'holder_name' => 'Test Provider',
            'account_number' => '1234567890',
            'iban' => 'SA1234567890123456789012',
            'is_default' => true,
        ]);

        // Initialize services
        $loyaltyService = new LoyaltyPointsService();
        $feeService = new FeeCalculationService();
        $this->orderService = new OrderService($loyaltyService, $feeService);
    }

    /** @test */
    public function it_creates_order_with_home_service_fees()
    {
        // Create cart with service
        $service = Service::factory()->create(['provider_id' => $this->provider->id, 'price' => 50.00]);
        $cart = Cart::factory()->create([
            'user_id' => $this->user->id,
            'provider_id' => $this->provider->id,
            'type' => 'service',
            'subtotal' => 50.00,
            'total' => 50.00,
        ]);

        $cart->items()->create([
            'item_type' => 'App\Models\Service',
            'item_id' => $service->id,
            'quantity' => 1,
            'price' => 50.00,
            'total' => 50.00,
        ]);

        // Create order with home booking
        $orderData = [
            'address_id' => $this->address->id,
            'booking_type' => 'home',
            'payment_method' => 'wallet',
        ];

        $order = $this->orderService->createOrderFromCart($this->user, $orderData);

        // Assert fees were applied correctly
        $this->assertEquals(10.00, $order->booking_fee); // Booking fee
        $this->assertEquals(20.00, $order->home_service_fee); // Provider's custom home fee
        $this->assertEquals(0.00, $order->delivery_fee); // No delivery for services
        $this->assertEquals('home', $order->booking_type);

        // Total should include all fees
        $expectedTotal = 50.00 + 10.00 + 20.00; // subtotal + booking + home service
        $this->assertEquals($expectedTotal, $order->total);
    }

    /** @test */
    public function it_creates_order_with_express_delivery_fees()
    {
        // Create cart with product
        $product = Product::factory()->create(['provider_id' => $this->provider->id, 'price' => 30.00]);
        $cart = Cart::factory()->create([
            'user_id' => $this->user->id,
            'provider_id' => $this->provider->id,
            'type' => 'product',
            'subtotal' => 30.00,
            'total' => 30.00,
        ]);

        $cart->items()->create([
            'item_type' => 'App\Models\Product',
            'item_id' => $product->id,
            'quantity' => 1,
            'price' => 30.00,
            'total' => 30.00,
        ]);

        // Create order with express delivery
        $orderData = [
            'address_id' => $this->address->id,
            'delivery_type' => 'express',
            'payment_method' => 'wallet',
        ];

        $order = $this->orderService->createOrderFromCart($this->user, $orderData);

        // Assert fees were applied correctly
        $this->assertEquals(0.00, $order->booking_fee); // No booking fee for products
        $this->assertEquals(0.00, $order->home_service_fee); // No home service fee
        $this->assertEquals(15.00, $order->delivery_fee); // Express delivery fee
        $this->assertEquals('express', $order->delivery_type);

        // Total should include delivery fee
        $expectedTotal = 30.00 + 15.00; // subtotal + express delivery
        $this->assertEquals($expectedTotal, $order->total);
    }

    /** @test */
    public function it_creates_order_with_bank_transfer_and_bank_account_id()
    {
        // Create cart with service
        $service = Service::factory()->create(['provider_id' => $this->provider->id, 'price' => 100.00]);
        $cart = Cart::factory()->create([
            'user_id' => $this->user->id,
            'provider_id' => $this->provider->id,
            'type' => 'service',
            'subtotal' => 100.00,
            'total' => 100.00,
        ]);

        $cart->items()->create([
            'item_type' => 'App\Models\Service',
            'item_id' => $service->id,
            'quantity' => 1,
            'price' => 100.00,
            'total' => 100.00,
        ]);

        $bankAccount = $this->provider->bankAccount;

        // Create order with bank transfer
        $orderData = [
            'address_id' => $this->address->id,
            'booking_type' => 'salon',
            'payment_method' => 'bank_transfer',
            'bank_account_id' => $bankAccount->id,
        ];

        $result = $this->orderService->createOrderFromCart($this->user, $orderData);

        // For bank transfer, result should be an array with bank details
        $this->assertIsArray($result);
        $this->assertTrue($result['requires_bank_transfer']);
        $this->assertArrayHasKey('bank_details', $result);

        // Check bank details include provider's account info
        $bankDetails = $result['bank_details'];
        $this->assertEquals('Test Bank', $bankDetails['bank_name']);
        $this->assertEquals('Test Provider', $bankDetails['beneficiary_name']);
        $this->assertEquals('1234567890', $bankDetails['account_number']);
    }

    /** @test */
    public function it_gets_user_orders_successfully()
    {
        // Create some orders for the user
        $orders = Order::factory()->count(3)->create(['user_id' => $this->user->id]);

        $result = $this->orderService->getUserOrders($this->user);

        $this->assertCount(3, $result->items());
        $this->assertEquals($this->user->id, $result->first()->user_id);
    }

    /** @test */
    public function it_gets_specific_user_order_successfully()
    {
        // Create an order for the user
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $result = $this->orderService->getUserOrder($this->user, $order->id);

        $this->assertEquals($order->id, $result->id);
        $this->assertEquals($this->user->id, $result->user_id);
    }

    /** @test */
    public function it_throws_exception_for_non_existent_order()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Order not found');

        $this->orderService->getUserOrder($this->user, 999);
    }

    /** @test */
    public function it_validates_provider_booking_capabilities()
    {
        // Create provider that only offers salon services
        $salonOnlyProvider = Provider::factory()->create([
            'in_home' => false,
            'in_salon' => true,
        ]);

        $service = Service::factory()->create(['provider_id' => $salonOnlyProvider->id]);
        $cart = Cart::factory()->create([
            'user_id' => $this->user->id,
            'provider_id' => $salonOnlyProvider->id,
            'type' => 'service',
        ]);

        $cart->items()->create([
            'item_type' => 'App\Models\Service',
            'item_id' => $service->id,
            'quantity' => 1,
            'price' => 50.00,
            'total' => 50.00,
        ]);

        // Try to book home service with salon-only provider
        $orderData = [
            'address_id' => $this->address->id,
            'booking_type' => 'home',
            'payment_method' => 'wallet',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('This service provider does not offer home services');

        $this->orderService->createOrderFromCart($this->user, $orderData);
    }

    /** @test */
    public function it_uses_provider_custom_home_fees()
    {
        // Create provider with custom home fees
        $customFeeProvider = Provider::factory()->create([
            'in_home' => true,
            'in_salon' => true,
            'home_fees' => 25.00, // Custom fee higher than default
        ]);

        $service = Service::factory()->create(['provider_id' => $customFeeProvider->id]);
        $cart = Cart::factory()->create([
            'user_id' => $this->user->id,
            'provider_id' => $customFeeProvider->id,
            'type' => 'service',
            'subtotal' => 50.00,
            'total' => 50.00,
        ]);

        $cart->items()->create([
            'item_type' => 'App\Models\Service',
            'item_id' => $service->id,
            'quantity' => 1,
            'price' => 50.00,
            'total' => 50.00,
        ]);

        // Create order with home booking
        $orderData = [
            'address_id' => $this->address->id,
            'booking_type' => 'home',
            'payment_method' => 'wallet',
        ];

        $order = $this->orderService->createOrderFromCart($this->user, $orderData);

        // Should use provider's custom home fee (25.00) instead of default (15.00)
        $this->assertEquals(25.00, $order->home_service_fee);
    }

    /** @test */
    public function it_validates_booking_time_against_working_hours()
    {
        // Create provider with working hours
        $provider = Provider::factory()->create([
            'in_home' => true,
            'in_salon' => true,
        ]);

        // Add working hours (Monday 9:00-17:00)
        $provider->workingHours()->create([
            'day' => 'monday',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_working' => true,
        ]);

        $service = Service::factory()->create(['provider_id' => $provider->id]);
        $cart = Cart::factory()->create([
            'user_id' => $this->user->id,
            'provider_id' => $provider->id,
            'type' => 'service',
        ]);

        $cart->items()->create([
            'item_type' => 'App\Models\Service',
            'item_id' => $service->id,
            'quantity' => 1,
            'price' => 50.00,
            'total' => 50.00,
        ]);

        // Try to book outside working hours (Monday 18:00)
        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->setTime(18, 0);

        $orderData = [
            'address_id' => $this->address->id,
            'booking_type' => 'salon',
            'payment_method' => 'wallet',
            'scheduled_at' => $nextMonday->toDateTimeString(),
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Scheduled time must be between 09:00 and 17:00');

        $this->orderService->createOrderFromCart($this->user, $orderData);
    }
}
