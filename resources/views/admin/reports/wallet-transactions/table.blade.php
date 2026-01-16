<div class="position-relative">
    {{-- table loader  --}}
    {{-- <div class="table_loader">
        {{ __('admin.loading') }}
    </div> --}}
    {{-- table loader  --}}

    {{-- table content --}}
    
 <table class="table" id="tab">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ __('admin.user') }}</th>
            <th>{{ __('admin.current_balance') }}</th>
            <th>{{ __('admin.amount') }}</th>
            <th>{{ __('admin.type') }}</th>
            <th>{{ __('admin.status') }}</th>
            <th>{{ __('admin.order_number') }}</th>
            <th>{{ __('admin.created_at') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($walletTransactions as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    @if($transaction->user)
                        <div>
                            <strong>{{ $transaction->user->name }}</strong><br>
                            <small class="text-muted">{{ $transaction->user->phone }}</small>
                        </div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <strong class="{{ $transaction->user && $transaction->user->wallet_balance >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($transaction->user ? $transaction->user->wallet_balance : 0, 2) }} {{ __('admin.sar') }}
                    </strong>
                </td>
                
                <td>
                    <strong class="{{ $transaction->amount >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($transaction->amount, 2) }} {{ __('admin.sar') }}
                    </strong>
                </td>
                <td>
                    <span class="badge badge-info">{{ __('admin.'.$transaction->type) }}</span>
                </td>
                <td>
                    <span class="badge badge-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                        {{ __('admin.'.$transaction->status) }}
                    </span>
                </td>
                <td>
                    <small class="text-muted">{{ $transaction->reference }}</small>
                </td>
                <td>
                    <div>{{ $transaction->created_at->format('d/m/Y') }}</div>
                    <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                </td>
             
            </tr>
        @endforeach
    </tbody>
</table>


    {{-- no data found div --}}
    @if ($walletTransactions->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{ asset('admin/app-assets/images/pages/404.png') }}" alt="">
            <span class="mt-2" style="font-family: cairo">{{ __('admin.there_are_no_matches_matching') }}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>

{{-- pagination links div --}}
@if ($walletTransactions->count() > 0 && $walletTransactions instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{ $walletTransactions->links() }}
    </div>
@endif
{{-- pagination links div --}}
