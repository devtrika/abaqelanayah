@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/index_page.css')}}">
@endsection

@section('content')

<x-admin.table 
    datefilter="true" 
    order="true" 
    addbutton="{{ route('admin.roles.create') }}" 
    :searchArray="[
        'name' => [
            'input_type' => 'text' , 
            'input_name' => __('admin.name_of_role') , 
        ] ,
        
    ]" 
>
    <x-slot name="tableContent">
        <div class="table_content_append card">

        </div>
    </x-slot>
</x-admin.table>


    
@endsection

@section('js')

    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js')}}"></script>

    {{-- delete all script --}}
        @include('admin.shared.deleteAll')
    {{-- delete all script --}}

    {{-- delete one user script --}}
        @include('admin.shared.deleteOne')
    {{-- delete one user script --}}

    {{-- delete one user script --}}
        @include('admin.shared.filter_js' , [ 'index_route' => url('admin/roles')])
    {{-- delete one user script --}}

         <script>
        // Product deletion validation SweetAlert messages
        $(document).ready(function() {
            // Remove existing delete-row event handlers to avoid conflicts
            $(document).off('click', '.delete-row');
            
            // Custom delete handler for products with validation
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
                                if (response.status === 'success') {
                                    Swal.fire({
                                        type: 'success',
                                        title: '{{__('admin.the_selected_has_been_successfully_deleted')}}',
                                        showConfirmButton: false,
                                        timer: 1500,
                                        confirmButtonClass: 'btn btn-primary',
                                        buttonsStyling: false,
                                    });
                                    getData({'searchArray' : searchArray()});
                                } else {
                                    // Show validation error for products with orders
                                    Swal.fire({
                                        type: 'error',
                                        title: '{{__('admin.error')}}',
                                        text: response.message || '{{__('admin.something_went_wrong')}}',
                                        confirmButtonClass: 'btn btn-primary',
                                        buttonsStyling: false,
                                    });
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
@endsection
