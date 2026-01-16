@extends('website.auth.layouts.master')

@section('title', 'Lia | ' . __('site.forgot_password_title'))

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
              <h1 class="auth_form-title">{{ __('site.forgot_password_title') }}</h1>
              <p class="auth_form-desc">
                {{ __('site.forgot_password_desc') }}
              </p>
            </div>
            <form data-validate action="{{ route('website.password_forget.submit') }}" method="POST">
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
                    data-validation="required|SaudiPhone"
                  />
                </div>
              </div>
              <div class="auth_form-btns">
                <button type="submit" class="submit-btn">{{ __('site.send') }}</button>
                <a href="{{ route('website.login') }}" class="back-btn">
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
<script src="{{ asset('website/js/intlTelInput.min.js') }}"></script>
<script src="{{ asset('website/js/form.js') }}"></script>
@endpush
