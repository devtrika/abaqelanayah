@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/index_page.css')}}">
@endsection

@section('content')

<x-admin.table
    datefilter="true"
    order="true"
    :searchArray="[
        'order_number' => [
            'input_type' => 'text',
            'input_name' => __('admin.order_number'),
        ],
        'user_id' => [
            'input_type' => 'select',
            'rows' => \App\Models\User::get(['id', 'name'])->map(function($user) {
                return ['id' => $user->id, 'name' => $user->name];
            })->toArray(),
            'input_name' => __('admin.user'),
        ],
        'cancel_reason_id' => [
            'input_type' => 'select',
            'rows' => \App\Models\CancelReason::get(['id', 'reason'])->map(function($reason) {
                $reasonData = json_decode($reason->reason, true);
                return [
                    'id' => $reason->id,
                    'name' => $reasonData[app()->getLocale()] ?? $reasonData['en'] ?? 'Unknown'
                ];
            })->toArray(),
            'input_name' => __('admin.cancel_reason'),
        ],
        'payment_method_id' => [
            'input_type' => 'select',
            'rows' => \App\Models\PaymentMethod::get(['id', 'name'])->map(function($method) {
                return ['id' => $method->id, 'name' => $method->name];
            })->toArray(),
            'input_name' => __('admin.payment_method'),
        ],
    ]"
>

    <x-slot name="tableContent">
        <div class="table_content_append card">
            {{-- table content will appends here  --}}
        </div>
    </x-slot>
</x-admin.table>

@endsection

@section('js')
    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js')}}"></script>
    @include('admin.shared.deleteAll')
    @include('admin.shared.deleteOne')
    @include('admin.shared.filter_js' , [ 'index_route' => url('admin/cancel-request-orders')])

    <script>
        // Accept Cancel Request
        $(document).on('click', '.accept-cancel-request', function() {
            const orderId = $(this).data('order-id');
            const orderTotal = $(this).data('order-total');
            const cancelReason = $(this).data('cancel-reason') || '{{ __("admin.no_reason_provided") }}';

            Swal.fire({
                title: '{{ __("admin.accept_cancel_request") }}',
                html: `
                    <div class="text-left">
                        <p>{{ __("admin.are_you_sure_accept_cancel_request") }}</p>
                        <div class="alert alert-info mt-3">
                            <strong>{{ __("admin.cancel_reason") }}:</strong><br>
                            <span class="text-muted">${cancelReason}</span>
                        </div>
                        <div class="form-group mt-3">
                            <label for="cancel_fees">{{ __("admin.cancel_fees") }} ({{ __("admin.sar") }}) *</label>
                            <input type="number" id="cancel_fees" class="form-control" min="0" max="${orderTotal}" step="0.01" value="5.00" required>
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
                    $.ajax({
                        url: `/admin/cancel-request-orders/${orderId}/accept`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            cancel_fees: result.value.cancel_fees
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: '{{ __("admin.success") }}',
                                    html: `
                                        <p>${response.message}</p>
                                        <div class="mt-2">
                                            <strong>{{ __("admin.refund_amount") }}:</strong> ${response.data.refund_amount} {{ __("admin.sar") }}<br>
                                            <strong>{{ __("admin.cancel_fees") }}:</strong> ${response.data.cancel_fees} {{ __("admin.sar") }}
                                        </div>
                                    `,
                                    type: 'success'
                                }).then(() => {
                                    window.location.href = `/admin/orders/${orderId}/show`;
                                });
                            } else {
                                Swal.fire('{{ __("admin.error") }}', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('{{ __("admin.error") }}', '{{ __("admin.something_went_wrong") }}', 'error');
                        }
                    });
                }
            });
        });

        // Reject Cancel Request
        $(document).on('click', '.reject-cancel-request', function() {
            const orderId = $(this).data('order-id');

            Swal.fire({
                title: '{{ __("admin.reject_cancel_request") }}',
                text: '{{ __("admin.are_you_sure_reject_cancel_request") }}',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __("admin.reject") }}',
                cancelButtonText: '{{ __("admin.cancel") }}',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/cancel-request-orders/${orderId}/reject`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            reason: 'Cancel request rejected by admin'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('{{ __("admin.success") }}', response.message, 'success').then(() => {
                                    window.location.href = `/admin/orders/${orderId}/show`;
                                });
                            } else {
                                Swal.fire('{{ __("admin.error") }}', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('{{ __("admin.error") }}', '{{ __("admin.something_went_wrong") }}', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection
