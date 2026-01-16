@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/index_page.css') }}">
@endsection

@section('content')

<div class="row mb-3">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">{{ __('admin.platform_commission') }}</h5>
                <p class="card-text h4">{{ number_format($totalCommission ?? 0, 2) }} {{ __('admin.sar') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">{{ __('admin.booking_fee') }}</h5>
                <p class="card-text h4">{{ number_format($totalBookingFee ?? 0, 2) }} {{ __('admin.sar') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">{{ __('admin.cancel_fees') }}</h5>
                <p class="card-text h4">{{ number_format($totalCancelFees ?? 0, 2) }} {{ __('admin.sar') }}</p>
            </div>
        </div>
    </div>
</div>

<x-admin.table
    datefilter="true"
    order="true"
    extrabuttons="true"
    :searchArray="[
    ]"
>
    <x-slot name="extrabuttonsdiv">
        {{-- أزرار إضافية هنا --}}
        <a class="btn bg-gradient-info mr-1 mb-1 waves-effect waves-light"
           href="{{ route('admin.reports.commission-report.export') }}">
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
    @include('admin.shared.filter_js', ['index_route' => url('admin/reports/commission-reports')])

   
        
    @endsection
