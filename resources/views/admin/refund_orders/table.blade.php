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
            <th>{{ __('admin.refund_number') }}</th>
            <th>{{ __('admin.order_number') }}</th>
            <th>{{ __('admin.customer_name') }}</th>
            <th>{{ __('admin.refund_amount') }}</th>
            <th>{{ __('admin.status') }}</th>
            <th>{{ __('admin.request_date') }}</th>
            <th>{{ __('admin.control') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($refundOrders as $order)
            <tr>
                <td>
                    @if($order->refund_number)
                        <span class="badge badge-info">{{ $order->refund_number }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.orders.show', $order->id) }}"
                       class="badge badge-primary" target="_blank">
                        {{ $order->order_number ?? $order->order_num }}
                    </a>
                </td>
                <td>
                    @if($order->user)
                        <div>
                            <strong>{{ $order->user->name }}</strong><br>
                            <small class="text-muted">{{ $order->user->phone }}</small>
                        </div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if($order->refund_amount)
                        <strong>{{ number_format($order->refund_amount, 2) }} {{ __('admin.sar') }}</strong>
                    @else
                        <span class="text-muted">{{ __('admin.not_set') }}</span>
                    @endif
                </td>
                <td>
                    @php
                        $statuscolors = [
                            'request_refund' => 'warning',
                            'new' => 'info',
                            'processing' => 'primary',
                            'out-for-delivery' => 'info',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            'refunded' => 'success',
                            'request_rejected' => 'danger',
                        ];
                        $displayStatus = $order->refund_status ?? $order->status;
                        $color = $statuscolors[$displayStatus] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $color }}">
                        {{ __('admin.' . $displayStatus) }}
                    </span>
                </td>
                <td>
                    @if($order->refund_requested_at)
                        <div>{{ $order->refund_requested_at->format('d/m/y') }}</div>
                        <small class="text-muted">{{ $order->refund_requested_at->format('h:i') }}</small>
                    @else
                        <div>{{ $order->created_at->format('d/m/y') }}</div>
                        <small class="text-muted">{{ $order->created_at->format('h:i') }}</small>
                    @endif
                </td>
                <td class="order-action">
                    <a href="{{ route('admin.refund_orders.show', ['id' => $order->id]) }}"
                       class="btn btn-warning btn-sm" title="{{ __('admin.view_details') }}">
                        <i class="feather icon-eye"></i> {{ __('admin.view') }}
                    </a>

                    @if(($order->refund_status ?? $order->status) === 'request_refund')
                        <button type="button" class="btn btn-success btn-sm accept-refund-btn"
                                data-id="{{ $order->id }}"
                                data-order-total="{{ $order->refund_amount ?? $order->total }}"
                                title="{{ __('admin.accept') }}">
                            <i class="feather icon-check"></i>
                        </button>

                        <button type="button" class="btn btn-danger btn-sm refuse-refund-btn"
                                data-id="{{ $order->id }}"
                                title="{{ __('admin.refuse') }}">
                            <i class="feather icon-x"></i>
                        </button>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


    {{-- no data found div --}}
    @if ($refundOrders->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{ asset('admin/app-assets/images/pages/404.png') }}" alt="">
            <span class="mt-2" style="font-family: cairo">{{ __('admin.there_are_no_matches_matching') }}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>

{{-- pagination links div --}}
@if ($refundOrders->count() > 0 && $refundOrders instanceof \illuminate\pagination\abstractpaginator )
    <div class="d-flex justify-content-center mt-3">
        {{ $refundOrders->links() }}
    </div>
@endif
{{-- pagination links div --}}
