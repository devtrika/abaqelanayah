<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Service;
use App\Models\Provider;
use App\Models\SiteSetting;
use App\Services\FeeCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeeCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected $feeCalculationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->feeCalculationService = new FeeCalculationService();
        
        // Set up fee settings
        SiteSetting::create(['key' => 'booking_fee_amount', 'value' => '10.00']);
        SiteSetting::create(['key' => 'default_home_service_fee', 'value' => '15.00']);
        SiteSetting::create(['key' => 'normal_delivery_fee', 'value' => '5.00']);
        SiteSetting::create(['key' => 'express_delivery_fee', 'value' => '15.00']);
        SiteSetting::create(['key' => 'cancellation_fee_amount', 'value' => '5.00']);
        SiteSetting::create(['key' => 'cancellation_fee_percentage', 'value' => '0']);
    }

    /** @test */
    public function it_calculates_booking_fee_for_services()
    {
        $user = User::factory()->create();
        $provider = Provider::factory()->create();
        $service = Service::factory()->create(['provider_id' => $provider->id]);
        
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'provider_id' => $provider->id,
            'type' => 'service'
        ]);
        
        $cart->items()->create([
            'item_type' => 'App\Models\Service',
            'item_id' => $service->id,
            'quantity' => 1,
            'price' => 50.00,
            'total' => 50.00,
        ]);

        $fees = $this->feeCalculationService->calculateCartFees($cart);

        $this->assertEquals(10.00, $fees['booking_fee']);
        $this->assertEquals(0, $fees['home_service_fee']);
        $this->assertEquals(0, $fees['delivery_fee']);
        $this->assertEquals(10.00, $fees['total_fees']);
    }

    /** @test */
    public function it_calculates_home_service_fee_when_booking_type_is_home()
    {
        $user = User::factory()->create();
        $provider = Provider::factory()->create(['home_fees' => 20.00]);
        $service = Service::factory()->create(['provider_id' => $provider->id]);
        
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'provider_id' => $provider->id,
            'type' => 'service'
        ]);
        
        $cart->items()->create([
            'item_type' => 'App\Models\Service',
            'item_id' => $service->id,
            'quantity' => 1,
            'price' => 50.00,
            'total' => 50.00,
        ]);

        $fees = $this->feeCalculationService->calculateCartFees($cart, ['booking_type' => 'home']);

        $this->assertEquals(10.00, $fees['booking_fee']);
        $this->assertEquals(20.00, $fees['home_service_fee']); // Provider's custom fee
        $this->assertEquals(0, $fees['delivery_fee']);
        $this->assertEquals(30.00, $fees['total_fees']);
    }

    /** @test */
    public function it_calculates_delivery_fee_for_products()
    {
        $user = User::factory()->create();
        $provider = Provider::factory()->create();
        $product = Product::factory()->create(['provider_id' => $provider->id]);
        
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'provider_id' => $provider->id,
            'type' => 'product'
        ]);
        
        $cart->items()->create([
            'item_type' => 'App\Models\Product',
            'item_id' => $product->id,
            'quantity' => 2,
            'price' => 25.00,
            'total' => 50.00,
        ]);

        // Normal delivery
        $fees = $this->feeCalculationService->calculateCartFees($cart, ['delivery_type' => 'normal']);
        $this->assertEquals(0, $fees['booking_fee']);
        $this->assertEquals(0, $fees['home_service_fee']);
        $this->assertEquals(5.00, $fees['delivery_fee']);
        $this->assertEquals(5.00, $fees['total_fees']);

        // Express delivery
        $fees = $this->feeCalculationService->calculateCartFees($cart, ['delivery_type' => 'express']);
        $this->assertEquals(15.00, $fees['delivery_fee']);
        $this->assertEquals(15.00, $fees['total_fees']);
    }

    /** @test */
    public function it_calculates_mixed_cart_fees()
    {
        $user = User::factory()->create();
        $provider = Provider::factory()->create(['home_fees' => 20.00]);
        $service = Service::factory()->create(['provider_id' => $provider->id]);
        $product = Product::factory()->create(['provider_id' => $provider->id]);
        
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'provider_id' => $provider->id,
            'type' => 'mixed'
        ]);
        
        // Add service
        $cart->items()->create([
            'item_type' => 'App\Models\Service',
            'item_id' => $service->id,
            'quantity' => 1,
            'price' => 50.00,
            'total' => 50.00,
        ]);
        
        // Add product
        $cart->items()->create([
            'item_type' => 'App\Models\Product',
            'item_id' => $product->id,
            'quantity' => 1,
            'price' => 30.00,
            'total' => 30.00,
        ]);

        $fees = $this->feeCalculationService->calculateCartFees($cart, [
            'booking_type' => 'home',
            'delivery_type' => 'express'
        ]);

        $this->assertEquals(10.00, $fees['booking_fee']); // Service booking fee
        $this->assertEquals(20.00, $fees['home_service_fee']); // Home service fee
        $this->assertEquals(15.00, $fees['delivery_fee']); // Express delivery fee
        $this->assertEquals(45.00, $fees['total_fees']);
    }

    /** @test */
    public function it_provides_fee_breakdown_with_descriptions()
    {
        $user = User::factory()->create();
        $provider = Provider::factory()->create();
        $service = Service::factory()->create(['provider_id' => $provider->id]);
        
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'provider_id' => $provider->id,
            'type' => 'service'
        ]);
        
        $cart->items()->create([
            'item_type' => 'App\Models\Service',
            'item_id' => $service->id,
            'quantity' => 1,
            'price' => 50.00,
            'total' => 50.00,
        ]);

        $breakdown = $this->feeCalculationService->getFeeBreakdown($cart, ['booking_type' => 'home']);

        $this->assertCount(2, $breakdown); // Booking fee + Home service fee
        
        // Check booking fee
        $bookingFee = collect($breakdown)->firstWhere('type', 'booking_fee');
        $this->assertEquals('booking_fee', $bookingFee['type']);
        $this->assertEquals('رسوم الحجز', $bookingFee['name_ar']);
        $this->assertEquals('Booking Fee', $bookingFee['name_en']);
        $this->assertEquals(10.00, $bookingFee['amount']);
        $this->assertTrue($bookingFee['refundable']);
        
        // Check home service fee
        $homeFee = collect($breakdown)->firstWhere('type', 'home_service_fee');
        $this->assertEquals('home_service_fee', $homeFee['type']);
        $this->assertEquals('رسوم الخدمة بالمنزل', $homeFee['name_ar']);
        $this->assertEquals('Home Service Fee', $homeFee['name_en']);
        $this->assertEquals(15.00, $homeFee['amount']); // Default since provider doesn't have custom fee
        $this->assertTrue($homeFee['refundable']);
    }
}
