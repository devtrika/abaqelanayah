<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    /**
     * Create a new CategoryRepository instance.
     *
     * @param Category $model
     */
    public function __construct(protected Category $model)
    {
    }

    /**
     * Get all active categories
     *
     * @return Collection
     */
public function getAllActive(): Collection
{
    return $this->model
        ->whereNull('parent_id')  
        ->where('is_active', 1) 
        ->get();
}


    /**
     * Get a category by ID
     *
     * @param int $id
     * @return Category|null
     */
    public function find(int $id): ?Category
    {
        return $this->model->find($id);
    }

    /**
         * Get all child categories with their products, or the parent if no children exist
         *
         * @param int $id
         * @return Collection|null
         */
public function findWithProducts(int $id): ?Category
{
    return $this->model->where('id', $id)
        ->where('is_active', 1)
        ->with([
            'children' => function ($query) {
                $query->where('is_active', 1)
                      ->with([
                          'products' => function ($q) {
                              $q->where('is_active', 1)->latest();
                          },
                          'parent:id,name'
                      ]);
            }
        ])
        ->first();
}


}