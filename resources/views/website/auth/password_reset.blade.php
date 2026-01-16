@extends('website.auth.layouts.master')

@section('title', 'Lia | ' . __('site.reset_password'))

@push('styles')
<link rel="stylesheet" href="{{ asset('website/css/auth.css') }}" />
@endpush

@section('content')
<main class="auth_form-body">
      <div class="auth_form_responsive-content">
        <div class="auth_form-content">
          <div class="auth_form-form">
            <div class="auth_form-head">
              <h1 class="auth_form-title">{{ __('site.reset_password') }}</h1>
            </div>
            <form data-validate action="{{ route('website.password_reset.submit') }}" method="POST">
              @csrf
              <div class="form-group">
                <div class="password-content iconed-input">
                  <span class="input-icon">
                    <i class="fal fa-lock"> </i>
                  </span>
                  <input
                    type="password"
                    name="password"
                    placeholder="{{ __('site.new_password') }}"
                    class="form-control"
                    data-validation="required"
                  />
                  <span class="password-toggle">
                    <i class="fal fa-eye"></i>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <div class="password-content iconed-input">
                  <span class="input-icon">
                    <i class="fal fa-lock"> </i>
                  </span>
                  <input
                    type="password"
                    name="password_confirmation"
                    placeholder="{{ __('site.confirm_new_password') }}"
                    class="form-control"
                    data-validation="required|equalTo:password"
                  />
                  <span class="password-toggle">
                    <i class="fal fa-eye"></i>
                  </span>
                </div>
              </div>
              <div class="auth_form-btns">
                <button type="submit" class="submit-btn">{{ __('site.reset') }}</button>
                <a href="{{ route('website.password_otp') }}" class="back-btn">
                  <i class="fal fa-arrow-right"></i>
                  {{ __('site.back') }}
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
<script src="{{ asset('website/js/form.js') }}"></script>
@endpush
