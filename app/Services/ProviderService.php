<?php

namespace App\Services;

use App\Models\Provider;
use App\Models\User;
use App\Models\ProviderSubOrder;

class ProviderService
{

    public function __construct(protected Provider $model)
    {
    }

    public function getProviderByCity(int $cityId)
    {
        return $this->model->where('status', 'accepted')
            ->whereHas('user', function ($query) use ($cityId) {
                    $query->where(['is_active' => true , 'city_id' => $cityId]);
            })
            ->get();
    }

    /**
     * Get providers based on authenticated user's gender or providers they have ordered from before
     *
     * @param User $authUser
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProvidersForUser(User $authUser)
    {
        $query = $this->model->where('status', 'accepted')
            ->where('accept_orders', true)
            ->whereHas('user', function ($userQuery) {
                $userQuery->where('is_active', true);
            });
        // Get previous provider IDs from suborders by first getting user's order IDs
        $userOrderIds = $authUser->orders()->pluck('id'); // Don't convert to array yet
        $previousProviderIds = [];
        if ($userOrderIds->isNotEmpty()) {
            $previousProviderIds = \App\Models\ProviderSubOrder::whereIn('order_id', $userOrderIds)
                ->distinct()
                ->pluck('provider_id')
                ->toArray();
        }
        // Build the main query with gender and previous orders logic
        $query->where(function ($mainQuery) use ($authUser, $previousProviderIds) {
            // Include providers with same gender as user
            if ($authUser->gender) {
                $mainQuery->orWhereHas('user', function ($genderQuery) use ($authUser) {
                    $genderQuery->where('gender', $authUser->gender);
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

    public function mostOrdered($limit = 10)
    {
        return $this->model->where('status', 'accepted')
            ->where('accept_orders', true)
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })
            ->withCount(['providerSubOrders as orders_count' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->orderByDesc('orders_count')
            ->limit($limit)
            ->with(['user:id,name,gender,city_id', 'activeServices:id,provider_id,name,price'])
            ->get();
    }

    /**
     * Filter providers with search, sorting, and filtering options
     *
     * Available sort_by options:
     * - latest: Sort by creation date (newest first)
     * - name_asc: Sort by commercial name (Arabic alphabetical أ-ى)
     * - name_desc: Sort by commercial name (Arabic alphabetical ى-أ)
     * - rates_desc: Sort by average rating (highest first)
     * - rates_asc: Sort by average rating (lowest first)
     */
    public function filterProviders(array $filters = [])
    {
        $query = $this->model->where('status', 'accepted')
            ->where('accept_orders', true)
            ->whereHas('user', function ($userQuery) use ($filters) {
                $userQuery->where('is_active', true);

                // Filter by city using when
                when($filters['city_id'] ?? null, function ($cityId) use ($userQuery) {
                    $userQuery->where('city_id', $cityId);
                });

                // Filter by gender using when
                when($filters['gender'] ?? null, function ($gender) use ($userQuery) {
                    $userQuery->where('gender', $gender);
                });
            });

        // Search in commercial_name or service names using when
        when($filters['search'] ?? null, function ($search) use ($query) {
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('commercial_name', 'LIKE', "%{$search}%")
                    ->orWhereHas('activeServices', function ($serviceQuery) use ($search) {
                        $serviceQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        });

        // Filter by provider type using when
        when($filters['provider_type'] ?? null, function ($providerType) use ($query) {
            $query->where('salon_type', $providerType);
        });

        // Filter by service type using when
        when($filters['service_type'] ?? null, function ($serviceType) use ($query) {
            when($serviceType === 'home', function () use ($query) {
                $query->where('in_home', true);
            });
            when($serviceType === 'salon', function () use ($query) {
                $query->where('in_salon', true);
            });
        });

        // Filter by service category using when
        when($filters['category_id'] ?? null, function ($categoryId) use ($query) {
            $query->whereHas('activeServices', function ($serviceQuery) use ($categoryId) {
                $serviceQuery->where('category_id', $categoryId);
            });
        });

        // Apply sorting using when instead of if conditions
        $sortBy = $filters['sort_by'] ?? 'latest';

        when($sortBy === 'latest', function () use ($query) {
            $query->orderByDesc('created_at');
        });

        when($sortBy === 'name_asc', function () use ($query) {
            // Arabic alphabetical أ-ى
            $query->orderByRaw('CONVERT(commercial_name USING utf8mb4) COLLATE utf8mb4_unicode_ci ASC');
        });

        when($sortBy === 'name_desc', function () use ($query) {
            // Arabic alphabetical ى-أ
            $query->orderByRaw('CONVERT(commercial_name USING utf8mb4) COLLATE utf8mb4_unicode_ci DESC');
        });

        when($sortBy === 'rates_desc', function () use ($query) {
            // Sort by highest rating first (polymorphic)
            $query->withAvg('rates as average_rating', 'rate')
                  ->orderByDesc('average_rating');
        });

        when($sortBy === 'rates_asc', function () use ($query) {
            // Sort by lowest rating first (polymorphic)
            $query->withAvg('rates as average_rating', 'rate')
                  ->orderBy('average_rating');
        });


        // Load relationships
        $query->with(['user:id,name,gender,city_id', 'activeServices:id,provider_id,name,price']);

        return $query->paginate($filters['limit'] ?? 15);
    }

   public function getById(int $id)
{
    return $this->model->find($id); 
}

}