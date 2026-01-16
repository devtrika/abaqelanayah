@extends('admin.layout.master')
{{-- extra css files --}}
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/toastr.css')}}">
@endsection
{{-- extra css files --}}

@section('content')
<!-- // Basic multiple Column Form section start -->
<form method="POST" action="{{route('admin.providers.store')}}" class="store form-horizontal" novalidate enctype="multipart/form-data">
<section id="multiple-column-form">
    <div class="row">
        <div class="col-md-3">
            <div class="col-12 card card-body">
                <div class="imgMontg col-12 text-center">
                    <div class="dropBox">
                        <div class="textCenter">
                            <div class="imagesUploadBlock">
                                <label class="uploadImg">
                                    <span><i class="feather icon-image"></i></span>
                                    <input type="file" accept="image/*" name="image" class="imageUploader">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center mt-2">{{__('admin.profile_image')}}</p>
            </div>
        </div>

        <div class="col-9">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        @csrf
                        <input type="hidden" name="type" value="provider">

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="basic-info-tab" data-toggle="tab" href="#basic-info" aria-controls="basic-info" role="tab" aria-selected="true">
                                    <i class="feather icon-user"></i> {{__('admin.basic_information')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="provider-info-tab" data-toggle="tab" href="#provider-info" aria-controls="provider-info" role="tab" aria-selected="false">
                                    <i class="feather icon-briefcase"></i> {{__('admin.provider_information')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents" aria-controls="documents" role="tab" aria-selected="false">
                                    <i class="feather icon-file-text"></i> {{__('admin.documents')}}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" id="salon-images-tab" data-toggle="tab" href="#salon-images" aria-controls="salon-images" role="tab" aria-selected="false">
                                    <i class="feather icon-image"></i> {{__('admin.salon_images')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="working-hours-tab" data-toggle="tab" href="#working-hours" aria-controls="working-hours" role="tab" aria-selected="false">
                                    <i class="feather icon-clock"></i> {{__('admin.working_hours')}}
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <!-- Basic Information Tab -->
                            <div class="tab-pane active" id="basic-info" role="tabpanel" aria-labelledby="basic-info-tab">
                                <div class="form-body mt-3">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="name">{{__('admin.name')}}</label>
                                                <div class="controls">
                                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="{{__('admin.write_the_name')}}" required data-validation-required-message="{{__('admin.this_field_is_required')}}" >
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="phone">{{__('admin.phone_number')}}</label>
                                                <div class="row">
                                                    <div class="col-md-4 col-12">
                                                        <select name="country_code" class="form-control select2 @error('country_code') is-invalid @enderror">
                                                            @foreach($countries as $country)
                                                                <option value="{{ $country->key }}"
                                                                    @if (old('country_code', $settings['default_country']) == $country->id)
                                                                        selected
                                                                    @endif >
                                                                {{ '+'.$country->key }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('country_code')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-8 col-12">
                                                        <div class="controls">
                                                            <input type="number" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="{{__('admin.enter_phone_number')}}" required data-validation-required-message="{{__('admin.this_field_is_required')}}" data-validation-number-message="{{__('admin.the_phone_number_ must_not_have_charachters_or_symbol')}}"  >
                                                            @error('phone')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="email">{{ __('admin.email')}}</label>
                                                <div class="controls">
                                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="{{__('admin.enter_the_email')}}" >
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="password">{{__('admin.password')}}</label>
                                                <div class="controls">
                                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"  required data-validation-required-message="{{__('admin.this_field_is_required')}}" >
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="password_confirmation">{{__('admin.password_confirmation')}}</label>
                                                <div class="controls">
                                                    <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required data-validation-required-message="{{__('admin.this_field_is_required')}}" >
                                                    @error('password_confirmation')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <label for="region_id">{{__('admin.Region')}}</label>
                                                <div class="controls">
                                                    <select name="region_id" id="region_id" class="form-control select2" required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                                        <option value="">{{__('admin.select_region')}}</option>
                                                        @foreach($regions as $region)
                                                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
        
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="city_id">{{__('admin.City')}}</label>
                                                <div class="controls">
                                                    <select name="city_id" id="city_id" class="form-control select2" required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                                        <option value="">{{__('admin.select_city')}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="gender">{{__('admin.gender')}}</label>
                                                <div class="controls">
                                                    <select name="gender" class="select2 form-control @error('gender') is-invalid @enderror" required data-validation-required-message="{{__('admin.this_field_is_required')}}" >
                                                        <option value="">{{__('admin.select_gender')}}</option>
                                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{__('admin.male')}}</option>
                                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{__('admin.female')}}</option>
                                                    </select>
                                                    @error('gender')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Provider Information Tab -->
                            <div class="tab-pane" id="provider-info" role="tabpanel" aria-labelledby="provider-info-tab">
                                <div class="form-body mt-3">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="commercial_name_ar">{{__('admin.commercial_name')}} ({{__('admin.arabic')}})</label>
                                                <div class="controls">
                                                    <input type="text" name="commercial_name[ar]" value="{{ old('commercial_name.ar') }}" class="form-control @error('commercial_name.ar') is-invalid @enderror" placeholder="{{__('admin.commercial_name')}} {{__('admin.in_arabic')}}" >
                                                    @error('commercial_name.ar')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="commercial_name_en">{{__('admin.commercial_name')}} ({{__('admin.english')}})</label>
                                                <div class="controls">
                                                    <input type="text" name="commercial_name[en]" value="{{ old('commercial_name.en') }}" class="form-control @error('commercial_name.en') is-invalid @enderror" placeholder="{{__('admin.commercial_name')}} {{__('admin.in_english')}}" >
                                                    @error('commercial_name.en')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="commercial_register_no">{{__('admin.commercial_register_no')}}</label>
                                                <div class="controls">
                                                    <input type="text" name="commercial_register_no" value="{{ old('commercial_register_no') }}" class="form-control @error('commercial_register_no') is-invalid @enderror" placeholder="{{__('admin.commercial_register_no')}}" >
                                                    @error('commercial_register_no')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="institution_name">{{__('admin.institution_name')}}</label>
                                                <div class="controls">
                                                    <input type="text" name="institution_name" value="{{ old('institution_name') }}" class="form-control @error('institution_name') is-invalid @enderror" placeholder="{{__('admin.institution_name')}}" >
                                                    @error('institution_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="sponsor_name">{{__('admin.sponsor_name')}}</label>
                                                <div class="controls">
                                                    <input type="text" name="sponsor_name" value="{{ old('sponsor_name') }}" class="form-control @error('sponsor_name') is-invalid @enderror" placeholder="{{__('admin.sponsor_name')}}" >
                                                    @error('sponsor_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="sponsor_phone">{{__('admin.sponsor_phone')}}</label>
                                                <div class="controls">
                                                    <input type="text" name="sponsor_phone" value="{{ old('sponsor_phone') }}" class="form-control @error('sponsor_phone') is-invalid @enderror" placeholder="{{__('admin.sponsor_phone')}}" >
                                                    @error('sponsor_phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="nationality">{{__('admin.nationality')}}</label>
                                                <div class="controls">
                                                    <select name="nationality" class="select2 form-control @error('nationality') is-invalid @enderror" required data-validation-required-message="{{__('admin.this_field_is_required')}}" >
                                                        <option value="">{{__('admin.select_nationality')}}</option>
                                                        <option value="saudi" {{ old('nationality') == 'saudi' ? 'selected' : '' }}>{{__('admin.saudi')}}</option>
                                                        <option value="other" {{ old('nationality') == 'other' ? 'selected' : '' }}>{{__('admin.other')}}</option>
                                                    </select>
                                                    @error('nationality')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="residence_type">{{__('admin.residence_type')}}</label>
                                                <div class="controls">
                                                    <select name="residence_type" class="select2 form-control @error('residence_type') is-invalid @enderror">
                                                        <option value="">{{__('admin.select_residence_type')}}</option>
                                                        <option value="individual" {{ old('residence_type') == 'individual' ? 'selected' : '' }}>{{__('admin.individual')}}</option>
                                                        <option value="professional" {{ old('residence_type') == 'professional' ? 'selected' : '' }}>{{__('admin.professional')}}</option>
                                                    </select>
                                                    @error('residence_type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="salon_type">{{__('admin.salon_type')}}</label>
                                                <div class="controls">
                                                    <select name="salon_type" class="select2 form-control @error('salon_type') is-invalid @enderror" required data-validation-required-message="{{__('admin.this_field_is_required')}}" >
                                                        <option value="">{{__('admin.select_salon_type')}}</option>
                                                        <option value="salon" {{ old('salon_type') == 'salon' ? 'selected' : '' }}>{{__('admin.salon')}}</option>
                                                        <option value="beauty_center" {{ old('salon_type') == 'beauty_center' ? 'selected' : '' }}>{{__('admin.beauty_center')}}</option>
                                                    </select>
                                                    @error('salon_type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-12">
                                            <div class="form-group">
                                                <label for="description">{{__('admin.description')}}</label>
                                                <div class="controls">
                                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="{{__('admin.description')}}">{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label>{{__('admin.in_home')}}</label>
                                                <div class="controls">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="in_home" value="1" class="custom-control-input" id="in_home">
                                                        <label class="custom-control-label" for="in_home">{{__('admin.provides_home_service')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label>{{__('admin.in_salon')}}</label>
                                                <div class="controls">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="in_salon" value="1" class="custom-control-input" id="in_salon">
                                                        <label class="custom-control-label" for="in_salon">{{__('admin.provides_salon_service')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                        
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="comission">{{__('admin.platform_commission')}}</label>
                                                <div class="controls">
                                                    <input type="number" step="0.01" name="comission" value="{{ $salonCommission }}" class="form-control @error('comission') is-invalid @enderror" placeholder="{{__('admin.comission')}}" >
                                                    @error('home_fees')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Salon Images Tab -->

                            <div class="tab-pane" id="salon-images" role="tabpanel" aria-labelledby="salon-images-tab">
                                <div class="form-body mt-3">
                                    <div class="row">
                                      <div class="col-md-12 col-12">
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="salon_images">{{__('admin.salon_images')}}</label>
                                                <div class="controls">
                                                    <input type="file" accept="image/*" name="salon_images[]" class="form-control @error('salon_images') is-invalid @enderror" multiple>
                                                    @error('salon_images')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                </div>
                            </div>

                                <!-- Documents Tab -->
                            <div class="tab-pane" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                                <div class="form-body mt-3">
                                    <div class="row">
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="logo">{{__('admin.logo')}} <span class="text-danger">*</span></label>
                                                <div class="controls">
                                                    <input type="file" accept="image/*" name="logo" class="form-control @error('logo') is-invalid @enderror" required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                                    @error('logo')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="commercial_register_image">{{__('admin.commercial_register_image')}} <span class="text-danger">*</span></label>
                                                <div class="controls">
                                                    <input type="file" accept="image/*" name="commercial_register_image" class="form-control @error('commercial_register_image') is-invalid @enderror" required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                                    @error('commercial_register_image')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="residence_image">{{__('admin.residence_image')}} <small>({{__('admin.required_if_nationality_other')}})</small></label>
                                                <div class="controls">
                                                    <input type="file" accept="image/*" name="residence_image" class="form-control @error('residence_image') is-invalid @enderror">
                                                    @error('residence_image')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    </div>
                            </div>

                            <!-- Working Hours Tab -->
                            <div class="tab-pane" id="working-hours" role="tabpanel" aria-labelledby="working-hours-tab">
                                <div class="form-body mt-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="mb-3">{{__('admin.working_hours')}}</h6>
                                            <div class="working-hours-container">
                                                @php
                                                    $days = [
                                                        'sunday' => __('admin.sunday'),
                                                        'monday' => __('admin.monday'),
                                                        'tuesday' => __('admin.tuesday'),
                                                        'wednesday' => __('admin.wednesday'),
                                                        'thursday' => __('admin.thursday'),
                                                        'friday' => __('admin.friday'),
                                                        'saturday' => __('admin.saturday')
                                                    ];
                                                @endphp

                                                @foreach($days as $dayKey => $dayName)
                                                <div class="card mb-2">
                                                    <div class="card-header bg-light-primary">
                                                        <h6 class="mb-0">{{ $dayName }}</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-3">
                                                                <div class="form-group mb-0">
                                                                    <div class="custom-control custom-switch">
                                                                        <input type="checkbox" class="custom-control-input"
                                                                               id="is_working_{{ $dayKey }}"
                                                                               name="working_hours[{{ $dayKey }}][is_working]"
                                                                               value="1"
                                                                               {{ old("working_hours.{$dayKey}.is_working", false) ? 'checked' : '' }}>
                                                                        <label class="custom-control-label" for="is_working_{{ $dayKey }}">
                                                                            {{__('admin.is_working')}}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group mb-0">
                                                                    <label for="start_time_{{ $dayKey }}">{{__('admin.start_time')}}</label>
                                                                    <input type="time"
                                                                           class="form-control"
                                                                           id="start_time_{{ $dayKey }}"
                                                                           name="working_hours[{{ $dayKey }}][start_time]"
                                                                           value="{{ old("working_hours.{$dayKey}.start_time", '09:00') }}"
                                                                           disabled>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group mb-0">
                                                                    <label for="end_time_{{ $dayKey }}">{{__('admin.end_time')}}</label>
                                                                    <input type="time"
                                                                           class="form-control"
                                                                           id="end_time_{{ $dayKey }}"
                                                                           name="working_hours[{{ $dayKey }}][end_time]"
                                                                           value="{{ old("working_hours.{$dayKey}.end_time", '22:00') }}"
                                                                           disabled>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="working_hours[{{ $dayKey }}][day]" value="{{ $dayKey }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
</section>
</form>
@endsection

@section('js')
    <script src="{{asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/forms/validation/form-validation.js')}}"></script>
    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js')}}"></script>
    <script src="{{asset('admin/app-assets/vendors/js/extensions/toastr.min.js')}}"></script>

    {{-- show selected image script --}}
    @include('admin.shared.addImage')
    {{-- show selected image script --}}

    {{-- submit add form script --}}
    @include('admin.shared.submitAddForm')
    {{-- submit add form script --}}
    @include('admin.shared.regionCityDropdown')

    {{-- Working hours toggle functionality --}}
    <script>
        $(document).ready(function() {
            // Working hours toggle functionality
            $(document).on('change', '.custom-control-input[id^="is_working_"]', function() {
                var dayContainer = $(this).closest('.card-body');
                var startTimeInput = dayContainer.find('input[name*="[start_time]"]');
                var endTimeInput = dayContainer.find('input[name*="[end_time]"]');

                if ($(this).is(':checked')) {
                    startTimeInput.prop('disabled', false);
                    endTimeInput.prop('disabled', false);

                    // Set default values if inputs are empty
                    if (!startTimeInput.val()) {
                        startTimeInput.val('09:00');
                    }
                    if (!endTimeInput.val()) {
                        endTimeInput.val('22:00');
                    }
                } else {
                    startTimeInput.prop('disabled', true);
                    endTimeInput.prop('disabled', true);
                }
            });

            // Prevent double form submission
            $('.store').on('submit', function() {
                $(this).find('button[type="submit"]').prop('disabled', true);
            });
        });
    </script>
@endsection
