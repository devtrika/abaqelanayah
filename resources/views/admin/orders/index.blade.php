@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/index_page.css') }}">
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
'status' => [
    'input_type' => 'select',
    'rows' => [
        'pending' => [
            'name' => __('admin.pending'),
            'id' => 'pending',
        ],
        'new' => [
            'name' => __('admin.new'),
            'id' => 'new',
        ],
        'out-for-delivery' => [
            'name' => __('admin.out_for_delivery'),
            'id' => 'out-for-delivery',
        ],
        'confirmed' => [
            'name' => __('admin.confirmed'),
            'id' => 'confirmed',
        ],
        'processing' => [
            'name' => __('admin.processing'),
            'id' => 'processing',
        ],
        'delivered' => [
            'name' => __('admin.delivered'),
            'id' => 'delivered',
        ],
        'problem' => [
            'name' => __('admin.problem'),
            'id' => 'problem',
        ],
        'cancelled' => [
            'name' => __('admin.cancelled'),
            'id' => 'cancelled',
        ],
        'request_refund' => [
            'name' => __('admin.request_refund'),
            'id' => 'request_refund',
        ],
        'refunded' => [
            'name' => __('admin.refunded'),
            'id' => 'refunded',
        ],
        'request_rejected' => [
            'name' => __('admin.request_rejected'),
            'id' => 'request_rejected',
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
                'success' => [
                    'name' => __('admin.success'),
                    'id' => 'success',
                ],
                'failed' => [
                    'name' => __('admin.failed'),
                    'id' => 'failed',
                ],


            ],
            'input_name' => __('admin.payment_status'),
        ],
         'delivery_type' => [
            'input_type' => 'select',
            'rows' => [
                'immediate' => [
                    'name' => __('admin.immediate'),
                    'id' => 'immediate',
                ],
                'scheduled' => [
                    'name' => __('admin.scheduled'),
                    'id' => 'scheduled',
                ],
           


            ],
            'input_name' => __('admin.delivery_type'),
        ],
      'payment_method_id' => [
    'input_type' => 'select',
    'rows' => [
        1 => [
            'name' => __('admin.visa'),
            'id' => 1,
        ],
        2 => [
            'name' => __('admin.mada'),
            'id' => 2,
        ],
        3 => [
            'name' => __('admin.apple_pay'),
            'id' => 3,
        ],
        4 => [
            'name' => __('admin.google_pay'),
            'id' => 4,
        ],
        5 => [
            'name' => __('admin.bank_transfer'),
            'id' => 5,
        ],
        6 => [
            'name' => __('admin.wallet'),
            'id' => 6,
        ],
    ],
    'input_name' => __('admin.payment_method'),
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

    <script src="{{ asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js') }}"></script>
    @include('admin.shared.deleteAll')
    @include('admin.shared.deleteOne')
    @include('admin.shared.filter_js', ['index_route' => url('admin/orders')])
@endsection
