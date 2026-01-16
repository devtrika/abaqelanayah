<!-- Quick Stats -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-info text-white py-3">
        <h5 class="card-title mb-0 text-white">
            <i class="feather icon-bar-chart-2 mr-2 text-white"></i>
            {{ __('admin.quick_stats') }}
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="row no-gutters">
            <!-- Total Amount -->
            <div class="col-md-3 border-right">
                <div class="p-4 text-center">
                    <div class="stat-icon mb-3">
                        <i class="feather icon-dollar-sign text-success" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="mb-2 font-weight-bold text-success">
                        {{ number_format($order->total ?? $order->final_total, 2) }}
                    </h5>
                    <small class="text-muted font-weight-medium">{{ __('admin.total_amount') }}</small>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="col-md-3 border-right">
                <div class="p-4 text-center">
                    <div class="stat-icon mb-3">
                        @php
                            $paymentStatusColors = [
                                'pending' => 'warning',
                                'paid' => 'success',
                                'failed' => 'danger',
                                'pending_verification' => 'info',
                                'refunded' => 'secondary'
                            ];
                            $paymentColor = $paymentStatusColors[$order->payment_status] ?? 'secondary';

                            $paymentIcons = [
                                'pending' => 'clock',
                                'paid' => 'check-circle',
                                'failed' => 'x-circle',
                                'pending_verification' => 'help-circle',
                                'refunded' => 'rotate-ccw'
                            ];
                            $paymentIcon = $paymentIcons[$order->payment_status] ?? 'circle';
                        @endphp
                        <i class="feather icon-{{ $paymentIcon }} text-{{ $paymentColor }}" style="font-size: 2.5rem;"></i>
                    </div>
                    <h6 class="mb-2 font-weight-bold text-{{ $paymentColor }}">
                        {{ __('admin.' . $order->payment_status) }}
                    </h6>
                    <small class="text-muted font-weight-medium">{{ __('admin.payment_status') }}</small>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="col-md-3 border-right">
                <div class="p-4 text-center">
                    <div class="stat-icon mb-3">
                        @if($order->payment_method === 'wallet')
                            <i class="feather icon-credit-card text-info" style="font-size: 2.5rem;"></i>
                        @elseif($order->payment_method === 'bank_transfer')
                            <i class="feather icon-send text-primary" style="font-size: 2.5rem;"></i>
                        @else
                            <i class="feather icon-credit-card text-secondary" style="font-size: 2.5rem;"></i>
                        @endif
                    </div>
                    <h6 class="mb-2 font-weight-bold">
                        {{ $paymentMethod ? $paymentMethod->name : 'Unknown' }}
                    </h6>
                    <small class="text-muted font-weight-medium">{{ __('admin.payment_method') }}</small>
                </div>
            </div>

            <!-- Total Items -->
            <div class="col-md-3">
                <div class="p-4 text-center">
                    <div class="stat-icon mb-3">
                        <i class="feather icon-package text-info" style="font-size: 2.5rem;"></i>
                    </div>
                    <h6 class="mb-2 font-weight-bold">
                        {{ $order->items ? $order->items->count() : 0 }}
                    </h6>
                    <small class="text-muted font-weight-medium">{{ __('admin.total_items') }}</small>
                </div>
            </div>
        </div>
    </div>

    @if($order->payment_method_id === 5 && $order->bankTransfer && $order->current_status == 'pending_payment')
    <div class="card-footer bg-light border-top">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <i class="feather icon-alert-triangle text-warning mr-2"></i>
                    <span class="text-warning font-weight-bold">
                        {{ __('admin.bank_transfer_pending_verification') }}
                    </span>
                </div>
                <small class="text-muted">
                    {{ __('admin.transfer_amount') }}: {{ number_format($order->bankTransfer->transfer_amount, 2) }} {{ __('admin.sar') }}
                </small>
            </div>
            <div class="col-md-4 text-right">
                <button class="btn btn-success btn-sm verify-transfer" data-order-id="{{ $order->id }}">
                    <i class="feather icon-check mr-1"></i>
                    {{ __('admin.verify') }}
                </button>
                <button class="btn btn-danger btn-sm reject-transfer ml-1" data-order-id="{{ $order->id }}">
                    <i class="feather icon-x mr-1"></i>
                    {{ __('admin.reject') }}
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
