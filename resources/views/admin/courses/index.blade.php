@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/index_page.css')}}">
@endsection

@section('content')

<x-admin.table
    datefilter="true"
    order="true"
    addbutton="{{ route('admin.courses.create') }}"
    deletebutton="{{ route('admin.courses.deleteAll') }}"
    :searchArray="[
        'name' => [
            'input_type' => 'text' ,
            'input_name' => __('admin.course_name') ,
        ] ,
        'instructor_name' => [
            'input_type' => 'text' ,
            'input_name' => __('admin.instructor_name') ,
        ] ,
        'price_from' => [
            'input_type' => 'number' ,
            'input_name' => __('admin.price_from') ,
        ] ,
        'price_to' => [
            'input_type' => 'number' ,
            'input_name' => __('admin.price_to') ,
        ] ,
                 'is_active' => [
            'input_type' => 'select' , 
            'rows'       => [
              '1' => [
                'name' => __('admin.active') , 
                'id' => 1 , 
              ],
              '2' => [
                'name' => __('admin.inactive') , 
                'id' => 0 , 
              ],
            ] , 
            'input_name' => __('admin.active') , 
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
    <script src="{{asset('admin/index_page.js')}}"></script>
    @include('admin.shared.deleteOne')
    @include('admin.shared.deleteAll')
    @include('admin.shared.filter_js', ['index_route' => url('admin/courses')])

    <script>
        // Setup CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('click', '.toggle-status', function() {
            let id = $(this).data('id');
            let button = $(this);

            $.ajax({
                url: '{{ route("admin.courses.toggleStatus") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Reload the table using the shared getData function
                        getData({'searchArray' : searchArray()});
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء تغيير حالة الدورة',
                        type: 'error'
                    });
                }
            });
        });
    </script>
@endsection
