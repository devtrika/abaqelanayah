<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Provider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class ProviderDistanceAttributeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_calculates_distance_from_authenticated_user()
    {
        // Create a user with location (Riyadh coordinates)
        $user = User::factory()->create([
            'lat' => '24.7136',
            'lng' => '46.6753',
        ]);

        // Create a provider user
        $providerUser = User::factory()->create();

        // Create a provider with location (about 1km away from user)
        $provider = Provider::factory()->create([
            'user_id' => $providerUser->id,
            'lat' => '24.7236', // About 1km north
            'lng' => '46.6853', // About 1km east
        ]);

        // Authenticate the user
        Auth::login($user);

        // Get the distance attribute
        $distance = $provider->distance_from_user;

        // Should be approximately 1-2 km
        $this->assertNotNull($distance);
        $this->assertGreaterThan(0, $distance);
        $this->assertLessThan(5, $distance); // Should be less than 5km
    }

    /** @test */
    public function it_returns_null_when_no_authenticated_user()
    {
        // Create a provider user
        $providerUser = User::factory()->create();

        // Create a provider with location
        $provider = Provider::factory()->create([
            'user_id' => $providerUser->id,
            'lat' => '24.7236',
            'lng' => '46.6853',
        ]);

        // No authenticated user
        Auth::logout();

        // Get the distance attribute
        $distance = $provider->distance_from_user;

        // Should be null when no authenticated user
        $this->assertNull($distance);
    }

    /** @test */
    public function it_returns_null_when_user_has_no_location()
    {
        // Create a user without location
        $user = User::factory()->create([
            'lat' => null,
            'lng' => null,
        ]);

        // Create a provider user
        $providerUser = User::factory()->create();

        // Create a provider with location
        $provider = Provider::factory()->create([
            'user_id' => $providerUser->id,
            'lat' => '24.7236',
            'lng' => '46.6853',
        ]);

        // Authenticate the user
        Auth::login($user);

        // Get the distance attribute
        $distance = $provider->distance_from_user;

        // Should be null when user has no location
        $this->assertNull($distance);
    }

    /** @test */
    public function it_returns_null_when_provider_has_no_location()
    {
        // Create a user with location
        $user = User::factory()->create([
            'lat' => '24.7136',
            'lng' => '46.6753',
        ]);

        // Create a provider user
        $providerUser = User::factory()->create();

        // Create a provider without location
        $provider = Provider::factory()->create([
            'user_id' => $providerUser->id,
            'lat' => null,
            'lng' => null,
        ]);

        // Authenticate the user
        Auth::login($user);

        // Get the distance attribute
        $distance = $provider->distance_from_user;

        // Should be null when provider has no location
        $this->assertNull($distance);
    }

    /** @test */
    public function it_calculates_distance_from_specific_coordinates()
    {
        // Create a provider user
        $providerUser = User::factory()->create();

        // Create a provider with location (Riyadh coordinates)
        $provider = Provider::factory()->create([
            'user_id' => $providerUser->id,
            'lat' => '24.7136',
            'lng' => '46.6753',
        ]);

        // Calculate distance from Jeddah coordinates
        $jeddahLat = 21.4858;
        $jeddahLng = 39.1925;

        $distance = $provider->distanceFrom($jeddahLat, $jeddahLng);

        // Distance between Riyadh and Jeddah is approximately 950km
        $this->assertGreaterThan(900, $distance);
        $this->assertLessThan(1000, $distance);
    }

    /** @test */
    public function it_returns_zero_when_provider_has_no_location_for_distance_from()
    {
        // Create a provider user
        $providerUser = User::factory()->create();

        // Create a provider without location
        $provider = Provider::factory()->create([
            'user_id' => $providerUser->id,
            'lat' => null,
            'lng' => null,
        ]);

        // Calculate distance from any coordinates
        $distance = $provider->distanceFrom(24.7136, 46.6753);

        // Should return 0 when provider has no location
        $this->assertEquals(0, $distance);
    }

    /** @test */
    public function it_calculates_accurate_distance_using_haversine_formula()
    {
        // Create a provider user
        $providerUser = User::factory()->create();

        // Create a provider at known coordinates (Riyadh King Khalid Airport)
        $provider = Provider::factory()->create([
            'user_id' => $providerUser->id,
            'lat' => '24.9576', // King Khalid Airport
            'lng' => '46.6988',
        ]);

        // Calculate distance from Riyadh city center
        $riyadhCenterLat = 24.7136;
        $riyadhCenterLng = 46.6753;

        $distance = $provider->distanceFrom($riyadhCenterLat, $riyadhCenterLng);

        // Distance should be approximately 27-30 km
        $this->assertGreaterThan(25, $distance);
        $this->assertLessThan(35, $distance);
    }
}
