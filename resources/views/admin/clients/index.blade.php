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
    addbutton="{{ route('admin.clients.create') }}"
    deletebutton="{{ route('admin.clients.deleteAll') }}"
    :searchArray="array_merge([
        'name' => [
            'input_type' => 'text' ,
            'input_name' => __('admin.name') ,
        ],
        'phone' => [
            'input_type' => 'text' ,
            'input_name' => __('admin.phone') ,
        ] ,
        'email' => [
            'input_type' => 'text' ,
            'input_name' => __('admin.email') ,
        ] ,
        'is_blocked' => [
            'input_type' => 'select' , 
            'rows'       => [
              '1' => [
                'name' => __('admin.Prohibited') , 
                'id' => 1 , 
              ],
              '2' => [
                'name' => __('admin.Unspoken') , 
                'id' => 0 , 
              ],
            ] , 
            'input_name' => __('admin.ban_status') , 
        ] ,
        'is_active' => [
            'input_type' => 'select' ,
            'rows'       => [
              '1' => [
                'name' => __('admin.activate') ,
                'id' => 1 ,
              ],
              '2' => [
                'name' => __('admin.dis_activate') ,
                'id' => 0 ,
              ],
            ] ,
            'input_name' => __('admin.phone_activation_status')  ,
        ] ,
    ], isset($district_id) && $district_id ? [
        'district_id' => [
            'input_type' => 'hidden',
            'value' => $district_id,
            'input_name' => __('admin.region') // ✅ Added this line
        ]
    ] : [], isset($city_id) && $city_id ? [
        'city_id' => [
            'input_type' => 'hidden',
            'value' => $city_id,
            'input_name' => __('admin.city') // ✅ Added this line
        ]
    ] : [])
    ">
  <x-slot name="extrabuttonsdiv">
    {{-- <a type="button" data-toggle="modal" data-target="#notify"
      class="btn bg-gradient-info mr-1 mb-1 waves-effect waves-light notify"
      data-id="all"><i class="feather icon-bell"></i> {{ __('admin.Send_notification') }}</a> --}}
  </x-slot>

    <x-slot name="tableContent">
        <div class="table_content_append card">
            @if(isset($district_id) && $district_id)
                <input type="hidden" name="district_id" value="{{ $district_id }}" class="search-input">
            @endif

             @if(isset($city_id) && $city_id)
                <input type="hidden" name="city_id" value="{{ $city_id }}" class="search-input">
            @endif
            @if(isset($is_active) && $is_active)
                <input type="hidden" name="is_active" value="{{ $is_active }}" class="search-input">
            @endif
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
    @include('admin.shared.filter_js' , [ 'index_route' => url('admin/clients')])
    @include('admin.shared.notify')
    @include('admin.shared.activate')
    {{-- import excel file script--}}
    @include('admin.shared.importFile')
    {{-- import excel file script --}}  
@endsection
