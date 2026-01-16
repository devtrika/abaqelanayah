@extends('admin.layout.master')
{{-- extra css files --}}
@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css') }}">
  <style>
    /* Make CKEditor instances larger and more visible */
    .editor-large {
      min-height: 500px !important;
    }

    /* CKEditor container styling */
    .ck-editor__editable {
      min-height: 500px !important;
      max-height: 700px !important;
    }

    /* Full width tab content */
    .tab-content.w-100 {
      width: 100% !important;
    }

    /* Better spacing for form groups */
    .tab-pane .form-group {
      margin-bottom: 2rem;
    }

    /* Label styling */
    .tab-pane label.font-weight-bold {
      font-size: 1.1rem;
      margin-bottom: 0.75rem;
      color: #5e5873;
    }
  </style>
@endsection
{{-- extra css files --}}
@section('content')

<div class="content-body">
  <!-- account setting page start -->
  <section id="page-account-settings">
      <div class="row">
          <!-- left menu section -->
          <div class="col-md-3 mb-2 mb-md-0 ">
              <ul class="nav nav-pills flex-column mt-md-0 mt-1 card card-body">

                <li class="nav-item">
                    <a class="nav-link d-flex py-75 active" id="account-pill-main" data-toggle="pill" href="#account-vertical-main" aria-expanded="true">
                        <i class="feather icon-settings mr-50 font-medium-3"></i>
                        {{__('admin.app_setting')}}
                    </a>
                </li>
                {{-- <li class="nav-item" style="margin-top: 3px" >
                    <a class="nav-link d-flex py-75" id="account-pill-language" data-toggle="pill" href="#account-vertical-language" aria-expanded="true">
                        <i class="feather icon-settings mr-50 font-medium-3"></i>
                        {{__('admin.language_setting')}}
                    </a>
                </li>
               --}}
                <li class="nav-item" style="margin-top: 3px" >
                    <a class="nav-link d-flex py-75" id="account-pill-terms" data-toggle="pill" href="#account-vertical-terms" aria-expanded="false">
                        <i class="feather icon-edit-1 mr-50 font-medium-3"></i>
                        {{__('admin.terms_and_conditions')}}
                    </a>
                </li>
                <li class="nav-item " style="margin-top: 3px">
                    <a class="nav-link d-flex py-75" id="account-pill-about" data-toggle="pill" href="#account-vertical-about" aria-expanded="false">
                        <i class="feather icon-edit-1 mr-50 font-medium-3"></i>
                        {{__('admin.about_app')}}
                    </a>
                </li>
                <li class="nav-item " style="margin-top: 3px">
                    <a class="nav-link d-flex py-75" id="account-pill-privacy" data-toggle="pill" href="#account-vertical-privacy" aria-expanded="false">
                        <i class="feather icon-award mr-50 font-medium-3"></i>
                        {{__('admin.Privacy_policy')}}
                    </a>
                </li>
                <li class="nav-item " style="margin-top: 3px">
                    <a class="nav-link d-flex py-75" id="account-pill-returns" data-toggle="pill" href="#account-vertical-returns" aria-expanded="false">
                        <i class="feather icon-rotate-ccw mr-50 font-medium-3"></i>
                        {{__('admin.return_policy')}}
                    </a>
                </li>

                <li class="nav-item " style="margin-top: 3px">
                    <a class="nav-link d-flex py-75" id="account-pill-cancel-policy" data-toggle="pill" href="#account-vertical-cancel-policy" aria-expanded="false">
                        <i class="feather icon-award mr-50 font-medium-3"></i>
                        {{__('admin.cancel_policy')}}
                    </a>
                </li>
                <li class="nav-item " style="margin-top: 3px">
                    <a class="nav-link d-flex py-75" id="account-pill-smtp" data-toggle="pill" href="#account-vertical-smtp" aria-expanded="false">
                        <i class="feather icon-mail mr-50 font-medium-3"></i>
                        {{__('admin.email_data')}}
                    </a>
                </li>
            
              
                <!-- <li class="nav-item " style="margin-top: 3px">
                    <a class="nav-link d-flex py-75" id="account-pill-loyalty" data-toggle="pill" href="#account-vertical-loyalty" aria-expanded="false">
                        <i class="feather icon-award mr-50 font-medium-3"></i>
                        {{ __('admin.loyalty_points_settings') }}
                    </a>
                </li> -->
                <li class="nav-item " style="margin-top: 3px">
                    <a class="nav-link d-flex py-75" id="account-pill-fees" data-toggle="pill" href="#account-vertical-fees" aria-expanded="false">
                        <i class="feather icon-dollar-sign mr-50 font-medium-3"></i>
                        {{ __('admin.fees_settings') }}
                    </a>
                </li>
               
              </ul>
          </div>
          <!-- right content section -->
          <div class="col-md-9">
              <div class="card">
                  <div class="card-content">
                      <div class="card-body">
                          <div class="tab-content">

                              <div role="tabpanel" class="tab-pane active" id="account-vertical-main" aria-labelledby="account-pill-main" aria-expanded="true">
                                <form accept="{{route('admin.settings.update')}}" method="post" enctype="multipart/form-data" class="form-horizontal" novalidate>
                                  @method('put')
                                  @csrf
                                <div class="row">
                                  <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label for="account-name">{{__('admin.the_name_of_the_application_in_arabic')}}</label>
                                                <input type="text" class="form-control" name="name_ar" id="account-name" placeholder="{{__('admin.the_name_of_the_application_in_arabic')}}" value="{{$data['name_ar']}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label for="account-name">{{__('admin.the_name_of_the_application_in_english')}}</label>
                                                <input type="text" class="form-control" name="name_en" id="account-name" placeholder="{{__('admin.the_name_of_the_application_in_english')}}" value="{{$data['name_en']}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label for="account-name">{{__('admin.email')}}</label>
                                                <input type="email" class="form-control" name="email" id="account-name" placeholder="{{__('admin.email')}}" value="{{$data['email']}}" data-validation-email-message="{{__('admin.verify_the_email_format')}}" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label for="account-name">{{__('admin.phone')}}</label>
                                                <input type="text" class="form-control" name="phone" id="account-name" placeholder="{{__('admin.phone')}}" value="{{$data['phone']}}" minlength="10" required data-validation-required-message="{{__('admin.this_field_is_required')}}" data-validation-minlength-message="{{__('admin.the_number_should_only_be_less_than_ten_numbers')}}" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label for="account-name">{{__('admin.whatts_app_number')}}</label>
                                                <input type="text" class="form-control" name="whatsapp" id="account-name" placeholder="{{__('admin.whatts_app_number')}}" value="{{$data['whatsapp']}}" minlength="10"  data-validation-minlength-message="{{__('admin.the_number_should_only_be_less_than_ten_numbers')}}"  >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
    <div class="form-group">
        <div class="controls">
            <label for="app-store-link">{{__('admin.app_store_link')}}</label>
            <input type="url" class="form-control" name="app_store_link" id="app-store-link" placeholder="{{__('admin.app_store_link')}}" value="{{$data['app_store_link'] ?? ''}}" >
        </div>
    </div>
