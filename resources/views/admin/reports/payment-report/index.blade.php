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
        <a class="btn bg-gradient-info mr-1 mb-1 waves-effect waves-light"
           href="{{ route('admin.reports.payment-report.export') }}">
            <i class="fa fa-file-excel-o"></i> {{ __('admin.export') }}
        </a>
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
    @include('admin.shared.filter_js', ['index_route' => url('admin/reports/payment-reports')])
@endsection
