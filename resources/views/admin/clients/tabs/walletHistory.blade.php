<div class="tab-pane fade" id="walletHistory">
    @if($row->transactions->count() > 0)
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('admin.wallet_history') }}</h5>
                <div class="header-elements">
                    <span class="badge badge-primary">
                        {{ $row->transactions->count() }} {{ __('admin.transactions') }}
                    </span>
                </div>
            </div>
            <div class="card-body">
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
                                @foreach($row->transactions as $key => $history)
                                    <tr class="text-center">
                                        <td class="text-center align-middle">{{ $key + 1 }}</td>
                                        <td class="text-center align-middle">{{ $history->message }}</td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-success">{{ $history->amount }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ \Carbon\Carbon::parse($history->created_at)->format('Y-m-d') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <div class="empty-state">
                <img src="{{ asset('admin/app-assets/images/pages/404.png') }}"
                     alt="{{ __('admin.no_transactions_found') }}"
                     class="img-fluid mb-3"
                     style="max-width: 200px;">
                <h5 class="text-muted mb-2">{{ __('admin.no_transactions_found') }}</h5>
                <p class="text-muted" style="font-family: cairo">
                    {{ __('admin.there_are_no_matches_matching') }}
                </p>
            </div>
        </div>
    @endif
</div>