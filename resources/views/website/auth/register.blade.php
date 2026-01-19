@extends('website.auth.layouts.master')

@section('title', __('site.register_title'))
@php
  $currentLocale = app()->getLocale();
@endphp
@push('styles')
<link rel="stylesheet" href="{{ asset('website/css/intlTelInput.min.css') }}" />
<link rel="stylesheet" href="{{ asset('website/css/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('website/css/auth.css') }}" />
@endpush

@section('header')
<header class="auth-header">
      <div class="container">
        <div class="auth-header-content">
          <a href="{{ route('website.home') }}" class="logo">
            <img
              loading="lazy"
              src="{{$settings['logo']}}"
              alt="auth_form-logo"
              class="img-contain"
            />
          </a>
          <div class="log-link">
            {{ __('site.have_account') }} <a href="{{ route('website.login') }}">{{ __('site.login_title') }}</a>
          </div>
        </div>
      </div>
    </header>
@endsection

@section('content')
<main class="auth_form-body">
      <div class="auth_form_responsive-content">
        <div class="auth_form-content">
          <div class="auth_form-form">
            <div class="auth_form-head">
              <h1 class="auth_form-title">{{ __('site.create_new_account') }}</h1>
            </div>

            <form data-validate action="{{ route('website.register.submit') }}" method="POST">
              @csrf
              <div class="form-grid">
                <div class="form-group">
                  <label for="name" class="form-label">{{ __('site.username') }}</label>
                  <input type="text" class="form-control" name="name" value="{{ old('name') }}" data-validation="required" />
                </div>
                <div class="form-group">
                  <label for="phone" class="form-label">{{ __('site.phone') }}</label>
                  <input
                    type="tel"
                    class="form-control"
                    name="phone"
                    value="{{ old('phone') }}"
                    intlTelInput
                    data-validation="required|SaudiPhone"
                  />
                </div>
                <div class="form-group">
                  <label for="email" class="form-label">
                    {{ __('site.email') }}
                  </label>
                  <input type="email" class="form-control" name="email" value="{{ old('email') }}" data-validation="email"
                  />
                </div>
                <div class="form-group">
                  <label for="gender" class="form-label"> {{ __('site.gender') }} </label>
                  <select class="form-control" name="gender" select2 data-validation="required">
                    <option hidden>{{ __('site.choose_from_list') }}</option>
                    <option value="male">{{ __('site.male') }}</option>
                    <option value="female">{{ __('site.female') }}</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="city_id" class="form-label"> {{ __('site.city') }} </label>
                  <select class="form-control" id="city_id" name="city_id" select2 data-validation="required">
                    <option value="" hidden>{{ __('site.choose_from_list') }}</option>
                    @foreach($cities as $city)
                      <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label for="district_id" class="form-label"> {{ __('site.district') }} </label>
                  <select class="form-control" id="district_id" name="district_id" select2 data-validation="required">
                    <option value="" hidden>{{ __('site.choose_from_list') }}</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="phone" class="form-label"> {{ __('site.password') }} </label>
                  <div class="password-content">
                    <input
                      type="password"
                      name="password"
                      class="form-control"
                      data-validation="required"
                    />
                    <span class="password-toggle">
                      <i class="fal fa-eye"></i>
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="phone" class="form-label">
                    {{ __('site.confirm_password') }}
                  </label>
                  <div class="password-content">
                    <input
                      type="password"
                      name="password_confirmation"
                      class="form-control"
                      data-validation="required|equalTo:password"
                    />
                    <span class="password-toggle">
                      <i class="fal fa-eye"></i>
                    </span>
                  </div>
                </div>
              </div>
              <div class="terms-group form-group flex-column">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" aria-label="terms" name="terms" data-validation="required" data-validation-error-msg="{{ __('site.must_agree_terms') }}" {{ old('terms') ? 'checked' : '' }} />
                    <span class="mark">
                      <i class="fa-regular fa-check"></i>
                    </span>
                    <span class="text">
                      {{ __('site.i_agree_to') }}
                      <a
                        data-bs-toggle="modal"
                        data-bs-target="#termsModal"
                        class="sidebar_menu-link"
                      >
                        {{ __('site.terms_and_conditions') }}
                      </a>
                    </span>
                  </label>
                </div>
                @error('terms')
                    <span class="text-danger small fw-bold d-block mt-1" style="font-size: 12px;">{{ $message }}</span>
                @enderror
              </div>
              <button type="submit" class="submit-btn">{{ __('site.register') }}</button>
            </form>
          </div>
          <p class="auth_form-copyrights">{{ __('site.copyright').$settings['name_'.$currentLocale] }}</p>
        </div>
      </div>
    </main>

    <!-- Start Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true" style="z-index: 999999 !important;">
      <div
        class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"
      >
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="modal-close" data-bs-dismiss="modal">
              <i class="far fa-xmark"></i>
            </button>
          </div>
          <div class="modal-body">
            <h2 class="modal-head">{{ __('site.terms_and_conditions') }}</h2>
            <div class="terms_modal-desc">
              {!! $settings['terms_' . app()->getLocale()] ?? __('site.terms_content') !!}
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Terms Modal -->
@endsection

@push('scripts')
<script src="{{ asset('website/js/intlTelInput.min.js') }}"></script>
<script src="{{ asset('website/js/select2.min.js') }}"></script>
<script src="{{ asset('website/js/form.js') }}"></script>

{{-- Include City/District AJAX Handler --}}
@include('website.shared.cityDistrictDropdown')
@endpush
