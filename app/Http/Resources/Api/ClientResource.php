<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    private $token = null;

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function toArray($request)
    {
        $data = [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'country_code' => $this->country_code,
            'image'        => $this->image,
            'city'         => $this->city ? [
                'id'   => $this->city->id,
                'name' => $this->city->name,
            ] : null,
            // 'region'         => $this->region ? [
            //     'id'   => $this->region->id,
            //     'name' => $this->region->name,
            // ] : null,
            'district'         => $this->district ? [
                'id'   => $this->district->id,
                'name' => $this->district->name,
            ] : null,
            'is_notify'    => (bool) $this->is_notify,

            'accept_orders' => (bool) $this->accept_orders,
            // 'type'         => $this->type,
            'status'       => $this->status ?? 'inactive',
            'gender'       => $this->gender,
            'type'       => $this->type,

            // 'wallet_balance' => (float) $this->wallet_balance,
            'created_at'   => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'   => $this->updated_at->format('Y-m-d H:i:s'),
        ];

        // Include branches if relation is loaded (for delivery users)
        if ($this->relationLoaded('branches')) {
            $data['branches'] = $this->branches->map(function($b) {
                // Flattened fields: supervisor_name, branch_name, supervisor_phone
                $supervisorName = null;
                $supervisorPhone = null;
                if ($b->relationLoaded('managers') && $b->managers->count()) {
                    $mgr = $b->managers->first();
                    $supervisorName = $mgr->name ?? null;
                    $supervisorPhone = $mgr->phone ?? null;
                }

                return [
                    'id' => $b->id,
                    'supervisor_name' => $supervisorName,
                    'branch_name' => $b->name,
                    'supervisor_phone' => $supervisorPhone,
                ];
            })->values();
        }

        // Include addresses if they're loaded
        // if ($this->relationLoaded('addresses')) {
        //     $data['addresses'] = $this->addresses->map(function ($address) {
        //         return [
        //             'id' => $address->id,
        //             'full_name' => $address->full_name,
        //             'city' => $address->city ? [
        //                 'id' => $address->city->id,
        //                 'name' => $address->city->name,
        //             ] : null,
        //             'neighborhood' => $address->neighborhood,
        //             'type' => $address->type,
        //             'address' => $address->address,
        //             'building_number' => $address->building_number,
        //             'floor_number' => $address->floor_number,
        //             'latitude' => $address->latitude,
        //             'longitude' => $address->longitude,
        //             'phone' => $address->phone,
        //             'notes' => $address->notes,
        //         ];
        //     });
        // }

        // Include token if it's set
        if ($this->token) {
            $data['token'] = $this->token;
        }

        return $data;
    }
}
