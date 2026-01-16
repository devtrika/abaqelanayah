<div class="tab-pane fade" id="orders">
    @if($row->orders->count() > 0)
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('admin.orders') }}</h5>
                <div class="header-elements">
                    <span class="badge badge-primary">
                        {{ $row->orders->count() }} {{ __('admin.orders') }}
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
                                    <th class="text-center">{{__('admin.order_number')}}</th>
                                    <th class="text-center">{{__('admin.total')}}</th>
                                    <th class="text-center">{{__('admin.payment_method')}}</th>
                                    <th class="text-center">{{__('admin.payment_status')}}</th>
                                    <th class="text-center">{{__('admin.order_status')}}</th>
                                    <th class="text-center">{{__('admin.created_at')}}</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @forelse($row->orders as $key => $order)
                                    <tr class="delete_row text-center">
                                        <td class="text-center align-middle">{{ $key + 1 }}</td>

                                        <td class="text-center align-middle">
                                            <a href="{{ route('admin.orders.show', $order->id) }}">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>

                                        <td class="text-center align-middle">{{ number_format($order->total, 2) }} {{ __('admin.sar') }}</td>
                                        <td class="text-center align-middle">
                                            @if($order->paymentMethod)
                                                <span class="badge badge-secondary">{{ $order->paymentMethod->name }}</span>
                                            @else
                                                <span class="badge badge-light">{{ __('admin.unknown') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            @switch($order->payment_status)
                                                @case('paid')
                                                    <span class="badge badge-success">{{ __('admin.paid') }}</span>
                                                    @break
                                                @case('pending')
                                                    <span class="badge badge-warning">{{ __('admin.pending') }}</span>
                                                    @break
                                                @case('failed')
                                                    <span class="badge badge-danger">{{ __('admin.failed') }}</span>
                                                    @break
                                                @case('refunded')
                                                    <span class="badge badge-info">{{ __('admin.refunded') }}</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ __('admin.'.$order->payment_status) }}</span>
                                            @endswitch
                                        </td>
                                        <td class="text-center align-middle">
    @switch($order->status)
        @case('pending')
            <span class="badge badge-warning">{{ __('admin.pending') }}</span>
            @break
        @case('processing')
            <span class="badge badge-info">{{ __('admin.processing') }}</span>
            @break
        @case('ready')
            <span class="badge badge-primary">{{ __('admin.ready') }}</span>
            @break
        @case('delivering')
            <span class="badge badge-info">{{ __('admin.delivering') }}</span>
            @break
        @case('waiting_pickup')
            <span class="badge badge-secondary">{{ __('admin.waiting_pickup') }}</span>
            @break
        @case('completed')
            <span class="badge badge-success">{{ __('admin.completed') }}</span>
            @break
        @case('problem')
            <span class="badge badge-danger">{{ __('admin.problem') }}</span>
            @break
        @case('cancelled')
            <span class="badge badge-danger">{{ __('admin.cancelled') }}</span>
            @break
        @case('request_cancel')
            <span class="badge badge-warning">{{ __('admin.request_cancel') }}</span>
            @break
        @default
            <span class="badge badge-secondary">{{ __('admin.'.$order->status) }}</span>
    @endswitch
</td>

                                        <td class="text-center align-middle">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                @endforelse
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
                     alt="{{ __('admin.no_orders_found') }}"
                     class="img-fluid mb-3"
                     style="max-width: 200px;">
                <h5 class="text-muted mb-2">{{ __('admin.no_orders_found') }}</h5>
                <p class="text-muted" style="font-family: cairo">
                    {{ __('admin.there_are_no_matches_matching') }}
                </p>
            </div>
        </div>
    @endif
</div>
