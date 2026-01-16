<div class="tab-pane fade" id="payments">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ __('admin.payments') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <div class="contain-table">
                    <table id="paymentsTable" class="table datatable-button-init-basic table-hover text-center align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center align-middle">{{ __('admin.transaction_id') }}</th>
                                <th class="text-center align-middle">{{ __('admin.amount') }}</th>
                                <th class="text-center align-middle">{{ __('admin.transaction_type') }}</th>
                                <th class="text-center align-middle">{{ __('admin.transaction_status') }}</th>
                                <th class="text-center align-middle">{{ __('admin.date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($row->payments as $transaction)
                                <tr>
                                    <td class="text-center align-middle">{{ $transaction->transaction_id ?? 'N/A' }}</td>
                                    <td class="text-center align-middle {{ ($transaction->amount ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ ($transaction->amount ?? 0) >= 0 ? '+' : '' }}{{ number_format($transaction->amount ?? 0, 2) }}
                                    </td>
                                    <td class="text-center align-middle">{{ __('admin.' . ($transaction->transaction_type ?? 'unknown')) }}</td>
                                    <td class="text-center align-middle">{{ __('admin.' . ($transaction->transaction_status ?? 'unknown')) }}</td>
                                    <td class="text-center align-middle">{{ $transaction->created_at ? $transaction->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
