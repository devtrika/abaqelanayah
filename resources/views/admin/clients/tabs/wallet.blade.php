<div class="tab-pane fade" id="wallet">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ __('admin.wallet') }}</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <strong>{{ __('admin.wallet_balance') }}:</strong>
                        {{ $row->wallet_balance ?? 0 }}
                    </div>
                </div>
            </div>
            <h6>{{ __('admin.wallet_history') }}</h6>
            <div class="table-responsive">
                <div class="contain-table text-center">
                    <table class="table datatable-button-init-basic text-center table-hover">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th class="text-center">#</th>
                                <th class="text-center">{{ __('admin.opertaion') }}</th>
                                <th class="text-center">{{ __('admin.the_amount') }}</th>
                                <th class="text-center">{{ __('admin.date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach($row->walletTransactions as $key => $transaction)
                                <tr class="text-center">
                                    <td class="text-center align-middle">{{ $key + 1 }}</td>
                                    <td class="text-center align-middle">{{ __('admin.' . $transaction->type) }}</td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-success">{{ $transaction->amount }}</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 