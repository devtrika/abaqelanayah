@extends('admin.layout.master')
{{-- extra css files --}}
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
@endsection
{{-- extra css files --}}

@section('content')
<!-- // Basic multiple Column Form section start -->
<form method="POST" action="{{ route('admin.clients.update', ['id' => $row->id]) }}" class="store form-horizontal" novalidate enctype="multipart/form-data">
    <section id="multiple-column-form">
        <div class="row">
            {{-- <div class="col-md-3">
                <div class="col-12 card card-body">
                    <div class="imgMontg col-12 text-center">
                        <div class="dropBox">
                            <div class="textCenter">
                                <div class="imagesUploadBlock">
                                    <label class="uploadImg">
                                        <span><i class="feather icon-image"></i></span>
                                        <input type="file" accept="image/*" name="image" class="imageUploader">
                                    </label>
                                    <div class="uploadedBlock">
                                        <img src="{{ $row->user_image }}">
                                        <button class="close"><i class="la la-times"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.name') }}</label>
                                            <div class="controls">
                                                <input type="text" name="name" value="{{ $row->name }}" class="form-control" placeholder="{{ __('admin.write_the_name') }}" required data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.phone_number') }}</label>
                                            <div class="row">
                                                <div class="col-md-4 col-12">
                                                    <select name="country_code" class="form-control select2">
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->key }}"
                                                                @if ($row->country_code == $country->key) selected @endif>
                                                                {{ '+' . $country->key }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-8 col-12">
                                                    <div class="controls">
                                                        <input type="number" name="phone" value="{{$row->phone }}" class="form-control" placeholder="{{ __('admin.enter_phone_number') }}" required data-validation-required-message="{{ __('admin.this_field_is_required') }}" data-validation-number-message="{{ __('admin.the_phone_number_ must_not_have_charachters_or_symbol') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.email') }}</label>
                                            <div class="controls">
                                                <input type="email" name="email" value="{{ $row->email }}" class="form-control" placeholder="{{ __('admin.enter_the_email') }}"  data-validation-email-message="{{ __('admin.email_formula_is_incorrect') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.password') }}</label>
                                            <div class="controls">
                                                <input type="password" name="password" class="form-control" placeholder="{{ __('admin.enter_the_password') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.confirm_password') }}</label>
                                            <div class="controls">
                                                <input type="password" name="password_confirmation" class="form-control" placeholder="{{ __('admin.retype_password') }}">
                                            </div>
                                        </div>
                                    </div>

                  


                                                                      <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="city_id">{{__('admin.City')}}</label>
                                            <div class="controls">
                                                <select name="city_id" id="city_id" class="form-control select2" required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                                    <option value="">{{__('admin.select_city')}}</option>
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->id }}" {{ (old('city_id', $row->city_id) == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="district_id">{{__('admin.District')}}</label>
                                            <div class="controls">
                                                <select name="district_id" id="district_id" class="form-control select2" required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                                    <option value="">{{__('admin.select_district')}}</option>
                                                    @foreach($districts as $district)
                                                        <option value="{{ $district->id }}" {{ (old('district_id', $row->district_id) == $district->id) ? 'selected' : '' }}>{{ $district->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                        

                                    {{-- <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.Validity') }}</label>
                                            <div class="controls">
                                                <select name="is_blocked" class="select2 form-control" required data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                    <option value="">{{ __('admin.Select_the_blocking_status') }}</option>
                                                    <option value="1" {{ $row->is_blocked == 1 ? 'selected' : '' }}>{{ __('admin.Prohibited') }}</option>
                                                    <option value="0" {{ $row->is_blocked == 0 ? 'selected' : '' }}>{{ __('admin.Unspoken') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="gender">{{ __('admin.gender') }}</label>
                                            <div class="controls">
                                                <select name="gender" id="gender" class="form-control select2" required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                                    <option value="">{{ __('admin.select_gender') }}</option>
                                                    <option value="male" {{ (old('gender', $row->gender) == 'male') ? 'selected' : '' }}>{{ __('admin.male') }}</option>
                                                    <option value="female" {{ (old('gender', $row->gender) == 'female') ? 'selected' : '' }}>{{ __('admin.female') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-center mt-3">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{ __('admin.update') }}</button>
                                        <a href="{{ url()->previous() }}" type="reset" class="btn btn-outline-warning mr-1 mb-1">{{ __('admin.back') }}</a>
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
    <script src="{{ asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/forms/validation/form-validation.js') }}"></script>
    <script src="{{ asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js') }}"></script>

    {{-- show selected image script --}}
    @include('admin.shared.addImage')
    {{-- show selected image script --}}

    {{-- submit edit form script --}}
    @include('admin.shared.submitEditForm')
    {{-- submit edit form script --}}

    {{-- City Districts AJAX Script --}}
    @include('admin.shared.cityDistrictDropdown')
    {{-- City Districts AJAX Script --}}
@endsection
