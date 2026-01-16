<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Provider;
use App\Services\LocationBasedProviderService;
use App\Services\ProviderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class LocationBasedProviderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected LocationBasedProviderService $locationService;
    protected User $user;
    protected Provider $nearProvider;
    protected Provider $farProvider;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->locationService = app(LocationBasedProviderService::class);
        
        // Create a test user with location (Riyadh coordinates)
        $this->user = User::factory()->create([
            'lat' => '24.7136',
            'lng' => '46.6753',
            'gender' => 'female',
            'city_id' => 1,
            'is_active' => true
        ]);

        // Create a provider user for the near provider
        $nearProviderUser = User::factory()->create([
            'gender' => 'female',
            'city_id' => 1,
            'is_active' => true
        ]);

        // Create a provider user for the far provider
        $farProviderUser = User::factory()->create([
            'gender' => 'female',
            'city_id' => 1,
            'is_active' => true
        ]);

        // Create a near provider (within 10km of user - Riyadh center)
        $this->nearProvider = Provider::factory()->create([
            'user_id' => $nearProviderUser->id,
            'lat' => '24.7236', // About 1km north
            'lng' => '46.6853', // About 1km east
            'status' => 'accepted',
            'accept_orders' => true,
            'is_active' => true
        ]);

        // Create a far provider (more than 50km away - Jeddah coordinates)
        $this->farProvider = Provider::factory()->create([
            'user_id' => $farProviderUser->id,
            'lat' => '21.4858', // Jeddah coordinates (about 950km away)
            'lng' => '39.1925',
            'status' => 'accepted',
            'accept_orders' => true,
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_can_calculate_distance_correctly()
    {
        // Test distance calculation using known coordinates
        $riyadhLat = 24.7136;
        $riyadhLng = 46.6753;
        $jeddahLat = 21.4858;
        $jeddahLng = 39.1925;

        $reflection = new \ReflectionClass($this->locationService);
        $method = $reflection->getMethod('calculateDistance');
        $method->setAccessible(true);

        $distance = $method->invoke(
            $this->locationService,
            $riyadhLat,
            $riyadhLng,
            $jeddahLat,
            $jeddahLng
        );

        // Distance between Riyadh and Jeddah is approximately 950km
        $this->assertGreaterThan(900, $distance);
        $this->assertLessThan(1000, $distance);
    }

    /** @test */
    public function it_filters_providers_by_distance()
    {
        $providers = collect([$this->nearProvider, $this->farProvider]);

        $reflection = new \ReflectionClass($this->locationService);
        $method = $reflection->getMethod('filterProvidersByDistance');
        $method->setAccessible(true);

        // Filter with 50km radius
        $filteredProviders = $method->invoke(
            $this->locationService,
            $providers,
            (float) $this->user->lat,
            (float) $this->user->lng,
            50.0
        );

        // Should only include the near provider
        $this->assertCount(1, $filteredProviders);
        $this->assertEquals($this->nearProvider->id, $filteredProviders->first()->id);
    }

    /** @test */
    public function it_returns_providers_within_user_radius()
    {
        $this->actingAs($this->user);

        $providers = $this->locationService->getProvidersWithinUserRadius($this->user, 50);

        // Should only include the near provider
        $this->assertCount(1, $providers);
        $this->assertEquals($this->nearProvider->id, $providers->first()->id);
    }

    /** @test */
    public function it_returns_all_providers_when_user_has_no_location()
    {
        // Create user without location
        $userWithoutLocation = User::factory()->create([
            'lat' => null,
            'lng' => null,
            'gender' => 'female',
            'city_id' => 1,
            'is_active' => true
        ]);

        $this->actingAs($userWithoutLocation);

        $providers = $this->locationService->getProvidersWithinUserRadius($userWithoutLocation, 50);

        // Should return providers using fallback behavior (gender matching)
        $this->assertGreaterThanOrEqual(1, $providers->count());
    }

    /** @test */
    public function it_can_get_providers_by_distance_from_coordinates()
    {
        $providers = $this->locationService->getProvidersByDistance(
            (float) $this->user->lat,
            (float) $this->user->lng,
            50.0
        );

        // Should only include the near provider
        $this->assertCount(1, $providers);
        $this->assertEquals($this->nearProvider->id, $providers->first()->id);
        
        // Should have distance attribute
        $this->assertObjectHasAttribute('distance_km', $providers->first());
        $this->assertLessThan(50, $providers->first()->distance_km);
    }

    /** @test */
    public function it_sorts_providers_by_distance()
    {
        // Create another provider at medium distance
        $mediumProviderUser = User::factory()->create([
            'gender' => 'female',
            'city_id' => 1,
            'is_active' => true
        ]);

        $mediumProvider = Provider::factory()->create([
            'user_id' => $mediumProviderUser->id,
            'lat' => '24.8136', // About 11km north
            'lng' => '46.7753', // About 11km east
            'status' => 'accepted',
            'accept_orders' => true,
            'is_active' => true
        ]);

        $providers = $this->locationService->getProvidersByDistance(
            (float) $this->user->lat,
            (float) $this->user->lng,
            50.0
        );

        // Should include both near and medium providers, sorted by distance
        $this->assertCount(2, $providers);
        $this->assertEquals($this->nearProvider->id, $providers->first()->id);
        $this->assertEquals($mediumProvider->id, $providers->last()->id);
        
        // Verify sorting by distance
        $this->assertLessThan($providers->last()->distance_km, $providers->first()->distance_km);
    }
}
