@extends('admin.layout.master')
{{-- extra css files --}}
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
@endsection
{{-- extra css files --}}

@section('content')
<form method="POST" action="{{route('admin.images.update', ['id' => $image->id])}}" class="store form-horizontal" novalidate enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{__('admin.edit')}}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">
                                            {{-- Name (AR) --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="name_ar">{{__('admin.name')}} ({{__('admin.ar')}})</label>
                                            <input type="text" id="name_ar" class="form-control" name="name[ar]" value="{{$image->getTranslation('name', 'ar')}}" required>
                                        </div>
                                    </div>

                                    {{-- Name (EN) --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="name_en">{{__('admin.name')}} ({{__('admin.en')}})</label>
                                            <input type="text" id="name_en" class="form-control" name="name[en]" value="{{$image->getTranslation('name', 'en')}}" required>
                                        </div>
                                    </div>

                                    {{-- Link Field --}}
                                    <div class="col-md-12 col-12">
                                        <div class="form-group">
                                            <label for="link">{{__('admin.link')}}</label>
                                            <input type="text" id="link" class="form-control" name="link" value="{{$image->link}}" placeholder="{{__('admin.write') . __('admin.link')}}">
                                        </div>
                                    </div>

                                    {{-- Image (AR) --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="image_ar">{{__('admin.image')}} ({{__('admin.ar')}})</label>
                                            <input type="file" id="image_ar" class="form-control" name="image_ar">
                                        </div>
                                        @if($image->image_ar)
                                            <img src="{{ $image->image_ar }}" alt="Arabic Image" class="img-thumbnail" width="100">
                                        @endif
                                    </div>

                                    {{-- Image (EN) --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="image_en">{{__('admin.image')}} ({{__('admin.en')}})</label>
                                            <input type="file" id="image_en" class="form-control" name="image_en">
                                        </div>
                                        @if($image->image_en)
                                            <img src="{{ $image->image_en }}" alt="English Image" class="img-thumbnail" width="100">
                                        @endif
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-12 col-12">
                                        <div class="form-group">
                                            <label for="is_active">{{__('admin.status')}}</label>
                                            <select name="is_active" class="form-control">
                                                <option value="1" {{$image->is_active ? 'selected' : ''}}>{{__('admin.active')}}</option>
                                                <option value="0" {{$image->is_active ? '' : 'selected'}}>{{__('admin.inactive')}}</option>
                                            </select>
                                        </div>
                                    </div>  

                                   
                                    <div class="col-12 d-flex justify-content-center mt-3">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.update')}}</button>
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

    {{-- submit edit form script --}}
        @include('admin.shared.submitEditForm')
    {{-- submit edit form script --}}
    
@endsection