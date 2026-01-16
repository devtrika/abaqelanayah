<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceService
{
    /**
     * Get all services for a provider with pagination
     *
     * @param int $providerId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */

     public function getAllServices(array $filters = [])
     {
        $query = Service::with(['provider', 'category'])
        ->when($filters['category_id'] ?? null, function ($q, $categoryId) {
            $q->where('category_id', $categoryId);
        })
        ->whereHas('provider', function ($providerQuery) {
            $providerQuery->where('status', 'accepted')
                ->whereHas('user', function ($userQuery) {
                    $userQuery->where('is_active', true);
                });
        });

    // Sorting logic
    $sort = $filters['sort'] ?? 'new_to_old';
    if ($sort === 'old_to_new') {
        $query->orderBy('created_at', 'asc');
    } else {
        $query->orderBy('created_at', 'desc');
    }

    return $query->get();
     }
    public function getProviderServices(int $providerId, array $filters = []): Collection
{
    $query = Service::forProvider($providerId)
        ->with(['category'])
        ->orderBy('created_at', 'desc');

    // Apply filters
    if (isset($filters['is_active'])) {
        $query->where('is_active', $filters['is_active']);
    }

    if (isset($filters['category_id'])) {
        $query->where('category_id', $filters['category_id']);
    }

    if (isset($filters['search'])) {
        $query->where(function ($q) use ($filters) {
            $q->where('name', 'like', '%' . $filters['search'] . '%')
              ->orWhere('description', 'like', '%' . $filters['search'] . '%');
        });
    }

    if (isset($filters['min_price'])) {
        $query->where('price', '>=', $filters['min_price']);
    }

    if (isset($filters['max_price'])) {
        $query->where('price', '<=', $filters['max_price']);
    }
    return $query->get(); // ✅ Return all data, no pagination
}

    /**
     * Create a new service for a provider
     *
     * @param int $providerId
     * @param array $data
     * @return Service
     */
    public function createService(int $providerId, array $data): Service
    {
        $service = new Service();
    
        // Set translatable fields
        $service->setTranslations('name', $data['name']);
        $service->setTranslations('description', $data['description']);
    
        // Set other attributes
        $service->provider_id = $providerId;
        $service->price = $data['price'];
        $service->duration = $data['duration'];
        $service->expected_time_to_accept = $data['expected_time_to_accept'];
        $service->is_active = $data['is_active'] ?? true;
        $service->category_id = $data['category_id'];
    
        $service->save();
    
        return $service;
    }
    
    /**
     * Update a service
     *
     * @param Service $service
     * @param array $data
     * @return Service
     */
    public function updateService(Service $service, array $data): Service
    {
        $service->update($data);
        
        return $service->refresh();
    }

    /**
     * Delete a service
     *
     * @param Service $service
     * @return bool
     */
    public function deleteService(Service $service): bool
    {
        return $service->delete();
    }

    /**
     * Toggle service active status
     *
     * @param Service $service
     * @return Service
     */
    public function toggleServiceStatus(Service $service): Service
    {
        $service->update(['is_active' => !$service->is_active]);
        
        return $service->refresh();
    }

    /**
     * Get service with relationships
     *
     * @param int $serviceId
     * @param int $providerId
     * @return Service|null
     */
    public function getServiceWithRelations(int $serviceId, int $providerId): ?Service
    {
        return Service::with(['category', 'provider'])
            ->where('id', $serviceId)
            ->where('provider_id', $providerId)
            ->first();
    }


    public function getServiceById(int $serviceId, int $providerId): ?Service
    {
        return Service::
            where('id', $serviceId)
            ->with(['category'])    
            ->where('provider_id', $providerId)
            ->first();
    }

    /**
     * Get active services for a provider
     *
     * @param int $providerId
     * @return Collection
     */
    public function getActiveServices(int $providerId): Collection
    {
        return Service::forProvider($providerId)
            ->active()
            ->with(['category'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Check if provider owns the service
     *
     * @param Service $service
     * @param int $providerId
     * @return bool
     */
    public function providerOwnsService(Service $service, int $providerId): bool
    {
        return $service->provider_id === $providerId;
    }

    /**
     * Get services statistics for a provider
     *
     * @param int $providerId
     * @return array
     */
    public function getProviderServicesStats(int $providerId): array
    {
        $totalServices = Service::forProvider($providerId)->count();
        $activeServices = Service::forProvider($providerId)->active()->count();
        $inactiveServices = $totalServices - $activeServices;

        return [
            'total_services' => $totalServices,
            'active_services' => $activeServices,
            'inactive_services' => $inactiveServices,
        ];
    }

    


  
}
