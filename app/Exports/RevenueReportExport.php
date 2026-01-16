<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RevenueReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Order::with(['user', 'provider', 'address', 'bankTransfer', 'paymentMethod']);
        // Apply filters if needed
        if (isset($this->filters['from_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['from_date']);
        }
        if (isset($this->filters['to_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['to_date']);
        }
        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            __('admin.serial_number'),
            __('admin.order_number'),
            __('admin.user'),
            __('admin.final_total'),
            __('admin.created_at'),
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->order_number,
            optional($order->user)->name,
            $order->total,
            $order->created_at,
        ];
    }
} 