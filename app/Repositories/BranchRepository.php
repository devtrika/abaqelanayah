<?php

namespace App\Repositories;

use App\Models\Branch;
use App\Models\BranchProduct;
use Illuminate\Database\Eloquent\Collection;

/**
 * BranchRepository
 * 
 * Handles all database operations for branches and branch products
 */
class BranchRepository
{
    /**
     * Find branch by ID
     *
     * @param int $id
     * @return Branch|null
     */
    public function find(int $id): ?Branch
    {
        return Branch::find($id);
    }

    /**
     * Find branch by ID or fail
     *
     * @param int $id
     * @return Branch
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Branch
    {
        return Branch::findOrFail($id);
    }

    /**
     * Get all active branches
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return Branch::where('status', 1)->get();
    }

    /**
     * Get all branches
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Branch::all();
    }

    /**
     * Get branch product stock
     *
     * @param int $branchId
     * @param int $productId
     * @return BranchProduct|null
     */
    public function getBranchProduct(int $branchId, int $productId): ?BranchProduct
    {
        return BranchProduct::where('branch_id', $branchId)
            ->where('product_id', $productId)
            ->first();
    }

    /**
     * Get branch product quantity
     *
     * @param int $branchId
     * @param int $productId
     * @return int
     */
    public function getBranchProductQuantity(int $branchId, int $productId): int
    {
        $branchProduct = $this->getBranchProduct($branchId, $productId);
        return $branchProduct ? $branchProduct->qty : 0;
    }

    /**
     * Decrement branch product quantity
     *
     * @param int $branchId
     * @param int $productId
     * @param int $quantity
     * @return int
     */
    public function decrementProductQuantity(int $branchId, int $productId, int $quantity): int
    {
        $branchProduct = $this->getBranchProduct($branchId, $productId);
        
        if ($branchProduct) {
            return $branchProduct->decrement('qty', $quantity);
        }

        return 0;
    }

    /**
     * Increment branch product quantity
     *
     * @param int $branchId
     * @param int $productId
     * @param int $quantity
     * @return int
     */
    public function incrementProductQuantity(int $branchId, int $productId, int $quantity): int
    {
        $branchProduct = $this->getBranchProduct($branchId, $productId);
        
        if ($branchProduct) {
            return $branchProduct->increment('qty', $quantity);
        }

        return 0;
    }

    /**
     * Check if product is available in branch
     *
     * @param int $branchId
     * @param int $productId
     * @param int $requiredQuantity
     * @return bool
     */
    public function hasProductStock(int $branchId, int $productId, int $requiredQuantity): bool
    {
        $available = $this->getBranchProductQuantity($branchId, $productId);
        return $available >= $requiredQuantity;
    }

    /**
     * Get branches by city
     *
     * @param int $cityId
     * @return Collection
     */
    public function getByCityId(int $cityId): Collection
    {
        return Branch::where('city_id', $cityId)
            ->where('status', 1)
            ->get();
    }

    /**
     * Get branches by region
     *
     * @param int $regionId
     * @return Collection
     */
    public function getByRegionId(int $regionId): Collection
    {
        return Branch::where('region_id', $regionId)
            ->where('status', 1)
            ->get();
    }

    /**
     * Update branch
     *
     * @param Branch $branch
     * @param array $data
     * @return bool
     */
    public function update(Branch $branch, array $data): bool
    {
        return $branch->update($data);
    }
}

