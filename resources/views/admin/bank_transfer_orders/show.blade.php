@extends('admin.layout.master')

@section('css')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
.provider-sub-order {
    transition: all 0.3s ease;
}
.provider-sub-order:hover {
    background-color: #f8f9fa;
}
.provider-sub-order:last-child {
    border-bottom: none !important;
}
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
.item-row {
    padding: 8px 0;
    border-bottom: 1px solid #f1f1f1;
}
.item-row:last-child {
    border-bottom: none;
}
.badge-sm {
    font-size: 0.75em;
    padding: 0.25em 0.5em;
}
.update-provider-status {
    transition: all 0.2s ease;
}
.update-provider-status:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Timeline Styles */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
}

.timeline-marker-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 4px solid #dee2e6;
    margin-left: 15px;
}

.timeline-header h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-body {
    border-top: 1px solid #dee2e6;
    padding-top: 10px;
}

.badge-sm {
    font-size: 0.75em;
    padding: 0.25em 0.5em;
}

/* Status-specific timeline content borders */
.timeline-item:has(.bg-success) .timeline-content {
    border-left-color: #28a745;
}

.timeline-item:has(.bg-danger) .timeline-content {
    border-left-color: #dc3545;
}

.timeline-item:has(.bg-warning) .timeline-content {
    border-left-color: #ffc107;
}

.timeline-item:has(.bg-info) .timeline-content {
    border-left-color: #17a2b8;
}

.timeline-item:has(.bg-primary) .timeline-content {
    border-left-color: #007bff;
}

/* Provider grouping styles */
.provider-status-group {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.provider-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}
.provider-status-history {
    background: #fff;
}
.provider-status-group:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: box-shadow 0.3s ease;
}
</style>
@endsection

@section('content')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
}

.timeline-marker-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 3px solid #dee2e6;
}

.timeline-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 10px;
}

.timeline-title {
    margin: 0;
    flex-grow: 1;
}

