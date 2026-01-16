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
<form method="POST" action="{{ route('admin.providers.update', ['id' => $user->id]) }}" class="store form-horizontal" novalidate enctype="multipart/form-data">
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
                                <div class="uploadedBlock">
                                    <img src="{{ $user->image }}">
                                    <button class="close"><i class="la la-times"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center mt-2">{{__('admin.profile_image')}}</p>
            </div>
        </div>

        <div class="col-9">
            <div class="card">
                <div class="card-header">
                    <div class="row w-100 align-items-center">
                        <div class="col-md-6">
                            <h4 class="card-title">{{__('admin.edit_provider')}}</h4>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label for="status" class="mb-1">{{__('admin.provider_status')}}</label>
                                <select class="form-control status-dropdown" data-id="{{$user->provider->id ?? ''}}" id="status">
                                    <option value="in_review" {{ ($user->provider->status ?? '') == 'in_review' ? 'selected' : '' }}>{{ __('admin.in_review') }}</option>
                                    <option value="pending" {{ ($user->provider->status ?? '') == 'pending' ? 'selected' : '' }}>{{ __('admin.pending') }}</option>
                                    <option value="accepted" {{ ($user->provider->status ?? '') == 'accepted' ? 'selected' : '' }}>{{ __('admin.accepted') }}</option>
                                    <option value="rejected" {{ ($user->provider->status ?? '') == 'rejected' ? 'selected' : '' }}>{{ __('admin.rejected') }}</option>
                                    <option value="blocked" {{ ($user->provider->status ?? '') == 'blocked' ? 'selected' : '' }}>{{ __('admin.blocked') }}</option>
                                    <option value="deleted" {{ ($user->provider->status ?? '') == 'deleted' ? 'selected' : '' }}>{{ __('admin.deleted') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        @csrf
                        @method('PUT')

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
                        <div class="tab-content"

                        <div class="form-body">
                            <!-- Basic Information Tab -->
                            <div class="tab-pane active" id="basic-info" role="tabpanel" aria-labelledby="basic-info-tab">
                                <div class="form-body mt-3">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label>{{ __('admin.name') }}</label>
                                                <div class="controls">
                                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('admin.write_the_name') }}" required data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label>{{ __('admin.phone_number') }}</label>
                                                <div class="row">
                                                    <div class="col-md-4 col-12">
                                                        <select name="country_code" class="form-control select2 @error('country_code') is-invalid @enderror">
                                                            @foreach($countries as $country)
                                                                <option value="{{ $country->key }}"
                                                                    @if (old('country_code', $user->country_code) == $country->key) selected @endif>
                                                                    {{ '+' . $country->key}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('country_code')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-8 col-12">
                                                        <div class="controls">
                                                            <input type="number" name="phone" value="{{ old('phone',  $user->phone) }}" class="form-control @error('phone') is-invalid @enderror" placeholder="{{ __('admin.enter_phone_number') }}" required data-validation-required-message="{{ __('admin.this_field_is_required') }}" data-validation-number-message="{{ __('admin.the_phone_number_ must_not_have_charachters_or_symbol') }}">
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
                                                <label>{{ __('admin.email') }}</label>
                                                <div class="controls">
                                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('admin.enter_the_email') }}">
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>  
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label>{{ __('admin.password') }}</label>
                                                <div class="controls">
                                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('admin.leave_empty_to_keep_current') }}">
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
                                                    <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"  >
                                                    @error('password_confirmation')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="region_id">{{__('admin.Region')}}</label>
                                                <div class="controls">
                                                    <select name="region_id" id="region_id" class="form-control select2" required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                                        <option value="">{{__('admin.select_region')}}</option>
                                                        @foreach($regions as $region)
                                                            <option value="{{ $region->id }}" {{ (old('region_id', $user->region_id) == $region->id) ? 'selected' : '' }}>{{ $region->name }}</option>
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
                                                        @foreach($cities as $city)
                                                            <option value="{{ $city->id }}" {{ (old('city_id', $user->city_id) == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label>{{ __('admin.gender') }}</label>
                                                <div class="controls">
                                                    <select name="gender" class="select2 form-control @error('gender') is-invalid @enderror" required data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                        <option value="">{{ __('admin.select_gender') }}</option>
                                                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>{{ __('admin.male') }}</option>
                                                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>{{ __('admin.female') }}</option>
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
                                                    <input type="text" name="commercial_name[ar]" value="{{ old('commercial_name.ar', $user->provider->getTranslation('commercial_name', 'ar') ?? '') }}" class="form-control @error('commercial_name.ar') is-invalid @enderror" placeholder="{{__('admin.commercial_name')}} {{__('admin.in_arabic')}}" required data-validation-required-message="{{__('admin.this_field_is_required')}}" >
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
                                                    <input type="text" name="commercial_name[en]" value="{{ old('commercial_name.en', $user->provider->getTranslation('commercial_name', 'en') ?? '') }}" class="form-control @error('commercial_name.en') is-invalid @enderror" placeholder="{{__('admin.commercial_name')}} {{__('admin.in_english')}}" >
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
                                                    <input type="text" name="commercial_register_no" value="{{ $user->provider->commercial_register_no ?? '' }}" class="form-control" placeholder="{{__('admin.commercial_register_no')}}" >
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="institution_name">{{__('admin.institution_name')}}</label>
                                                <div class="controls">
                                                    <input type="text" name="institution_name" value="{{ $user->provider->institution_name ?? '' }}" class="form-control" placeholder="{{__('admin.institution_name')}}" >
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="sponsor_name">{{__('admin.sponsor_name')}}</label>
                                                <div class="controls">
                                                    <input type="text" name="sponsor_name" value="{{ $user->provider->sponsor_name ?? '' }}" class="form-control" placeholder="{{__('admin.sponsor_name')}}" >
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="sponsor_phone">{{__('admin.sponsor_phone')}}</label>
                                                <div class="controls">
                                                    <input type="text" name="sponsor_phone" value="{{ $user->provider->sponsor_phone ?? '' }}" class="form-control" placeholder="{{__('admin.sponsor_phone')}}" >
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="nationality">{{__('admin.nationality')}}</label>
                                                <div class="controls">
                                                    <select name="nationality" class="select2 form-control @error('nationality') is-invalid @enderror" required data-validation-required-message="{{__('admin.this_field_is_required')}}" >
                                                        <option value="">{{__('admin.select_nationality')}}</option>
                                                        <option value="saudi" {{ old('nationality', $user->provider->nationality ?? '') == 'saudi' ? 'selected' : '' }}>{{__('admin.saudi')}}</option>
                                                        <option value="other" {{ old('nationality', $user->provider->nationality ?? '') == 'other' ? 'selected' : '' }}>{{__('admin.other')}}</option>
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
                                                        <option value="individual" {{ old('residence_type', $user->provider->residence_type ?? '') == 'individual' ? 'selected' : '' }}>{{__('admin.individual')}}</option>
                                                        <option value="professional" {{ old('residence_type', $user->provider->residence_type ?? '') == 'professional' ? 'selected' : '' }}>{{__('admin.professional')}}</option>
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
                                                        <option value="salon" {{ old('salon_type', $user->provider->salon_type ?? '') == 'salon' ? 'selected' : '' }}>{{__('admin.salon')}}</option>
                                                        <option value="beauty_center" {{ old('salon_type', $user->provider->salon_type ?? '') == 'beauty_center' ? 'selected' : '' }}>{{__('admin.beauty_center')}}</option>
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
                                                    <textarea name="description" class="form-control" rows="3" placeholder="{{__('admin.description')}}">{{ $user->provider->description ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label>{{__('admin.in_home')}}</label>
                                                <div class="controls">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="in_home" value="1" class="custom-control-input" id="in_home" {{ ($user->provider->in_home ?? 0) ? 'checked' : '' }}>
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
                                                        <input type="checkbox" name="in_salon" value="1" class="custom-control-input" id="in_salon" {{ ($user->provider->in_salon ?? 0) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="in_salon">{{__('admin.provides_salon_service')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="comission">{{__('admin.platform_commission')}}</label>
                                                <div class="controls">
                                                    <input type="number" step="0.01" name="comission" value="{{ $user->provider->comission ?? $salonCommission ??  '' }}" class="form-control" placeholder="{{__('admin.comission')}}" >
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
                                                <label for="logo">{{__('admin.logo')}}</label>
                                                <div class="controls">
                                                    <input type="file" accept="image/*" name="logo" class="form-control">
                                                    @if($user->provider && $user->provider->getFirstMediaUrl('logo'))
                                                        <div class="mt-2">
                                                            <img src="{{ $user->provider->getFirstMediaUrl('logo') }}" alt="Logo" class="img-thumbnail" width="100">
                                                            <input type="hidden" name="existing_logo_id" value="{{ $user->provider->getFirstMedia('logo')?->first()->id ?? '' }}">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="commercial_register_image">{{__('admin.commercial_register_image')}}</label>
                                                <div class="controls">
                                                    <input type="file" accept="image/*" name="commercial_register_image" class="form-control">
                                                    @if($user->provider && $user->provider->getFirstMediaUrl('commercial_register_image'))
                                                        <div class="mt-2">
                                                            <img src="{{ $user->provider->getFirstMediaUrl('commercial_register_image') }}" alt="Commercial Register" class="img-thumbnail" width="100">
                                                            <input type="hidden" name="existing_commercial_register_image_id" value="{{ $user->provider->getFirstMedia('commercial_register_image')?->first()->id ?? '' }}">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="residence_image">{{__('admin.residence_image')}}</label>
                                                <div class="controls">
                                                    <input type="file" accept="image/*" name="residence_image" class="form-control">
                                                    @if($user->provider && $user->provider->getFirstMediaUrl('residence_image'))
                                                        <div class="mt-2">
                                                            <img src="{{ $user->provider->getFirstMediaUrl('residence_image') }}" alt="Residence" class="img-thumbnail" width="100">
                                                            <input type="hidden" name="existing_residence_image_id" value="{{ $user->provider->getFirstMedia('residence_image')?->first()->id ?? '' }}">
                                                        </div>
                                                    @endif
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
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="salon_images">{{__('admin.salon_images')}}</label>
                                                <div class="controls">
                                                    <input type="file" accept="image/*" name="salon_images[]" class="form-control" multiple>
                                                    @if($user->provider && $user->provider->getMedia('salon_images'))
                                                        <div class="mt-2">
                                                            @foreach($user->provider->getMedia('salon_images') as $salonImage)
                                                                <img src="{{ $salonImage->getUrl() }}" alt="Salon Image" class="img-thumbnail" width="100">
                                                                <input type="hidden" name="existing_salon_images_id[]" value="{{ $salonImage->id }}">
                                                            @endforeach
                                                        </div>
                                                    @endif
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

                                                    // Get existing working hours
                                                    $existingWorkingHours = $user->provider ? $user->provider->workingHours->keyBy('day') : collect();
                                                @endphp

                                                @foreach($days as $dayKey => $dayName)
                                                @php
                                                    $workingHour = $existingWorkingHours->get($dayKey);
                                                    $isWorking = $workingHour ? $workingHour->is_working : false;
                                                    $startTime = $workingHour ? $workingHour->start_time : '';
                                                    $endTime = $workingHour ? $workingHour->end_time : '';
                                                    $isDisabled = !$workingHour || !$isWorking;
                                                @endphp
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
                                                                               {{ old("working_hours.{$dayKey}.is_working", $isWorking) ? 'checked' : '' }}>
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
                                                                           value="{{ old("working_hours.{$dayKey}.start_time", $startTime) }}"
                                                                           {{ $isDisabled ? 'disabled' : '' }}>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group mb-0">
                                                                    <label for="end_time_{{ $dayKey }}">{{__('admin.end_time')}}</label>
                                                                    <input type="time"
                                                                           class="form-control"
                                                                           id="end_time_{{ $dayKey }}"
                                                                           name="working_hours[{{ $dayKey }}][end_time]"
                                                                           value="{{ old("working_hours.{$dayKey}.end_time", $endTime) }}"
                                                                           {{ $isDisabled ? 'disabled' : '' }}>
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

                        <!-- Submit Buttons -->
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
</section>
</form>
@endsection

@section('js')
    <script src="{{ asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/forms/validation/form-validation.js') }}"></script>
    <script src="{{ asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js') }}"></script>
    <script src="{{ asset('admin/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>

    {{-- show selected image script --}}
    @include('admin.shared.addImage')
    {{-- show selected image script --}}

    {{-- submit edit form script --}}
    @include('admin.shared.submitEditForm')
    {{-- submit edit form script --}}
    @include('admin.shared.regionCityDropdown')

    {{-- Show validation errors in toaster --}}
    @if ($errors->any())
        <script>
            $(document).ready(function() {
                @foreach ($errors->all() as $error)
                    toastr.error('{{ $error }}', '{{ __('admin.validation_error') }}', {
                        closeButton: true,
                        progressBar: true,
                        timeOut: 5000,
                        positionClass: 'toast-top-right'
                    });
                @endforeach
            });
        </script>
    @endif

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
