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
    deletebutton="{{ route('admin.course_enrollments.deleteAll') }}"
    :searchArray="[
        'id' => [
            'input_type' => 'text' ,
            'input_name' => __('admin.enrollment_id') ,
        ] ,
        'course_id' => [
            'input_type' => 'select' ,
            'rows'       => $courses->mapWithKeys(function($course) {
                return [$course->id => ['name' => $course->name, 'id' => $course->id]];
            })->toArray() ,
            'input_name' => __('admin.course_name') ,
        ] ,
        'user_id' => [
            'input_type' => 'select' ,
            'rows'       => $users->mapWithKeys(function($user) {
                return [$user->id => ['name' => $user->name, 'id' => $user->id]];
            })->toArray() ,
            'input_name' => __('admin.client_name') ,
        ] ,
        'payment_method' => [
            'input_type' => 'select' ,
            'rows'       => [
                'wallet' => [
                    'name' => 'محفظة' ,
                    'id' => 'wallet' ,
                ],
                'bank_transfer' => [
                    'name' => 'تحويل بنكي' ,
                    'id' => 'bank_transfer' ,
                ],
                'credit_card' => [
                    'name' => 'بطاقة ائتمان' ,
                    'id' => 'credit_card' ,
                ],
                'mada' => [
                    'name' => 'مدى' ,
                    'id' => 'mada' ,
                ],
                'apple_pay' => [
                    'name' => 'Apple Pay' ,
                    'id' => 'apple_pay' ,
                ],
            ] ,
            'input_name' => __('admin.payment_method') ,
        ] ,
        'payment_status' => [
            'input_type' => 'select' ,
            'rows'       => [
                'pending' => [
                    'name' => 'في الانتظار' ,
                    'id' => 'pending' ,
                ],
                'paid' => [
                    'name' => 'مدفوع' ,
                    'id' => 'paid' ,
                ],
                'failed' => [
                    'name' => 'فشل' ,
                    'id' => 'failed' ,
                ],
                'refunded' => [
                    'name' => 'مسترد' ,
                    'id' => 'refunded' ,
                ],
            ] ,
            'input_name' => 'حالة الدفع' ,
        ] ,
        'status' => [
            'input_type' => 'select' ,
            'rows'       => [
                'pending_payment' => [
                    'name' => 'في انتظار الدفع' ,
                    'id' => 'pending_payment' ,
                ],
                'active' => [
                    'name' => 'نشط' ,
                    'id' => 'active' ,
                ],
                'suspended' => [
                    'name' => 'معلق' ,
                    'id' => 'suspended' ,
                ],
                'completed' => [
                    'name' => 'مكتمل' ,
                    'id' => 'completed' ,
                ],
                'cancelled' => [
                    'name' => 'ملغي' ,
                    'id' => 'cancelled' ,
                ],
            ] ,
            'input_name' => 'حالة الاشتراك' ,
        ] ,
        'amount_from' => [
            'input_type' => 'number' ,
            'input_name' => 'المبلغ من' ,
        ] ,
        'amount_to' => [
            'input_type' => 'number' ,
            'input_name' => 'المبلغ إلى' ,
        ] ,
    ]"
>

    <x-slot name="extrabuttonsdiv">
        <a class="btn bg-gradient-info mr-1 mb-1 waves-effect waves-light"  href="{{url(route('admin.master-export', 'CourseEnrollment'))}}"><i  class="fa fa-file-excel-o"></i>
            {{ __('admin.export_enrollments') }}</a>
    </x-slot>

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

    {{-- filter and search script --}}
        @include('admin.shared.filter_js' , [ 'index_route' => url('admin/course-enrollments')])
    {{-- filter and search script --}}
@endsection
