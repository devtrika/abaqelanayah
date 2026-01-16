@extends('website.auth.layouts.master')

@section('title', 'Lia | ' . __('site.registration_success'))

@push('styles')
<link rel="stylesheet" href="{{ asset('website/css/auth.css') }}" />
@endpush

@section('header')
<header class="auth-header">
      <div class="container">
        <div class="auth-header-content">
          <a href="../index_guest.html" class="logo">
            <img
              loading="lazy"
              src="{{ asset('website/images/logo.svg') }}"
              alt="auth_form-logo"
              class="img-contain"
            />
          </a>
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
              <span class="auth_form-icon">
                <i class="fat fa-check-circle"></i>
              </span>
              <h1 class="auth_form-title">{{ __('site.registration_success_title') }}</h1>
              <p class="auth_form-desc">
                {{ __('site.registration_success_desc') }}
              </p>
            </div>
            <a href="{{ route('website.login') }}" class="submit-btn">{{ __('site.login') }}</a>
          </div>
          <p class="auth_form-copyrights">{{ __('site.copyright') }}</p>
        </div>
      </div>
    </main>
@endsection

@push('scripts')
<script src="{{ asset('website/js/form.js') }}"></script>
@endpush
