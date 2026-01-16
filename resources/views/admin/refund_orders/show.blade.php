@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center mb-1">
                <h4 class="card-title mb-0">
                    <i class="feather icon-rotate-ccw mr-2"></i>
                    {{ __('admin.refund_request_details') }}
                </h4>
                <div class="card-tools">
                    <a href="{{ route('admin.refund_orders.index') }}" class="btn btn-outline-secondary">
                        <i class="feather icon-arrow-left"></i> {{ __('admin.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="app-user-view">
        <!-- Top Row: Order Details + Action Buttons -->
        <div class="row">
    <!-- Order Information - Left Side -->
    <div class="col-lg-8 col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ __('admin.order_details') }}</h5>
                <div class="card-tools">
                    @if($order->is_refund && $order->originalOrder)
                        <!-- This is a refund order, link to original -->
                        <a href="{{ route('admin.orders.show', $order->id) }}"
                           class="btn btn-outline-info btn-sm" target="_blank">
                            <i class="feather icon-external-link"></i> {{ __('admin.view_original_order') }}
                        </a>
                    @else
                        <!-- This is original order with refund request -->
                        <a href="{{ route('admin.orders.show', $order->id) }}"
                           class="btn btn-outline-info btn-sm" target="_blank">
                            <i class="feather icon-external-link"></i> {{ __('admin.view_full_order') }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Refund Order Badge -->
                @if($order->is_refund)
                    <div class="alert alert-info mb-3">
                        <i class="feather icon-info mr-2"></i>
                        <strong>{{ __('admin.refund_order') }}</strong> -
                        {{ __('admin.this_is_refund_order_for') }}
                        <a href="{{ route('admin.orders.show', $order->id) }}" target="_blank">
                            #{{ $order->originalOrder->order_number ?? '' }}
                        </a>
                    </div>
                @endif

                <!-- Customer Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">{{ __('admin.customer_information') }}</h6>
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar mr-2">
                                <img src="{{ $order->user->image ?? asset('admin/app-assets/images/portrait/small/avatar-s-11.jpg') }}"
                                     alt="Avatar" height="40" width="40" class="rounded-circle">
                            </div>
                            <div>
                                <p class="mb-0 font-weight-bold">{{ $order->user->name }}</p>
                                <small class="text-muted">{{ $order->user->phone }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">{{ __('admin.order_information') }}</h6>
                        <p class="mb-1">
                            <strong>{{ __('admin.order_number') }}:</strong>
                            #{{ $order->order_number }}
                        </p>
                        @if($order->is_refund && $order->refund_number)
                            <p class="mb-1">
                                <strong>{{ __('admin.refund_number') }}:</strong>
                                <span class="text-primary">#{{ $order->refund_number }}</span>
                            </p>
                        @endif
                        <p class="mb-1">
                            <strong>{{ __('admin.order_date') }}:</strong>
                            {{ $order->created_at->format('Y-m-d H:i') }}
                        </p>
                        <p class="mb-1">
                            <strong>{{ __('admin.order_total') }}:</strong>
                            <span class="text-success font-weight-bold">
                                {{ number_format($order->total, 2) }} {{ __('admin.sar') }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Order Items -->
                <h6 class="font-weight-bold mb-3">
                    @if($order->refund_status === 'refunded')
                        {{ __('admin.refunded_items') }}
                    @else
                        {{ __('admin.order_items') }}
                    @endif
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('admin.item') }}</th>
                                <th>{{ __('admin.quantity') }}</th>
                                <th>{{ __('admin.price') }}</th>
                                <th>{{ __('admin.total') }}</th>
                                <th>{{ __('admin.refund_status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // If order is refunded, only show refunded items
                                $itemsToShow = $order->refund_status === 'refunded'
                                    ? $order->items->where('is_refunded', true)
                                    : $order->items;
                            @endphp

                            @foreach($itemsToShow as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->image_url)
                                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                                     class="mr-2 rounded" height="30" width="30">
                                            @endif
                                            <span>{{ $item->product->name ?? $item->item_name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($order->refund_status === 'refunded')
                                            {{ $item->refund_quantity }}
                                        @else
                                            {{ $item->quantity }}
                                            @if($item->refund_quantity > 0)
                                                <br><small class="text-danger">({{ $item->refund_quantity }} {{ __('admin.refunded') }})</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->price, 2) }} {{ __('admin.sar') }}</td>
                                    <td>
                                        @if($order->refund_status === 'refunded')
                                            {{ number_format($item->refund_amount, 2) }} {{ __('admin.sar') }}
                                        @else
                                            {{ number_format($item->total, 2) }} {{ __('admin.sar') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->is_refunded)
                                            <span class="badge badge-success">{{ __('admin.refunded') }}</span>
                                        @elseif($item->refund_quantity > 0)
                                            <span class="badge badge-info">{{ __('admin.partially_refunded') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('admin.not_refunded') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons - Right Side -->
    <div class="col-lg-4 col-md-5">
        @if($order->refund_status === 'request_refund')
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="feather icon-settings mr-1"></i>
                        {{ __('admin.refund_actions') }}
                    </h5>
                </div>
                <div class="card-body text-center py-3">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success btn-block mb-2" id="acceptRefundBtn">
                            <i class="feather icon-check mr-1"></i>
                            {{ __('admin.accept_refund_request') }}
                        </button>

                        <button type="button" class="btn btn-danger btn-block" id="refuseRefundBtn">
                            <i class="feather icon-x mr-1"></i>
                            {{ __('admin.refuse_refund_request') }}
                        </button>
                    </div>
                </div>
            </div>
        @elseif($order->is_refund)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="feather icon-info mr-1"></i>
                        {{ __('admin.refund_order_status') }}
                    </h5>
                </div>
                <div class="card-body text-center py-3">
                  
                    <p class="mb-2">{{ __('admin.refund_status') }}:</p>
                    <span class="badge badge-lg badge-{{ $order->refund_status === 'refunded' ? 'success' : ($order->refund_status === 'request_refund' ? 'warning' : 'info') }}">
                        {{ __(ucfirst($order->refund_status ?? '')) }}
                    </span>
                    @if($order->delivery)
                        <hr>
                        <p class="mb-1"><strong>{{ __('admin.delivery_person') }}:</strong></p>
                        <p class="mb-0">{{ $order->delivery->name }}</p>
                        <small class="text-muted">{{ $order->delivery->phone }}</small>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Refund Images Section -->
@if($order->getMedia('refund_images')->count() > 0)
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="feather icon-image mr-1"></i>{{ __('admin.refund_images') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($order->getMedia('refund_images') as $media)
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                                <div class="card border">
                                    @if(in_array($media->mime_type, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']))
                                        <!-- Image -->
                                        <img src="{{ $media->getUrl() }}"
                                             alt="Refund Image"
                                             class="card-img-top"
                                             style="height: 120px; object-fit: cover; cursor: pointer;"
                                             onclick="showImageModal('{{ $media->getUrl() }}', '{{ $media->name }}')">
                                    @else
                                        <!-- Video -->
                                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light"
                                             style="height: 120px; cursor: pointer;"
                                             onclick="showVideoModal('{{ $media->getUrl() }}', '{{ $media->name }}')">
                                            <i class="feather icon-play-circle text-primary" style="font-size: 2rem;"></i>
                                        </div>
                                    @endif
                                    <div class="card-body p-2">
                                        <small class="text-muted d-block text-truncate">{{ $media->name }}</small>
                                        <small class="text-muted">{{ $media->human_readable_size }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Bottom Row: Refund Information and Cost Details -->
<div class="row mt-3">
    <!-- Refund Information - Left Side -->
    <div class="col-lg-6 col-md-6">
        <!-- Refund Information Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="feather icon-rotate-ccw mr-1"></i>{{ __('admin.refund_information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($order->refund_number)
                        <div class="col-12 mb-2">
                            <small class="text-muted">{{ __('admin.refund_number') }}</small>
                            <p class="text-primary font-weight-bold mb-0">{{ $order->refund_number }}</p>
                        </div>
                    @endif
                    <div class="col-6 mb-2">
                        <small class="text-muted">{{ __('admin.status') }}</small>
                        <p class="mb-0">
                            <span class="badge badge-{{ $order->refund_status === 'request_refund' ? 'warning' : ($order->refund_status === 'refunded' ? 'success' : 'info') }}">
                                {{ __(ucfirst($order->refund_status ?? '')) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-6 mb-2">
                        <small class="text-muted">{{ __('admin.refund_amount') }}</small>
                        <p class="mb-0">
                            @if($order->refund_amount)
                                <span class="text-success font-weight-bold">
                                    {{ number_format($order->refund_amount, 2) }} {{ __('admin.sar') }}
                                </span>
                            @else
                                <span class="text-muted">{{ __('admin.not_set') }}</span>
                            @endif
                        </p>
                    </div>
                    @if($order->refund_requested_at)
                        <div class="col-12 mb-2">
                            <small class="text-muted">{{ __('admin.request_date') }}</small>
                            <p class="mb-0">{{ $order->refund_requested_at->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif
                    @if($order->refundReason)
                        <div class="col-12 mb-2">
                            <small class="text-muted">{{ __('admin.refund_reason') }}</small>
                            <p class="text-dark mb-0">{{ $order->refundReason->reason }}</p>
                            @if($order->refund_reason_text)
                                <small class="text-muted">{{ $order->refund_reason_text }}</small>
                            @endif
                        </div>
                    @endif
                    @if($order->refund_approved_at)
                        <div class="col-12 mb-2">
                            <small class="text-muted">{{ __('admin.approved_at') }}</small>
                            <p class="mb-0">{{ $order->refund_approved_at->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif
                    @if($order->refund_rejected_at)
                        <div class="col-12 mb-2">
                            <small class="text-muted">{{ __('admin.rejected_at') }}</small>
                            <p class="mb-0 text-danger">{{ $order->refund_rejected_at->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cost Details - Right Side -->
    <div class="col-lg-6 col-md-6">
        <!-- Cost Details Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="feather icon-dollar-sign mr-1"></i>{{ __('admin.cost_details') }}
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                        @if($order->is_refund && $order->originalOrder)
                            <!-- Show original order cost details for refund orders -->
                            @foreach($order->originalOrder->cost_details_arabic as $detail)
                                <tr>
                                    <td class="font-weight-bold">{{ $detail['label'] }}</td>
                                    <td class="text-right">{{ $detail['value'] }}</td>
                                </tr>
                            @endforeach
                            <tr class="border-top bg-light">
                                <td class="font-weight-bold text-primary">{{ __('admin.original_order_total') }}</td>
                                <td class="text-right text-success font-weight-bold">
                                    {{ number_format($order->originalOrder->total ?? 0, 2) }} {{ __('admin.sar') }}
                                </td>
                            </tr>
                            <tr class="border-top bg-warning">
                                <td class="font-weight-bold text-danger">{{ __('admin.refund_amount') }}</td>
                                <td class="text-right text-danger font-weight-bold">
                                    -{{ number_format($order->refund_amount ?? 0, 2) }} {{ __('admin.sar') }}
                                </td>
                            </tr>
                        @else
                            <!-- Show current order cost details -->
                            @if(method_exists($order, 'cost_details_arabic'))
                                @foreach($order->cost_details_arabic as $detail)
                                    <tr>
                                        <td class="font-weight-bold">{{ $detail['label'] }}</td>
                                        <td class="text-right">{{ $detail['value'] }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr class="border-top bg-light">
                                <td class="font-weight-bold text-primary">{{ __('admin.final_total') }}</td>
                                <td class="text-right text-success font-weight-bold">
                                    {{ number_format($order->total ?? 0, 2) }} {{ __('admin.sar') }}
                                </td>
                            </tr>
                            @if($order->refund_amount > 0)
                                <tr class="border-top bg-warning">
                                    <td class="font-weight-bold text-danger">{{ __('admin.total_refunded') }}</td>
                                    <td class="text-right text-danger font-weight-bold">
                                        -{{ number_format($order->refund_amount, 2) }} {{ __('admin.sar') }}
                                    </td>
                                </tr>
                            @endif
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
    </section>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle">{{ __('admin.refund_image') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Refund Image" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoModalTitle">{{ __('admin.refund_video') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <video id="modalVideo" controls class="w-100" style="max-height: 400px;">
                    <source id="videoSource" src="" type="video/mp4">
                    {{ __('admin.video_not_supported') }}
                </video>
            </div>
        </div>
    </div>
</div>

<!-- Accept Refund Modal -->
<div class="modal fade" id="acceptRefundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="feather icon-check mr-2"></i>
                    {{ __('admin.accept_refund_request') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="acceptRefundForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="refund_amount" class="font-weight-bold">
                                    <i class="feather icon-dollar-sign mr-1"></i>
                                    {{ __('admin.refund_amount') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-lg" id="refund_amount" name="refund_amount"
                                           step="0.01" min="0" value="{{ $order->refund_amount ?? $order->total }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ __('admin.sar') }}</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">{{ __('admin.enter_amount_to_refund') }}</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="delivery_id" class="font-weight-bold">
                                    <i class="feather icon-truck mr-1"></i>
                                    {{ __('admin.assign_delivery_person') }}
                                </label>
                                <select class="form-control form-control-lg" id="delivery_id" name="delivery_id" required>
                                    <option value="">{{ __('admin.select_delivery_person') }}</option>
                                    @foreach($deliveryPersons as $delivery)
                                        <option value="{{ $delivery->id }}">
                                            {{ $delivery->name }} - {{ $delivery->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">
                        <i class="feather icon-x mr-2"></i>{{ __('admin.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="feather icon-check mr-2"></i>{{ __('admin.accept_refund') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Refuse Refund Modal -->
<div class="modal fade" id="refuseRefundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="feather icon-x mr-2"></i>
                    {{ __('admin.refuse_refund_request') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="feather icon-alert-triangle text-warning" style="font-size: 3rem;"></i>
                </div>
                <h6 class="mb-3">{{ __('admin.refuse_refund_confirmation') }}</h6>
                <p class="text-muted">{{ __('admin.this_action_cannot_be_undone') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">
                    <i class="feather icon-arrow-left mr-2"></i>{{ __('admin.cancel') }}
                </button>
                <button type="button" class="btn btn-danger btn-lg" id="confirmRefuseBtn">
                    <i class="feather icon-x mr-2"></i>{{ __('admin.yes_refuse') }}
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
    <script src="{{ asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js') }}"></script>
    
    <script>
    $(document).ready(function() {
        // Accept refund button click
        $('#acceptRefundBtn').click(function() {
            $('#acceptRefundModal').modal('show');
        });
        
        // Accept refund form submission
        $('#acceptRefundForm').submit(function(e) {
            e.preventDefault();
            
            const formData = {
                refund_amount: $('#refund_amount').val(),
                delivery_id: $('#delivery_id').val(),
                _token: '{{ csrf_token() }}'
            };
            
            $.ajax({
                url: '{{ route("admin.refund_orders.accept", $order->id) }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("admin.success") }}',
                            text: response.message,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("admin.error") }}',
                            text: response.message
                        });
                    }
                    $('#acceptRefundModal').modal('hide');
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("admin.error") }}',
                        text: response.message || '{{ __("admin.something_went_wrong") }}'
                    });
                    $('#acceptRefundModal').modal('hide');
                }
            });
        });
        
        // Refuse refund button click
        $('#refuseRefundBtn').click(function() {
            $('#refuseRefundModal').modal('show');
        });

        // Confirm refuse refund
        $('#confirmRefuseBtn').click(function() {
            $.ajax({
                url: '{{ route("admin.refund_orders.refuse", $order->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#refuseRefundModal').modal('hide');
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("admin.success") }}',
                            text: response.message,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("admin.error") }}',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    $('#refuseRefundModal').modal('hide');
                    const response = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("admin.error") }}',
                        text: response.message || '{{ __("admin.something_went_wrong") }}'
                    });
                }
            });
        });

        // Image and Video Modal Functions
        window.showImageModal = function(imageUrl, imageName) {
            $('#modalImage').attr('src', imageUrl);
            $('#imageModalTitle').text(imageName || '{{ __("admin.refund_image") }}');
            $('#imageModal').modal('show');
        };

        window.showVideoModal = function(videoUrl, videoName) {
            $('#videoSource').attr('src', videoUrl);
            $('#modalVideo')[0].load(); // Reload video element
            $('#videoModalTitle').text(videoName || '{{ __("admin.refund_video") }}');
            $('#videoModal').modal('show');
        };

        // Pause video when modal is closed
        $('#videoModal').on('hidden.bs.modal', function () {
            $('#modalVideo')[0].pause();
        });
    });
    </script>
@endsection
