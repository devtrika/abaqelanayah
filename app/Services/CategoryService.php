<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ProductCategory;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Create a new CategoryService instance.
     *
     * @param CategoryRepository $repository
     */
    public function __construct(protected CategoryRepository $repository)
    {
    }
    /**
     * Get all active categories
     *
     * @return Collection
     */
    public function getAllCategories(): Collection
    {
        return $this->repository->getAllActive();
    }

    /**
     * Get a category with its products
     *
     * @param int $id
     * @return Category|null
     */
 public function getCategoryWithProducts(int $id): ?Category
{
    return $this->repository->findWithProducts($id);
}

    
}