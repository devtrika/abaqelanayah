@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/index_page.css')}}">
@endsection

@section('content')

<x-admin.table
    datefilter="true"
    order="true"
    extrabuttons="true"
    :searchArray="[
        'user.name' => [
            'input_type' => 'text' ,
            'input_name' => __('admin.user_name') ,
        ],
        'user.email' => [
            'input_type' => 'text' ,
            'input_name' => __('admin.email') ,
        ] ,
        'user.phone' => [
            'input_type' => 'text' ,
            'input_name' => __('admin.phone') ,
        ] ,
        'status' => [
            'input_type' => 'select' ,
            'input_name' => __('admin.status') ,
            'rows' => [
                ['id' => '', 'name' => __('admin.all')],
                ['id' => 'pending', 'name' => __('admin.pending')],
                ['id' => 'approved', 'name' => __('admin.approved')],
                ['id' => 'rejected', 'name' => __('admin.rejected')]
            ]
        ] ,
    ]"
>
    <x-slot name="extrabuttonsdiv">
       
    </x-slot>

    <x-slot name="tableContent">
        <div class="table_content_append card">
            {{-- table content will appends here  --}}
        </div>
    </x-slot>
</x-admin.table>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.approve_deletion_request') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="approveForm">
                    <div class="form-group">
                        <label>{{ __('admin.admin_notes') }}</label>
                        <textarea class="form-control" name="admin_notes" rows="3" placeholder="{{ __('admin.enter_admin_notes') }}"></textarea>
                    </div>
                </form>
                <div class="alert alert-warning">
                    <i class="feather icon-alert-triangle"></i>
                    {{ __('admin.approve_deletion_warning') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin.cancel') }}</button>
                <button type="button" class="btn btn-success" id="confirmApprove">{{ __('admin.accept') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.reject_deletion_request') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    <div class="form-group">
                        <label>{{ __('admin.rejection_reason') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="admin_notes" rows="3" placeholder="{{ __('admin.enter_rejection_reason') }}" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmReject">{{ __('admin.reject') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
    <script src="{{ asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js') }}"></script>
    @include('admin.shared.deleteAll')
    @include('admin.shared.deleteOne')
    @include('admin.shared.filter_js' , [ 'index_route' => url('admin/account-deletion-requests')])
    <script>
        let currentRequestId = null;

        $(document).ready(function(){
            // Show cant_delete_provider message if set in session
            @if(session('cant_delete_provider'))
                Swal.fire({
                    position: 'top-start',
                    icon: 'error',
                    title: @json(session('cant_delete_provider')),
                    showConfirmButton: false,
                    timer: 2000,
                    confirmButtonClass: 'btn btn-primary',
                    buttonsStyling: false,
                });
            @endif

            // Handle approve button click
            $(document).on('click', '.approve-request', function(e){
                e.preventDefault();
                currentRequestId = $(this).data('id');
                $('#approveModal').modal('show');
            });

            // Handle reject button click
            $(document).on('click', '.reject-request', function(e){
                e.preventDefault();
                currentRequestId = $(this).data('id');
                $('#rejectModal').modal('show');
            });

            // Handle approve confirmation
            $('#confirmApprove').click(function(){
                if(!currentRequestId) return;

                let adminNotes = $('#approveForm textarea[name="admin_notes"]').val();

                $.ajax({
                    url: '{{ url('admin/account-deletion-requests') }}/' + currentRequestId + '/approve',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        admin_notes: adminNotes
                    },
                    success: function(response) {
                        $('#approveModal').modal('hide');
                        Swal.fire({
                            position: 'top-start',
                            type: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                            confirmButtonClass: 'btn btn-primary',
                            buttonsStyling: false,
                        });
                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        $('#approveModal').modal('hide');
                        let errorMessage = xhr.responseJSON?.error || xhr.responseJSON?.message || '{{ __('admin.error_occurred') }}';
                        Swal.fire({
                            position: 'top-start',
                            type: 'error',
                            title: errorMessage,
                            showConfirmButton: false,
                            timer: 1500,
                            confirmButtonClass: 'btn btn-primary',
                            buttonsStyling: false,
                        });
                    }
                });
            });

            // Handle reject confirmation
            $('#confirmReject').click(function(){
                if(!currentRequestId) return;

                let adminNotes = $('#rejectForm textarea[name="admin_notes"]').val();

                if(!adminNotes.trim()) {
                    Swal.fire({
                        position: 'top-start',
                        type: 'error',
                        title: '{{ __('admin.rejection_reason_required') }}',
                        showConfirmButton: false,
                        timer: 1500,
                        confirmButtonClass: 'btn btn-primary',
                        buttonsStyling: false,
                    });
                    return;
                }

                $.ajax({
                    url: '{{ url('admin/account-deletion-requests') }}/' + currentRequestId + '/reject',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        admin_notes: adminNotes
                    },
                    success: function(response) {
                        $('#rejectModal').modal('hide');
                        Swal.fire({
                            position: 'top-start',
                            type: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                            confirmButtonClass: 'btn btn-primary',
                            buttonsStyling: false,
                        });
                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        $('#rejectModal').modal('hide');
                        let errorMessage = xhr.responseJSON?.message || '{{ __('admin.error_occurred') }}';
                        Swal.fire({
                            position: 'top-start',
                            type: 'error',
                            title: errorMessage,
                            showConfirmButton: false,
                            timer: 1500,
                            confirmButtonClass: 'btn btn-primary',
                            buttonsStyling: false,
                        });
                    }
                });
            });
        });
    </script>
@endsection
