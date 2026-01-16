@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/index_page.css') }}">
    <style>
        .nav-tabs .nav-link {
            border: none;
            border-radius: 0;
            color: #626262;
            font-weight: 500;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }
        .nav-tabs .nav-link.active {
            background-color: transparent;
            border-bottom: 3px solid #1853db;
            color: #1853db;
            font-weight: 600;
        }
        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: #1853db;
        }
        .tab-content {
            padding-top: 20px;
        }
        .orders-tab-content {
            min-height: 600px;
        }
    </style>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <h4 class="card-title">
            <i class="feather icon-shopping-cart mr-2"></i>
            {{ __('admin.orders_management') }}
        </h4>
        <p class="card-text">{{ __('admin.manage_all_orders_from_one_place') }}</p>
    </div>
    <div class="card-body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="orderTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="all-orders-tab" data-toggle="tab" href="#all-orders" role="tab" aria-controls="all-orders" aria-selected="true">
                    <i class="feather icon-list mr-2"></i>
                    {{ __('admin.all_orders') }}
                    <span class="badge badge-primary ml-2" id="all-orders-count">-</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pending-orders-tab" data-toggle="tab" href="#pending-orders" role="tab" aria-controls="pending-orders" aria-selected="false">
                    <i class="feather icon-clock mr-2"></i>
                    {{ __('admin.pending_orders') }}
                    <span class="badge badge-warning ml-2" id="pending-count">-</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="new-orders-tab" data-toggle="tab" href="#new-orders" role="tab" aria-controls="new-orders" aria-selected="false">
                    <i class="feather icon-user-check mr-2"></i>
                    {{ __('admin.new_orders') }}
                    <span class="badge badge-info ml-2" id="new-count">-</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="confirmed-orders-tab" data-toggle="tab" href="#confirmed-orders" role="tab" aria-controls="confirmed-orders" aria-selected="false">
                    <i class="feather icon-check mr-2"></i>
                    {{ __('admin.confirmed_orders') }}
                    <span class="badge badge-primary ml-2" id="confirmed-count">-</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="delivered-orders-tab" data-toggle="tab" href="#delivered-orders" role="tab" aria-controls="delivered-orders" aria-selected="false">
                    <i class="feather icon-truck mr-2"></i>
                    {{ __('admin.delivered_orders') }}
                    <span class="badge badge-success ml-2" id="delivered-count">-</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="cancelled-orders-tab" data-toggle="tab" href="#cancelled-orders" role="tab" aria-controls="cancelled-orders" aria-selected="false">
                    <i class="feather icon-x-circle mr-2"></i>
                    {{ __('admin.cancelled_orders') }}
                    <span class="badge badge-danger ml-2" id="cancelled-count">-</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="refund-orders-tab" data-toggle="tab" href="#refund-orders" role="tab" aria-controls="refund-orders" aria-selected="false">
                    <i class="feather icon-rotate-ccw mr-2"></i>
                    {{ __('admin.refund_orders') }}
                    <span class="badge badge-secondary ml-2" id="refunded-count">-</span>
                </a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content orders-tab-content" id="orderTabsContent">
            <!-- All Orders Tab -->
            <div class="tab-pane fade show active" id="all-orders" role="tabpanel" aria-labelledby="all-orders-tab">
                <div class="mt-3">
                    <iframe src="{{ route('admin.orders.index') }}" width="100%" height="700" frameborder="0" style="border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"></iframe>
                </div>
            </div>

            <!-- Pending Orders Tab -->
            <div class="tab-pane fade" id="pending-orders" role="tabpanel" aria-labelledby="pending-orders-tab">
                <div class="mt-3">
                    <iframe src="{{ route('admin.orders.byStatus', 'pending') }}" width="100%" height="700" frameborder="0" style="border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"></iframe>
                </div>
            </div>

            <!-- New Orders Tab -->
            <div class="tab-pane fade" id="new-orders" role="tabpanel" aria-labelledby="new-orders-tab">
                <div class="mt-3">
                    <iframe src="{{ route('admin.orders.byStatus', 'new') }}" width="100%" height="700" frameborder="0" style="border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"></iframe>
                </div>
            </div>

            <!-- Confirmed Orders Tab -->
            <div class="tab-pane fade" id="confirmed-orders" role="tabpanel" aria-labelledby="confirmed-orders-tab">
                <div class="mt-3">
                    <iframe src="{{ route('admin.orders.byStatus', 'confirmed') }}" width="100%" height="700" frameborder="0" style="border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"></iframe>
                </div>
            </div>

            <!-- Delivered Orders Tab -->
            <div class="tab-pane fade" id="delivered-orders" role="tabpanel" aria-labelledby="delivered-orders-tab">
                <div class="mt-3">
                    <iframe src="{{ route('admin.orders.byStatus', 'delivered') }}" width="100%" height="700" frameborder="0" style="border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"></iframe>
                </div>
            </div>

            <!-- Cancelled Orders Tab -->
            <div class="tab-pane fade" id="cancelled-orders" role="tabpanel" aria-labelledby="cancelled-orders-tab">
                <div class="mt-3">
                    <iframe src="{{ route('admin.orders.byStatus', 'cancelled') }}" width="100%" height="700" frameborder="0" style="border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"></iframe>
                </div>
            </div>

            <!-- Refund Orders Tab -->
            <div class="tab-pane fade" id="refund-orders" role="tabpanel" aria-labelledby="refund-orders-tab">
                <div class="mt-3">
                    <iframe src="{{ route('admin.orders.byStatus', 'refunded') }}" width="100%" height="700" frameborder="0" style="border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"></iframe>
                </div>
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
            // Load order counts on page load
            loadOrderCounts();

            // Refresh counts when tab is switched
            $('#orderTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                loadOrderCounts();
            });

            // Auto-refresh counts every 30 seconds
            setInterval(loadOrderCounts, 30000);
        });

        function loadOrderCounts() {
            $.ajax({
                url: '{{ route("admin.orders.counts") }}',
                method: 'GET',
                success: function(data) {
                    $('#all-orders-count').text(data.all_orders || 0);
                    $('#pending-count').text(data.pending || 0);
                    $('#new-count').text(data.new || 0);
                    $('#confirmed-count').text(data.confirmed || 0);
                    $('#delivered-count').text(data.delivered || 0);
                    $('#cancelled-count').text(data.cancelled || 0);
                    $('#refunded-count').text(data.refunded || 0);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load order counts:', error);
                    // Set all counts to '-' on error
                    $('#all-orders-count, #pending-count, #new-count, #confirmed-count, #delivered-count, #cancelled-count, #refunded-count').text('-');
                }
            });
        }
    </script>
@endsection
