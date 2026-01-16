<?php
namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService
{
    /**
     * Create a new ProductService instance.
     *
     * @param ProductRepository $repo
     */
    public function __construct(protected ProductRepository $repo) {}

    /**
     * Get a filtered list of products
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function list(array $filters = [])
    {
        return $this->repo->getFilteredList($filters);
    }

    /**
     * Get a product by ID
     *
     * @param int $id
     * @return \App\Models\Product
     */
    public function get($id)
    {
        return $this->repo->find($id);
    }

    public function latestOffers()
    {
        return $this->repo->getOffers();
    }


    /**
     * Get a filtered, paginated list of products
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listPaginated(array $filters = [], int $perPage = 12)
    {
        return $this->repo->getFilteredPaginated($filters, $perPage);
    }

}