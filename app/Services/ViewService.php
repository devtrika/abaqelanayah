<?php

namespace App\Services;

use App\Models\View;
use Illuminate\Support\Facades\Auth;

class ViewService
{
    public function __construct(protected View $view){

    }

    public function store($provider_id)
    {
        $user_id = auth()->id();
        
        // Check if user is authenticated
        if (!$user_id) {
            return false;
        }
        
        // Check if this user has already viewed this provider
        $existingView = $this->view->where('user_id', $user_id)
                                  ->where('provider_id', $provider_id)
                                  ->first();
        
        if ($existingView) {
            // User has already viewed this provider, don't create new record
            return $existingView;
        }
        
        // Create new view record only if no existing view
        return $this->view->create([
            'user_id' => $user_id,
            'provider_id' => $provider_id
        ]);
    }

    /**
     * Get total views for a provider
     *
     * @param int $provider_id
     * @return int
     */
    public function getTotalViews($provider_id)
    {
        return $this->view->where('provider_id', $provider_id)->sum('views');
    }

    /**
     * Get today's views for a provider
     *
     * @param int $provider_id
     * @return int
     */
    public function getTodayViews($provider_id)
    {
        return $this->view->where('provider_id', $provider_id)
                         ->whereDate('created_at', today())
                         ->sum('views');
    }
}