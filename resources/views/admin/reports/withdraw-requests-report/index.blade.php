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
  'status' => [
    'input_type' => 'select',
    'rows' => [
        1 => [
            'name' => __('admin.pending'),
            'id' => 'pending',
        ],
        2 => [
            'name' => __('admin.accepted'),
            'id' => 'accepted',
        ],
        3 => [
            'name' => __('admin.rejected'),
            'id' => 'rejected',
        ],
        
    ],
    'input_name' => __('admin.payment_method'),
    ],
 'provider_id' => [
            'input_type' => 'select' ,
            'input_name' => __('admin.service_provider') ,
            'rows' => collect([['id' => '', 'name' => __('admin.all')]])->concat(
                \App\Models\Provider::with('user')->get()->map(function($provider) {
                    return ['id' => $provider->id, 'name' => $provider->commercial_name ?? 'No Name'];
                })
            )->toArray()
        ] ,

  
    ]"
>
    <x-slot name="extrabuttonsdiv">
        {{-- أزرار إضافية هنا --}}
        <a class="btn bg-gradient-info mr-1 mb-1 waves-effect waves-light"
           href="{{ route('admin.reports.withdraw-requests-report.export') }}">
            <i class="fa fa-file-excel-o"></i> {{ __('admin.export') }}
        </a>
    </x-slot>

    <x-slot name="tableContent">
        <div class="table_content_append card">
            {{-- table content will appends here  --}}
        </div>
    </x-slot>
</x-admin.table>

<div class="modal fade" id="acceptWithdrawModal" tabindex="-1" role="dialog" aria-labelledby="acceptWithdrawModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="acceptWithdrawForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="acceptWithdrawModalLabel">{{ __('admin.accept') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="withdraw_request_id" id="accept_withdraw_request_id">
          <div class="form-group">
            <label>{{ __('admin.amount') }}</label>
            <input type="number" class="form-control" name="amount" id="accept_amount">
          </div>
          <div class="form-group">
            <label>{{ __('admin.image') }}</label>
            <input type="file" class="form-control" name="image" id="accept_image" accept="image/*">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin.close') }}</button>
          <button type="submit" class="btn btn-success">{{ __('admin.confirm') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="rejectWithdrawModal" tabindex="-1" role="dialog" aria-labelledby="rejectWithdrawModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="rejectWithdrawForm">
        <div class="modal-header">
          <h5 class="modal-title" id="rejectWithdrawModalLabel">{{ __('admin.reject') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="withdraw_request_id" id="reject_withdraw_request_id">
          <p>{{ __('admin.are_you_sure') }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin.close') }}</button>
          <button type="submit" class="btn btn-danger">{{ __('admin.confirm') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('js')


    <script src="{{ asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js') }}"></script>
    @include('admin.shared.deleteAll')
    @include('admin.shared.deleteOne')
    @include('admin.shared.filter_js', ['index_route' => url('admin/reports/withdraw-requests-reports')])

    <script>
        $(document).on('click', '.accept-withdraw-btn', function() {
            var id = $(this).data('id');
            var amount = $(this).data('amount');
            $('#accept_withdraw_request_id').val(id);
            $('#accept_amount').val(amount);
            $('#acceptWithdrawModal').modal('show');
        });
        
        $(document).on('click', '.reject-withdraw-btn', function() {
            var id = $(this).data('id');
            $('#reject_withdraw_request_id').val(id);
            $('#rejectWithdrawModal').modal('show');
        });
        
        $('#acceptWithdrawForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: '{{ route('admin.reports.withdraw-requests.accept') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    $('#acceptWithdrawModal').modal('hide');
                    toastr.success(response.message || '{{ __('admin.success') }}');
                    location.reload();
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || '{{ __('admin.error') }}');
                }
            });
        });
        
        $('#rejectWithdrawForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: '{{ route('admin.reports.withdraw-requests.reject') }}',
                type: 'POST',
                data: formData,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    $('#rejectWithdrawModal').modal('hide');
                    toastr.success(response.message || '{{ __('admin.success') }}');
                    location.reload();
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || '{{ __('admin.error') }}');
                }
            });
        });
        </script>
        
        
    @endsection
