@extends('admin.layout.master')

@section('css')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
.provider-sub-order {

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

.empty-state {
    padding: 20px;
}

.rating-item .progress {
    background-color: #f8f9fa;
}

.rating-item .progress-bar {
    transition: width 0.6s ease;
}

.stars .feather {
    margin-right: 2px;
}

.rating-media img {
    transition: transform 0.2s ease;
}

.rating-media img:hover {
    transform: scale(1.05);
}
</style>
@endsection

@section('content')
<section id="order-details" class="container-fluid">
    <style>
        .card {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
        }
        .card + .card {
            margin-top: 0;
        }
        .row.no-gutters > [class*="col-"] .card {
            border-radius: 0;
        }
        .row.no-gutters > [class*="col-"]:first-child .card {
            border-top-left-radius: 0.375rem;
            border-bottom-left-radius: 0.375rem;
        }
        .row.no-gutters > [class*="col-"]:last-child .card {
            border-top-right-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }
        .row.no-gutters > [class*="col-"] + [class*="col-"] .card {
            border-left: 0;
        }
    </style>
    <!-- Order Header -->
    @include('admin.orders.partials.header')

        <!-- Delivery User Info -->
    @if($order->status === 'new' && $order->deliveryUser)
        <div class="card mt-3">
            <div class="card-header provider-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="feather icon-truck text-primary mr-2"></i>
                    @if($order->status === 'new')
                        {{ __('admin.new_responsible') }}
                    @else
                        {{ __('admin.delivery_in_progress') }}
                    @endif
                </h5>
            </div>
            <div class="card-body provider-status-history">
                <div class="timeline">
                    <div class="timeline-item mb-3">
                        <div class="timeline-marker">
                            <div class="timeline-marker-icon bg-primary">
                                <i class="feather icon-user text-white"></i>
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <h6 class="timeline-title">
                                    <span class="badge badge-primary">
                                        {{ $order->deliveryUser->name ?? '-' }}
                                    </span>
                                </h6>
                            </div>
                            <div class="timeline-body">
                                <p><strong>
                                    @if($order->status === 'new')
                                        {{ __('admin.new_responsible') }}
                                    @else
                                        {{ __('admin.delivery_in_progress') }}
                                    @endif
                                :</strong> {{ $order->deliveryUser->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Stats -->
    @include('admin.orders.partials.stats')



    <!-- Basic Information & Customer Information -->
    @include('admin.orders.partials.basic-info')


    <!-- Branch Information -->


     @include('admin.orders.partials.branch-info')


     @include('admin.orders.partials.cost_details')



    <!-- Address Information (if exists) -->
  


    <!-- Financial Breakdown & Payment Information -->
    {{-- @include('admin.orders.partials.financial-payment') --}}



    <!-- Product Details for Each Order Item -->
    @foreach($order->items as $item)
        @include('admin.orders.partials.product-details', ['product' => $item->product, 'item' => $item])
    @endforeach




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
                                {{ __('admin.' . $statusChange->status) }}
                            </span>
                        </h6>
                        <small class="text-muted">
                            {{ $statusChange->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                    <div class="timeline-body">
                        @if($statusChange->map_desc)
                        <p class="mb-0">
                            <strong>{{ __('admin.notes') }}:</strong>
                            @php
                                $desc = $statusChange->map_desc;
                                $desc_ar = $desc;
                                $desc_en = $desc;
                                // إذا كانت الملاحظة تبدأ بـ "Status changed to"
                                if(Str::startsWith($desc, 'Status changed to')) {
                                    // استخراج الحالة
                                    preg_match('/Status changed to (.*?) by admin/', $desc, $matches);
                                    $status = $matches[1] ?? '';
                                    // ترجمة الحالة
                                    $status_ar = __("admin.$status");
                                    $desc_ar = "تم تغيير الحالة إلى $status_ar بواسطة الإدارة";
                                    $desc_en = $desc;
                                }
                            @endphp
                            @if(app()->getLocale() == 'ar')
                                <span>{{ $desc_ar }}</span>
                            @else
                                <span>{{ $desc_en }}</span>
                            @endif
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

    <!-- Order Rating -->
@if($order->rates->count())
    @php
        $averageRating = number_format($order->rates->avg('rating'), 1);
        $ratingColor = $averageRating >= 4 ? 'success' : ($averageRating >= 3 ? 'warning' : 'danger');
        $totalRatings = $order->rates->count();
    @endphp

    <div class="card mt-3">
        <div class="card-header provider-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
                <i class="feather icon-star text-warning mr-2"></i>
                {{ __('admin.order_rating') }}
            </h5>
            <span class="badge badge-{{ $ratingColor }} px-2 py-1">
                {{ $averageRating }}/5 
                <small class="ml-1 text-muted">({{ $totalRatings }} {{ __('admin.rating_count') }})</small>
            </span>
        </div>

        <div class="card-body provider-status-history">
            <div class="timeline">
                @foreach($order->rates as $rate)
                    @php
                        $rating = $rate->rating;
                        $ratingColor = $rate->rating >= 4 ? 'success' : ($rate->rating >= 3 ? 'warning' : 'danger');
                    @endphp

                    <div class="timeline-item mb-3">
                        <div class="timeline-marker">
                            <div class="timeline-marker-icon bg-{{ $ratingColor }}">
                                <i class="feather icon-star text-white"></i>
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <h6 class="timeline-title">
                                    <span class="badge badge-{{ $ratingColor }}">
                                        {{ __('admin.rating') }}: {{ $rate->rating }}/5
                                    </span>
                                </h6>
                                <small class="text-muted">
                                    {{ $rate->created_at ? $rate->created_at->format('d/m/Y H:i') : '' }}
                                </small>
                            </div>
                            <div class="timeline-body">
                                <div class="mb-2 d-flex align-items-center justify-content-center stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $rate->rating)
                                            <span class="text-warning h4">★</span>
                                        @else
                                            <span class="text-secondary h4">★</span>
                                        @endif
                                    @endfor
                                    <span class="font-weight-bold ml-2">{{ $rate->rating }}/5</span>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>{{ __('admin.customer') }}:</strong> {{ $rate->user->name ?? '-' }}</p>
                                        <p><strong>{{ __('admin.rating') }}:</strong> {{ $rate->rating }}/5</p>
                                    </div>
                                    <div class="col-md-6">
                                        @if($rate->comment)
                                            <blockquote class="blockquote mb-0">
                                                <p class="mb-0">{{ $rate->comment }}</p>
                                                <footer class="blockquote-footer mt-1">{{ $rate->user->name ?? '-' }}</footer>
                                            </blockquote>
                                        @else
                                            <p class="text-muted">{{ __('admin.no_comment_provided') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

   {{-- Gift details (if present) --}}
    @if($order->order_type === 'gift' && (
        $order->reciver_name || $order->reciver_phone || $order->gift_address_name || $order->gift_latitude || $order->gift_longitude || $order->message
    ))
        <div class="card mt-3">
            <div class="card-header provider-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="feather icon-gift text-primary mr-2"></i>
                    {{ __('admin.gift_details') ?? 'Gift Details' }}
                </h5>
            </div>
            <div class="card-body provider-status-history">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{ __('admin.recipient_name') ?? 'Recipient' }}:</strong> {{ $order->reciver_name ?? '-' }}</p>
                        <p><strong>{{ __('admin.recipient_phone') ?? 'Phone' }}:</strong> {{ $order->reciver_phone ?? '-' }}</p>
                        <p><strong>{{ __('admin.gift_address_name') ?? 'Address name' }}:</strong> {{ $order->gift_address_name ?? '-' }}</p>
                        <p><strong>{{ __('admin.message') ?? 'Message' }}:</strong> {{ $order->message ?? '-' }}</p>
                        <p><strong>{{ __('admin.whatsapp') ?? 'WhatsApp' }}:</strong> {{ isset($order->whatsapp) ? ($order->whatsapp ? __('admin.yes') : __('admin.no')) : '-' }}</p>
                        <p><strong>{{ __('admin.hide_sender') ?? 'Hide sender' }}:</strong> {{ isset($order->hide_sender) ? ($order->hide_sender ? __('admin.yes') : __('admin.no')) : '-' }}</p>
                    </div>

                    <div class="col-md-6">
                        @if($order->gift_latitude && $order->gift_longitude)
                            {{-- <p><strong>{{ __('admin.gift_coordinates') ?? 'Coordinates' }}:</strong> {{ $order->gift_latitude }}, {{ $order->gift_longitude }}</p> --}}

                            {{-- <div id="gift-map" style="height:300px; width:100%; border:1px solid #e9ecef; border-radius:6px; margin-bottom:10px;"></div> --}}

                            <p class="mb-0">
                                <a href="https://maps.google.com/?q={{ $order->gift_latitude }},{{ $order->gift_longitude }}" target="_blank" class="btn btn-sm btn-outline-primary mr-2">
                                    <i class="feather icon-map-pin"></i> {{ __('admin.view_on_map') ?? 'View on map' }}
                                </a>
                                {{-- <a href="https://www.google.com/maps/dir/?api=1&destination={{ $order->gift_latitude }},{{ $order->gift_longitude }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="feather icon-navigation"></i> {{ __('admin.get_directions') ?? 'Get directions' }}
                                </a> --}}
                            </p>
                        @else
                            <p><strong>{{ __('admin.gift_latitude') ?? 'Latitude' }}:</strong> {{ $order->gift_latitude ?? '-' }}</p>
                            <p><strong>{{ __('admin.gift_longitude') ?? 'Longitude' }}:</strong> {{ $order->gift_longitude ?? '-' }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif


@if($order->status === 'problem' && ($order->problem || $order->notes))
    <div class="card mt-3">
        <div class="card-header provider-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
                <i class="feather icon-alert-triangle text-danger mr-2"></i>
                {{ __('admin.order_problem') }}
            </h5>
        </div>
        <div class="card-body provider-status-history">
            <div class="timeline">
                <div class="timeline-item mb-3">
                    <div class="timeline-marker">
                        <div class="timeline-marker-icon bg-danger">
                            <i class="feather icon-alert-triangle text-white"></i>
                        </div>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h6 class="timeline-title">
                                <span class="badge badge-danger">
                                    {{ __('admin.problem') }}
                                </span>
                            </h6>
                        </div>
                        <div class="timeline-body">
                            <p><strong>{{ __('admin.problem') }}:</strong> {{ !empty($order->notes) ? $order->notes : ($order->problem->problem ?? null) }}</p>
                            @php
                                // Try to get reporter info from status history (map_desc JSON)
                                $problemStatus = null;
                                if (isset($order->statusChanges) && $order->statusChanges) {
                                    $problemStatus = $order->statusChanges->firstWhere('status', 'problem');
                                }
                                $reporter = null;
                                if ($problemStatus && $problemStatus->map_desc) {
                                    $map = json_decode($problemStatus->map_desc, true);
                                    if (is_array($map)) {
                                        // New canonical format: ['reporter' => ['type'=>..., 'name'=>..., 'phone'=>...]]
                                        if (isset($map['reporter']) && is_array($map['reporter'])) {
                                            $reporter = $map['reporter'];
                                        } else {
                                            // Legacy/alternate format: { reported_by: 'delivery'|'client', name: '', phone: '' }
                                            $reportedBy = $map['reported_by'] ?? ($map['reportedBy'] ?? null);
                                            $name = $map['name'] ?? $map['reporter_name'] ?? null;
                                            $phone = $map['phone'] ?? $map['reporter_phone'] ?? null;
                                            if ($reportedBy || $name || $phone) {
                                                $reporter = [
                                                    'type' => $reportedBy,
                                                    'name' => $name,
                                                    'phone' => $phone,
                                                ];
                                            }
                                        }
                                    }
                                }

                                // Prepare display values with fallbacks
                                $rType = $reporter['type'] ?? null;
                                $rName = $reporter['name'] ?? null;
                                $rPhone = $reporter['phone'] ?? null;

                                // If no explicit reporter name, try sensible fallbacks
                                if (empty($rName)) {
                                    if (!empty($rType) && $rType === 'delivery') {
                                        $rName = $order->deliveryUser?->name ?? '-';
                                    } else {
                                        $rName = $order->user?->name ?? '-';
                                    }
                                }
                                if (empty($rPhone)) {
                                    if (!empty($rType) && $rType === 'delivery') {
                                        $rPhone = $order->deliveryUser?->phone ?? '-';
                                    } else {
                                        $rPhone = $order->user?->phone ?? '-';
                                    }
                                }
                                if (empty($rType)) {
                                    // If reporter type not recorded, show dash
                                    $rType = '-';
                                }
                                $typeLabel = $rType === 'delivery' ? 'مندوب التوصيل' : ($rType === 'client' ? 'العميل' : $rType);
                            @endphp

                            <div class="mt-2">
                                <strong>المبلغ عن المشكلة:</strong>
                                {{-- <div>النوع: <span class="font-weight-bold">{{ $typeLabel }}</span></div> --}}
                                <div>الاسم: <span class="font-weight-bold">{{ $rName }}</span></div>
                                <div>الهاتف: <span class="font-weight-bold">{{ $rPhone }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if($order->status === 'cancelled' && ($order->cancelReason || $order->notes))
    <div class="card mt-3">
        <div class="card-header provider-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
                <i class="feather icon-x text-warning mr-2"></i>
                {{ __('admin.order_cancel_reason') }}
            </h5>
        </div>
        <div class="card-body provider-status-history">
            <div class="timeline">
                <div class="timeline-item mb-3">
                    <div class="timeline-marker">
                        <div class="timeline-marker-icon bg-warning">
                            <i class="feather icon-x text-white"></i>
                        </div>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h6 class="timeline-title">
                                <span class="badge badge-warning">
                                    {{ __('admin.cancel_reason') }}
                                </span>
                            </h6>
                        </div>
                        <div class="timeline-body">
                            <p><strong>{{ __('admin.reason') }}:</strong> {{ $order->cancelReason ? $order->cancelReason->reason : $order->notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif





   
    <!-- Action Buttons -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body text-center py-4">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-lg px-4">
                <i class="feather icon-arrow-left mr-2"></i> {{ __('admin.back_to_orders') }}
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
            fetch(`/admin/orders/${orderId}/status`, {
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




// Order status change functionality
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.change-order-status').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const orderId = this.dataset.orderId;
            const newStatus = this.dataset.status;
            const statusLabel = this.textContent.trim();
            
            Swal.fire({
                title: '{{ __("admin.confirm_status_change") }}',
                text: `{{ __("admin.are_you_sure_change_order_status_to") }} "${statusLabel}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __("admin.yes_change") }}',
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

                    // Make AJAX request to change order status
                    fetch(`/admin/orders/${orderId}/change-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ 
                            status: newStatus,
                            notes: '' // Optional notes field
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '{{ __("admin.success") }}',
                                text: data.message || '{{ __("admin.order_status_updated_successfully") }}',
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
                        console.error('Error:', error);
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