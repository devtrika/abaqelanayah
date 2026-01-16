@extends('admin.layout.master')
{{-- extra css files --}}
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
@endsection
{{-- extra css files --}}

@section('content')
<form method="POST" action="{{route('admin.images.store')}}" class="store form-horizontal" novalidate enctype="multipart/form-data">
    @csrf
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{__('admin.add')}}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">
                                    {{-- Name Fields --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="name_ar">{{__('admin.name')}} ({{__('admin.ar')}})</label>
                                            <input type="text" id="name_ar" class="form-control" name="name[ar]" 
                                                placeholder="{{__('admin.write') . __('admin.name')}} {{__('admin.ar')}}"
                                                required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="name_en">{{__('admin.name')}} ({{__('admin.en')}})</label>
                                            <input type="text" id="name_en" class="form-control" name="name[en]" 
                                                placeholder="{{__('admin.write') . __('admin.name')}} {{__('admin.en')}}"
                                                required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                        </div>
                                    </div>

                                    {{-- Link Field --}}
                                    <div class="col-md-12 col-12">
                                        <div class="form-group">
                                            <label for="link">{{__('admin.link')}}</label>
                                            <input type="text" id="link" class="form-control" name="link" placeholder="{{__('admin.write') . __('admin.link')}}">
                                        </div>
                                    </div>

                                    {{-- Image (AR) --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="image_ar">{{__('admin.image')}} ({{__('admin.ar')}})</label>
                                            <input type="file" id="image_ar" class="form-control" name="image_ar" required>
                                        </div>
                                    </div>

                                    {{-- Image (EN) --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="image_en">{{__('admin.image')}} ({{__('admin.en')}})</label>
                                            <input type="file" id="image_en" class="form-control" name="image_en" required>
                                        </div>
                                    </div>

                              

                                    {{-- Status --}}
                                    <div class="col-md-12 col-12">
                                        <div class="form-group">
                                            <label for="is_active">{{__('admin.status')}}</label>
                                            <select name="is_active" class="form-control">
                                                <option value="1">{{__('admin.active')}}</option>
                                                <option value="0">{{__('admin.inactive')}}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-center mt-3">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.add')}}</button>
                                        <a href="{{ url()->previous() }}" type="reset" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</form>

@endsection
@section('js')
    <script src="{{asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/forms/validation/form-validation.js')}}"></script>
    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js')}}"></script>
    
    {{-- show selected image script --}}
        @include('admin.shared.addImage')
    {{-- show selected image script --}}

    {{-- submit add form script --}}
        @include('admin.shared.submitAddForm')
    {{-- submit add form script --}}
    
@endsection