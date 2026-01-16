<?php
namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LoyaltyPointsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::select('name', 'phone', 'loyalty_points')->get();
    }

    public function headings(): array
    {
        return [
            __('admin.name'),
            __('admin.phone'),
            __('admin.points'),
            __('admin.value'),
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->phone,
            $user->loyality_points,
            $user->loyalty_points_value,
        ];
    }
}