.timeline-body p {
    margin-bottom: 5px;
    font-size: 14px;
}
</style>
<section id="order-details">
    <!-- Order Header -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h4 class="card-title mb-0 text-white">
                        <i class="feather icon-file-text mr-2"></i>
                        {{ __('admin.order_details') }}
                    </h4>
                    <small class="text-white-50">
                        {{ __('admin.created_at') }}: {{ $order->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>
                <div class="col-md-4 text-center">
                    <div class="order-number">
                        <span class="badge badge-light px-2 py-1" style="font-size: 0.875rem;">
                            <i class="feather icon-hash mr-1" style="font-size: 0.8rem;"></i>
                            {{ $order->order_number ?? $order->order_num }}
                        </span>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <div class="order-status">
                        @php
                            $statusColors = [
                                'pending_payment' => 'warning',
                                'processing' => 'info',
                                'confirmed' => 'primary',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                'pending_verification' => 'secondary'
                            ];
                            $color = $statusColors[$order->current_status] ?? 'secondary';

                            $statusIcons = [
                                'pending_payment' => 'clock',
                                'processing' => 'refresh-cw',
                                'confirmed' => 'check-circle',
                                'completed' => 'check-circle-2',
                                'cancelled' => 'x-circle',
                                'pending_verification' => 'help-circle'
                            ];
                            $icon = $statusIcons[$order->current_status] ?? 'circle';
                        @endphp
                        <span class="badge badge-{{ $color }} px-2 py-1" style="font-size: 0.875rem;">
                            <i class="feather icon-{{ $icon }} mr-1" style="font-size: 0.8rem;"></i>
                            {{ ucfirst(str_replace('_', ' ', __('admin.' . $order->current_status))) }}
                        </span>
                        @if($order->current_status === 'cancelled' && $order->cancellation_reason)
                            <div class="mt-2">
                                <small class="text-white-75 d-block">
                                    <i class="feather icon-info mr-1" style="font-size: 0.7rem;"></i>
                                    {{ __('admin.cancel_reason') }}:
                                </small>
                                <small class="text-white-50 font-italic">
                                    "{{ $order->cancellation_reason }}"
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="row no-gutters">
                <!-- Quick Stats -->
                <div class="col-md-3 border-right">
                    <div class="p-3 text-center">
                        <div class="stat-icon mb-2">
                            <i class="feather icon-dollar-sign text-success" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="mb-1 font-weight-bold text-success">
                            {{ number_format($order->total ?? $order->final_total, 2) }}
                        </h5>
                        <small class="text-muted">{{ __('admin.total_amount') }}</small>
                    </div>
                </div>

                <div class="col-md-3 border-right">
                    <div class="p-3 text-center">
                        <div class="stat-icon mb-2">
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
                            <i class="feather icon-{{ $paymentIcon }} text-{{ $paymentColor }}" style="font-size: 2rem;"></i>
                        </div>
                        <h6 class="mb-1 font-weight-bold text-{{ $paymentColor }}">
                            {{ ucfirst(str_replace('_', ' ', __('admin.' . $order->payment_status))) }}
                        </h6>
                        <small class="text-muted">{{ __('admin.payment_status') }}</small>
                    </div>
                </div>

                <div class="col-md-3 border-right">
                    <div class="p-3 text-center">
                        <div class="stat-icon mb-2">
                            @if($order->payment_method === 'wallet')
                                <i class="feather icon-credit-card text-info" style="font-size: 2rem;"></i>
                            @elseif($order->payment_method === 'bank_transfer')
                                <i class="feather icon-send text-primary" style="font-size: 2rem;"></i>
                            @else
                                <i class="feather icon-credit-card text-secondary" style="font-size: 2rem;"></i>
                            @endif
                        </div>
                        <h6 class="mb-1 font-weight-bold">
                            {{ $paymentMethod ? $paymentMethod->name : 'Unknown' }}
                        </h6>
                        <small class="text-muted">{{ __('admin.payment_method') }}</small>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 text-center">
                        <div class="stat-icon mb-2">
                            <i class="feather icon-package text-info" style="font-size: 2rem;"></i>
                        </div>
                        <h6 class="mb-1 font-weight-bold">
                            {{ $order->items ? $order->items->count() : 0 }}
                        </h6>
                        <small class="text-muted">{{ __('admin.total_items') }}</small>
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


    <div class="row">
        <!-- Basic Order Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ __('admin.basic_information') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">{{ __('admin.order_number') }}:</th>
                            <td><span class="badge badge-info">{{ $order->order_number ?? $order->order_num }}</span></td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.order_date') }}:</th>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.booking_type') }}:</th>
                            <td>
                                @if($order->booking_type)
                                    <span class="badge badge-secondary">{{ ucfirst(__('admin.' . $order->booking_type)) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.delivery_type') }}:</th>
                            <td>
                                @if($order->delivery_type)
                                    <span class="badge badge-secondary">{{ ucfirst(__('admin.'.$order->delivery_type)) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @if($order->scheduled_at)
                        <tr>
                            <th>{{ __('admin.scheduled_at') }}:</th>
                            <td>{{ $order->scheduled_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                        @if($order->current_status === 'cancelled' && $order->cancelReason)
                        <tr>
                            <th>{{ __('admin.cancel_reason') }}:</th>
                            <td>
                                @php
                                    $reasonData = json_decode($order->cancelReason->reason, true);
                                    $reasonText = $reasonData[app()->getLocale()] ?? $reasonData['en'] ?? 'Unknown';
                                @endphp
                                <div class="alert alert-danger py-2 px-3 mb-0">
                                    <i class="feather icon-x-circle mr-2"></i>
                                    <span class="font-italic">"{{ $reasonText }}"</span>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ __('admin.customer_information') }}</h5>
                </div>
                <div class="card-body">
                    @if($order->user)
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">{{ __('admin.name') }}:</th>
                            <td>{{ $order->user->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.phone') }}:</th>
                            <td>{{ $order->user->phone }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.email') }}:</th>
                            <td>{{ $order->user->email ?? '-' }}</td>
                        </tr>
                        @if($order->user->wallet_balance)
                        <tr>
                            <th>{{ __('admin.wallet_balance') }}:</th>
                            <td>{{ number_format($order->user->wallet_balance, 2) }} {{ __('admin.sar') }}</td>
                        </tr>
                        @endif
                    </table>
                    @else
                    <p class="text-muted">{{ __('admin.no_customer_info') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Provider Sub-Orders -->
    @if($order->providerSubOrders && $order->providerSubOrders->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="feather icon-users mr-2"></i>
                        {{ __('admin.orders') }}
                        <span class="badge badge-info ml-2">{{ $order->providerSubOrders->count() }}</span>
                    </h5>
                    @if($order->hasMultipleProviders())
                        <small class="text-muted">{{ __('admin.multi_provider_order_note') }}</small>
                    @endif
                </div>
                <div class="card-body p-0">
                    @foreach($order->providerSubOrders as $subOrder)
                    <div class="provider-sub-order border-bottom">
                        <div class="row no-gutters">
                            <!-- Provider Info -->
                            <div class="col-md-4 border-right">
                                <div class="p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="provider-avatar mr-3">
                                            @php
                                                $providerLogo = $subOrder->provider->getFirstMediaUrl('logo', 'thumb');
                                            @endphp
                                            @if($providerLogo)
                                                <img src="{{ $providerLogo }}" alt="{{ $subOrder->provider->commercial_name ?? $subOrder->provider->user->name }}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                                            @else
                                                <div class="avatar-circle bg-primary text-white">
                                                    {{ substr($subOrder->provider->commercial_name ?? $subOrder->provider->user->name, 0, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">{{ $subOrder->provider->commercial_name ?? $subOrder->provider->user->name }}</h6>
                                            <small class="text-muted">{{ $subOrder->sub_order_number }}</small>
                                        </div>
                                    </div>
                                    <div class="provider-details">
                                        <p class="mb-1 small"><i class="feather icon-phone mr-1"></i> {{ $subOrder->provider->user->phone }}</p>
                                        <p class="mb-1 small"><i class="feather icon-mail mr-1"></i> {{ $subOrder->provider->user->email ?? '-' }}</p>
                                        <div class="provider-type">
                                            @if($subOrder->provider->in_home && $subOrder->provider->in_salon)
                                                <span class="badge badge-success badge-sm">{{ __('admin.home_and_salon') }}</span>
                                            @elseif($subOrder->provider->in_home)
                                                <span class="badge badge-info badge-sm">{{ __('admin.home_service') }}</span>
                                            @elseif($subOrder->provider->in_salon)
                                                <span class="badge badge-primary badge-sm">{{ __('admin.salon_service') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="col-md-5 border-right">
                                <div class="p-3">
                                    <h6 class="mb-2">{{ __('admin.order_items') }}</h6>
                                    @php
                                        // Get items for this provider - try relationship first, then fallback to filtering main order items
                                        $providerItems = $subOrder->orderItems;
                                        if ($providerItems->isEmpty()) {
                                            $providerItems = $order->items->filter(function($item) use ($subOrder) {
                                                if ($item->item && isset($item->item->provider_id)) {
                                                    return $item->item->provider_id == $subOrder->provider_id;
                                                }
                                                return false;
                                            });
                                        }
                                    @endphp

                                    @if($providerItems && $providerItems->count() > 0)
                                        <div class="items-list">
                                            @foreach($providerItems as $item)
                                            <div class="item-row d-flex justify-content-between align-items-center mb-2">
                                                <div class="item-info d-flex align-items-center">
                                                    @if($item->item_type === 'App\\Models\\Product' && $item->item)
                                                        <img src="{{ $item->item->getFirstMediaUrl('product-images', 'thumb') }}" alt="{{ $item->name }}" class="mr-2 rounded" style="width:40px;height:40px;object-fit:cover;">
                                                    @endif
                                                    <span class="item-name font-weight-medium">{{ $item->name }}</span>
                                                    @if($item->item_type === 'App\\Models\\Service')
                                                        <span class="badge badge-info badge-sm ml-1">{{ __('admin.service') }}</span>
                                                    @else
                                                        <span class="badge badge-success badge-sm ml-1">{{ __('admin.product') }}</span>
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">{{ __('admin.qty') }}: {{ $item->quantity }} × {{ number_format($item->price, 2) }}</small>
                                                </div>
                                                <div class="item-total">
                                                    <strong>{{ number_format($item->total, 2) }}</strong>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted small">{{ __('admin.no_items') }}</p>
                                        {{-- Debug info --}}
                                        <small class="text-danger">Debug: Provider ID {{ $subOrder->provider_id }}, Total Order Items: {{ $order->items->count() }}</small>
                                    @endif
                                </div>
                            </div>

                            <!-- Status & Actions -->
                            <div class="col-md-3">
                                <div class="p-3">
                                    <div class="status-section mb-3">
                                        @php
                                            $statusColors = [
                                                'pending_payment' => 'warning',
                                                'processing' => 'info',
                                                'confirmed' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $color = $statusColors[$subOrder->status] ?? 'secondary';
                                        @endphp
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge badge-{{ $color }}">
                                                {{ ucfirst(str_replace('_', ' ', __('admin.' . $subOrder->status))) }}
                                            </span>
                                        </div>

                                        @if($subOrder->status === 'cancelled' && $order->cancelReason)
                                        <div class="cancellation-reason mb-2">
                                            <small class="text-danger d-block">
                                                <i class="feather icon-info mr-1"></i>
                                                {{ __('admin.cancel_reason') }}:
                                            </small>
                                            @php
                                                $reasonData = json_decode($order->cancelReason->reason, true);
                                                $reasonText = $reasonData[app()->getLocale()] ?? $reasonData['en'] ?? 'Unknown';
                                            @endphp
                                            <small class="text-muted font-italic">
                                                "{{ $reasonText }}"
                                            </small>
                                        </div>
                                        @endif

                                        <!-- Provider Total -->
                                        <div class="provider-total mb-2">
                                            <small class="text-muted">{{ __('admin.provider_total') }}:</small>
                                            <div class="font-weight-bold text-success">{{ number_format($subOrder->total, 2) }} {{ __('admin.sar') }}</div>
                                        </div>

                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Address Information -->
        @if($order->address)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ __('admin.address_information') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">{{ __('admin.address') }}:</th>
                            <td>{{ $order->address->details ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>{{ __('admin.phone') }}:</th>
                            <td>{{ $order->address->phone ?? '-' }}</td>
                        </tr>
                        @if($order->address->latitude && $order->address->longitude)
                        <tr>
                            <th>{{ __('admin.location') }}:</th>
                            <td>
                                <a href="https://maps.google.com/?q={{ $order->address->latitude }},{{ $order->address->longitude }}"
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="feather icon-map-pin"></i> {{ __('admin.view_on_map') }}
                                </a>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>



    <div class="row">
        <!-- Financial Breakdown -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ __('admin.financial_breakdown') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="50%">{{ __('admin.subtotal') }}:</th>
                            <td class="text-right">{{ number_format($order->subtotal ?? 0, 2) }} {{ __('admin.sar') }}</td>
                        </tr>
                        @if($order->services_total > 0)
                        <tr>
                            <th>{{ __('admin.services_total') }}:</th>
                            <td class="text-right">{{ number_format($order->services_total, 2) }} {{ __('admin.sar') }}</td>
                        </tr>
                        @endif
                        @if($order->products_total > 0)
                        <tr>
                            <th>{{ __('admin.products_total') }}:</th>
                            <td class="text-right">{{ number_format($order->products_total, 2) }} {{ __('admin.sar') }}</td>
                        </tr>
                        @endif
                        @if($order->booking_fee > 0)
                        <tr>
                            <th>{{ __('admin.booking_fee') }}:</th>
                            <td class="text-right">{{ number_format($order->booking_fee, 2) }} {{ __('admin.sar') }}</td>
                        </tr>
                        @endif
                        @if($order->home_service_fee > 0)
                        <tr>
                            <th>{{ __('admin.home_service_fee') }}:</th>
                            <td class="text-right">{{ number_format($order->home_service_fee, 2) }} {{ __('admin.sar') }}</td>
                        </tr>
                        @endif
                        @if($order->delivery_fee > 0)
                        <tr>
                            <th>{{ __('admin.delivery_fee') }}:</th>
                            <td class="text-right">{{ number_format($order->delivery_fee, 2) }} {{ __('admin.sar') }}</td>
                        </tr>
                        @endif
                        @if($order->discount_amount > 0)
                        <tr class="text-success">
                            <th>{{ __('admin.discount') }}:</th>
                            <td class="text-right">-{{ number_format($order->discount_amount, 2) }} {{ __('admin.sar') }}</td>
                        </tr>
                        @endif
                        @if($order->loyalty_points_used > 0)
                        <tr class="text-success">
                            <th>{{ __('admin.loyalty_points_used') }}:</th>
                            <td class="text-right">-{{ number_format($order->loyalty_points_used, 2) }} {{ __('admin.sar') }}</td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <th><strong>{{ __('admin.total') }}:</strong></th>
                            <td class="text-right"><strong>{{ number_format($order->total ?? $order->final_total, 2) }} {{ __('admin.sar') }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ __('admin.payment_information') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="50%">{{ __('admin.payment_method') }}:</th>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ $paymentMethod ? $paymentMethod->name : 'Unknown' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.payment_status') }}:</th>
                            <td>
                                @php
                                    $paymentStatusColors = [
                                        'pending' => 'warning',
                                        'paid' => 'success',
                                        'failed' => 'danger',
                                        'pending_verification' => 'info',
                                        'refunded' => 'secondary'
                                    ];
                                    $paymentColor = $paymentStatusColors[$order->payment_status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $paymentColor }}">
                                    {{ ucfirst(str_replace('_', ' ', __('admin.' . $order->payment_status))) }}
                                </span>
                            </td>
                        </tr>
                        @if($order->payment_reference)
                        <tr>
                            <th>{{ __('admin.payment_reference') }}:</th>
                            <td><code>{{ $order->payment_reference }}</code></td>
                        </tr>
                        @endif
                        @if($order->payment_date)
                        <tr>
                            <th>{{ __('admin.payment_date') }}:</th>
                            <td>{{ $order->payment_date?->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                        @if($order->amount_paid > 0)
                        <tr>
                            <th>{{ __('admin.amount_paid') }}:</th>
                            <td><strong>{{ number_format($order->amount_paid, 2) }} {{ __('admin.sar') }}</strong></td>
                        </tr>
                        @endif
                        @if($order->coupon_code)
                        <tr>
                            <th>{{ __('admin.coupon_code') }}:</th>
                            <td>
                                <span class="badge badge-success">{{ $order->coupon_code }}</span>
                                @if($order->coupon)
                                    <br><small class="text-muted">{{ $order->coupon->name ?? '' }}</small>
                                @endif
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Transfer Details -->
    @if($order->payment_method_id === 5 && $order->bankTransfer)
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('admin.bank_transfer_details') }}</h5>
            <div>
                @if($order->bankTransfer->status === 'pending')
                    <button class="btn btn-success btn-sm verify-transfer" data-order-id="{{ $order->id }}">
                        <i class="feather icon-check"></i> {{ __('admin.verify_transfer') }}
                    </button>
                    <button class="btn btn-danger btn-sm reject-transfer" data-order-id="{{ $order->id }}">
                        <i class="feather icon-x"></i> {{ __('admin.reject_transfer') }}
                    </button>
                @else
                    <span class="badge badge-{{ $order->bankTransfer->status === 'verified' ? 'success' : 'danger' }}">
                        {{ ucfirst($order->bankTransfer->status) }}
                    </span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="50%">{{ __('admin.sender_bank_name') }}:</th>
                            <td>{{ $order->bankTransfer->sender_bank_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.sender_account_holder') }}:</th>
                            <td>{{ $order->bankTransfer->sender_account_holder_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.sender_account_number') }}:</th>
                            <td><code>{{ $order->bankTransfer->sender_account_number }}</code></td>
                        </tr>
                        @if($order->bankTransfer->sender_iban)
                        <tr>
                            <th>{{ __('admin.sender_iban') }}:</th>
                            <td><code>{{ $order->bankTransfer->sender_iban }}</code></td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="50%">{{ __('admin.transfer_amount') }}:</th>
                            <td><strong>{{ number_format($order->bankTransfer->transfer_amount, 2) }} {{ __('admin.sar') }}</strong></td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.transfer_date') }}:</th>
                            <td>{{ $order->payment_date?->format('d/m/Y') }}</td>
                        </tr>
                        @if($order->bankTransfer->transfer_reference)
                        <tr>
                            <th>{{ __('admin.transfer_reference') }}:</th>
                            <td><code>{{ $order->bankTransfer->transfer_reference }}</code></td>
                        </tr>
                        @endif
                        @if($order->bankTransfer->admin_notes)
                        <tr>
                            <th>{{ __('admin.admin_notes') }}:</th>
                            <td>{{ $order->bankTransfer->admin_notes }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Order Status History -->
    @if($order->statusChanges && $order->statusChanges->count() > 0)
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ __('admin.order_status_history') }}</h5>
        </div>
        <div class="card-body">
            <div class="timeline">
                @foreach($order->statusChanges as $statusChange)
                <div class="timeline-item">
                    <div class="timeline-marker">
                        @php
                            $statusColors = [
                                'pending_payment' => 'warning',
                                'processing' => 'info',
                                'confirmed' => 'primary',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                'new' => 'secondary'
                            ];
                            $color = $statusColors[$statusChange->status] ?? 'secondary';
                        @endphp
                        <div class="timeline-marker-icon bg-{{ $color }}">
                            @switch($statusChange->status)
                                @case('completed')
                                    <i class="feather icon-check text-white"></i>
                                    @break
                                @case('cancelled')
                                    <i class="feather icon-x text-white"></i>
                                    @break
                                @case('processing')
                                    <i class="feather icon-clock text-white"></i>
                                    @break
                                @default
                                    <i class="feather icon-circle text-white"></i>
                            @endswitch
                        </div>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h6 class="timeline-title">
                                <span class="badge badge-{{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $statusChange->status)) }}
                                </span>
                            </h6>
                            <small class="text-muted">
                                {{ $statusChange->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        <div class="timeline-body">
                            <p class="mb-1">
                                <strong>{{ __('admin.changed_by') }}:</strong>
                                {{ $statusChange->changed_by_name }}
                            </p>
                            @if($statusChange->map_desc)
                            <p class="mb-0">
                                <strong>{{ __('admin.notes') }}:</strong>
                                {{ $statusChange->map_desc }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Order Status History -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="feather icon-clock mr-2"></i>
                {{ __('admin.provider_orders_status_history') }}
            </h5>
            <small class="text-muted">{{ __('admin.provider_sub_orders_audit_trail') }}</small>
        </div>
        <div class="card-body">
            @if($order->providerSubOrders && $order->providerSubOrders->count() > 0)
                @foreach($order->providerSubOrders as $subOrder)
                    @php
                        // Get status changes for this specific sub-order
                        $subOrderStatuses = \App\Models\OrderStatus::where('provider_sub_order_id', $subOrder->id)
                            ->with(['statusable'])
                            ->orderBy('created_at', 'desc')
                            ->get();
                    @endphp

                    <div class="provider-status-group mb-4">
                        <!-- Provider Header -->
                        <div class="provider-header bg-light p-3 rounded-top border">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="provider-avatar mr-3">
                                        <div class="avatar-circle bg-primary text-white">
                                            {{ substr($subOrder->provider->commercial_name ?? $subOrder->provider->user->name, 0, 2) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 font-weight-bold">
                                            {{ $subOrder->provider->commercial_name ?? $subOrder->provider->user->name }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ __('admin.sub_order') }}: {{ $subOrder->sub_order_number }}
                                        </small>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @php
                                        $currentStatusColors = [
                                            'new' => 'secondary',
                                            'processing' => 'info',
                                            'confirmed' => 'primary',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $currentColor = $currentStatusColors[$subOrder->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $currentColor }} px-2 py-1">
                                        {{ ucfirst(str_replace('_', ' ', $subOrder->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Status History for this Provider -->
                        <div class="provider-status-history border border-top-0 rounded-bottom">
                            @if($subOrderStatuses->count() > 0)
                                <div class="timeline p-3">
                                    @foreach($subOrderStatuses as $statusChange)
                                    <div class="timeline-item">
                                        <div class="timeline-marker">
                                            @php
                                                $statusColors = [
                                                    'new' => 'secondary',
                                                    'processing' => 'info',
                                                    'confirmed' => 'primary',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $color = $statusColors[$statusChange->status] ?? 'secondary';
                                            @endphp
                                            <div class="timeline-marker-icon bg-{{ $color }}">
                                                @if($statusChange->status === 'completed')
                                                    <i class="feather icon-check text-white"></i>
                                                @elseif($statusChange->status === 'cancelled')
                                                    <i class="feather icon-x text-white"></i>
                                                @elseif($statusChange->status === 'processing')
                                                    <i class="feather icon-play text-white"></i>
                                                @elseif($statusChange->status === 'confirmed')
                                                    <i class="feather icon-check-circle text-white"></i>
                                                @else
                                                    <i class="feather icon-clock text-white"></i>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="timeline-header">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <span class="badge badge-{{ $color }} mr-2">
                                                                {{ ucfirst(str_replace('_', ' ', $statusChange->status)) }}
                                                            </span>
                                                        </h6>
                                                        <small class="text-muted">
                                                            <i class="feather icon-calendar mr-1"></i>
                                                            {{ $statusChange->created_at->format('M d, Y H:i:s') }}
                                                            <span class="mx-2">•</span>
                                                            <i class="feather icon-user mr-1"></i>
                                                            @if($statusChange->statusable_type === 'App\Models\Admin')
                                                                {{ __('admin.admin') }}: {{ $statusChange->statusable->name ?? 'System' }}
                                                            @elseif($statusChange->statusable_type === 'App\Models\User')
                                                                {{ __('admin.customer') }}: {{ $statusChange->statusable->name ?? 'Customer' }}
                                                            @elseif($statusChange->statusable_type === 'App\Models\Provider')
                                                                {{ __('admin.provider') }}: {{ $statusChange->statusable->user->name ?? 'Provider' }}
                                                            @else
                                                                {{ __('admin.system') }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ $statusChange->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            </div>
                                            @if($statusChange->map_desc)
                                            <div class="timeline-body mt-2">
                                                <p class="mb-0 text-muted">
                                                    <i class="feather icon-message-circle mr-1"></i>
                                                    {{ $statusChange->map_desc }}
                                                </p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="feather icon-clock text-muted" style="font-size: 24px;"></i>
                                    <p class="mt-2 text-muted mb-0">{{ __('admin.no_status_changes_for_provider') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="feather icon-clock text-muted" style="font-size: 48px;"></i>
                    <h6 class="mt-3 text-muted">{{ __('admin.no_provider_orders') }}</h6>
                    <p class="text-muted">{{ __('admin.provider_orders_will_appear_here') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card">
        <div class="card-body text-center">
            <a href="{{ route('admin.bank_transfer_orders.index') }}" class="btn btn-secondary">
                <i class="feather icon-arrow-left"></i> {{ __('admin.back_to_orders') }}
            </a>
        </div>
    </div>
</section>
@endsection
@section('js')
<!-- SweetAlert2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
function updateOrderStatus(orderId, status) {
    Swal.fire({
        title: '{{ __("admin.are_you_sure") }}',
        text: '{{ __("admin.confirm_status_change") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ __("admin.yes_update") }}',
        cancelButtonText: '{{ __("admin.cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: '{{ __("admin.updating") }}',
                text: '{{ __("admin.please_wait") }}',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            // Add AJAX call to update order status
            fetch(`/admin/bank-transfer-orders/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '{{ __("admin.success") }}',
                        text: '{{ __("admin.status_updated_successfully") }}',
                        icon: 'success',
                        confirmButtonText: '{{ __("admin.ok") }}'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: '{{ __("admin.error") }}',
                        text: data.message || '{{ __("admin.error_occurred") }}',
                        icon: 'error',
                        confirmButtonText: '{{ __("admin.ok") }}'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: '{{ __("admin.error") }}',
                    text: '{{ __("admin.error_occurred") }}',
                    icon: 'error',
                    confirmButtonText: '{{ __("admin.ok") }}'
                });
            });
        }
    });
}

function markPaymentAsPaid(orderId) {
    Swal.fire({
        title: '{{ __("admin.confirm_mark_payment_paid") }}',
        text: '{{ __("admin.payment_will_be_marked_paid") }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ __("admin.yes_mark_paid") }}',
        cancelButtonText: '{{ __("admin.cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: '{{ __("admin.processing") }}',
                text: '{{ __("admin.please_wait") }}',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            fetch(`/admin/orders/${orderId}/mark-payment-paid`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '{{ __("admin.success") }}',
                        text: '{{ __("admin.payment_marked_paid_successfully") }}',
                        icon: 'success',
                        confirmButtonText: '{{ __("admin.ok") }}'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: '{{ __("admin.error") }}',
                        text: data.message || '{{ __("admin.error_occurred") }}',
                        icon: 'error',
                        confirmButtonText: '{{ __("admin.ok") }}'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: '{{ __("admin.error") }}',
                    text: '{{ __("admin.error_occurred") }}',
                    icon: 'error',
                    confirmButtonText: '{{ __("admin.ok") }}'
                });
            });
        }
    });
}

function updateProviderStatus(orderId, providerId, status) {
    Swal.fire({
        title: '{{ __("admin.are_you_sure") }}',
        text: '{{ __("admin.confirm_provider_status_change") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ __("admin.yes_update") }}',
        cancelButtonText: '{{ __("admin.cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: '{{ __("admin.updating") }}',
                text: '{{ __("admin.please_wait") }}',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            // Add AJAX call to update provider status
            fetch(`/admin/orders/${orderId}/provider/${providerId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '{{ __("admin.success") }}',
                        text: '{{ __("admin.provider_status_updated_successfully") }}',
                        icon: 'success',
                        confirmButtonText: '{{ __("admin.ok") }}'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: '{{ __("admin.error") }}',
                        text: data.message || '{{ __("admin.error_occurred") }}',
                        icon: 'error',
                        confirmButtonText: '{{ __("admin.ok") }}'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: '{{ __("admin.error") }}',
                    text: '{{ __("admin.error_occurred") }}',
                    icon: 'error',
                    confirmButtonText: '{{ __("admin.ok") }}'
                });
            });
        }
    });
}

// Bank transfer verification
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.verify-transfer').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;

            Swal.fire({
                title: '{{ __("admin.confirm_verify_transfer") }}',
                text: '{{ __("admin.transfer_will_be_verified") }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __("admin.yes_verify") }}',
                cancelButtonText: '{{ __("admin.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: '{{ __("admin.verifying") }}',
                        text: '{{ __("admin.please_wait") }}',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    // Add AJAX call to verify transfer
                    fetch(`/admin/bank-transfer-orders/${orderId}/verify-transfer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '{{ __("admin.success") }}',
                                text: '{{ __("admin.transfer_verified_successfully") }}',
                                icon: 'success',
                                confirmButtonText: '{{ __("admin.ok") }}'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: '{{ __("admin.error") }}',
                                text: data.message || '{{ __("admin.error_occurred") }}',
                                icon: 'error',
                                confirmButtonText: '{{ __("admin.ok") }}'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: '{{ __("admin.error") }}',
                            text: '{{ __("admin.error_occurred") }}',
                            icon: 'error',
                            confirmButtonText: '{{ __("admin.ok") }}'
                        });
                    });
                }
            });
        });
    });

    document.querySelectorAll('.reject-transfer').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;

            Swal.fire({
                title: '{{ __("admin.rejection_reason") }}',
                input: 'textarea',
                inputLabel: '{{ __("admin.please_provide_reason") }}',
                inputPlaceholder: '{{ __("admin.enter_rejection_reason") }}',
                inputAttributes: {
                    'aria-label': '{{ __("admin.rejection_reason") }}'
                },
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __("admin.yes_reject") }}',
                cancelButtonText: '{{ __("admin.cancel") }}',
                inputValidator: (value) => {
                    if (!value) {
                        return '{{ __("admin.reason_required") }}'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: '{{ __("admin.rejecting") }}',
                        text: '{{ __("admin.please_wait") }}',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    // Add AJAX call to reject transfer
                    fetch(`/admin/bank-transfer-orders/${orderId}/reject-transfer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ reason: result.value })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '{{ __("admin.success") }}',
                                text: '{{ __("admin.transfer_rejected_successfully") }}',
                                icon: 'success',
                                confirmButtonText: '{{ __("admin.ok") }}'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: '{{ __("admin.error") }}',
                                text: data.message || '{{ __("admin.error_occurred") }}',
                                icon: 'error',
                                confirmButtonText: '{{ __("admin.ok") }}'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: '{{ __("admin.error") }}',
                            text: '{{ __("admin.error_occurred") }}',
                            icon: 'error',
                            confirmButtonText: '{{ __("admin.ok") }}'
                        });
                    });
                }
            });
        });
    });
});
</script>
@endsection