</div>

<div class="col-12 col-md-6">
    <div class="form-group">
        <div class="controls">
            <label for="google-play-link">{{__('admin.google_play_link')}}</label>
            <input type="url" class="form-control" name="google_play_link" id="google-play-link" placeholder="{{__('admin.google_play_link')}}" value="{{$data['google_play_link'] ?? ''}}" >
        </div>
    </div>
</div>

                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="account-name">is production </label>
                                            <div class="custom-control custom-switch custom-switch-success mr-2 mb-1">
                                                <input name="is_production" {{$data['is_production']  == '1' ? 'checked' : ''}}   type="checkbox" class="custom-control-input" id="customSwitch11">
                                                <label class="custom-control-label" for="customSwitch11">
                                                    <span class="switch-icon-left"><i class="feather icon-check"></i></span>
                                                    <span class="switch-icon-right"><i class="feather icon-check"></i></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-12">
                                      <div class="row">

                                        <div class="imgMontg col-12 col-lg-4 col-md-6 text-center">
                                            <div class="dropBox">
                                                <div class="textCenter d-flex flex-column">
                                                    <div class="imagesUploadBlock">
                                                        <label class="uploadImg">
                                                            <span><i class="feather icon-image"></i></span>
                                                            <input type="file" accept="image/*" name="logo" class="imageUploader">
                                                        </label>
                                                        <div class="uploadedBlock">
                                                            @if(isset($imageSettings['logo']) && $imageSettings['logo']->getFirstMediaUrl('logo'))
                                                                <img src="{{ $imageSettings['logo']->getFirstMediaUrl('logo') }}">
                                                            @else
                                                                <img src="{{ asset('storage/images/default.png') }}">
                                                            @endif
                                                            <button type="button" class="close remove-image-btn" data-img-key="logo"><i class="feather icon-trash-2"></i></button>
                                                        </div>
                                                        <input type="hidden" name="remove_logo" id="remove_logo" value="0">
                                                    </div>
                                                    <span>{{__('admin.logo_image')}}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="imgMontg col-12 col-lg-4 col-md-6 text-center">
                                            <div class="dropBox">
                                                <div class="textCenter d-flex flex-column">
                                                    <div class="imagesUploadBlock">
                                                        <label class="uploadImg">
                                                            <span><i class="feather icon-image"></i></span>
                                                            <input type="file" accept="image/*" name="fav_icon" class="imageUploader">
                                                        </label>
                                                        <div class="uploadedBlock">
                                                            @if(isset($imageSettings['fav_icon']) && $imageSettings['fav_icon']->getFirstMediaUrl('fav_icon'))
                                                                <img src="{{ $imageSettings['fav_icon']->getFirstMediaUrl('fav_icon') }}">
                                                            @else
                                                                <img src="{{ asset('storage/images/default.png') }}">
                                                            @endif
                                                            <button type="button" class="close remove-image-btn" data-img-key="fav_icon"><i class="feather icon-trash-2"></i></button>
                                                        </div>
                                                        <input type="hidden" name="remove_fav_icon" id="remove_fav_icon" value="0">
                                                    </div>
                                                    <span>{{__('admin.fav_icon_image')}}</span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="imgMontg col-12 col-lg-4 col-md-6 text-center">
                                            <div class="dropBox">
                                                <div class="textCenter d-flex flex-column">
                                                    <div class="imagesUploadBlock">
                                                        <label class="uploadImg">
                                                            <span><i class="feather icon-image"></i></span>
                                                            <input type="file" accept="image/*" name="login_background" class="imageUploader">
                                                        </label>
                                                        <div class="uploadedBlock">
                                                            <img src="{{$data['login_background']}}">
                                                            <button class="close"><i class="feather icon-trash-2"></i></button>
                                                        </div>
                                                      </div>
                                                      <span>{{__('admin.background_image')}}</span>
                                                </div>
                                            </div>
                                        </div> --}}
                                     
                                       
                                      </div>

                                    </div>
                                    <div class="col-12 d-flex justify-content-center mt-3">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.saving_changes')}}</button>
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                    </div>
                                </div>
                                </form>
                              </div>


                              <div role="tabpanel" class="tab-pane" id="account-vertical-language" aria-labelledby="account-pill-language" aria-expanded="false">
                                <form accept="{{route('admin.settings.update')}}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="row">

                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.supported_languages')}}</label>
                                                    <select name="locales[]" class="form-control select2" multiple="">
                                                        @foreach (config('available-locales') as $key => $language)
                                                            <option value="{{ $key }}"
                                                                @if (in_array($key,json_decode($data['locales'])))
                                                                    selected
                                                                @endif >
                                                                {{ $language['native'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.rtl_languages')}}</label>
                                                    <select name="rtl_locales[]" class="form-control select2" multiple="">
                                                        @foreach (config('available-locales') as $key => $language)
                                                            <option value="{{ $key }}"
                                                                    @if (in_array($key,json_decode($data['rtl_locales'])))
                                                                    selected
                                                                @endif>
                                                                {{ $language['native'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.default_language')}}</label>
                                                    <select name="default_locale" class="form-control select2">
                                                        @foreach (config('available-locales') as $key => $language)
                                                            <option value="{{ $key }}"
                                                                    @if ($data['default_locale'] == $key)
                                                                    selected
                                                                @endif>
                                                                {{ $language['native'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-center mt-3">
                                          <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.saving_changes')}}</button>
                                          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                      </div>
                                    </div>
                                </form>
                              </div>

                              <div role="tabpanel" class="tab-pane" id="account-vertical-countries" aria-labelledby="account-pill-countries" aria-expanded="false">
                                <form accept="{{route('admin.settings.update')}}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="row">

                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.supported_countries')}}</label>
                                                    <select name="countries[]" class="form-control select2" multiple="">
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}"
                                                                @if (in_array($country->id,json_decode($data['countries'])))
                                                                    selected
                                                                @endif >
                                                                {{ $country->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.default_country')}}</label>
                                                    <select name="default_country" class="form-control select2">
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}"
                                                                    @if ($data['default_country'] == $country->id)
                                                                    selected
                                                                @endif>
                                                                {{ $country->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.supported_currencies')}}</label>
                                                    <select name="currencies[]" class="form-control select2" multiple="">
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->currency_code }}"
                                                                @if (in_array($country->currency_code,json_decode($data['currencies'])))
                                                                    selected
                                                                @endif >
                                                                {{ $country->currency }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.default_currency')}}</label>
                                                    <select name="default_currency" class="form-control select2">
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->currency_code }}"
                                                                    @if ($data['default_currency'] == $country->currency_code)
                                                                    selected
                                                                @endif>
                                                                {{ $country->currency }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="vat_amount">{{__('admin.vat_amount')}} (%)</label>
                                                    <input type="number" class="form-control" name="vat_amount" id="vat_amount"
                                                        placeholder="{{__('admin.vat_amount')}}"
                                                        value="{{ $data['vat_amount'] ?? 15 }}"
                                                        min="0" max="100" step="0.01" required
                                                        data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                                    <small class="form-text text-muted">{{__('admin.vat_amount_help')}}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-center mt-3">
                                          <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.saving_changes')}}</button>
                                          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                      </div>
                                    </div>
                                </form>
                              </div>

                              <div role="tabpanel" class="tab-pane" id="account-vertical-terms" aria-labelledby="account-pill-terms" aria-expanded="false">
                                <form accept="{{route('admin.settings.update')}}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="row">

                                        <div class="col-12">
                                            <ul class="nav nav-tabs  mb-3">
                                                @foreach (languages() as $lang)
                                                    <li class="nav-item">
                                                        <a class="nav-link @if($loop->first) active @endif"  data-toggle="pill" href="#first_{{$lang}}" aria-expanded="true">{{  __('admin.data') }} {{ $lang }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <div class="col-12">
                                            <div class="tab-content w-100">
                                                @foreach (languages() as $lang)
                                                    <div role="tabpanel" class="tab-pane fade @if($loop->first) show active @endif " id="first_{{$lang}}" aria-labelledby="first_{{$lang}}" aria-expanded="true">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label for="terms_{{ $lang }}_editor" class="font-weight-bold">{{__('admin.conditions_and_conditions')}} {{ $lang  }}</label>
                                                                <textarea class="form-control editor-large" name="terms_{{ $lang }}" id="terms_{{ $lang }}_editor" cols="30" rows="20" placeholder="{{__('admin.conditions_and_conditions')}} {{ $lang }}">{{$data['terms_'.$lang]??''}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-center mt-3">
                                          <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.saving_changes')}}</button>
                                          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                      </div>
                                    </div>
                                </form>
                              </div>

                              <div role="tabpanel" class="tab-pane" id="account-vertical-about" aria-labelledby="account-pill-about" aria-expanded="false">
                                <form accept="{{route('admin.settings.update')}}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="row">

                                        <div class="col-12">
                                            <ul class="nav nav-tabs  mb-3">
                                                @foreach (languages() as $lang)
                                                    <li class="nav-item">
                                                        <a class="nav-link @if($loop->first) active @endif"  data-toggle="pill" href="#about_{{$lang}}" aria-expanded="true">{{  __('admin.data') }} {{ $lang }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <div class="tab-content">
                                            @foreach (languages() as $lang)
                                                <div role="tabpanel" class="tab-pane fade @if($loop->first) show active @endif " id="about_{{$lang}}" aria-labelledby="first_{{$lang}}" aria-expanded="true">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label for="account-name">{{__('admin.about_the_application')}} {{ $lang  }}</label>
                                                                <textarea class="form-control" name="about_{{ $lang }}" id="about_{{ $lang }}_editor" cols="30" rows="10" placeholder="{{__('admin.about_the_application')}} {{ $lang }}">{{$data['about_'.$lang]??''}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="col-12 d-flex justify-content-center mt-3">
                                          <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.saving_changes')}}</button>
                                          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                      </div>
                                    </div>
                                </form>
                              </div>

                              <div role="tabpanel" class="tab-pane" id="account-vertical-privacy" aria-labelledby="account-pill-privacy" aria-expanded="false">
                                <form accept="{{route('admin.settings.update')}}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="row">

                                        <div class="col-12">
                                            <ul class="nav nav-tabs  mb-3">
                                                @foreach (languages() as $lang)
                                                    <li class="nav-item">
                                                        <a class="nav-link @if($loop->first) active @endif"  data-toggle="pill" href="#privacy_{{$lang}}" aria-expanded="true">{{  __('admin.data') }} {{ $lang }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <div class="col-12">
                                            <div class="tab-content w-100">
                                                @foreach (languages() as $lang)
                                                    <div role="tabpanel" class="tab-pane fade @if($loop->first) show active @endif " id="privacy_{{$lang}}" aria-labelledby="first_{{$lang}}" aria-expanded="true">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label for="privacy_{{ $lang }}_editor" class="font-weight-bold">{{__('admin.privacy_policy')}} {{ $lang  }}</label>
                                                                <textarea class="form-control editor-large" name="privacy_{{ $lang }}" id="privacy_{{ $lang }}_editor" cols="30" rows="20" placeholder="{{__('admin.privacy_policy')}} {{ $lang }}">{{$data['privacy_'.$lang]??''}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-center mt-3">
                                          <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.saving_changes')}}</button>
                                          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                      </div>
                                    </div>
                                </form>
                              </div>

                              <div role="tabpanel" class="tab-pane" id="account-vertical-returns" aria-labelledby="account-pill-returns" aria-expanded="false">
                                <form accept="{{route('admin.settings.update')}}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="row">

                                        <div class="col-12">
                                            <ul class="nav nav-tabs  mb-3">
                                                @foreach (languages() as $lang)
                                                    <li class="nav-item">
                                                        <a class="nav-link @if($loop->first) active @endif"  data-toggle="pill" href="#returns_{{$lang}}" aria-expanded="true">{{  __('admin.data') }} {{ $lang }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <div class="col-12">
                                            <div class="tab-content w-100">
                                                @foreach (languages() as $lang)
                                                    <div role="tabpanel" class="tab-pane fade @if($loop->first) show active @endif " id="returns_{{$lang}}" aria-labelledby="first_{{$lang}}" aria-expanded="true">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label for="returns_{{ $lang }}_editor" class="font-weight-bold">{{__('admin.return_policy')}} {{ $lang  }}</label>
                                                                <textarea class="form-control editor-large" name="returns_{{ $lang }}" id="returns_{{ $lang }}_editor" cols="30" rows="20" placeholder="{{__('admin.return_policy')}} {{ $lang }}">{{$data['returns_'.$lang]??''}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-center mt-3">
                                          <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.saving_changes')}}</button>
                                          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                      </div>
                                    </div>
                                </form>
                              </div>
                                <div role="tabpanel" class="tab-pane" id="account-vertical-cancel-policy" aria-labelledby="account-pill-cancel-policy" aria-expanded="false">
                                <form accept="{{route('admin.settings.update')}}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="row">

                                        <div class="col-12">
                                            <ul class="nav nav-tabs  mb-3">
                                                @foreach (languages() as $lang)
                                                    <li class="nav-item">
                                                        <a class="nav-link @if($loop->first) active @endif"  data-toggle="pill" href="#cancel_policy_{{$lang}}" aria-expanded="true">{{  __('admin.data') }} {{ $lang }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <div class="tab-content">
                                            @foreach (languages() as $lang)
                                                <div role="tabpanel" class="tab-pane fade @if($loop->first) show active @endif " id="cancel_policy_{{$lang}}" aria-labelledby="first_{{$lang}}" aria-expanded="true">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label for="account-name">{{__('admin.cancel_policy_in_'.$lang)}}</label>
                                                                <textarea class="form-control" name="cancel_policy_{{ $lang }}" id="cancel_policy_{{ $lang }}_editor" cols="30" rows="10" placeholder="{{__('admin.cancel_policy_in_'.$lang)}}">{{$data['cancel_policy_'.$lang]??''}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="col-12 d-flex justify-content-center mt-3">
                                          <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.saving_changes')}}</button>
                                          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                      </div>
                                    </div>
                                </form>
                              </div>

                              <div role="tabpanel" class="tab-pane" id="account-vertical-smtp" aria-labelledby="account-pill-smtp" aria-expanded="false">
                                <form accept="{{route('admin.settings.update')}}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="row">

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.user_name')}}</label>
                                                    <input type="text" class="form-control" name="smtp_user_name" id="account-name" placeholder="{{__('admin.user_name')}}" value="{{$data['smtp_user_name']}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.password')}}</label>
                                                    <input type="password" class="form-control" name="smtp_password" id="account-name" placeholder="{{__('admin.password')}}" value="{{$data['smtp_password']}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.email_Sender')}}</label>
                                                    <input type="text" class="form-control" name="smtp_mail_from" id="account-name" placeholder="{{__('admin.email_Sender')}}" value="{{$data['smtp_mail_from']}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.the_sender_name')}}</label>
                                                    <input type="text" class="form-control" name="smtp_sender_name" id="account-name" placeholder="{{__('admin.the_sender_name')}}" value="{{$data['smtp_sender_name']}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.the_nouns_al')}}</label>
                                                    <input type="text" class="form-control" name="smtp_host" id="account-name" placeholder="{{__('admin.the_nouns_al')}}" value="{{$data['smtp_host']}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.encryption_type')}}</label>
                                                    <input type="text" class="form-control" name="smtp_encryption" id="account-name" placeholder="{{__('admin.encryption_type')}}" value="{{$data['smtp_encryption']}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.Port_number')}}</label>
                                                    <input type="number" class="form-control" name="smtp_port" id="account-name" placeholder="{{__('admin.Port_number')}}" value="{{$data['smtp_port']}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-center mt-3">
                                          <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.saving_changes')}}</button>
                                          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                      </div>
                                    </div>
                                </form>
                              </div>


                              <div role="tabpanel" class="tab-pane" id="account-vertical-api" aria-labelledby="account-pill-api" aria-expanded="false">
                                <form accept="{{route('admin.settings.update')}}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="row">

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.live_chat')}}</label>
                                                    <input type="text" class="form-control" name="live_chat" id="account-name" placeholder="{{__('admin.live_chat')}}" value="{{$data['live_chat']}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.google_analytics')}}</label>
                                                    <input type="text" class="form-control" name="google_analytics" id="account-name" placeholder="{{__('admin.google_analytics')}}" value="{{$data['google_analytics']}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">{{__('admin.google_places')}}</label>
                                                    <input type="text" class="form-control" name="google_places" id="account-name" placeholder="{{__('admin.google_places')}}" value="{{$data['google_places']}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-center mt-3">
                                          <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.saving_changes')}}</button>
                                          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                      </div>
                                    </div>
                                </form>
                              </div>
                              <div role="tabpanel" class="tab-pane" id="account-vertical-loyalty" aria-labelledby="account-pill-loyalty" aria-expanded="false">
                                <form action="{{ route('admin.settings.update') }}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="row">
                            
                                        <div class="col-12">
                                            <h4 class="mb-3">{{ __('admin.loyalty_points_settings') }}</h4>
                                            <p class="text-muted mb-4">{{ __('admin.loyalty_points_description') }}</p>
                                        </div>
                            
                                  
                            
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="loyalty_points_earn_rate">{{ __('admin.points_earned_per_sar') }}</label>
                                                    <input type="number" class="form-control" name="loyalty_points_earn_rate" id="loyalty_points_earn_rate"
                                                        placeholder="1" value="{{ $data['loyalty_points_earn_rate'] }}"
                                                        min="0" step="0.01" required>
                                                    <small class="form-text text-muted">{{ __('admin.points_earn_example') }}</small>
                                                </div>
                                            </div>
                                        </div>
                            
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="loyalty_points_redeem_rate">{{ __('admin.point_value_in_sar') }}</label>
                                                    <input type="number" class="form-control" name="loyalty_points_redeem_rate" id="loyalty_points_redeem_rate"
                                                        placeholder="1" value="{{ $data['loyalty_points_redeem_rate'] }}"
                                                        min="0" step="0.01" required>
                                                    <small class="form-text text-muted">{{ __('admin.point_value_example') }}</small>
                                                </div>
                                            </div>
                                        </div>
                            
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="loyalty_points_min_redeem">{{ __('admin.min_points_to_redeem') }}</label>
                                                    <input type="number" class="form-control" name="loyalty_points_min_redeem" id="loyalty_points_min_redeem"
                                                        placeholder="10" value="{{ $data['loyalty_points_min_redeem'] }}"
                                                        min="1" required>
                                                    <small class="form-text text-muted">{{ __('admin.min_points_hint') }}</small>
                                                </div>
                                            </div>
                                        </div>
                            
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="loyalty_points_max_redeem_percentage">{{ __('admin.max_points_percentage') }}</label>
                                                    <input type="number" class="form-control" name="loyalty_points_max_redeem_percentage" id="loyalty_points_max_redeem_percentage"
                                                        placeholder="50" value="{{ $data['loyalty_points_max_redeem_percentage'] }}"
                                                        min="1" max="100" required>
                                                    <small class="form-text text-muted">{{ __('admin.max_points_hint') }}</small>
                                                </div>
                                            </div>
                                        </div>
                            
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <h5><i class="feather icon-info mr-1"></i> {{ __('admin.loyalty_notes_title') }}</h5>
                                                <ul class="mb-0">
                                                    <li>{{ __('admin.note_points_on_payment') }}</li>
                                                    <li>{{ __('admin.note_used_points_no_earn') }}</li>
                                                    <li>{{ __('admin.note_earned_from_paid_amount') }}</li>
                                                </ul>
                                            </div>
                                        </div>
                            
                                        <div class="col-12 d-flex justify-content-center mt-3">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{ __('admin.saving_changes') }}</button>
                                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{ __('admin.back') }}</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            

                              <div role="tabpanel" class="tab-pane" id="account-vertical-fees" aria-labelledby="account-pill-fees" aria-expanded="false">
                                <form accept="{{route('admin.settings.update')}}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="row">

                                        <div class="col-12">
                                            <h4 class="mb-3">{{ __('admin.fees_settings') }}</h4>
                                            <p class="text-muted mb-4">{{ __('admin.fees_settings_desc') }}</p>
                                        </div>

                                

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="scheduled_delivery_fee">{{ __('admin.Scheduled_Delivery_Fee') }}</label>
                                                    <input type="number" class="form-control" name="scheduled_delivery_fee" id="scheduled_delivery_fee"
                                                        placeholder="0.00" value="{{$data['scheduled_delivery_fee'] ?? '0.00'}}"
                                                        min="0" step="0.01" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="ordinary_delivery_fee">{{ __('admin.Ordinary_Delivery_Fee') }}</label>
                                                    <input type="number" class="form-control" name="ordinary_delivery_fee" id="ordinary_delivery_fee"
                                                        placeholder="0.00" value="{{$data['ordinary_delivery_fee'] ?? '0.00'}}"
                                                        min="0" step="0.01" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="gift_fee">{{ __('admin.Gift_Fee') }}</label>
                                                    <input type="number" class="form-control" name="gift_fee" id="gift_fee"
                                                        placeholder="0.00" value="{{$data['gift_fee'] ?? '0.00'}}"
                                                        min="0" step="0.01" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="delivery_per_km_fee">{{ __('admin.delivery_per_km_fee') }}</label>
                                                    <input type="number" class="form-control" name="delivery_per_km_fee" id="delivery_per_km_fee"
                                                        placeholder="1.00" value="{{$data['delivery_per_km_fee'] ?? '1.00'}}"
                                                        min="0" step="0.01">
                                                    <small class="form-text text-muted">{{ __('admin.delivery_per_km_fee_hint') }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="delivery_distance_threshold">{{ __('admin.delivery_distance_threshold') }}</label>
                                                    <input type="number" class="form-control" name="delivery_distance_threshold" id="delivery_distance_threshold"
                                                        placeholder="5" value="{{$data['delivery_distance_threshold'] ?? '5'}}"
                                                        min="0" step="0.01">
                                                    <small class="form-text text-muted">{{ __('admin.delivery_distance_threshold_hint') }}</small>
                                                </div>
                                            </div>
                                        </div>

                                    
                                            <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="min_wallet_deduction">{{ __('admin.min_wallet_deduction') }}</label>
                                                    <input type="number" class="form-control" name="min_wallet_deduction" id="min_wallet_deduction"
                                                        placeholder="5.00" value="{{$data['min_wallet_deduction'] ?? '5.00'}}"
                                                        min="0" step="0.01" required>
                                                    <small class="form-text text-muted">{{ __('admin.min_wallet_deduction') }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="min_refund_balance">{{ __('admin.min_refund_balance') }}</label>
                                                    <input type="number" class="form-control" name="min_refund_balance" id="min_refund_balance"
                                                        placeholder="5.00" value="{{$data['min_refund_balance'] ?? '5.00'}}"
                                                        min="0" step="0.01" required>
                                                    <small class="form-text text-muted">{{ __('admin.min_refund_balance') }}</small>
                                                </div>
                                            </div>
                                        </div>

                      <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="vat_amount">{{ __('admin.vat_amount') }}</label>
                                                    <input type="number" class="form-control" name="vat_amount" id="vat_amount"
                                                        placeholder="5.00" value="{{$data['vat_amount'] ?? '5.00'}}"
                                                        min="0" step="0.01" required>
                                                    <small class="form-text text-muted">{{ __('admin.vat_amount') }}</small>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-12 d-flex justify-content-center mt-3">
                                          <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.saving_changes')}}</button>
                                          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                      </div>
                                    </div>
                                </form>
                              </div>


                              <div role="tabpanel" class="tab-pane" id="account-vertical-commission" aria-labelledby="account-pill-fees" aria-expanded="false">
                                <form accept="{{route('admin.settings.update')}}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="row">

                                                   

                                                                               <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="express_delivery_fee">{{ __('admin.platform_commission') }}</label>
                                                    <input type="number" class="form-control" name="salon_comission" id="express_delivery_fee"
                                                        placeholder="15.00" value="{{$data['salon_comission'] ?? '15.00'}}"
                                                        min="0" step="0.01" required>
                                                </div>
                                            </div>
                                        </div>

                 

                                        <div class="col-12 d-flex justify-content-center mt-3">
                                          <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.saving_changes')}}</button>
                                          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                      </div>
                                    </div>
                                </form>
                              </div>

                              <div role="tabpanel" class="tab-pane" id="account-vertical-referral" aria-labelledby="account-pill-fees" aria-expanded="false">
                                <form accept="{{route('admin.settings.update')}}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="normal_delivery_fee">{{ __('admin.service_referral_commission') }}</label>
                                                    <input type="number" class="form-control" name="service_referral_commission" id="normal_delivery_fee"
                                                        placeholder="5.00" value="{{$data['service_referral_commission'] ?? '5.00'}}"
                                                        min="0" step="0.01" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="express_delivery_fee">{{ __('admin.product_referral_commission') }}</label>
                                                    <input type="number" class="form-control" name="product_referral_commission" id="express_delivery_fee"
                                                        placeholder="15.00" value="{{$data['product_referral_commission'] ?? '15.00'}}"
                                                        min="0" step="0.01" required>
                                                </div>
                                            </div>
                                        </div>

                 

                                        <div class="col-12 d-flex justify-content-center mt-3">
                                          <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.saving_changes')}}</button>
                                          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                      </div>
                                    </div>
                                </form>
                              </div>

                              <div role="tabpanel" class="tab-pane" id="account-vertical-withdrawal" aria-labelledby="account-pill-fees" aria-expanded="false">
                                <form accept="{{route('admin.settings.update')}}" method="post" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="normal_delivery_fee">{{ __('admin.comission_withdrawal_fee') }}</label>
                                                    <input type="number" class="form-control" name="comission_withdrawal_fee" id="normal_delivery_fee"
                                                        placeholder="5.00" value="{{$data['comission_withdrawal_fee'] ?? '5.00'}}"
                                                        min="0" step="0.01" required>
                                                </div>
                                            </div>
                                        </div>

                                
                 

                                        <div class="col-12 d-flex justify-content-center mt-3">
                                          <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.saving_changes')}}</button>
                                          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                      </div>
                                    </div>
                                </form>
                              </div>

                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </section>
  <!-- account setting page end -->

</div>

@endsection
@section('js')
    <script src="{{asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/forms/validation/form-validation.js')}}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
    <script>
        $(document).ready(function() {
            @foreach(languages() as $lang)
                // Initialize CKEditor 5 for about section
                ClassicEditor
                    .create(document.querySelector('#about_{{ $lang }}_editor'), {
                        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
                        language: '{{app()->getLocale() === "ar" ? "ar" : "en"}}'
                    })
                    .catch(error => {
                        console.error('CKEditor initialization error for about_{{ $lang }}:', error);
                    });

                // Initialize CKEditor 5 for terms section
                ClassicEditor
                    .create(document.querySelector('#terms_{{ $lang }}_editor'), {
                        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
                        language: '{{app()->getLocale() === "ar" ? "ar" : "en"}}'
                    })
                    .catch(error => {
                        console.error('CKEditor initialization error for terms_{{ $lang }}:', error);
                    });

                // Initialize CKEditor 5 for privacy section
                ClassicEditor
                    .create(document.querySelector('#privacy_{{ $lang }}_editor'), {
                        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
                        language: '{{app()->getLocale() === "ar" ? "ar" : "en"}}'
                    })
                    .catch(error => {
                        console.error('CKEditor initialization error for privacy_{{ $lang }}:', error);
                    });

                // Initialize CKEditor 5 for returns section
                ClassicEditor
                    .create(document.querySelector('#returns_{{ $lang }}_editor'), {
                        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
                        language: '{{app()->getLocale() === "ar" ? "ar" : "en"}}'
                    })
                    .catch(error => {
                        console.error('CKEditor initialization error for returns_{{ $lang }}:', error);
                    });

                // Initialize CKEditor 5 for cancel policy section
                ClassicEditor
                    .create(document.querySelector('#cancel_policy_{{ $lang }}_editor'), {
                        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
                        language: '{{app()->getLocale() === "ar" ? "ar" : "en"}}'
                    })
                    .catch(error => {
                        console.error('CKEditor initialization error for cancel_policy_{{ $lang }}:', error);
                    });
            @endforeach
        });

    </script>
  {{-- show selected image script --}}
    @include('admin.shared.addImage')
  {{-- show selected image script --}}
  <script>
    document.querySelectorAll('.remove-image-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var key = btn.getAttribute('data-img-key');
            document.getElementById('remove_' + key).value = 1;
            btn.closest('.uploadedBlock').style.display = 'none';
        });
    });
  </script>

  {{-- Remember active tab script --}}
  <script>
    $(document).ready(function() {
        // Restore active main tab from localStorage
        var activeMainTab = localStorage.getItem('activeSettingsMainTab');
        if (activeMainTab) {
            $('.nav-pills a[href="' + activeMainTab + '"]').tab('show');
        }

        // Restore active language sub-tab from localStorage
        var activeSubTab = localStorage.getItem('activeSettingsSubTab');
        if (activeSubTab) {
            setTimeout(function() {
                $('.nav-tabs a[href="' + activeSubTab + '"]').tab('show');
            }, 100);
        }

        // Save main tab when clicked
        $('.nav-pills a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
            var tabId = $(e.target).attr('href');
            localStorage.setItem('activeSettingsMainTab', tabId);
        });

        // Save language sub-tab when clicked
        $('.nav-tabs a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
            var tabId = $(e.target).attr('href');
            localStorage.setItem('activeSettingsSubTab', tabId);
        });

        // Clear sub-tab when switching main tabs
        $('.nav-pills a[data-toggle="pill"]').on('show.bs.tab', function(e) {
            localStorage.removeItem('activeSettingsSubTab');
        });
    });
  </script>
@endsection

