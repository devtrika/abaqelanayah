<?php

namespace App\Services;

use App\Models\ProviderWorkingHour;
use Illuminate\Database\Eloquent\Collection;

class WorkingHoursService
{
    /**
     * Get working hours for a provider
     */
    public function getProviderWorkingHours(int $providerId): Collection
    {
        return ProviderWorkingHour::where('provider_id', $providerId)
            ->orderByRaw("FIELD(day, 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')")
            ->get();
    }

    /**
     * Store or update working hours for a provider
     */
    public function storeWorkingHours(int $providerId, array $data): Collection
    {
        // Get all possible days
        $allDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        // Get days that are being set as working
        $workingDays = [];
        if (isset($data['working_hours'])) {
            foreach ($data['working_hours'] as $workingHourData) {
                $workingDays[] = $workingHourData['day'];

                // Update or create working hours for working days
                ProviderWorkingHour::updateOrCreate(
                    [
                        'provider_id' => $providerId,
                        'day' => $workingHourData['day'],
                    ],
                    [
                        'start_time' => $workingHourData['start_time'],
                        'end_time' => $workingHourData['end_time'],
                        'is_working' => true,
                    ]
                );
            }
        }

        // Delete working hours for days that are not working
        $nonWorkingDays = array_diff($allDays, $workingDays);
        if (!empty($nonWorkingDays)) {
            ProviderWorkingHour::where('provider_id', $providerId)
                ->whereIn('day', $nonWorkingDays)
                ->delete();
        }

        return $this->getProviderWorkingHours($providerId);
    }

    /**
     * Get working hours for a specific day
     */
    public function getWorkingHoursForDay(int $providerId, string $day): ?ProviderWorkingHour
    {
        return ProviderWorkingHour::where('provider_id', $providerId)
            ->where('day', $day)
            ->first();
    }

    /**
     * Check if provider is working on a specific day
     */
    public function isProviderWorkingOnDay(int $providerId, string $day): bool
    {
        $workingHour = $this->getWorkingHoursForDay($providerId, $day);

        return $workingHour && $workingHour->is_working;
    }

    /**
     * Get formatted working hours as array
     */
    public function getFormattedWorkingHours(int $providerId): array
    {
        $workingHours = $this->getProviderWorkingHours($providerId);

        return $workingHours->map(function ($workingHour) {
            return [
                'day' => $workingHour->day,
                'start_time' => $workingHour->start_time,
                'end_time' => $workingHour->end_time,
                'is_working' => $workingHour->is_working,
            ];
        })->toArray();
    }
}
