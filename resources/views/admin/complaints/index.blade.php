@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/index_page.css')}}">
@endsection

@section('content')

<x-admin.table 
    datefilter="true" 
    order="true" 
    deletebutton="{{ route('admin.complaints.deleteAll') }}" 
    :searchArray="[
        'user_name' => [
            'input_type' => 'text' , 
            'input_name' => __('admin.the_sender_name') , 
        ] ,
        'type' => [
            'input_type' => 'select',
            'rows' => [
                'suggest' => [
                    'name' => __('admin.suggestion'),
                    'id' => 'suggestion',
                ],
                'complaint' => [
                    'name' => __('admin.complaint'),
                    'id' => 'complaint',
                ],
            ],
            'input_name' => __('admin.type'),
        ],
        {{-- 'user_type' => [
            'input_type' => 'select',
            'rows' => [
                'client' => [
                    'name' => __('admin.client'),
                    'id' => 'client',
                ],
                'provider' => [
                    'name' => __('admin.provider'),
                    'id' => 'provider',
                ],
            ],
            'input_name' => __('admin.user_type'),
        ], --}}
        'is_read' => [
            'input_type' => 'select',
            'rows' => [
                '1' => [
                    'name' => __('admin.read'),
                    'id' => '1',
                ],
                '0' => [
                    'name' => __('admin.unread'),
                    'id' => '0',
                ],
            ],
            'input_name' => __('admin.read_status'),
        ],
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
    @include('admin.shared.deleteAll')
    @include('admin.shared.deleteOne')
    @include('admin.shared.filter_js' , [ 'index_route' => url('admin/all-complaints')])
@endsection
