@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/index_page.css')}}">
@endsection

@section('content')

<x-admin.table
    datefilter="true"
    order="true"
    extrabuttons="true"
    :searchArray="[
        'order_number' => [
            'input_type' => 'text',
            'input_name' => __('admin.order_number'),
        ],
        'current_status' => [
            'input_type' => 'select',
            'rows' => [
                'pending_payment' => [
                    'name' => __('admin.pending_payment'),
                    'id' => 'pending_payment',
                ],
                'processing' => [
                    'name' => __('admin.processing'),
                    'id' => 'processing',
                ],
                'confirmed' => [
                    'name' => __('admin.confirmed'),
                    'id' => 'confirmed',
                ],
                'completed' => [
                    'name' => __('admin.completed'),
                    'id' => 'completed',
                ],
                'cancelled' => [
                    'name' => __('admin.cancelled'),
                    'id' => 'cancelled',
                ],
              
            ],
            'input_name' => __('admin.order_status'),
        ],
        'payment_status' => [
            'input_type' => 'select',
            'rows' => [
                'pending' => [
                    'name' => __('admin.pending'),
                    'id' => 'pending',
                ],
                'paid' => [
                    'name' => __('admin.paid'),
                    'id' => 'paid',
                ],
                'failed' => [
                    'name' => __('admin.failed'),
                    'id' => 'failed',
                ],
                'refunded' => [
                    'name' => __('admin.refunded'),
                    'id' => 'refunded',
                ],
            ],
            'input_name' => __('admin.payment_status'),
        ],
        'user_id' => [
            'input_type' => 'select',
            'rows' => \App\Models\User::get(['id', 'name'])->map(function($user) {
                return ['id' => $user->id, 'name' => $user->name];
            })->toArray(),
            'input_name' => __('admin.user'),
        ],
    ]"
>
    <x-slot name="extrabuttonsdiv">
        {{-- أزرار إضافية هنا --}}
    </x-slot>

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
    @include('admin.shared.filter_js' , [ 'index_route' => url('admin/bank-transfer-orders')])

    <script>
        // Verify Transfer
        $(document).on('click', '.verify-transfer', function() {
            const orderId = $(this).data('order-id');
            
            Swal.fire({
                title: '{{ __("admin.verify_transfer") }}',
                text: '{{ __("admin.are_you_sure_verify_transfer") }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '{{ __("admin.verify") }}',
                cancelButtonText: '{{ __("admin.cancel") }}',
                input: 'textarea',
                inputPlaceholder: '{{ __("admin.notes_optional") }}',
                inputAttributes: {
                    'aria-label': '{{ __("admin.notes") }}'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/bank-transfer-orders/${orderId}/verify-transfer`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            notes: result.value
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('{{ __("admin.success") }}', response.message, 'success');
                                location.reload();
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

        // Reject Transfer
        $(document).on('click', '.reject-transfer', function() {
            const orderId = $(this).data('order-id');
            
            Swal.fire({
                title: '{{ __("admin.reject_transfer") }}',
                text: '{{ __("admin.are_you_sure_reject_transfer") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __("admin.reject") }}',
                cancelButtonText: '{{ __("admin.cancel") }}',
                input: 'textarea',
                inputPlaceholder: '{{ __("admin.rejection_reason") }}',
                inputAttributes: {
                    'aria-label': '{{ __("admin.reason") }}'
                },
                inputValidator: (value) => {
                    if (!value) {
                        return '{{ __("admin.rejection_reason_required") }}'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/bank-transfer-orders/${orderId}/reject-transfer`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            reason: result.value
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('{{ __("admin.success") }}', response.message, 'success');
                                location.reload();
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
