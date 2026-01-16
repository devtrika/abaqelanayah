<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\BrandRepository;
use Illuminate\Database\Eloquent\Collection;

class BrandService
{
    /**
     * Create a new CategoryService instance.
     *
     * @param BrandRepository $repository
     */
    public function __construct(protected BrandRepository $repository)
    {
    }
    /**
     * Get all active categories
     *
     * @return Collection
     */
    public function getAllBrands($onboarding = null): Collection
    {
        return $this->repository->getAllActive($onboarding);
    }


    
}