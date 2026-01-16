@extends('website.auth.layouts.master')

@section('title', 'Lia | ' . __('site.login_title'))

@push('styles')
<link rel="stylesheet" href="{{ asset('website/css/intlTelInput.min.css') }}" />
<link rel="stylesheet" href="{{ asset('website/css/auth.css') }}" />
@endpush

@section('content')
<main class="auth_form-body">
      <div class="auth_form_responsive-content">
        <div class="auth_form-content">
          <div class="auth_form-form">
            <div class="auth_form-head">
              <a href="{{ route('website.home') }}" class="auth_form-logo">
                <img
                  loading="lazy"
                  src="{{ asset('website/images/logo.svg') }}"
                  alt="auth_form-logo"
                  class="img-contain"
                />
              </a>
              <h1 class="auth_form-title">{{ __('site.login_title') }}</h1>
            </div>
            <form data-validate action="{{ route('website.login.submit') }}" method="POST">
              @csrf
              <div class="form-group">
                <div class="iconed-input">
                  <span class="input-icon">
                    <i class="fal fa-phone"> </i>
                  </span>
                  <input
                    type="tel"
                    class="form-control"
                    name="phone"
                    placeholder="5xxxxxxxx"
                    intlTelInput
                    data-validation="required"
                    data-validation-error-msg="{{ __('site.phone_required') }}"
                  />
                </div>
              </div>
              <div class="form-group">
                <div class="password-content iconed-input">
                  <span class="input-icon">
                    <i class="fal fa-lock"> </i>
                  </span>
                  <input
                    type="password"
                    name="password"
                    placeholder="{{ __('site.password') }}"
                    class="form-control"
                    data-validation="required"
                    data-validation-error-msg="{{ __('site.password_required') }}"
                  />
                  <span class="password-toggle">
                    <i class="fal fa-eye"></i>
                  </span>
                </div>
              </div>
              <a href="{{ route('website.password_forget') }}" class="password-link">
                {{ __('site.forgot_password') }}
              </a>
              <div class="auth_form-btns">
                <button type="submit" class="submit-btn">{{ __('site.login_title') }}</button> <!-- Add class "disabled" to disable button -->
                <a href="{{ route('website.register') }}" class="submit-btn second-btn">
                  {{ __('site.register_title') }}
                </a>
              </div>
            </form>
          </div>
          <p class="auth_form-copyrights">{{ __('site.copyright') }}</p>
        </div>
      </div>
      <div class="auth_form-img">
        <img
          loading="lazy"
          src="{{ asset('website/images/auth/1.jpg') }}"
          alt="auth_form-img"
          class="img-cover"
        />
      </div>
    </main>
@endsection

@push('scripts')
<script src="{{ asset('website/js/intlTelInput.min.js') }}"></script>
<script src="{{ asset('website/js/form.js') }}"></script>
@endpush
