@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
@endsection

@section('content')
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('admin.account_deletion_request_details') }}</h4>
                    <div class="card-header-toolbar d-flex align-items-center">
                        @switch($request->status)
                            @case('pending')
                                <span class="badge badge-warning mr-2">{{ __('admin.pending') }}</span>
                                @break
                            @case('approved')
                                <span class="badge badge-success mr-2">{{ __('admin.approved') }}</span>
                                @break
                            @case('rejected')
                                <span class="badge badge-danger mr-2">{{ __('admin.rejected') }}</span>
                                @break
                        @endswitch

                        <a href="{{ route('admin.account-deletion-requests.index') }}" class="btn btn-primary btn-sm mr-1">
                            <i class="feather icon-arrow-left"></i> {{ __('admin.back') }}
                        </a>

                        @if($request->isPending())
                            <button class="btn btn-success btn-sm mr-1 approve-request" data-id="{{ $request->id }}">
                                <i class="feather icon-check"></i> {{ __('admin.accept') }}
                            </button>
                            <button class="btn btn-danger btn-sm reject-request" data-id="{{ $request->id }}">
                                <i class="feather icon-x"></i> {{ __('admin.reject') }}
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <!-- User Information -->
                            <div class="col-md-6">
                                <h5>{{ __('admin.user_information') }}</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>{{ __('admin.name') }}:</strong></td>
                                        <td>{{ $request->user->name ?? __('admin.deleted_user') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('admin.email') }}:</strong></td>
                                        <td>{{ $request->user->email ?? __('admin.not_available') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('admin.phone') }}:</strong></td>
                                        <td>{{ $request->user->full_phone ?? __('admin.not_available') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('admin.user_type') }}:</strong></td>
                                        <td>
                                            @if($request->user)
                                                @switch($request->user->type)
                                                    @case('client')
                                                        <span class="badge badge-info">{{ __('admin.client') }}</span>
                                                        @break
                                                    @case('provider')
                                                        <span class="badge badge-warning">{{ __('admin.provider') }}</span>
                                                        @break
                                                    @case('delivery')
                                                        <span class="badge badge-secondary">{{ __('admin.delivery') }}</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-light">{{ $request->user->type }}</span>
                                                @endswitch
                                            @else
                                                <span class="text-muted">{{ __('admin.not_available') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('admin.registration_date') }}:</strong></td>
                                        <td>{{ $request->user->created_at->format('Y-m-d H:i') ?? __('admin.not_available') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Request Information -->
                            <div class="col-md-6">
                                <h5>{{ __('admin.request_information') }}</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>{{ __('admin.request_date') }}:</strong></td>
                                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('admin.status') }}:</strong></td>
                                        <td>
                                            @switch($request->status)
                                                @case('pending')
                                                    <span class="badge badge-warning">{{ __('admin.pending') }}</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge badge-success">{{ __('admin.approved') }}</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge badge-danger">{{ __('admin.rejected') }}</span>
                                                    @break
                                            @endswitch
                                        </td>
                                    </tr>
                                    @if($request->processed_at)
                                        <tr>
                                            <td><strong>{{ __('admin.processed_date') }}:</strong></td>
                                            <td>{{ $request->processed_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @endif
                                    @if($request->processedBy)
                                        <tr>
                                            <td><strong>{{ __('admin.processed_by') }}:</strong></td>
                                            <td>{{ $request->processedBy->name }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <!-- User Reason -->
                        @if($request->reason)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>{{ __('admin.user_reason') }}</h5>
                                    <div class="card">
                                        <div class="card-body">
                                            <p class="card-text">{{ $request->reason }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Admin Notes -->
                        @if($request->admin_notes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>{{ __('admin.admin_notes') }}</h5>
                                    <div class="card">
                                        <div class="card-body">
                                            <p class="card-text">{{ $request->admin_notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
    <script>
        let currentRequestId = {{ $request->id }};

        $(document).ready(function(){
            // Handle approve button click
            $(document).on('click', '.approve-request', function(e){
                e.preventDefault();
                $('#approveModal').modal('show');
            });

            // Handle reject button click
            $(document).on('click', '.reject-request', function(e){
                e.preventDefault();
                $('#rejectModal').modal('show');
            });

            // Handle approve confirmation
            $('#confirmApprove').click(function(){
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
                            title: '{{ __('admin.success') }}',
                            text: response.message,
                            type: 'success',
                            confirmButtonText: '{{ __('admin.close') }}'
                        });
                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        $('#approveModal').modal('hide');
                        let errorMessage = xhr.responseJSON?.error || xhr.responseJSON?.message || '{{ __('admin.error_occurred') }}';
                        Swal.fire({
                            title: '{{ __('admin.error') }}',
                            text: errorMessage,
                            type: 'error',
                            confirmButtonText: '{{ __('admin.close') }}'
                        });
                    }
                });
            });

            // Handle reject confirmation
            $('#confirmReject').click(function(){
                let adminNotes = $('#rejectForm textarea[name="admin_notes"]').val();

                if(!adminNotes.trim()) {
                    alert('{{ __('admin.rejection_reason_required') }}');
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
                            title: '{{ __('admin.success') }}',
                            text: response.message,
                            type: 'success',
                            confirmButtonText: '{{ __('admin.close') }}'
                        });
                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        $('#rejectModal').modal('hide');
                        let errorMessage = xhr.responseJSON?.message || '{{ __('admin.error_occurred') }}';
                        Swal.fire({
                            title: '{{ __('admin.error') }}',
                            text: errorMessage,
                            type: 'error',
                            confirmButtonText: '{{ __('admin.close') }}'
                        });
                    }
                });
            });
        });
    </script>
@endsection
