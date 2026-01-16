@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/index_page.css')}}">
@endsection

@section('content')

<x-admin.table
    datefilter="true"
    order="true"
    extrabuttons="true"
    :searchArray="[
        'name' => [
            'input_type' => 'text' ,
            'input_name' => __('admin.name') ,
        ],
        'phone' => [
            'input_type' => 'text' ,
            'input_name' => __('admin.phone') ,
        ] ,
       

    ]"
>
<x-slot name="extrabuttonsdiv">
    <a class="btn bg-gradient-info mr-1 mb-1 waves-effect waves-light"  href="{{url(route('admin.loyalty.export'))}}"><i  class="fa fa-file-excel-o"></i>
        {{ __('admin.export') }}</a>
</x-slot>
    <x-slot name="tableContent">
        <div class="table_content_append card">

        </div>
    </x-slot>
</x-admin.table>
  {{-- notify users model --}}
  <x-admin.NotifyAll route="{{ route('admin.clients.notify') }}" />
  {{-- notify users model --}}
  {{-- import files model --}}
  <x-admin.ImportFile route="{{ route('admin.clients.importFile') }}" />
  {{-- import files  model --}}
@endsection

@section('js')
    <script src="{{asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/forms/validation/form-validation.js')}}"></script>
    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js')}}"></script>
    @include('admin.shared.deleteAll')
    @include('admin.shared.deleteOne')
    @include('admin.shared.filter_js' , [ 'index_route' => url('admin/points-reports')])
    @include('admin.shared.notify')

    {{-- import excel file script--}}
    @include('admin.shared.importFile')
    {{-- import excel file script --}}
@endsection
