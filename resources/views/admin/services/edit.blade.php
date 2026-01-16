@extends('admin.layout.master')
{{-- extra css files --}}
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
@endsection
{{-- extra css files --}}

@section('content')
<!-- // Basic multiple Column Form section start -->
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <form method="POST" action="{{route('admin.services.update', ['id' => $service->id])}}" class="store form-horizontal" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">

                                    {{-- Service Name in Multiple Languages --}}
                                    @foreach (languages() as $lang)
                                        <div class="col-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="name_{{$lang}}">{{__('admin.service_name')}} {{__('admin.in_' . $lang)}} <span class="text-danger">*</span></label>
                                                    <input type="text" name="name[{{$lang}}]" class="form-control"
                                                           placeholder="{{__('admin.service_name')}} {{__('admin.in_' . $lang)}}"
                                                           value="{{$service->getTranslations('name')[$lang] ?? ''}}" required>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Service Description in Multiple Languages --}}
                                    @foreach (languages() as $lang)
                                        <div class="col-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="description_{{$lang}}">{{__('admin.service_description')}} {{__('admin.in_' . $lang)}} <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="description[{{$lang}}]" rows="4"
                                                              placeholder="{{__('admin.service_description')}} {{__('admin.in_' . $lang)}}" required>{{$service->getTranslations('description')[$lang] ?? ''}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Provider Selection --}}
                                    <div class="col-6">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label for="provider_id">{{__('admin.service_provider')}} <span class="text-danger">*</span></label>
                                                <select class="form-control" name="provider_id" required>
                                                    <option value="">{{__('admin.select_provider')}}</option>
                                                    @foreach($providers as $provider)
                                                        <option value="{{$provider->id}}" {{$service->provider_id == $provider->id ? 'selected' : ''}}>
                                                            {{$provider->user->name}} - {{$provider->commercial_name}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Category Selection --}}
                                    <div class="col-6">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label for="category_id">{{__('admin.service_category')}} <span class="text-danger">*</span></label>
                                                <select class="form-control" name="category_id" required>
                                                    <option value="">{{__('admin.select_category')}}</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{$category->id}}" {{$service->category_id == $category->id ? 'selected' : ''}}>
                                                            {{$category->name}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Price --}}
                                    <div class="col-6">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label for="price">{{__('admin.service_price')}} <span class="text-danger">*</span></label>
                                                <input type="number" name="price" class="form-control" step="0.01" min="0" max="999999.99"
                                                       placeholder="{{__('admin.service_price')}}" value="{{$service->price}}" required>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Duration --}}
                                    <div class="col-6">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label for="duration">{{__('admin.duration_in_minutes')}} <span class="text-danger">*</span></label>
                                                <input type="number" name="duration" class="form-control" min="1" max="1440"
                                                       placeholder="{{__('admin.duration_in_minutes')}}" value="{{$service->duration}}" required>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Expected Time to Accept --}}
                                    <div class="col-6">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label for="expected_time_to_accept">{{__('admin.expected_time_in_minutes')}} <span class="text-danger">*</span></label>
                                                <input type="number" name="expected_time_to_accept" class="form-control" min="1" max="1440"
                                                       placeholder="{{__('admin.expected_time_in_minutes')}}" value="{{$service->expected_time_to_accept}}" required>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-6">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label for="is_active">{{__('admin.service_status')}}</label>
                                                <select class="form-control" name="is_active">
                                                    <option value="1" {{$service->is_active ? 'selected' : ''}}>{{__('admin.active')}}</option>
                                                    <option value="0" {{!$service->is_active ? 'selected' : ''}}>{{__('admin.inactive')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-center mt-3">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.update')}}</button>
                                        <a href="{{ url()->previous() }}" type="reset" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@section('js')
    <script src="{{asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/forms/validation/form-validation.js')}}"></script>
    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js')}}"></script>

    {{-- submit edit form script --}}
    @include('admin.shared.submitEditForm')
    {{-- submit edit form script --}}

@endsection
