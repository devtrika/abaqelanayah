<?php

namespace App\Repositories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;

class BrandRepository
{
    /**
     * Create a new BrandRepository instance.
     *
     * @param Brand $model
     */
    public function __construct(protected Brand $model)
    {
    }

    /**
     * Get all active categories
     *
     * @return Collection
     */
// public function getAllActive(): Collection
// {
//     return $this->model
//         ->where('is_active', true) 
//         ->get();
// }
public function getAllActive($onboarding = null): Collection
{
    $query = $this->model->where('is_active', true);

    if (!is_null($onboarding)) {
        $query->where('onboarding', (bool) $onboarding);
    }

    return $query->get();
}



    /**
     * Get a Brand by ID
     *
     * @param int $id
     * @return Brand|null
     */
    public function find(int $id): ?Brand
    {
        return $this->model->find($id);
    }


   
}