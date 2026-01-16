<?php

namespace App\Repositories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * AddressRepository
 * 
 * Handles all database operations for addresses
 */
class AddressRepository
{
    /**
     * Create a new address
     *
     * @param array $data
     * @return Address
     */
    public function create(array $data): Address
    {
        return Address::create($data);
    }

    /**
     * Find address by ID
     *
     * @param int $id
     * @return Address|null
     */
    public function find(int $id): ?Address
    {
        return Address::find($id);
    }

    /**
     * Find address by ID or fail
     *
     * @param int $id
     * @return Address
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Address
    {
        return Address::findOrFail($id);
    }

    /**
     * Update address
     *
     * @param Address $address
     * @param array $data
     * @return bool
     */
    public function update(Address $address, array $data): bool
    {
        return $address->update($data);
    }

    /**
     * Delete address
     *
     * @param Address $address
     * @return bool|null
     */
    public function delete(Address $address): ?bool
    {
        return $address->delete();
    }

    /**
     * Get all addresses for a user
     *
     * @param User $user
     * @return Collection
     */
    public function getUserAddresses(User $user): Collection
    {
        return $user->addresses()->orderBy('id' , 'DESC')->get();
    }

    /**
     * Get user address by ID
     *
     * @param User $user
     * @param int $addressId
     * @return Address|null
     */
    public function getUserAddress(User $user, int $addressId): ?Address
    {
        return $user->addresses()->find($addressId);
    }

    /**
     * Get default address for user
     *
     * @param User $user
     * @return Address|null
     */
    public function getDefaultAddress(User $user): ?Address
    {
        return $user->addresses()->where('is_default', 1)->first();
    }

    /**
     * Set address as default
     *
     * @param Address $address
     * @return bool
     */
    public function setAsDefault(Address $address): bool
    {
        // Remove default from other addresses
        Address::where('user_id', $address->user_id)
            ->where('id', '!=', $address->id)
            ->update(['is_default' => 0]);

        // Set this address as default
        return $address->update(['is_default' => 1]);
    }

    /**
     * Check if address belongs to user
     *
     * @param Address $address
     * @param User $user
     * @return bool
     */
    public function belongsToUser(Address $address, User $user): bool
    {
        return $address->user_id === $user->id;
    }

    /**
     * Get addresses by city
     *
     * @param int $cityId
     * @return Collection
     */
    public function getByCityId(int $cityId): Collection
    {
        return Address::where('city_id', $cityId)->get();
    }

    /**
     * Get addresses by district
     *
     * @param int $districtId
     * @return Collection
     */
    public function getByDistrictId(int $districtId): Collection
    {
        return Address::where('district_id', $districtId)->get();
    }

    /**
     * Count user addresses
     *
     * @param User $user
     * @return int
     */
    public function countUserAddresses(User $user): int
    {
        return $user->addresses()->count();
    }
}

