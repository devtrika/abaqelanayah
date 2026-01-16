<?php

namespace App\Http\Controllers\Api\Client;

use App\Facades\Responder;
use Illuminate\Http\Request;
use App\Services\ProviderService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Client\ProviderResource;
use App\Http\Resources\Api\Client\ProviderDetailsResource;
use App\Services\ViewService;
use Carbon\Carbon;
use App\Services\WorkingHoursService;
use App\Services\LocationBasedProviderService;

class ProviderController extends Controller
{
    public function __construct(
        protected ProviderService $providerService,
        protected WorkingHoursService $workingHoursService,
        protected ViewService $viewService,
        protected LocationBasedProviderService $locationBasedProviderService
    ) {
    }


    public function getProviderWithCity($cityId, Request $request)
    {
        try {
            $user = auth()->user();

            // Always use location-based filtering as the main logic
            $data = $this->locationBasedProviderService->getProvidersByCityWithinRadius($cityId, $user);

            return Responder::success(ProviderResource::collection($data));
        } catch (\Exception $e) {
            return Responder::error('Failed to retrieve providers by city', ['error' => $e->getMessage()], 500);
        }
    }



    /**
     * Get providers based on user's gender preference and order history
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProvidersForUser(Request $request)
    {
        try {
            $user = auth()->user();

            // Always use location-based filtering as the main logic
            $providers = $this->locationBasedProviderService->getProvidersWithinUserRadius($user);

            return Responder::success(ProviderResource::collection($providers));
        } catch (\Exception $e) {
            return Responder::error('Failed to retrieve providers for user', ['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Get most ordered providers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMostOrderedProviders(Request $request)
    {
        try {
            $user = auth()->user();
            $limit = $request->input('limit', 10);

            // Always use location-based filtering as the main logic
            $providers = $this->locationBasedProviderService->getMostOrderedProvidersWithinRadius($user, $limit);

            return Responder::success(ProviderResource::collection($providers));

        } catch (\Exception $e) {
            return Responder::error('Failed to retrieve most ordered providers', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Filter and search providers with advanced options
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterProviders(Request $request)
    {
        try {
            $filters = [
                'search' => $request->input('search'), // Search in commercial_name or service names
                'gender' => $request->input('gender'), // male, female
                'provider_type' => $request->input('provider_type'), // salon, beauty_center
                'sort_by' => $request->input('sort_by', 'latest'), // latest, name_asc, name_desc, rates_desc, rates_asc
                'city_id' => $request->input('city_id'),
                'service_type' => $request->input('service_type'), // home, salon
                'category_id' => $request->input('category_id'), // filter by service category
                'limit' => $request->input('limit', 15),
                'page' => $request->input('page', 1)
            ];

            $user = auth()->user();

            // Always use location-based filtering as the main logic
            $providers = $this->locationBasedProviderService->filterProvidersWithLocation($filters, $user);

            return Responder::success(ProviderResource::collection($providers));

        } catch (\Exception $e) {
            return Responder::error('Failed to filter providers', ['error' => $e->getMessage()], 500);
        }
    }

    

public function show(int $id)
{
    $provider = $this->providerService->getById($id);

    if (!$provider) {
        return Responder::error('not_found', [], 404);
    }
    $this->viewService->store($provider->id);
    $provider->load(['activeServices', 'activeProducts', 'WorkingHours', 'rates']);

    return Responder::success(ProviderDetailsResource::make($provider));
}

public function getProviderWorkingHours(int $id)
{
    $provider = $this->providerService->getById($id);

    if (!$provider) {
        return Responder::error('not_found', [], 404);
    }

    $dateParam = request('date');
    $startDate = $dateParam ? Carbon::parse($dateParam) : Carbon::today();

    // Get all working hours for the provider, indexed by day
    $workingHours = $this->workingHoursService->getProviderWorkingHours($id)->keyBy(function($item) {
        return strtolower($item->day);
    });

    $result = [];
    for ($i = 0; $i < 7; $i++) {
        $date = $startDate->copy()->addDays($i);
        $dayName = strtolower($date->format('l'));

        // Check if this day exists in provider's working hours AND is a working day
        $workingHour = $workingHours->where('day', $dayName)->where('is_working', true)->first();

        // Skip this date if the day doesn't exist in working hours OR is not a working day
        if (!$workingHour) {
            continue;
        }

        $slots = [];
        if ($workingHour->start_time && $workingHour->end_time) {
            $slotStart = Carbon::createFromFormat('H:i:s', $workingHour->start_time);
            $slotEnd = Carbon::createFromFormat('H:i:s', $workingHour->end_time);
            while ($slotStart->lt($slotEnd)) {
                $nextSlot = $slotStart->copy()->addHour();
                if ($nextSlot->gt($slotEnd)) {
                    break;
                }
                $slots[] = $slotStart->format('H:i') . ' - ' . $nextSlot->format('H:i');
                $slotStart = $nextSlot;
            }
        }
        $result[] = [
            'date' => $date->toDateString(),
            'day' => ucfirst($dayName),
            'start_time' => $workingHour->start_time,
            'end_time' => $workingHour->end_time,
            'is_working' => true, // Always true since we filtered for is_working = true
            'slots' => $slots,
        ];
    }

    return Responder::success($result);
}


}