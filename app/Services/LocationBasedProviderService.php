<?php

namespace App\Services;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class LocationBasedProviderService
{
    /**
     * Default radius in kilometers
     */
    const DEFAULT_RADIUS_KM = 50;

    public function __construct(protected ProviderService $providerService)
    {
    }

    /**
     * Get providers within radius of authenticated user's location
     * This is now the primary method that starts with location-based filtering
     * If user has no location data, returns all providers
     *
     * @param User $user
     * @param float|null $radiusKm
     * @return Collection
     */
    public function getProvidersWithinUserRadius(User $user, ?float $radiusKm = null): Collection
    {
        // Use default radius if not provided
        $radiusKm = $radiusKm ?? self::DEFAULT_RADIUS_KM;

        // Check if user has location data
        if (!$user->lat || !$user->lng) {
            // If user has no location, return all providers with user-specific logic
            return $this->getAllProvidersWithUserLogic($user);
        }

        // Start with location-based filtering as primary logic
        $providers = $this->getProvidersByDistanceWithUserLogic(
            (float) $user->lat,
            (float) $user->lng,
            $radiusKm,
            $user
        );

        return $providers;
    }

    /**
     * Get most ordered providers within user's radius
     * If user has no location data, returns all most ordered providers
     *
     * @param User $user
     * @param float|null $radiusKm
     * @param int $limit
     * @return Collection
     */
    public function getMostOrderedProvidersWithinRadius(User $user, ?float $radiusKm = null, int $limit = 10): Collection
    {
        $radiusKm = $radiusKm ?? self::DEFAULT_RADIUS_KM;

        // If user has no location, return all most ordered providers
        if (!$user->lat || !$user->lng) {
            return $this->providerService->mostOrdered($limit);
        }

        // Start with location-based query and add order count logic
        $query = Provider::where('status', 'accepted')
            ->where('accept_orders', true)
            ->whereHas('user', function ($userQuery) {
                $userQuery->where('is_active', true);
            })
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->withCount(['providerSubOrders as orders_count' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->orderByDesc('orders_count')
            ->with(['user:id,name,gender,city_id', 'activeServices:id,provider_id,name,price']);

        $providers = $query->get();

        // Filter by distance first, then take the limit
        $filteredProviders = $this->filterProvidersByDistance(
            $providers,
            (float) $user->lat,
            (float) $user->lng,
            $radiusKm
        );

        // Return only the requested limit, maintaining order by orders_count
        return $filteredProviders->sortByDesc('orders_count')->take($limit)->values();
    }

    /**
     * Filter providers with location-based filtering as primary logic
     * If user has no location data, returns all providers with applied filters
     *
     * @param array $filters
     * @param User $user
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function filterProvidersWithLocation(array $filters, User $user)
    {
        $radiusKm = $filters['radius_km'] ?? self::DEFAULT_RADIUS_KM;

        // If user has no location, use regular filtering to return all providers
        if (!$user->lat || !$user->lng) {
            return $this->providerService->filterProviders($filters);
        }

        // Start with location-based query as primary logic
        $query = Provider::where('status', 'accepted')
            ->where('accept_orders', true)
            ->whereHas('user', function ($userQuery) use ($filters) {
                $userQuery->where('is_active', true);

                // Filter by city
                if (!empty($filters['city_id'])) {
                    $userQuery->where('city_id', $filters['city_id']);
                }

                // Filter by gender
                if (!empty($filters['gender'])) {
                    $userQuery->where('gender', $filters['gender']);
                }
            })
            ->whereNotNull('lat')
            ->whereNotNull('lng');

        // Apply provider-specific filters
        if (!empty($filters['provider_type'])) {
            $query->where('salon_type', $filters['provider_type']);
        }

        // Filter by service type
        if (!empty($filters['service_type'])) {
            if ($filters['service_type'] === 'home') {
                $query->where('in_home', true);
            } elseif ($filters['service_type'] === 'salon') {
                $query->where('in_salon', true);
            }
        }

        // Filter by service category
        if (!empty($filters['category_id'])) {
            $query->whereHas('activeServices', function ($serviceQuery) use ($filters) {
                $serviceQuery->where('category_id', $filters['category_id']);
            });
        }

        // Search in commercial_name or service names
        if (!empty($filters['search'])) {
            $query->where(function ($searchQuery) use ($filters) {
                $searchQuery->where('commercial_name', 'LIKE', "%{$filters['search']}%")
                    ->orWhereHas('activeServices', function ($serviceQuery) use ($filters) {
                        $serviceQuery->where('name', 'LIKE', "%{$filters['search']}%");
                    });
            });
        }

        // Load relationships
        $query->with(['user:id,name,gender,city_id', 'activeServices:id,provider_id,name,price']);

        $allProviders = $query->get();

        // Filter by distance
        $filteredProviders = $this->filterProvidersByDistance(
            $allProviders,
            (float) $user->lat,
            (float) $user->lng,
            $radiusKm
        );

        // Apply sorting after distance filtering
        $sortBy = $filters['sort_by'] ?? 'distance'; // Default to distance sorting
        $filteredProviders = $this->applySorting($filteredProviders, $sortBy);

        // Manually paginate the filtered results
        $page = $filters['page'] ?? 1;
        $perPage = $filters['limit'] ?? 15;
        $offset = ($page - 1) * $perPage;

        $paginatedItems = $filteredProviders->slice($offset, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $filteredProviders->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }

    /**
     * Get providers by city with location filtering as primary logic
     * If user has no location data, returns all providers in the specified city
     *
     * @param int $cityId
     * @param User $user
     * @param float|null $radiusKm
     * @return Collection
     */
    public function getProvidersByCityWithinRadius(int $cityId, User $user, ?float $radiusKm = null): Collection
    {
        $radiusKm = $radiusKm ?? self::DEFAULT_RADIUS_KM;

        // If user has no location, return all providers in the specified city
        if (!$user->lat || !$user->lng) {
            return $this->providerService->getProviderByCity($cityId);
        }

        // Start with location-based query filtered by city
        $query = Provider::where('status', 'accepted')
            ->where('accept_orders', true)
            ->whereHas('user', function ($userQuery) use ($cityId) {
                $userQuery->where('is_active', true)
                         ->where('city_id', $cityId);
            })
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->with(['user:id,name,gender,city_id', 'activeServices:id,provider_id,name,price']);

        $providers = $query->get();

        return $this->filterProvidersByDistance(
            $providers,
            (float) $user->lat,
            (float) $user->lng,
            $radiusKm
        );
    }

    /**
     * Filter providers by distance from a given point
     *
     * @param Collection $providers
     * @param float $userLat
     * @param float $userLng
     * @param float $radiusKm
     * @return Collection
     */
    protected function filterProvidersByDistance(Collection $providers, float $userLat, float $userLng, float $radiusKm): Collection
    {
        return $providers->filter(function ($provider) use ($userLat, $userLng, $radiusKm) {
            // Skip providers without location data
            if (!$provider->lat || !$provider->lng) {
                return false;
            }

            $distance = $this->calculateDistance(
                $userLat,
                $userLng,
                (float) $provider->lat,
                (float) $provider->lng
            );

            return $distance <= $radiusKm;
        })->map(function ($provider) use ($userLat, $userLng) {
            // Add distance to provider for potential sorting
            $provider->distance_km = $this->calculateDistance(
                $userLat,
                $userLng,
                (float) $provider->lat,
                (float) $provider->lng
            );

            return $provider;
        })->sortBy('distance_km')->values();
    }

    /**
     * Calculate distance between two points using Haversine formula
     *
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return float Distance in kilometers
     */
    protected function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get providers within radius sorted by distance
     * If coordinates are null/0, returns all providers without distance filtering
     *
     * @param float|null $userLat
     * @param float|null $userLng
     * @param float|null $radiusKm
     * @param array $additionalFilters
     * @return Collection
     */
    public function getProvidersByDistance(?float $userLat, ?float $userLng, ?float $radiusKm = null, array $additionalFilters = []): Collection
    {
        $radiusKm = $radiusKm ?? self::DEFAULT_RADIUS_KM;

        // Start with basic provider query
        $query = Provider::where('status', 'accepted')
            ->where('accept_orders', true)
            ->whereHas('user', function ($userQuery) {
                $userQuery->where('is_active', true);
            });

        // Only filter by location if we have valid coordinates
        if ($userLat && $userLng) {
            $query->whereNotNull('lat')->whereNotNull('lng');
        }

        // Apply additional filters if provided
        if (!empty($additionalFilters['city_id'])) {
            $query->whereHas('user', function ($userQuery) use ($additionalFilters) {
                $userQuery->where('city_id', $additionalFilters['city_id']);
            });
        }

        if (!empty($additionalFilters['gender'])) {
            $query->whereHas('user', function ($userQuery) use ($additionalFilters) {
                $userQuery->where('gender', $additionalFilters['gender']);
            });
        }

        if (!empty($additionalFilters['provider_type'])) {
            $query->where('salon_type', $additionalFilters['provider_type']);
        }

        $providers = $query->with(['user:id,name,gender,city_id', 'activeServices:id,provider_id,name,price'])->get();

        // If user has location, filter by distance
        if ($userLat && $userLng) {
            return $this->filterProvidersByDistance($providers, $userLat, $userLng, $radiusKm);
        }

        // If no user location, return all providers without distance filtering
        return $providers;
    }

    /**
     * Get providers by distance with user-specific logic (gender, order history)
     * This method starts with location filtering and then applies user preferences
     * If user has no location, returns all providers with user-specific logic applied
     *
     * @param float|null $userLat
     * @param float|null $userLng
     * @param float $radiusKm
     * @param User $user
     * @return Collection
     */
    protected function getProvidersByDistanceWithUserLogic(?float $userLat, ?float $userLng, float $radiusKm, User $user): Collection
    {
        // Start with basic provider query
        $query = Provider::where('status', 'accepted')
            ->where('accept_orders', true)
            ->whereHas('user', function ($userQuery) {
                $userQuery->where('is_active', true);
            });

        // Only filter by location if we have valid coordinates
        if ($userLat && $userLng) {
            $query->whereNotNull('lat')->whereNotNull('lng');
        }

        // Get previous provider IDs from suborders for order history logic
        $userOrderIds = $user->orders()->pluck('id');
        $previousProviderIds = [];
        if ($userOrderIds->isNotEmpty()) {
            $previousProviderIds = \App\Models\ProviderSubOrder::whereIn('order_id', $userOrderIds)
                ->distinct()
                ->pluck('provider_id')
                ->toArray();
        }

        // Apply user-specific logic (gender preference and order history)
        $query->where(function ($mainQuery) use ($user, $previousProviderIds) {
            // Include providers with same gender as user
            if ($user->gender) {
                $mainQuery->orWhereHas('user', function ($genderQuery) use ($user) {
                    $genderQuery->where('gender', $user->gender);
                });
            }
            // Include providers the user has ordered from before (regardless of gender)
            if (!empty($previousProviderIds)) {
                $mainQuery->orWhereIn('id', $previousProviderIds);
            }
        });

        // Load relationships
        $query->with([
            'user:id,name,gender,city_id',
            'activeServices:id,provider_id,name,price',
            'workingHours'
        ]);

        $providers = $query->get();

        // If user has location, filter by distance and sort by distance
        if ($userLat && $userLng) {
            return $this->filterProvidersByDistance($providers, $userLat, $userLng, $radiusKm);
        }

        // If no user location, return all providers without distance filtering
        return $providers;
    }

    /**
     * Apply sorting to filtered providers
     * Handles cases where distance_km might not be available (when user has no location)
     *
     * @param Collection $providers
     * @param string $sortBy
     * @return Collection
     */
    protected function applySorting(Collection $providers, string $sortBy): Collection
    {
        switch ($sortBy) {
            case 'distance':
                // Only sort by distance if distance_km is available
                if ($providers->first() && isset($providers->first()->distance_km)) {
                    return $providers->sortBy('distance_km')->values();
                }
                // Fallback to name sorting if no distance available
                return $providers->sortBy('commercial_name')->values();

            case 'latest':
                return $providers->sortByDesc('created_at')->values();

            case 'name_asc':
                return $providers->sortBy('commercial_name')->values();

            case 'name_desc':
                return $providers->sortByDesc('commercial_name')->values();

            case 'rates_desc':
                return $providers->sortByDesc(function ($provider) {
                    return $provider->rates()->avg('rate') ?? 0;
                })->values();

            case 'rates_asc':
                return $providers->sortBy(function ($provider) {
                    return $provider->rates()->avg('rate') ?? 0;
                })->values();

            default:
                // Default to distance sorting if available, otherwise name sorting
                if ($providers->first() && isset($providers->first()->distance_km)) {
                    return $providers->sortBy('distance_km')->values();
                }
                return $providers->sortBy('commercial_name')->values();
        }
    }

    /**
     * Get all providers with user-specific logic when user has no location
     * Applies gender preference and order history logic without location filtering
     *
     * @param User $user
     * @return Collection
     */
    protected function getAllProvidersWithUserLogic(User $user): Collection
    {
        // Start with basic provider query
        $query = Provider::where('status', 'accepted')
            ->where('accept_orders', true)
            ->whereHas('user', function ($userQuery) {
                $userQuery->where('is_active', true);
            });

        // Get previous provider IDs from suborders for order history logic
        $userOrderIds = $user->orders()->pluck('id');
        $previousProviderIds = [];
        if ($userOrderIds->isNotEmpty()) {
            $previousProviderIds = \App\Models\ProviderSubOrder::whereIn('order_id', $userOrderIds)
                ->distinct()
                ->pluck('provider_id')
                ->toArray();
        }

        // Apply user-specific logic (gender preference and order history)
        $query->where(function ($mainQuery) use ($user, $previousProviderIds) {
            // Include providers with same gender as user
            if ($user->gender) {
                $mainQuery->orWhereHas('user', function ($genderQuery) use ($user) {
                    $genderQuery->where('gender', $user->gender);
                });
            }
            // Include providers the user has ordered from before (regardless of gender)
            if (!empty($previousProviderIds)) {
                $mainQuery->orWhereIn('id', $previousProviderIds);
            }
        });

        // Load relationships
        $query->with([
            'user:id,name,gender,city_id',
            'activeServices:id,provider_id,name,price',
            'workingHours'
        ]);

        return $query->get();
    }
}
