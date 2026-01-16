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
        'email' => [
            'input_type' => 'text' ,
            'input_name' => __('admin.email') ,
        ] ,
         
    ]"
>
  <x-slot name="extrabuttonsdiv">
    <a href="{{ route('admin.providers.index') }}"
      class="btn bg-gradient-primary mr-1 mb-1 waves-effect waves-light">
      <i class="feather icon-arrow-left"></i> {{ __('admin.back_to_all_providers') }}
    </a>
  </x-slot>

    <x-slot name="tableContent">
        <div class="table_content_append card">

        </div>
    </x-slot>
</x-admin.table>

@endsection

@section('js')
    <script src="{{asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/forms/validation/form-validation.js')}}"></script>
    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js')}}"></script>
    @include('admin.shared.filter_js' , [ 'index_route' => url('admin/pending-requests')])
    <script>
      $(document).ready(function(){
          // Handle provider approval
          $(document).on('click','.approve-provider',function(e){
              e.preventDefault();
              let providerId = $(this).data('id');

              console.log('Approve clicked for provider ID:', providerId);

              Swal.fire({
                  title: '{{ __('admin.are_you_sure') }}',
                  text: '{{ __('admin.approve_provider_request') }}',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: '{{ __('admin.approve_request') }}',
                  cancelButtonText: '{{ __('admin.cancel') }}'
              }).then(function(result) {
                  console.log('SweetAlert result:', result);
                  if (result.value) {
                      console.log('Sending approve request...');
                      $.ajax({
                          url: '{{ url('admin/providers') }}/' + providerId + '/approve',
                          method: 'POST',
                          data: {
                              _token: '{{ csrf_token() }}'
                          },
                          beforeSend: function() {
                              console.log('Request being sent...');
                          },
                          success: function(response) {
                              console.log('Success response:', response);
                              Swal.fire({
                                  title: '{{ __('admin.success') }}',
                                  text: response.message,
                                  type: 'success',
                                  confirmButtonText: '{{ __('admin.close') }}'
                              });
                              setTimeout(function(){
                                  window.location.reload()
                              }, 1000);
                          },
                          error: function(xhr, status, error) {
                              console.log('Error response:', xhr, status, error);
                              var errorMessage = xhr.responseJSON?.message || '{{ __('admin.error_occurred') }}';
                              Swal.fire({
                                  title: '{{ __('admin.error') }}',
                                  text: errorMessage,
                                  type: 'error',
                                  confirmButtonText: '{{ __('admin.close') }}'
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

              console.log('Reject clicked for provider ID:', providerId);

              Swal.fire({
                  title: '{{ __('admin.rejection_reason') }}',
                  input: 'textarea',
                  inputPlaceholder: '{{ __('admin.enter_rejection_reason') }}',
                  showCancelButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#3085d6',
                  confirmButtonText: '{{ __('admin.reject_request') }}',
                  cancelButtonText: '{{ __('admin.cancel') }}',
                  inputValidator: function(value) {
                      if (!value) {
                          return '{{ __('admin.reason_required') }}'
                      }
                  }
              }).then(function(result) {
                  console.log('SweetAlert reject result:', result);
                  if (result.value) {
                      var rejectionReason = result.value;
                      console.log('Sending reject request with reason:', rejectionReason);
                      $.ajax({
                          url: '{{ url('admin/providers') }}/' + providerId + '/reject',
                          method: 'POST',
                          data: {
                              _token: '{{ csrf_token() }}',
                              rejection_reason: rejectionReason
                          },
                          beforeSend: function() {
                              console.log('Reject request being sent...');
                          },
                          success: function(response) {
                              console.log('Reject success response:', response);
                              Swal.fire({
                                  title: '{{ __('admin.success') }}',
                                  text: response.message,
                                  type: 'success',
                                  confirmButtonText: '{{ __('admin.close') }}'
                              });
                              setTimeout(function(){
                                  window.location.reload()
                              }, 1000);
                          },
                          error: function(xhr, status, error) {
                              console.log('Reject error response:', xhr, status, error);
                              var errorMessage = xhr.responseJSON?.message || '{{ __('admin.error_occurred') }}';
                              Swal.fire({
                                  title: '{{ __('admin.error') }}',
                                  text: errorMessage,
                                  type: 'error',
                                  confirmButtonText: '{{ __('admin.close') }}'
                              });
                          }
                      });
                  }
              });
          });

          // Handle viewing rejection reason
          $(document).on('click','.view-rejection-reason',function(e){
              e.preventDefault();
              let reason = $(this).data('reason');

              Swal.fire({
                  title: '{{ __('admin.rejection_reason') }}',
                  text: reason,
                  type: 'info',
                  confirmButtonText: '{{ __('admin.close') }}'
              });
          });
      });
  </script>
@endsection
