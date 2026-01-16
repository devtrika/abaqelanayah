<?php
namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    /**
     * Get all products with options
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return Product::with('options')->get();
    }

    /**
     * Find a product by ID with options
     *
     * @param int $id
     * @return \App\Models\Product
     */
    public function find($id)
    {
        return Product::with(['category'])->findOrFail($id);
    }
    /**
     * Get filtered products list
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFilteredList(array $filters = [])
    {
        $query = Product::query();

        // final_price is computed as base_price * (100 - discount_percentage) / 100
        $finalPriceExpr = "(base_price * (100 - COALESCE(discount_percentage,0)) / 100)";

        // === FILTERS (WHERE clauses) ===

        if (! empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['parent_category_id'])) {
            $query->where('parent_category_id', $filters['parent_category_id']);
        }

        if (! empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (! empty($filters['min_price'])) {
            $query->whereRaw("{$finalPriceExpr} >= ?", [$filters['min_price']]);
        }

        if (! empty($filters['max_price'])) {
            $query->whereRaw("{$finalPriceExpr} <= ?", [$filters['max_price']]);
        }

        if (! empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Filter: Only products with offers/discounts
        if (! empty($filters['latest_offer']) || ! empty($filters['has_offer'])) {
            $query->where('discount_percentage', '>', 0);
        }

        // === SORTING (ORDER BY clauses) ===

        if (! empty($filters['sort_by'])) {
            $sorts = explode(',', $filters['sort_by']);
            foreach ($sorts as $sort) {
                switch (trim($sort)) {
                    case 'latest':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'oldest':
                        $query->orderBy('created_at', 'asc');
                        break;
                    case 'name':
                        $query->orderBy('name', 'asc');
                        break;
                    case 'price_low':
                        $query->orderByRaw("{$finalPriceExpr} asc");
                        break;
                    case 'price_high':
                        $query->orderByRaw("{$finalPriceExpr} desc");
                        break;
                    case 'latest_offers':
                        // Filter by offers and sort by latest
                        $query->where('discount_percentage', '>', 0);
                        break;
                    case 'most_ordered':
                        $query->whereHas('orderItems')
                            ->withCount('orderItems')
                            ->orderBy('order_items_count', 'desc');
                        break;
                }
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->get();
    }

    /**
     * Get products with active offers/discounts
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOffers()
    {
        return Product::where('is_active', 1)
            ->where('discount_percentage', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get filtered products list with pagination
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFilteredPaginated(array $filters = [], int $perPage = 12)
    {
        $query = Product::query();

        // final_price is computed as base_price * (100 - discount_percentage) / 100
        $finalPriceExpr = "(base_price * (100 - COALESCE(discount_percentage,0)) / 100)";

        // === FILTERS (WHERE clauses) ===

        if (! empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['parent_category_id'])) {
            $query->where('parent_category_id', $filters['parent_category_id']);
        }

        if (! empty($filters['category_ids']) && is_array($filters['category_ids'])) {
            $query->whereIn('category_id', $filters['category_ids']);
        }

        if (! empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (! empty($filters['brand_ids']) && is_array($filters['brand_ids'])) {
            $query->whereIn('brand_id', $filters['brand_ids']);
        }

        if (! empty($filters['min_price'])) {
            $query->whereRaw("{$finalPriceExpr} >= ?", [$filters['min_price']]);
        }

        if (! empty($filters['max_price'])) {
            $query->whereRaw("{$finalPriceExpr} <= ?", [$filters['max_price']]);
        }

        if (! empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Filter: Only products with offers/discounts
        if (! empty($filters['latest_offer']) || ! empty($filters['has_offer'])) {
            $query->where('discount_percentage', '>', 0);
        }

        // === SORTING (ORDER BY clauses) ===

        if (! empty($filters['sort_by'])) {
            $sorts = explode(',', $filters['sort_by']);
            foreach ($sorts as $sort) {
                switch (trim($sort)) {
                    case 'latest':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'oldest':
                        $query->orderBy('created_at', 'asc');
                        break;
                    case 'name':
                        $query->orderBy('name', 'asc');
                        break;
                    case 'price_low':
                        $query->orderByRaw("{$finalPriceExpr} asc");
                        break;
                    case 'price_high':
                        $query->orderByRaw("{$finalPriceExpr} desc");
                        break;
                    case 'latest_offers':
                        // Filter by offers and sort by latest
                        $query->where('discount_percentage', '>', 0)->orderBy('created_at', 'desc');
                        break;
                    case 'most_ordered':
                        $query->whereHas('orderItems')
                            ->withCount('orderItems')
                            ->orderBy('order_items_count', 'desc');
                        break;
                }
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

}
