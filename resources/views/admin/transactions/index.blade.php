@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/index_page.css')}}">
@endsection

@section('content')

<x-admin.table
    datefilter="true"
    order="true"
    extrabuttons="true"
    {{-- addbutton="{{ route('admin.transactions.create') }}" --}}
    {{-- deletebutton="{{ route('admin.transactions.deleteAll') }}" --}}
    :searchArray="[
        'user_name' => [
            'input_type' => 'text',
            'input_name' => __('admin.user_name'),
        ],
        'status' => [
            'input_type' => 'select',
            'input_name' => __('admin.status'),
            'rows' => [
                '' => ['name' => __('admin.all'), 'id' => ''],
                'pending' => ['name' => __('admin.pending'), 'id' => 'pending'],
                'accepted' => ['name' => __('admin.accepted') ?? 'Accepted', 'id' => 'accepted'],
                'rejected' => ['name' => __('admin.rejected') ?? 'Rejected', 'id' => 'rejected'],
            ],
        ],
    ]"
>

    <x-slot name="extrabuttonsdiv">
        {{-- <a type="button" data-toggle="modal" data-target="#notify" class="btn bg-gradient-info mr-1 mb-1 waves-effect waves-light notify" data-id="all"><i class="feather icon-bell"></i> {{ __('admin.Send_notification') }}</a> --}}
    </x-slot>

    <x-slot name="tableContent">
        <div class="table_content_append card">
            {{-- table content will appends here  --}}
        </div>
    </x-slot>
</x-admin.table>



@endsection

@section('js')

    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js')}}"></script>
    @include('admin.shared.deleteAll')
    @include('admin.shared.deleteOne')
    @include('admin.shared.filter_js' , [ 'index_route' => url('admin/transactions')])
    
    <script>
        // Product deletion validation SweetAlert messages
        $(document).ready(function() {
            // Remove existing delete-row event handlers to avoid conflicts
            $(document).off('click', '.delete-row');
            
            // Custom delete handler for transactions with validation
            $(document).on('click', '.delete-row', function(e) {
                e.preventDefault();
                var deleteUrl = $(this).data('url');
                
                Swal.fire({
                    title: "{{__('admin.are_you_want_to_continue')}}",
                    text: "{{__('admin.are_you_sure_you_want_to_complete_deletion')}}",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{__('admin.confirm')}}',
                    confirmButtonClass: 'btn btn-primary',
                    cancelButtonText: '{{__('admin.cancel')}}',
                    cancelButtonClass: 'btn btn-danger ml-1',
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "delete",
                            url: deleteUrl,
                            data: {},
                            dataType: "json",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.error) {
                                    // Show validation error for transactions with orders
                                    Swal.fire({
                                        type: 'error',
                                        title: '{{__('admin.error')}}',
                                        text: response.message || '{{__('admin.something_went_wrong')}}',
                                        confirmButtonClass: 'btn btn-primary',
                                        buttonsStyling: false,
                                    });
                                } else {
                                    Swal.fire({
                                        type: 'success',
                                        title: '{{__('admin.the_selected_has_been_successfully_deleted')}}',
                                        showConfirmButton: false,
                                        timer: 1500,
                                        confirmButtonClass: 'btn btn-primary',
                                        buttonsStyling: false,
                                    });
                                    getData({'searchArray' : searchArray()});
                                }
                            },
                            error: function(xhr) {
                                var errorMessage = '{{__('admin.something_went_wrong')}}';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    type: 'error',
                                    title: '{{__('admin.error')}}',
                                    text: errorMessage,
                                    confirmButtonClass: 'btn btn-primary',
                                    buttonsStyling: false,
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script>
        // Handle accept / reject actions
        $(document).on('click', '.accept-transaction, .reject-transaction', function(e) {
            e.preventDefault();
            var btn = $(this);
            var url = btn.data('url');
            var isAccept = btn.hasClass('accept-transaction');

            Swal.fire({
                title: isAccept ? "{{ __('admin.are_you_sure') }}" : "{{ __('admin.are_you_sure') }}",
                text: isAccept ? "{{ __('admin.confirm_transaction_action') }}" : "{{ __('admin.reject_transaction_action') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('admin.confirm') }}',
                cancelButtonText: '{{ __('admin.cancel') }}',
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        type: 'post',
                        url: url,
                        data: {},
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(resp) {
                            if (resp.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: resp.message || '{{ __('admin.success') }}',
                                    showConfirmButton: false,
                                    timer: 1200
                                });

                                // Refresh table
                                try { getData({'searchArray' : searchArray()}); } catch (e) { location.reload(); }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('admin.error') }}',
                                    text: resp.message || '{{ __('admin.something_went_wrong') }}'
                                });
                            }
                        },
                        error: function(xhr) {
                            var msg = '{{ __('admin.something_went_wrong') }}';
                            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                            Swal.fire({ icon: 'error', title: '{{ __('admin.error') }}', text: msg });
                        }
                    });
                }
            });
        });
    </script>
@endsection
