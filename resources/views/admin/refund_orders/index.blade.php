@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/index_page.css') }}">
@endsection

@section('content')

<x-admin.table
    datefilter="true"
    order="true"
    extrabuttons="false"
    :searchArray="[
    ]"
>
    <x-slot name="extrabuttonsdiv">
        {{-- Additional buttons can be added here --}}
    </x-slot>

    <x-slot name="tableContent">
        <div class="table_content_append card">
            {{-- table content will appends here  --}}
        </div>
    </x-slot>
</x-admin.table>

@endsection

<!-- Accept Refund Modal -->
<div class="modal fade" id="acceptRefundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.accept_refund_request') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="acceptRefundForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="refund_amount">{{ __('admin.refund_amount') }}</label>
                        <input type="number" class="form-control" id="refund_amount" name="refund_amount"
                               step="0.01" min="0" required>
                        <small class="form-text text-muted">{{ __('admin.enter_amount_to_refund') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="delivery_id">{{ __('admin.assign_delivery_person') }}</label>
                        <select class="form-control" id="delivery_id" name="delivery_id" required>
                            <option value="">{{ __('admin.select_delivery_person') }}</option>
                            @foreach(\App\Models\User::where('type', 'delivery')->get() as $delivery)
                                <option value="{{ $delivery->id }}">{{ $delivery->name }} - {{ $delivery->phone }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin.cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('admin.accept_refund') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Refuse Refund Modal -->
<div class="modal fade" id="refuseRefundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.refuse_refund_request') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{ __('admin.refuse_refund_confirmation') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmRefuseBtn">{{ __('admin.yes_refuse') }}</button>
            </div>
        </div>
    </div>
</div>

@section('js')
    <script src="{{ asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js') }}"></script>
       @include('admin.shared.deleteAll')
    @include('admin.shared.deleteOne')
    @include('admin.shared.filter_js', ['index_route' => url('admin/refund-orders')])

    <script>
    $(document).ready(function() {
        let currentRefundId = null;

        // Accept refund button click
        $(document).on('click', '.accept-refund-btn', function() {
            currentRefundId = $(this).data('id');
            const orderTotal = $(this).data('order-total');

            // Set the order total as default refund amount
            $('#refund_amount').val(orderTotal);

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
                url: `/admin/refund-orders/${currentRefundId}/accept`,
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
        let currentRefuseRefundId = null;
        $(document).on('click', '.refuse-refund-btn', function() {
            currentRefuseRefundId = $(this).data('id');
            $('#refuseRefundModal').modal('show');
        });

        // Confirm refuse refund
        $('#confirmRefuseBtn').click(function() {
            if (currentRefuseRefundId) {
                $.ajax({
                    url: `/admin/refund-orders/${currentRefuseRefundId}/refuse`,
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
            }
        });
    });
    </script>
@endsection
