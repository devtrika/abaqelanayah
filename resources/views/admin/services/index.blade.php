@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/index_page.css')}}">
@endsection

@section('content')

<x-admin.table
    datefilter="true"
    order="true"
    addbutton="{{ route('admin.services.create') }}"

    deletebutton="{{ route('admin.services.deleteAll') }}"
    :searchArray="[
        'name' => [
            'input_type' => 'text' ,
            'input_name' => __('admin.service_name') ,
        ] ,
        'description' => [
            'input_type' => 'text' ,
            'input_name' => __('admin.service_description') ,
        ] ,
        'provider_id' => [
            'input_type' => 'select' ,
            'input_name' => __('admin.service_provider') ,
            'rows' => collect([['id' => '', 'name' => __('admin.all')]])->concat(
                \App\Models\Provider::with('user')->get()->map(function($provider) {
                    return ['id' => $provider->id, 'name' => $provider->user->name ?? 'No Name'];
                })
            )->toArray()
        ] ,
        'category_id' => [
            'input_type' => 'select' ,
            'input_name' => __('admin.service_category') ,
            'rows' => collect([['id' => '', 'name' => __('admin.all')]])->concat(
                \App\Models\Category::where('is_active', 1)->get()->map(function($category) {
                    return ['id' => $category->id, 'name' => $category->name];
                })
            )->toArray()
        ] ,
        'price' => [
            'input_type' => 'number' ,
            'input_name' => __('admin.service_price') ,
        ] ,
        'is_active' => [
            'input_type' => 'select' ,
            'input_name' => __('admin.service_status') ,
            'rows' => [
                ['id' => '', 'name' => __('admin.all')],
                ['id' => '1', 'name' => __('admin.active')],
                ['id' => '0', 'name' => __('admin.inactive')],
            ]
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
    @include('admin.shared.deleteAll')
    @include('admin.shared.deleteOne')
    @include('admin.shared.filter_js' , [ 'index_route' => url('admin/services')])
@endsection
