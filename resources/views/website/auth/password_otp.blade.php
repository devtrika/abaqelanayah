@extends('website.auth.layouts.master')

@section('title', 'Lia | ' . __('site.password_otp_title'))

@push('styles')
<link rel="stylesheet" href="{{ asset('website/css/auth.css') }}" />
@endpush

@section('content')
<main class="auth_form-body">
      <div class="auth_form_responsive-content">
        <div class="auth_form-content">
          <div class="auth_form-form">
            <div class="auth_form-head">
              <h1 class="auth_form-title">{{ __('site.password_otp_title') }}</h1>
              <p class="auth_form-desc">
                {{ __('site.password_otp_desc') }}
              </p>
            </div>
            <form data-validate action="{{ route('website.password_otp.submit') }}" method="POST">
              @csrf
              <div id="otp-input" class="otp-input">
                <input
                  type="number"
                  step="1"
                  min="0"
                  max="9"
                  autocomplete="no"
                  pattern="\d*"
                />
                <input
                  type="number"
                  step="1"
                  min="0"
                  max="9"
                  autocomplete="no"
                  pattern="\d*"
                  disabled
                />
                <input
                  type="number"
                  step="1"
                  min="0"
                  max="9"
                  autocomplete="no"
                  pattern="\d*"
                  disabled
                />
                <input
                  type="number"
                  step="1"
                  min="0"
                  max="9"
                  autocomplete="no"
                  pattern="\d*"
                  disabled
                />
                <input
                  type="number"
                  step="1"
                  min="0"
                  max="9"
                  autocomplete="no"
                  pattern="\d*"
                  disabled
                />
              </div>
              <input type="hidden" name="otp" />

              <!-- Timer and Resend Code Section -->
              <div class="otp-timer-section">
                <p class="timer-text">
                  <span id="timer-label">{{ __('site.resend_code_in') }}</span>
                  <span id="timer-countdown">01:00</span>
                </p>
                <button type="button" id="resend-code-btn" class="resend-code-btn" disabled>
                  {{ __('site.resend_code') }}
                </button>
              </div>

              <div class="auth_form-btns">
                <button type="submit" class="submit-btn" disabled>{{ __('site.confirm') }}</button>
                <a href="{{ route('website.password_forget') }}" class="back-btn">
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
