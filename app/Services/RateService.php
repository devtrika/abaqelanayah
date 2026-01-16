<?php
namespace App\Services;

use App\Models\Rate;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RateService
{
    public function __construct(protected Rate $model)
    {
    }

    /**
     * Store a new rating for any rateable model (Provider, Product, etc.)
     *
     * @param User $user
     * @param Model $rateable
     * @param array $data
     * @return Rate
     * @throws \Exception
     */
    public function store(array $data): Rate
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();

            // Get the rateable model
            $modelClass = match ($data['rateable_type']) {
                'provider' => 'App\\Models\\Provider',
                'product' => 'App\\Models\\Product',
                'order' => 'App\\Models\\Order',
            };

            $rateable = $modelClass::findOrFail($data['rateable_id']);

            // Check if user has already rated this item
            $existingRating = $this->model->where('user_id', $user->id)
                ->where('rateable_type', get_class($rateable))
                ->where('rateable_id', $rateable->id)
                ->first();

            if ($existingRating) {
                throw new \Exception(__('apis.item_already_rated'));
            }

            // Create the rating
            $rating = $this->model->create([
                'user_id'       => $user->id,
                'rateable_type' => get_class($rateable),
                'rateable_id'   => $rateable->id,
                'rate'          => $data['rate'],
                'body'          => $data['body'] ?? '',
            ]);

            // Handle image/video uploads if provided
            if (isset($data['media']) && is_array($data['media'])) {
                foreach ($data['media'] as $image) {
                    $rating->addMedia($image)
                        ->toMediaCollection('rate-media');
                }
            }

            DB::commit();

            return $rating->load(['user', 'rateable']);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get ratings for a specific rateable item
     *
     * @param Model $rateable
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getRatingsForItem(Model $rateable, int $perPage = 10)
    {
        return $this->model->where('rateable_type', get_class($rateable))
            ->where('rateable_id', $rateable->id)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get ratings by type (Provider, Product, etc.)
     *
     * @param string $rateableType
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getRatingsByType(string $rateableType, int $perPage = 10)
    {
        return $this->model->where('rateable_type', $rateableType)
            ->with(['user', 'rateable'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get rating by ID
     *
     * @param int $id
     * @return Rate|null
     */
    public function getRatingById(int $id): ?Rate
    {
        return $this->model->with(['user', 'rateable'])->find($id);
    }

    /**
     * Update an existing rating
     *
     * @param Rate $rating
     * @param array $data
     * @return Rate
     * @throws \Exception
     */
    public function update(Rate $rating, array $data): Rate
    {
        try {
            DB::beginTransaction();

            $rating->update([
                'rate' => $data['rate'] ?? $rating->rate,
                'body' => $data['body'] ?? $rating->body,
            ]);

            // Handle new media uploads
            if (isset($data['images']) && is_array($data['images'])) {
                // Clear existing media if new ones are provided
                $rating->clearMediaCollection('rate-media');

                foreach ($data['images'] as $image) {
                    $rating->addMedia($image)
                        ->toMediaCollection('rate-media');
                }
            }

            DB::commit();

            return $rating->load(['user', 'rateable']);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a rating
     *
     * @param Rate $rating
     * @return bool
     * @throws \Exception
     */
    public function delete(Rate $rating): bool
    {
        try {
            DB::beginTransaction();

            // Clear media files
            $rating->clearMediaCollection('rate-media');

            // Delete the rating
            $deleted = $rating->delete();

            DB::commit();

            return $deleted;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get average rating for a specific rateable item
     *
     * @param Model $rateable
     * @return float
     */
    public function getAverageRating(Model $rateable): float
    {
        return $this->model->where('rateable_type', get_class($rateable))
            ->where('rateable_id', $rateable->id)
            ->avg('rate') ?? 0;
    }

    /**
     * Get rating statistics for a specific rateable item
     *
     * @param Model $rateable
     * @return array
     */
    public function getRatingStats(Model $rateable): array
    {
        $ratings = $this->model->where('rateable_type', get_class($rateable))
            ->where('rateable_id', $rateable->id)
            ->pluck('rate');

        if ($ratings->isEmpty()) {
            return [
                'average'      => 0,
                'count'        => 0,
                'distribution' => [
                    '5' => 0,
                    '4' => 0,
                    '3' => 0,
                    '2' => 0,
                    '1' => 0,
                ],
            ];
        }

        return [
            'average'      => round($ratings->avg(), 1),
            'count'        => $ratings->count(),
            'distribution' => [
                '5' => $ratings->where('rate', 5)->count(),
                '4' => $ratings->where('rate', 4)->count(),
                '3' => $ratings->where('rate', 3)->count(),
                '2' => $ratings->where('rate', 2)->count(),
                '1' => $ratings->where('rate', 1)->count(),
            ],
        ];
    }
}
