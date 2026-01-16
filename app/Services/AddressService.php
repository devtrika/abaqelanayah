<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressService
{
    public function __construct(
        private readonly Address $address
    ) {}

    public function index(): Collection
    {
        return $this->address
            ->whereUserId(Auth::id())
            ->latest()
            ->get();
    }

    public function store(array $data): Address
    {
        try {
            DB::beginTransaction();
            
            // If this address is set as default, unset other default addresses
            if (($data['is_default'] ?? 0) == 1) {
                // no exclude id when creating a new address
                $this->unsetDefaultAddresses(null);
            }
                $data['user_id'] = Auth::id();
            $address = $this->address->create($data);
            
            DB::commit();
            return $address;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(string $id): Address|false|null
    {
        $address = $this->address->find($id);
        
        if (!$address) {
            return null;
        }
        
        return $address->user_id === Auth::id() 
            ? $address 
            : false;
    }

    public function update(array $data, string $id): Address|false|null
    {
        try {
            DB::beginTransaction();
            
            $address = $this->address->find($id);
            
            if (!$address) {
                return null;
            }
            
            if ($address->user_id !== Auth::id()) {
                return false;
            }
            // If this address is being set as default, unset other default addresses
            if (($data['is_default'] ?? 0) == 1) {
                // pass current address id to exclude it from being unset
                $this->unsetDefaultAddresses($address->id);
            }
            
            $address->update($data);
            
            DB::commit();
            return $address;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(string $id): bool|null
    {
        try {
            DB::beginTransaction();
            
            $address = $this->address->find($id);
            
            if (!$address) {
                return null;
            }
            
            if ($address->user_id !== Auth::id()) {
                return false;
            }
            
            $address->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function unsetDefaultAddresses(?int $excludeId = null): void
    {
        $query = $this->address
            ->whereUserId(Auth::id())
            ->where('is_default', 1);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        $query->update(['is_default' => 0]);
    }
}