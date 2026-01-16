<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Service;
use App\Models\Provider;
use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartMechanismTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $cartService;
    protected $provider1;
    protected $provider2;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->cartService = new CartService();
        
        $this->provider1 = Provider::factory()->create(['id' => 1]);
        $this->provider2 = Provider::factory()->create(['id' => 2]);
    }

    /** @test */
    public function case_1_adding_services_and_products_from_same_provider_works()
    {
        // Create service and product from same provider
        $service = Service::factory()->create(['provider_id' => $this->provider1->id]);
        $product = Product::factory()->create(['provider_id' => $this->provider1->id]);

        // Add service first
        $result1 = $this->cartService->addToCart($this->user, [
            'item_type' => 'service',
            'item_id' => $service->id,
            'quantity' => 1,
        ]);

        $this->assertInstanceOf(Cart::class, $result1);
        $this->assertEquals($this->provider1->id, $result1->provider_id);

        // Add product from same provider - should work without conflict
        $result2 = $this->cartService->addToCart($this->user, [
            'item_type' => 'product',
            'item_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->assertInstanceOf(Cart::class, $result2);
        $this->assertEquals(2, $result2->items()->count());
        $this->assertTrue($result2->hasServices());
        $this->assertTrue($result2->hasProducts());
    }

    /** @test */
    public function case_2_adding_service_from_different_provider_requires_confirmation()
    {
        // Add service from provider 1
        $service1 = Service::factory()->create(['provider_id' => $this->provider1->id]);
        $product1 = Product::factory()->create(['provider_id' => $this->provider1->id]);

        $this->cartService->addToCart($this->user, [
            'item_type' => 'service',
            'item_id' => $service1->id,
            'quantity' => 1,
        ]);

        $this->cartService->addToCart($this->user, [
            'item_type' => 'product',
            'item_id' => $product1->id,
            'quantity' => 1,
        ]);

        // Try to add service from provider 2 - should require confirmation
        $service2 = Service::factory()->create(['provider_id' => $this->provider2->id]);
        
        $result = $this->cartService->addToCart($this->user, [
            'item_type' => 'service',
            'item_id' => $service2->id,
            'quantity' => 1,
        ]);

        $this->assertIsArray($result);
        $this->assertTrue($result['requires_confirmation']);
        $this->assertEquals('service_different_provider', $result['conflict_type']);
        $this->assertNotEmpty($result['items_to_remove']);
    }

    /** @test */
    public function case_3_adding_service_with_multiple_products_requires_confirmation()
    {
        // Add products from multiple providers
        $product1 = Product::factory()->create(['provider_id' => $this->provider1->id]);
        $product2 = Product::factory()->create(['provider_id' => $this->provider2->id]);

        $this->cartService->addToCart($this->user, [
            'item_type' => 'product',
            'item_id' => $product1->id,
            'quantity' => 1,
        ]);

        $this->cartService->addToCart($this->user, [
            'item_type' => 'product',
            'item_id' => $product2->id,
            'quantity' => 1,
        ]);

        // Try to add service - should require confirmation
        $service = Service::factory()->create(['provider_id' => $this->provider1->id]);
        
        $result = $this->cartService->addToCart($this->user, [
            'item_type' => 'service',
            'item_id' => $service->id,
            'quantity' => 1,
        ]);

        $this->assertIsArray($result);
        $this->assertTrue($result['requires_confirmation']);
        $this->assertEquals('service_with_multiple_products', $result['conflict_type']);
    }

    /** @test */
    public function case_4_adding_products_from_multiple_providers_works()
    {
        // Add products from different providers - should work fine
        $product1 = Product::factory()->create(['provider_id' => $this->provider1->id]);
        $product2 = Product::factory()->create(['provider_id' => $this->provider2->id]);

        $result1 = $this->cartService->addToCart($this->user, [
            'item_type' => 'product',
            'item_id' => $product1->id,
            'quantity' => 1,
        ]);

        $this->assertInstanceOf(Cart::class, $result1);

        $result2 = $this->cartService->addToCart($this->user, [
            'item_type' => 'product',
            'item_id' => $product2->id,
            'quantity' => 2,
        ]);

        $this->assertInstanceOf(Cart::class, $result2);
        $this->assertEquals(2, $result2->items()->count());
        $this->assertTrue($result2->hasProducts());
        $this->assertFalse($result2->hasServices());
        $this->assertNull($result2->provider_id); // No single provider
    }

    /** @test */
    public function force_add_removes_conflicting_items()
    {
        // Add service from provider 1
        $service1 = Service::factory()->create(['provider_id' => $this->provider1->id]);
        $this->cartService->addToCart($this->user, [
            'item_type' => 'service',
            'item_id' => $service1->id,
            'quantity' => 1,
        ]);

        // Force add service from provider 2
        $service2 = Service::factory()->create(['provider_id' => $this->provider2->id]);
        
        $result = $this->cartService->addToCart($this->user, [
            'item_type' => 'service',
            'item_id' => $service2->id,
            'quantity' => 1,
            'force_add' => true,
        ]);

        $this->assertInstanceOf(Cart::class, $result);
        $this->assertEquals(1, $result->items()->count());
        $this->assertEquals($this->provider2->id, $result->provider_id);
        
        // Should only have the new service
        $this->assertEquals($service2->id, $result->items()->first()->item_id);
    }
}
