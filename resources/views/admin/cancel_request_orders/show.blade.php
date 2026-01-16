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
    font-size: 16px;
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
<!-- order header -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white py-4">
        <div class="d-flex flex-wrap align-items-start justify-content-between w-100">
            <!-- order info + button (right in rtl) -->
            <div>
                <h4 class="card-title mb-2 text-white">
                    <i class="feather icon-file-text mr-2 text-white"></i>
                    <span class="text-white">{{ __('admin.order_details') }}</span>
                    <span class="text-warning font-weight-bold">{{ $order->order_number }}</span>
                </h4>
                <small class="text-white-75 d-block mb-3">
                    {{ __('admin.created_at') }}: {{ $order->created_at->format('d/m/y h:i') }}
                </small>
                <a href="{{ route('admin.reports.payment-report.download-invoice', $order->id) }}" class="btn btn-light btn-sm">
                    <i class="feather icon-download mr-1"></i> {{ __('admin.download_invoice') }}
                </a>
            </div>
    
            <!-- status badge (left in rtl) -->
            {{-- <div class="text-left mt-2 mt-md-0">
                @php
                   
                        $statuscolors = [
                            'pending' => 'warning',
                            'processing' => 'info',
                            'ready' => 'info',
                            'delivering' => 'primary',
                            'waiting_pickup' => 'warning',
                            'completed' => 'success',
                            'problem' => 'danger',
                            'cancelled' => 'danger',
                        ];
                    $color = $statuscolors[$order->status] ?? 'secondary';

                    $statusicons = [
                      
                            'pending' => 'clock',
                            'processing' => 'refresh-cw',
                            'ready' => 'package',
                            'delivering' => 'truck',
                            'waiting_pickup' => 'user-check',
                            'completed' => 'check-circle-2',
                            'problem' => 'alert-triangle',
                            'cancelled' => 'x-circle',
                    ];
                    $icon = $statusicons[$order->status] ?? 'circle';
                
                @endphp
                   @if($order->status === 'cancelled' && ($order->cancelReason || $order->notes))
                            <div class="mt-2">
                                <small class="text-white-75 d-block">
                                    <i class="feather icon-info mr-1" style="font-size: 0.7rem;"></i>
                                    {{ __('admin.cancel_reason') }}:
                                </small>
                                @php
                                    $reasonText = $order->cancelReason ? $order->cancelReason->reason : $order->notes;
                                @endphp
                                <small class="text-white-50 font-italic">
                                    "{{ $reasonText }}"
                                </small>
                            </div>
                        @endif
                
                <!-- status change dropdown -->
                <div class="d-flex align-items-center">
                
                    
                    <!-- current status badge -->
                    <span class="badge badge-{{ $color }} px-3 py-2" style="font-size: 0.875rem;">
                        <i class="feather icon-{{ $icon }} mr-1" style="font-size: 0.8rem;"></i>
                        {{ __('admin.' . $order->status) }}
                    </span>
                </div>
    
                @if($order->status === 'cancelled' && ($order->cancelreason || $order->notes))
                    <div class="mt-2">
                        <small class="text-white-75 d-block">
                            <i class="feather icon-info mr-1" style="font-size: 0.7rem;"></i>
                            {{ __('admin.cancel_reason') }}:
                        </small>
                        @php
                            $reasontext = $order->cancelreason ? $order->cancelreason->reason : $order->notes;
                        @endphp
                        <small class="text-white-50 font-italic">
                            "{{ $reasontext }}"
                        </small>
                    </div>
                @endif
            </div> --}}
        </div>
    </div>
      @if($order->status === 'request_cancel')
        <div class="card-footer bg-warning border-top">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <i class="feather icon-alert-triangle text-dark mr-2"></i>
                        <span class="text-dark font-weight-bold">
                            {{ __('admin.cancel_request_pending_review') }}
                        </span>
                    </div>
                    <small class="text-dark">
                        {{ __('admin.cancel_reason') }}:
                        @if($order->cancelReason)
                            @php
                                $reasonText = $order->cancelReason->reason;
                            @endphp
                            {{ $reasonText }}
                        @elseif(!empty($order->notes))
                            {{ $order->notes }}
                        @else
                            {{ __('admin.no_reason_provided') }}
                        @endif
                    </small>
                </div>
                <div class="col-md-4 text-right">
                    @php
                        $cancelReasonText = __('admin.no_reason_provided');
                        if($order->cancelReason) {
                            $cancelReasonText = $order->cancelReason->reason;
                        } elseif (!empty($order->notes)) {
                            $cancelReasonText = $order->notes;
                        }
                    @endphp
                    <button class="btn btn-success btn-sm accept-cancel-request"
                            data-order-id="{{ $order->id }}"
                            data-order-total="{{ $order->total ?? $order->final_total }}"
                            data-cancel-reason="{{ $cancelReasonText }}">
                        <i class="feather icon-check mr-1"></i>
                        {{ __('admin.accept') }}
                    </button>
                    <button class="btn btn-danger btn-sm reject-cancel-request ml-1"
                            data-order-id="{{ $order->id }}">
                        <i class="feather icon-x mr-1"></i>
                        {{ __('admin.reject') }}
                    </button>
                </div>
            </div>
        </div>
        @endif


