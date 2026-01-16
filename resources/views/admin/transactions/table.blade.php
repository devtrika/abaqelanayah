<div class="position-relative">

    {{-- table content --}}
    <table class="table" id="transactionsTable">
        <thead>
            <tr>
                <th>
                    <label class="container-checkbox">
                        <input type="checkbox" id="checkedAll">
                        <span class="checkmark"></span>
                    </label>
                </th>
                <th>{{ __('admin.created_at') }}</th>
                <th>{{ __('admin.transaction_id') }}</th>
                <th>{{ __('admin.user_name') }}</th>
                <th>{{ __('admin.amount') }}</th>
                <th>{{ __('admin.transaction_type') }}</th>
                <th>{{ __('admin.transfer_reference') }}</th>
                {{-- <th>{{ __('admin.payment_method') }}</th> --}}
                <th>{{ __('admin.status') }}</th>
                <th>{{ __('admin.control') }}</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($transactions as $transaction)
                <tr class="delete_row">
                    <td class="text-center">
                        <label class="container-checkbox">
                            <input type="checkbox" class="checkSingle" id="{{ $transaction->id }}">
                            <span class="checkmark"></span>
                        </label>
                    </td>

                    <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>#{{ $transaction->id }}</td>

                    <td>
                        @if($transaction->user)
                            {{ $transaction->user->name }}
                            <br>
                            <small class="text-muted">{{ $transaction->user->email }}</small>
                        @else
                            <span class="text-muted">{{ __('admin.no_user') }}</span>
                        @endif
                    </td>

                    <td>{{ number_format($transaction->amount, 2) }} {{ __('admin.sar') }}</td>

                        @php
                            $typeKey = $transaction->type ?? null;
                            $typeLabel = $typeKey ? (Lang::has('admin.' . $typeKey) ? __('admin.' . $typeKey) : ucfirst(str_replace(['-','_'], ' ', $typeKey))) : '-';
                            $note = $transaction->note ?? null;
                            $locale = app()->getLocale();
                            $noteText = '';
                            if (is_array($note)) {
                                $noteText = $note[$locale] ?? ($note['ar'] ?? ($note['en'] ?? ''));
                            } elseif (is_string($note)) {
                                $decoded = json_decode($note, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $noteText = $decoded[$locale] ?? ($decoded['ar'] ?? ($decoded['en'] ?? ''));
                                } else {
                                    $noteText = $note;
                                }
                            }
                            $isRecharge = \Illuminate\Support\Str::contains($noteText, ['شحن', 'Recharge']);
                            $isRefundDeposit = ($typeKey === 'wallet-addons') && !$isRecharge;
                            $displayStatus = $transaction->status;
                            if ($typeKey === 'wallet-payment') {
                                $displayStatus = 'accepted';
                            } elseif ($isRefundDeposit) {
                                $displayStatus = 'accepted';
                            }
                            $transferRefVal = ($typeKey === 'wallet-payment') ? '-' : ($transaction->transfer_reference ?? $transaction->reference ?? '-');
                        @endphp
                        <td>{{ $typeLabel }}</td>
                        <td>{{ $transferRefVal }}</td>

                    {{-- <td>
                        @if($transaction->payment_method)
                            {{ ucfirst($transaction->payment_method) }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td> --}}

                    <td>
                        @php $s = isset($displayStatus) ? $displayStatus : ($transaction->status ?? null); @endphp
                        @if($s == 'accepted' ||  $s == 'approved')
                            <span class="badge bg-success">{{ __('admin.accepted') }}</span>
                        @elseif($s == 'rejected')
                            <span class="badge bg-danger">{{ __('admin.rejected') }}</span>
                        @else
                            <span class="badge bg-warning">{{ __('admin.pending') }}</span>
                        @endif
                    </td>

                    <td class="product-action">

    {{-- زر عرض التفاصيل --}}
    <span class="action-view text-info">
        <a href="{{ route('admin.transactions.show', ['id' => $transaction->id]) }}"
           class="btn btn-info btn-sm p-1"
           title="{{ __('admin.view') }}">
            <i class="feather icon-eye"></i>
        </a>
    </span>

    {{-- Confirm / Reject for pending withdraws --}}
    @if($transaction->status == 'pending')
        <span class="action-accept">
            <button class="btn btn-success btn-sm p-1 accept-transaction"
                    data-url="{{ route('admin.transactions.accept', ['id' => $transaction->id]) }}"
                    title="{{ __('admin.confirm') }}">
                <i class="feather icon-check"></i>
            </button>
        </span>

        <span class="action-reject">
            <button class="btn btn-danger btn-sm p-1 reject-transaction"
                    data-url="{{ route('admin.transactions.reject', ['id' => $transaction->id]) }}"
                    title="{{ __('admin.reject') }}">
                <i class="feather icon-x"></i>
            </button>
        </span>
    @endif

</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- no data found --}}
    @if ($transactions->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{ asset('admin/app-assets/images/pages/404.png') }}" alt="">
            <span class="mt-2" style="font-family: cairo">{{ __('admin.no_transactions_found') }}</span>
        </div>
    @endif
</div>

{{-- pagination --}}
@if ($transactions->count() > 0 && $transactions instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="d-flex justify-content-center mt-3">
        {{ $transactions->links() }}
    </div>
@endif
