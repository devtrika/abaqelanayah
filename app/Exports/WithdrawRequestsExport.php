<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WithdrawRequestsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $withdrawRequests;

    public function __construct($withdrawRequests)
    {
        $this->withdrawRequests = $withdrawRequests;
    }

    public function collection()
    {
        return $this->withdrawRequests;
    }

    public function headings(): array
    {
        return [
            '#',
            'Order Number',
            'Provider',
            'Phone',
            'Bank Account',
            'Amount',
            'Status',
            'Created At',
        ];
    }

    public function map($withdrawRequest): array
    {
        return [
            $withdrawRequest->id,
            $withdrawRequest->number,
            optional($withdrawRequest->provider)->commercial_name,
            optional(optional($withdrawRequest->provider)->user)->phone,
            optional($withdrawRequest->provider->bankAccount)->bank_name . ' / ' .
                optional($withdrawRequest->provider->bankAccount)->account_number . ' / ' .
                optional($withdrawRequest->provider->bankAccount)->iban,
            $withdrawRequest->amount,
            $withdrawRequest->status,
            $withdrawRequest->created_at,
        ];
    }
} 