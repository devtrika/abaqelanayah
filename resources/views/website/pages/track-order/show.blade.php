@extends('website.layouts.app')

@section('title', __('site.track_order') . ' #' . $order->order_number)

@push('styles')
<style>
    .tracking-page-container {
        background-color: #fff;
    }
    .order-box {
        border: 1px solid #f0f0f0;
        border-radius: 12px;
        padding: 24px;
        background: #fff;
        margin-bottom: 30px;
    }
    .text-title {
        color: #1a1a1a;
        font-weight: 600;
    }
    .text-muted-dark {
        color: #8c8c8c;
    }
    
    /* Stepper Styles */
    .tracking-stepper-container {
        position: relative;
        padding: 20px 0;
        margin-top: 20px;
    }
    .stepper-line-bg {
        position: absolute;
        top: 36px;
        left: 5%;
        right: 5%;
        height: 2px;
        background-color: #f0f0f0;
        z-index: 1;
    }
    .stepper-line-progress {
        position: absolute;
        top: 36px;
        left: 5%;
        height: 2px;
        background-color: #ffc107;
        z-index: 2;
        transition: width 0.3s ease;
    }
    .stepper-items {
        display: flex;
        justify-content: space-between;
        position: relative;
        z-index: 3;
    }
    .stepper-item {
        text-align: center;
        flex: 1;
    }
    .stepper-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #fff;
        border: 2px solid #f0f0f0;
        color: #8c8c8c;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .stepper-item.completed .stepper-circle {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #fff;
    }
    .stepper-title {
        font-size: 14px;
        font-weight: 600;
        color: #8c8c8c;
        margin-bottom: 4px;
    }
    .stepper-item.completed .stepper-title {
        color: #ffc107;
    }
    .stepper-date {
        font-size: 12px;
        color: #8c8c8c;
    }

    /* Product Table */
    .product-img {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 8px;
        background-color: #f8f9fa;
        padding: 4px;
    }
    .table-items th {
        color: #8c8c8c;
        font-weight: 500;
        font-size: 14px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 16px;
    }
    .table-items td {
        vertical-align: middle;
        padding: 20px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .table-items tr:last-child td {
        border-bottom: none;
    }
    
    .qty-display {
        display: inline-flex;
        align-items: center;
        gap: 16px;
        color: #1a1a1a;
        font-weight: 500;
    }
    .qty-display span {
        color: #8c8c8c;
    }

    .btn-invoice {
        background-color: #ffc107;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        transition: opacity 0.2s;
    }
    .btn-invoice:hover {
        opacity: 0.9;
        color: #fff;
    }
</style>
@endpush

@section('content')
<main class="page-content tracking-page-container pt-5 pb-5">
    <div class="container" style="max-width: 900px;">
        
        <!-- Header -->
        <div class="text-center mb-5">
            <h2 class="text-title mb-3 fs-3">{{ __('site.order_tracking') }}</h2>
            <p class="text-muted-dark mx-auto" style="max-width: 700px; font-size: 14px; line-height: 1.6;">
                {{ __('site.order_tracking_desc') }}
            </p>
        </div>

        <!-- Order Details Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-title fs-5 mb-0">{{ __('site.order_details') }}</h4>
            @php
                $orderNumberForUrl = $order->order_number ?? $order->id;
                if (auth()->check() && (int) $order->user_id === (int) auth()->id()) {
                    $invoiceUrl = route('website.orders.invoice', $order->id);
                } else {
                    $invoiceUrl = \Illuminate\Support\Facades\URL::signedRoute(
                        'website.orders.invoice.public',
                        ['orderNumber' => $orderNumberForUrl]
                    );
                }
            @endphp
            <a href="{{ $invoiceUrl }}" target="_blank" class="btn-invoice">{{ __('site.download_invoice') }}</a>
        </div>

        <!-- Order Info Summary -->
        <div class="row text-start mb-5">
            <div class="col-md-2 col-6 mb-3">
                <div class="text-muted-dark small mb-2">{{ __('site.order_number') }}</div>
                <div class="text-title fw-bold">#{{ $order->order_number }}</div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="text-muted-dark small mb-2">{{ __('site.order_placed') }}</div>
                <div class="text-title fw-bold">{{ $order->created_at->format('M d, Y') }}</div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="text-muted-dark small mb-2">{{ __('site.order_delivered') }}</div>
                <div class="text-title fw-bold">
                    {{ $order->status === 'delivered' ? $order->updated_at->format('M d, Y') : ($order->schedule_date ? \Carbon\Carbon::parse($order->schedule_date)->format('M d, Y') : __('site.pending')) }}
                </div>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <div class="text-muted-dark small mb-2">{{ __('site.no_of_items') }}</div>
                <div class="text-title fw-bold">{{ $order->items->sum('quantity') }} {{ __('site.items') }}</div>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <div class="text-muted-dark small mb-2">{{ __('site.status') }}</div>
                <div class="text-title fw-bold">{{ __('site.order_status_' . $order->status) }}</div>
            </div>
        </div>

        <!-- Order Tracking Stepper -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-title fs-5 mb-0">{{ __('site.order_tracking') }}</h4>
            <div class="text-muted-dark">{{ __('site.order_id') }} #{{ $order->order_number }}</div>
        </div>

        @php
            $historyStatuses = $order->statusChanges->pluck('status')->toArray();
            $historyDates = $order->statusChanges->pluck('created_at', 'status')->toArray();
            
            // Normalize history mapping based on our DB enum and generic steps
            $steps = [
                [
                    'id' => 1,
                    'label' => __('site.order_status_pending'),
                    'keys' => ['pending', 'new'],
                    'fallback_date' => $order->created_at
                ],
                [
                    'id' => 2,
                    'label' => __('site.order_status_confirmed'),
                    'keys' => ['confirmed', 'processing'],
                    'fallback_date' => null
                ],
                [
                    'id' => 3,
                    'label' => __('site.order_status_out-for-delivery'),
                    'keys' => ['out-for-delivery'],
                    'fallback_date' => null
                ],
                [
                    'id' => 4,
                    'label' => __('site.order_status_delivered'),
                    'keys' => ['delivered'],
                    'fallback_date' => null
                ],
            ];

            $completedCount = 0;
            foreach ($steps as &$step) {
                $isCompleted = false;
                $stepDate = $step['fallback_date'];
                
                // If current status is delivered, all previous are completed
                if ($order->status === 'delivered') {
                    $isCompleted = true;
                }
                
                // Or if current status is further down the line
                foreach ($step['keys'] as $key) {
                    if (in_array($key, $historyStatuses) || $order->status === $key) {
                        $isCompleted = true;
                        if (isset($historyDates[$key])) {
                            $stepDate = $historyDates[$key];
                        }
                    }
                }
                
                $step['completed'] = $isCompleted;
                $step['date'] = $stepDate;
                if ($isCompleted) {
                    $completedCount++;
                }
            }
            unset($step);
            
            // Fix completion logic so prior steps are completed if a later step is
            for ($i = 0; $i < count($steps); $i++) {
                if ($steps[$i]['completed']) {
                    for ($j = 0; $j < $i; $j++) {
                        $steps[$j]['completed'] = true;
                    }
                }
            }
            
            // Recalculate completed count for progress line
            $completedCount = collect($steps)->where('completed', true)->count();
            $progressPercentage = 0;
            if ($completedCount === 1) $progressPercentage = 0;
            else if ($completedCount === 2) $progressPercentage = 33;
            else if ($completedCount === 3) $progressPercentage = 66;
            else if ($completedCount >= 4) $progressPercentage = 100;
        @endphp

        <div class="order-box">
            @if($order->status === 'cancelled' || $order->status === 'problem' || str_contains($order->status, 'refund'))
                <div class="text-center py-4">
                    <h5 class="text-danger fw-bold mb-2">{{ __('site.order_status_' . $order->status) }}</h5>
                    <p class="text-muted-dark">{{ __('site.tracking_unavailable_because_status') }} {{ __('site.order_status_' . $order->status) }}.</p>
                </div>
            @else
                <div class="tracking-stepper-container">
                    <div class="stepper-line-bg"></div>
                    <div class="stepper-line-progress" style="width: {{ $progressPercentage }}%;"></div>
                    <div class="stepper-items">
                        @foreach($steps as $index => $step)
                            <div class="stepper-item {{ $step['completed'] ? 'completed' : '' }}">
                                <div class="stepper-circle">{{ $step['id'] }}</div>
                                <div class="stepper-title">{{ $step['label'] }}</div>
                                <div class="stepper-date">
                                    {{ $step['date'] ? \Carbon\Carbon::parse($step['date'])->format('M d, Y') : '-' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Items Table -->
        <h4 class="text-title fs-5 mb-3 mt-5">{{ __('site.order_items') }}</h4>
        
        <div class="table-responsive mb-4">
            <table class="table table-borderless table-items w-100">
                <thead>
                    <tr>
                        <th style="width: 50%;">{{ __('site.product') }}</th>
                        <th class="text-center" style="width: 15%;">{{ __('site.size') }}</th>
                        <th class="text-center" style="width: 20%;">{{ __('site.quantity') }}</th>
                        <th class="text-end" style="width: 15%;">{{ __('site.price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($item->product && $item->product->image_url)
                                        <img src="{{ $item->product->image_url }}" alt="{{ __('site.product') }}" class="product-img">
                                    @else
                                        <div class="product-img d-flex align-items-center justify-content-center text-muted">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-title fw-bold">{{ $item->product->name ?? __('site.product') }}</div>
                                        <div class="text-muted-dark small mt-1">{{ __('site.product_id') }}: {{ $item->product_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center text-title fw-bold">
                                {{ $item->weightOption ? $item->weightOption->name : '-' }}
                            </td>
                            <td class="text-center">
                                <div class="qty-display justify-content-center">
                                    <span>—</span>
                                    {{ str_pad($item->quantity, 2, '0', STR_PAD_LEFT) }}
                                    <span>+</span>
                                </div>
                            </td>
                            <td class="text-end text-title fw-bold">
                                ${{ number_format($item->price, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Order Summary Cards -->
        <div class="row mt-4 g-4">
            <!-- Left Card: Discount & Delivery -->
            <div class="col-md-6">
                <div class="order-box h-100 mb-0 d-flex flex-column justify-content-center">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted-dark">{{ __('site.discount') }}</span>
                        <span class="text-title fw-bold">${{ number_format($order->discount_amount ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted-dark">{{ __('site.delivery') }}</span>
                        <span class="text-title fw-bold">${{ number_format($order->delivery_fee ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Right Card: Subtotal & Total -->
            <div class="col-md-6">
                <div class="order-box h-100 mb-0 d-flex flex-column justify-content-center">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted-dark">{{ __('site.subtotal') }}</span>
                        <span class="text-title fw-bold">${{ number_format($order->subtotal ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted-dark">{{ __('site.total') }}</span>
                        <span class="text-title fw-bold">${{ number_format($order->total ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</main>
@endsection
