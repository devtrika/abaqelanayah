@extends('website.layouts.app')

@section('title', __('site.myAccount') . ' - ' . __('site.change_password'))

@section('meta_description', __('site.change_password'))

@section('content')

<!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('website.password') }}" class="breadcrumb-link">
              {{ __('site.change_password') }}
            </a>
          </li>
        </ul>
      </div>
    </section>
    <!-- End Breadcrumb -->

    <section class="page-content account-page">
      <div class="container">
        <div class="account-content">
          <div class="account-overlay"></div>
          <button class="account-trigger">
            <i class="fal fa-user-gear"></i>
          </button>
          @include('website.pages.account.sidebar')
          <div class="account-main">
            <div class="account-header">
              <h2 class="account-title">{{ __('site.change_password') }}</h2>
            </div>
            <form data-validate method="POST" action="{{ route('website.password.update') }}">
              @csrf
              <div class="form-grid grid-3">
                <div class="form-group">
                  <label for="current_password" class="form-label">
                    {{ __('site.old_password') }}
                  </label>
                  <div class="password-content">
                    <input
                      type="password"
                      name="current_password"
                      class="form-control"
                      data-rules="required|minLength:6|maxLength:191"
                    />
                    <span class="password-toggle">
                      <i class="fal fa-eye"></i>
                    </span>
                  </div>
                </div>
              </div>
              <div class="form-grid grid-3">
                <div class="form-group">
                  <label for="password" class="form-label">
                    {{ __('site.new_password') }}
                  </label>
                  <div class="password-content">
                    <input
                      type="password"
                      name="password"
                      class="form-control"
                      data-rules="required|minLength:6|maxLength:191"
                    />
                    <span class="password-toggle">
                      <i class="fal fa-eye"></i>
                    </span>
                  </div>
                </div>
              </div>
              <div class="form-grid grid-3">
                <div class="form-group">
                  <label for="confirm-password" class="form-label">
                    {{ __('site.confirm_password') }}
                  </label>
                  <div class="password-content">
                    <input
                      type="password"
                      name="password_confirmation"
                      class="form-control"
                      data-rules="required|equalTo:password|minLength:6|maxLength:191"
                    />
                    <span class="password-toggle">
                      <i class="fal fa-eye"></i>
                    </span>
                  </div>
                </div>
              </div>
              <button type="submit" class="submit-btn">{{ __('site.save_changes') }}</button>
            </form>
          </div>
        </div>
      </div>
    </section>

@endsection