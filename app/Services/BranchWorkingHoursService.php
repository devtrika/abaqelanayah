<?php

namespace App\Services;

use App\Models\BranchWorkingHour;
use App\Models\BranchDeliveryHour;
use Illuminate\Database\Eloquent\Collection;

class BranchWorkingHoursService
{
    /**
     * Get working hours for a branch
     */
    public function getBranchWorkingHours(int $branchId): Collection
    {
        return BranchWorkingHour::where('branch_id', $branchId)
            ->orderByRaw("FIELD(day, 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')")
            ->get();
    }

    /**
     * Get delivery hours for a branch
     */
    public function getBranchDeliveryHours(int $branchId): Collection
    {
        return BranchDeliveryHour::where('branch_id', $branchId)
            ->orderByRaw("FIELD(day, 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')")
            ->get();
    }

    /**
     * Store or update working hours for a branch
     */
    public function storeWorkingHours(int $branchId, array $data): Collection
    {
        // Get all possible days
        $allDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        // Get days that are being set as working
        $workingDays = [];
        if (isset($data['working_hours'])) {
            foreach ($data['working_hours'] as $workingHourData) {
                $workingDays[] = $workingHourData['day'];

                // Update or create working hours for working days
                BranchWorkingHour::updateOrCreate(
                    [
                        'branch_id' => $branchId,
                        'day' => $workingHourData['day'],
                    ],
                    [
                        'start_time' => $workingHourData['start_time'],
                        'end_time' => $workingHourData['end_time'],
                        'is_working' => $workingHourData['is_working'] ?? true,
                    ]
                );
            }
        }

        // Delete working hours for days that are not working
        $nonWorkingDays = array_diff($allDays, $workingDays);
        if (!empty($nonWorkingDays)) {
            BranchWorkingHour::where('branch_id', $branchId)
                ->whereIn('day', $nonWorkingDays)
                ->delete();
        }

        return $this->getBranchWorkingHours($branchId);
    }

    /**
     * Store or update delivery hours for a branch
     */
    public function storeDeliveryHours(int $branchId, array $data): Collection
    {
        // Get all possible days
        $allDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        // Get days that are being set as working
        $workingDays = [];
        if (isset($data['delivery_hours'])) {
            foreach ($data['delivery_hours'] as $deliveryHourData) {
                $workingDays[] = $deliveryHourData['day'];

                // Update or create delivery hours for working days
                BranchDeliveryHour::updateOrCreate(
                    [
                        'branch_id' => $branchId,
                        'day' => $deliveryHourData['day'],
                    ],
                    [
                        'start_time' => $deliveryHourData['start_time'],
                        'end_time' => $deliveryHourData['end_time'],
                        'is_working' => $deliveryHourData['is_working'] ?? true,
                    ]
                );
            }
        }

        // Delete delivery hours for days that are not working
        $nonWorkingDays = array_diff($allDays, $workingDays);
        if (!empty($nonWorkingDays)) {
            BranchDeliveryHour::where('branch_id', $branchId)
                ->whereIn('day', $nonWorkingDays)
                ->delete();
        }

        return $this->getBranchDeliveryHours($branchId);
    }

    /**
     * Get working hours for a specific day
     */
    public function getWorkingHoursForDay(int $branchId, string $day): ?BranchWorkingHour
    {
        return BranchWorkingHour::where('branch_id', $branchId)
            ->where('day', $day)
            ->first();
    }

    /**
     * Get delivery hours for a specific day
     */
    public function getDeliveryHoursForDay(int $branchId, string $day): ?BranchDeliveryHour
    {
        return BranchDeliveryHour::where('branch_id', $branchId)
            ->where('day', $day)
            ->first();
    }

    /**
     * Check if branch is working on a specific day
     */
    public function isBranchWorkingOnDay(int $branchId, string $day): bool
    {
        $workingHour = $this->getWorkingHoursForDay($branchId, $day);

        return $workingHour && $workingHour->is_working;
    }

    /**
     * Check if branch has delivery on a specific day
     */
    public function isBranchDeliveringOnDay(int $branchId, string $day): bool
    {
        $deliveryHour = $this->getDeliveryHoursForDay($branchId, $day);

        return $deliveryHour && $deliveryHour->is_working;
    }

    /**
     * Get formatted working hours as array
     */
    public function getFormattedWorkingHours(int $branchId): array
    {
        $workingHours = $this->getBranchWorkingHours($branchId);

        return $workingHours->map(function ($workingHour) {
            return [
                'day' => $workingHour->day,
                'start_time' => $workingHour->start_time,
                'end_time' => $workingHour->end_time,
                'is_working' => $workingHour->is_working,
            ];
        })->toArray();
    }

    /**
     * Get formatted delivery hours as array
     */
    public function getFormattedDeliveryHours(int $branchId): array
    {
        $deliveryHours = $this->getBranchDeliveryHours($branchId);

        return $deliveryHours->map(function ($deliveryHour) {
            return [
                'day' => $deliveryHour->day,
                'start_time' => $deliveryHour->start_time,
                'end_time' => $deliveryHour->end_time,
                'is_working' => $deliveryHour->is_working,
            ];
        })->toArray();
    }
}