<?php

namespace App\Services;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class BusinessRegistrationService
{
    public function register(array $data, User $user): bool
    {
        try {
            if (
                Provider::where('commercial_register_no', $data['tax_id'])->exists()
                || Provider::where('user_id', $user->id)->exists()
            ) {
                Log::warning('Business registration rejected', [
                    'user_id' => $user->id,
                    'tax_id_hash' => hash('sha256', $data['tax_id']),
                    'reason' => 'duplicate_tax_or_user_provider',
                ]);
                return false;
            }

            Provider::create([
                'user_id' => $user->id,
                'commercial_name' => $data['name'],
                'commercial_register_no' => $data['tax_id'],
                'map_desc' => $data['address'],
                'description' => $data['description'] ?? null,
                'is_active' => false,
                'accept_orders' => false,
            ]);

            Log::info('Business registration successful', [
                'user_id' => $user->id,
                'tax_id_hash' => hash('sha256', $data['tax_id']),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Business registration error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

