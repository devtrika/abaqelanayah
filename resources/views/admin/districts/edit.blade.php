@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
@endsection

@section('content')
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.districts.update', $district->id) }}" class="store form-horizontal" novalidate autocomplete="off">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <ul class="nav nav-tabs mb-3">
                                            @foreach (languages() as $lang)
                                                <li class="nav-item">
                                                    <a class="nav-link @if($loop->first) active @endif"  data-toggle="pill" href="#first_{{$lang}}" aria-expanded="true">{{  __('admin.data') }} {{ $lang }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div class="tab-content">
                                            @foreach (languages() as $lang)
                                                <div role="tabpanel" class="tab-pane fade @if($loop->first) show active @endif " id="first_{{$lang}}" aria-labelledby="first_{{$lang}}" aria-expanded="true">
                                                    <div class="col-md-12 col-12">
                                                        <div class="form-group">
                                                            <label for="first-name-column">{{__('admin.name')}} {{ $lang }}</label>
                                                            <div class="controls">
                                                                <input type="text" name="name[{{$lang}}]" class="form-control" placeholder="{{__('admin.write') . __('admin.name')}} {{ $lang }}" value="{{ old('name.'.$lang, $district->getTranslation('name', $lang)) }}" @if($loop->first) required data-validation-required-message="{{__('admin.this_field_is_required')}}" @endif >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-md-12 col-12">
                                            <div class="form-group">
                                                <label for="city-column">{{__('admin.city')}}</label>
                                                <div class="controls">
                                                    <select name="city_id" class="select2 form-control" required data-validation-required-message="{{__('admin.this_field_is_required')}}" >
                                                        <option value="">{{__('admin.select_city')}}</option>
                                                        @foreach ($cities as $city)
                                                            <option value="{{$city->id}}" @if(old('city_id', $district->city_id) == $city->id) selected @endif>{{$city->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-12">
                                            <div class="form-group">
                                                <label for="status-column">{{__('admin.status')}}</label>
                                                <div class="controls">
                                                    <select name="status" class="form-control" required data-validation-required-message="{{__('admin.this_field_is_required')}}" >
                                                        <option value="1" @if(old('status', $district->status) == '1') selected @endif>{{__('admin.active')}}</option>
                                                        <option value="0" @if(old('status', $district->status) == '0') selected @endif>{{__('admin.inactive')}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-center mt-3">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.update')}}</button>
                                        <a href="{{ route('admin.districts.index') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
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
    @include('admin.shared.submitAddForm')
@endsection
