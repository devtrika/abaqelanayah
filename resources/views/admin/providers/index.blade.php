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
    addbutton="{{ route('admin.providers.create') }}"
    deletebutton="{{ route('admin.providers.deleteAll') }}"
    :searchArray="array_merge([
        'name' => [
            'input_type' => 'text',
            'input_name' => __('admin.name'),
        ],
        'phone' => [
            'input_type' => 'text',
            'input_name' => __('admin.phone'),
        ],
        'email' => [
            'input_type' => 'text',
            'input_name' => __('admin.email'),
        ],
        'p_status' => [
            'input_type' => 'select',
            'rows' => [
                'in_review' => [
                    'name' => __('admin.in_review'),
                    'id' => 'in_review',
                ],
                'pending' => [
                    'name' => __('admin.pending'),
                    'id' => 'pending',
                ],
                'accepted' => [
                    'name' => __('admin.accepted'),
                    'id' => 'accepted',
                ],
                'rejected' => [
                    'name' => __('admin.rejected'),
                    'id' => 'rejected',
                ],
                'deleted' => [
                    'name' => __('admin.deleted'),
                    'id' => 'deleted',
                ],
                'blocked' => [
                    'name' => __('admin.blocked'),
                    'id' => 'blocked',
                ],
            ],
            'input_name' => __('admin.status'),
        ],
    ], isset($region_id) && $region_id ? ['region_id' => ['input_type' => 'hidden', 'input_name' => 'region_id', 'value' => $region_id]] : [], isset($city_id) && $city_id ? ['city_id' => ['input_type' => 'hidden', 'input_name' => 'city_id', 'value' => $city_id]] : [])"
    >
  <x-slot name="extrabuttonsdiv">
    {{-- <a type="button" data-toggle="modal" data-target="#notify"
      class="btn bg-gradient-info mr-1 mb-1 waves-effect waves-light notify"
      data-id="all"><i class="feather icon-bell"></i> {{ __('admin.Send_notification') }}</a> --}}

    <a href="{{ route('admin.pendingRequests') }}"
      class="btn bg-gradient-warning mr-1 mb-1 waves-effect waves-light">
      <i class="feather icon-clock"></i> {{ __('admin.pending_provider_requests') }}
    </a>
  </x-slot>

  <x-slot name="tableContent">
    <div class="table_content_append card">
        @if(isset($region_id) && $region_id)
            <input type="hidden" name="region_id" value="{{ $region_id }}" class="search-input">
        @endif

        @if(isset($city_id) && $city_id)
        <input type="hidden" name="city_id" value="{{ $city_id }}" class="search-input">
    @endif
    </div>
</x-slot>
</x-admin.table>
  {{-- notify providers model --}}
  <x-admin.NotifyAll route="{{ route('admin.providers.notify') }}" />
  {{-- notify providers model --}}
@endsection

@section('js')
    <script src="{{asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/forms/validation/form-validation.js')}}"></script>
    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js')}}"></script>
    @include('admin.shared.deleteAll')
    @include('admin.shared.deleteOne')
    @include('admin.shared.filter_js' , [ 'index_route' => url('admin/providers')])
    @include('admin.shared.notify')
    <script>
      $(document).ready(function(){
          // Setup CSRF token for AJAX requests
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });



          // Handle provider approval
          $(document).on('click','.approve-provider',function(e){
              e.preventDefault();
              let providerId = $(this).data('id');

              Swal.fire({
                  title: '{{ __('admin.are_you_sure') }}',
                  text: '{{ __('admin.approve_provider_request') }}',
                  type: 'question',
                  showCancelButton: true,
                  confirmButtonText: '{{ __('admin.approve_request') }}',
                  cancelButtonText: '{{ __('admin.cancel') }}',
                  confirmButtonClass: 'btn btn-success',
                  cancelButtonClass: 'btn btn-secondary',
                  buttonsStyling: false
              }).then((result) => {
                  if (result.isConfirmed) {
                      $.ajax({
                          url: `/admin/providers/${providerId}/approve`,
                          type: 'POST',
                          data: {
                              _token: '{{ csrf_token() }}'
                          },
                          success: function(response) {
                              Swal.fire({
                                  title: '{{ __('admin.success') }}',
                                  text: response.message,
                                  type: 'success',
                                  confirmButtonClass: 'btn btn-primary',
                                  buttonsStyling: false
                              });
                              setTimeout(function(){
                                  window.location.reload()
                              }, 1000);
                          },
                          error: function() {
                              Swal.fire({
                                  title: '{{ __('admin.error') }}',
                                  text: '{{ __('admin.error_occurred') }}',
                                  type: 'error',
                                  confirmButtonClass: 'btn btn-primary',
                                  buttonsStyling: false
                              });
                          }
                      });
                  }
              });
          });

          // Handle provider rejection
          $(document).on('click','.reject-provider',function(e){
              e.preventDefault();
              let providerId = $(this).data('id');

              Swal.fire({
                  title: '{{ __('admin.rejection_reason') }}',
                  input: 'textarea',
                  inputPlaceholder: '{{ __('admin.enter_rejection_reason') }}',
                  showCancelButton: true,
                  confirmButtonText: '{{ __('admin.reject_request') }}',
                  cancelButtonText: '{{ __('admin.cancel') }}',
                  confirmButtonClass: 'btn btn-danger',
                  cancelButtonClass: 'btn btn-secondary',
                  buttonsStyling: false,
                  inputValidator: (value) => {
                      if (!value) {
                          return '{{ __('admin.reason_required') }}'
                      }
                  }
              }).then((result) => {
                  if (result.isConfirmed) {
                      $.ajax({
                          url: `/admin/providers/${providerId}/reject`,
                          type: 'POST',
                          data: {
                              _token: '{{ csrf_token() }}',
                              rejection_reason: result.value
                          },
                          success: function(response) {
                              Swal.fire({
                                  title: '{{ __('admin.success') }}',
                                  text: response.message,
                                  type: 'success',
                                  confirmButtonClass: 'btn btn-primary',
                                  buttonsStyling: false
                              });
                              setTimeout(function(){
                                  window.location.reload()
                              }, 1000);
                          },
                          error: function() {
                              Swal.fire({
                                  title: '{{ __('admin.error') }}',
                                  text: '{{ __('admin.error_occurred') }}',
                                  type: 'error',
                                  confirmButtonClass: 'btn btn-primary',
                                  buttonsStyling: false
                              });
                          }
                      });
                  }
              });
          });


      });
  </script>
@endsection
