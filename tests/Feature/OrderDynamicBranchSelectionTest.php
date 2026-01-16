<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Address;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\PaymentMethod;
use App\Services\OrderService;
use App\Services\CartService;
use App\Services\LoyaltyPointsService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class OrderDynamicBranchSelectionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $orderService;
    protected $user;
    protected $branch;
    protected $product;
    protected $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create services
        $cartService = app(CartService::class);
        $loyaltyService = app(LoyaltyPointsService::class);
        $paymentService = app(PaymentService::class);
        
        $this->orderService = new OrderService($cartService, $loyaltyService, $paymentService);
        
        // Create test data
        $this->user = User::factory()->create();
        $this->paymentMethod = PaymentMethod::factory()->create();
        $this->product = Product::factory()->create(['quantity' => 10]);
        
        // Create a branch with polygon
        $this->branch = Branch::factory()->create([
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'polygon' => json_encode([[
                ['lat' => 24.7000, 'lng' => 46.6500],
                ['lat' => 24.7200, 'lng' => 46.6500],
                ['lat' => 24.7200, 'lng' => 46.7000],
                ['lat' => 24.7000, 'lng' => 46.7000],
                ['lat' => 24.7000, 'lng' => 46.6500], // Close polygon
            ]]),
            'status' => 1
        ]);
        
        // Create cart with items
        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
    }

    /** @test */
    public function it_can_select_branch_using_address_id()
    {
        // Create address within branch polygon
        $address = Address::factory()->create([
            'user_id' => $this->user->id,
            'latitude' => 24.7100, // Within polygon
            'longitude' => 46.6750, // Within polygon
        ]);

        $orderData = [
            'address_id' => $address->id,
            'payment_method_id' => $this->paymentMethod->id,
            'delivery_type' => 'immediate',
            'order_type' => 'ordinary',
        ];

        $result = $this->orderService->createOrderRequest($this->user, $orderData);

        $this->assertArrayHasKey('order', $result);
        $this->assertEquals($this->branch->id, $result['order']->branch_id);
        $this->assertEquals($address->id, $result['order']->address_id);
    }

    /** @test */
    public function it_can_select_branch_using_direct_coordinates()
    {
        $orderData = [
            'latitude' => 24.7100, // Within polygon
            'longitude' => 46.6750, // Within polygon
            'payment_method_id' => $this->paymentMethod->id,
            'delivery_type' => 'immediate',
            'order_type' => 'ordinary',
            'phone' => '+966501234567',
            'address_name' => 'Test Address',
            'recipient_name' => 'Test Recipient',
            'city_id' => 1,
            'districts_id' => 1,
            'country_code' => '966',
        ];

        $result = $this->orderService->createOrderRequest($this->user, $orderData);

        $this->assertArrayHasKey('order', $result);
        $this->assertEquals($this->branch->id, $result['order']->branch_id);
        $this->assertNull($result['order']->address_id);
    }

    /** @test */
    public function it_throws_exception_when_location_is_outside_all_branch_areas()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(__('apis.address_not_in_branch_area'));

        $orderData = [
            'latitude' => 25.0000, // Outside polygon
            'longitude' => 47.0000, // Outside polygon
            'payment_method_id' => $this->paymentMethod->id,
            'delivery_type' => 'immediate',
            'order_type' => 'ordinary',
            'phone' => '+966501234567',
            'address_name' => 'Test Address',
            'recipient_name' => 'Test Recipient',
            'city_id' => 1,
            'districts_id' => 1,
            'country_code' => '966',
        ];

        $this->orderService->createOrderRequest($this->user, $orderData);
    }

    /** @test */
    public function it_throws_exception_when_no_location_data_provided()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(__('apis.location_required'));

        $orderData = [
            'payment_method_id' => $this->paymentMethod->id,
            'delivery_type' => 'immediate',
            'order_type' => 'ordinary',
        ];

        $this->orderService->createOrderRequest($this->user, $orderData);
    }

    /** @test */
    public function it_calculates_delivery_fee_based_on_distance()
    {
        // Mock site settings
        config(['site_settings.delivery_fee' => 10.0]);
        config(['site_settings.delivery_per_km_fee' => 2.0]);
        config(['site_settings.delivery_distance_threshold' => 5.0]);

        $orderData = [
            'latitude' => 24.7100,
            'longitude' => 46.6750,
            'payment_method_id' => $this->paymentMethod->id,
            'delivery_type' => 'immediate',
            'order_type' => 'ordinary',
            'phone' => '+966501234567',
            'address_name' => 'Test Address',
            'recipient_name' => 'Test Recipient',
            'city_id' => 1,
            'districts_id' => 1,
            'country_code' => '966',
        ];

        $result = $this->orderService->createOrderRequest($this->user, $orderData);

        $this->assertArrayHasKey('delivery_fee', $result);
        $this->assertIsNumeric($result['delivery_fee']);
        $this->assertGreaterThanOrEqual(0, $result['delivery_fee']);
    }
}