</div>


    <!-- Quick Stats -->
    @include('admin.orders.partials.stats')


    <!-- Basic Information & Customer Information -->
    @include('admin.orders.partials.basic-info')



    <!-- Address Information (if exists) -->
  


    <!-- Financial Breakdown & Payment Information -->
    @include('admin.orders.partials.financial-payment')



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

    <!-- Order Rating -->
    @php
        // Normalize rate to a single model: some relations return a Collection, others a single model
        $rateModel = null;
        if ($order->rate) {
            if ($order->rate instanceof \Illuminate\Support\Collection) {
                $rateModel = $order->rate->first();
            } else {
                $rateModel = $order->rate;
            }
        }
    @endphp
    @if($rateModel)
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="feather icon-star mr-2"></i>
                {{ __('admin.order_rating') }}
            </h5>
            <div class="header-elements">
                @php
                    $q = $rateModel->quality_rate ?? 0;
                    $s = $rateModel->service_rate ?? 0;
                    $t = $rateModel->timing_rate ?? 0;
                    $averageRating = round((($q + $s + $t) / 3), 1);
                    $ratingColor = $averageRating >= 4 ? 'success' : ($averageRating >= 3 ? 'warning' : 'danger');
                @endphp
                <span class="badge badge-{{ $ratingColor }} px-2 py-1">
                    <i class="feather icon-star mr-1"></i>
                    {{ $averageRating }}/5
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="rating-details">

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

// Cancel request handling
document.addEventListener('DOMContentLoaded', function() {
    // Accept Cancel Request
    document.querySelectorAll('.accept-cancel-request').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            const orderTotal = this.dataset.orderTotal;
            const cancelReason = this.dataset.cancelReason || '{{ __("admin.no_reason_provided") }}';

            Swal.fire({
                title: '{{ __("admin.accept_cancel_request") }}',
                html: `
                    <div class="text-left">
                        <p>{{ __("admin.are_you_sure_accept_cancel_request") }}</p>
                        <div class="form-group mt-3">
                            <label for="cancel_fees">{{ __("admin.cancel_fees") }} ({{ __("admin.sar") }}) *</label>
                            <input type="number" id="cancel_fees" class="form-control" min="0" max="${orderTotal}" step="0.01" value="{{ $cancellationFeeAmount }}" required>
                            <small class="text-muted">{{ __("admin.total") }}: ${orderTotal} {{ __("admin.sar") }}</small>
                        </div>
                    </div>
                `,
                type: 'question',
                showCancelButton: true,
                confirmButtonText: '{{ __("admin.accept") }}',
                cancelButtonText: '{{ __("admin.cancel") }}',
                confirmButtonColor: '#28a745',
                preConfirm: () => {
                    const cancelFees = document.getElementById('cancel_fees').value;

                    if (!cancelFees || cancelFees === '' || isNaN(cancelFees)) {
                        Swal.showValidationMessage('{{ __("admin.cancel_fees_required") }}');
                        return false;
                    }

                    if (parseFloat(cancelFees) < 0) {
                        Swal.showValidationMessage('{{ __("admin.cancel_fees_must_be_positive") }}');
                        return false;
                    }

                    if (parseFloat(cancelFees) > parseFloat(orderTotal)) {
                        Swal.showValidationMessage('{{ __("admin.cancel_fees_cannot_exceed_total") }}');
                        return false;
                    }

                    return {
                        cancel_fees: parseFloat(cancelFees)
                    };
                }
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

                    fetch(`/admin/cancel-request-orders/${orderId}/accept`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            cancel_fees: result.value.cancel_fees
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '{{ __("admin.success") }}',
                                html: `
                                    <p>${data.message}</p>
                                    <div class="mt-2">
                                        <strong>{{ __("admin.refund_amount") }}:</strong> ${data.data.refund_amount} {{ __("admin.sar") }}<br>
                                        <strong>{{ __("admin.cancel_fees") }}:</strong> ${data.data.cancel_fees} {{ __("admin.sar") }}
                                    </div>
                                `,
                                type: 'success',
                                confirmButtonText: '{{ __("admin.ok") }}'
                            }).then(() => {
                                window.location.href = `/admin/orders/${orderId}/show`;
                            });
                        } else {
                            Swal.fire({
                                title: '{{ __("admin.error") }}',
                                text: data.message || '{{ __("admin.error_occurred") }}',
                                type: 'error',
                                confirmButtonText: '{{ __("admin.ok") }}'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: '{{ __("admin.error") }}',
                            text: '{{ __("admin.error_occurred") }}',
                            type: 'error',
                            confirmButtonText: '{{ __("admin.ok") }}'
                        });
                    });
                }
            });
        });
    });

    // Reject Cancel Request
    document.querySelectorAll('.reject-cancel-request').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;

            Swal.fire({
                title: '{{ __("admin.reject_cancel_request") }}',
                text: '{{ __("admin.are_you_sure_reject_cancel_request") }}',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __("admin.reject") }}',
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

                    fetch(`/admin/cancel-request-orders/${orderId}/reject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ reason: 'Cancel request rejected by admin' })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '{{ __("admin.success") }}',
                                text: data.message,
                                type: 'success',
                                confirmButtonText: '{{ __("admin.ok") }}'
                            }).then(() => {
                                window.location.href = `/admin/orders/${orderId}/show`;
                            });
                        } else {
                            Swal.fire({
                                title: '{{ __("admin.error") }}',
                                text: data.message || '{{ __("admin.error_occurred") }}',
                                type: 'error',
                                confirmButtonText: '{{ __("admin.ok") }}'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: '{{ __("admin.error") }}',
                            text: '{{ __("admin.error_occurred") }}',
                            type: 'error',
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