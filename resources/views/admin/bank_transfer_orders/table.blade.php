<div class="position-relative">
   
 <table class="table" id="tab">
    <thead>
        <tr>
            <th>{{ __('admin.order_number') }}</th>
            <th>{{ __('admin.user') }}</th>
            <th>{{ __('admin.payment_status') }}</th>
            <th>{{ __('admin.status') }}</th>
            <th>{{ __('admin.final_total') }}</th>
            <th>{{ __('admin.created_at') }}</th>
            <th>{{ __('admin.control') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>
                    <span class="badge badge-info">{{ $order->order_number ?? $order->order_num }}</span>
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
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'paid' => 'success',
                            'failed' => 'danger',
                            'pending_verification' => 'info',
                            'refunded' => 'secondary'
                        ];
                        $color = $statusColors[$order->payment_status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $color }}">
                        {{ ucfirst(str_replace('_', ' ', __('admin.' . $order->payment_status))) }}
                    </span>
                </td>
                <td>
                    @php
                        $orderStatusColors = [
                            'pending_payment' => 'warning',
                            'processing' => 'info',
                            'confirmed' => 'primary',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                            'pending_verification' => 'secondary'
                        ];
                        $orderColor = $orderStatusColors[$order->current_status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $orderColor }}">
                        {{ ucfirst(str_replace('_', ' ', __('admin.' . $order->current_status))) }}
                    </span>
                    @if($order->current_status === 'cancelled' && $order->cancelReason)
                        <br>
                        @php
                            $reasonData = json_decode($order->cancelReason->reason, true);
                            $reasonText = $reasonData[app()->getLocale()] ?? $reasonData['en'] ?? 'Unknown';
                        @endphp
                        <small class="text-danger" title="{{ $reasonText }}">
                            <i class="feather icon-info mr-1"></i>
                            {{ Str::limit($reasonText, 30) }}
                        </small>
                    @endif
                </td>
                <td>
                    <strong>{{ number_format($order->final_total ?? $order->total, 2) }} {{ __('admin.sar') }}</strong>
                    @if($order->services_total > 0 || $order->products_total > 0)
                        <br>
                        <small class="text-muted">
                            @if($order->services_total > 0)
                                {{ __('admin.services') }}: {{ number_format($order->services_total, 2) }}
                            @endif
                            @if($order->products_total > 0)
                                @if($order->services_total > 0) | @endif
                                {{ __('admin.products') }}: {{ number_format($order->products_total, 2) }}
                            @endif
                        </small>
                    @endif
                </td>
                <td>
                    <div>{{ $order->created_at->format('d/m/Y') }}</div>
                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                </td>
                <td class="order-action">
                    <a href="{{ route('admin.bank_transfer_orders.show', ['id' => $order->id]) }}"
                       class="btn btn-warning btn-sm" title="{{ __('admin.view_details') }}">
                        <i class="feather icon-eye"></i> {{ __('admin.view') }}
                    </a>

                    @if($order->bankTransfer && $order->bankTransfer->status === 'pending')
                        <button class="btn btn-success btn-sm verify-transfer" data-order-id="{{ $order->id }}" title="{{ __('admin.verify_transfer') }}">
                            <i class="feather icon-check"></i>
                        </button>
                        <button class="btn btn-danger btn-sm reject-transfer" data-order-id="{{ $order->id }}" title="{{ __('admin.reject_transfer') }}">
                            <i class="feather icon-x"></i>
                        </button>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


    {{-- no data found div --}}
    @if ($orders->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{ asset('admin/app-assets/images/pages/404.png') }}" alt="">
            <span class="mt-2" style="font-family: cairo">{{ __('admin.there_are_no_matches_matching') }}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>

{{-- pagination links div --}}
@if ($orders->count() > 0 && $orders instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{ $orders->links() }}
    </div>
@endif
{{-- pagination links div --}}
