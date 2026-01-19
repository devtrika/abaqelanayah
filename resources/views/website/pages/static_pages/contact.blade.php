@extends('website.layouts.app')

@section('title', __('site.contact_us') )
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
@endpush    


@section('meta_description', __('site.cart'))
@section('content')

    <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('website.contact') }}" class="breadcrumb-link"> {{ __('site.contact_us') }} </a>
          </li>
        </ul>
      </div>
    </section>
    <!-- End Breadcrumb -->

    <section class="page-content">
      <div class="container">
        <div class="section-head page-head">
          <h1 class="section-title">{{ __('site.contact_us') }}</h1>
        </div>
        <div class="contact-information">
          <h3 class="contact-title">{{ __('site.contact_information') }}</h3>
          <ul class="contact-list">
            @if($contactInfo['email'])
            <li>
              <a href="mailto:{{ $contactInfo['email'] }}" aria-label="contact link">
                <i class="fal fa-envelope"></i>
                <span class="en">{{ $contactInfo['email'] }}</span>
              </a>
            </li>
            @endif
            @if($contactInfo['phone'])
            <li>
              <a href="tel:{{ $contactInfo['phone'] }}" aria-label="contact link">
                <i class="fal fa-mobile"></i>
                <span class="en">{{ $contactInfo['phone'] }}</span>
              </a>
            </li>
            @endif
            @if($contactInfo['whatsapp'])
            <li>
              <a href="https://wa.me/{{ $contactInfo['whatsapp'] }}" aria-label="contact link">
                <i class="fab fa-whatsapp"></i>
                <span class="en">{{ $contactInfo['whatsapp'] }}</span>
              </a>
            </li>
            @endif
          </ul>
        </div>
        <div class="contact-form">
          <h3 class="contact-title">
            {{ __('site.contact_form_description') }}
          </h3>
          <form data-validate action="{{ route('website.contact.submit') }}" method="POST">
            @csrf
            <div class="form-grid grid-4">
              <div class="form-group">
                <label class="form-label">{{ __('site.name') }}</label>
                <input
                  type="text"
                  name="name"
                  aria-label="name"
                  class="form-control"
                  value="{{ old('name', auth()->user()->name ?? '') }}"
                  data-validation="required"
                />
              </div>
              <div class="form-group">
                <label class="form-label">{{ __('site.phone') }}</label>
                <input
                  type="tel"
                  name="phone"
                  aria-label="phone"
                  class="form-control"
                  value="{{ old('phone', auth()->user()->phone ?? '') }}"
                  intlTelInput
                  data-validation="required"
                />
              </div>
              @guest
              <div class="form-group">
                <label class="form-label">{{ __('site.email') }}</label>
                <input
                  type="email"
                  name="email"
                  aria-label="email"
                  class="form-control"
                  value="{{ old('email') }}"
                  data-validation="required|email"
                />
              </div>
              @endguest
              <div class="form-group">
                <label class="form-label">{{ __('site.message_type') }}</label>
                <select
                  class="form-control"
                  name="type"
                  aria-label="message type"
                  data-validation="required"
                >
                  <option value="" selected hidden>{{ __('site.choose_from_list') }}</option>
                  <option value="suggestion" {{ old('type') == 'suggestion' ? 'selected' : '' }}>{{ __('site.suggestion') }}</option>
                  <option value="complaint" {{ old('type') == 'complaint' ? 'selected' : '' }}>{{ __('site.complaint') }}</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">{{ __('site.message_title') }}</label>
                <input
                  type="text"
                  name="title"
                  aria-label="title"
                  class="form-control"
                  value="{{ old('title') }}"
                  data-validation="required"
                />
              </div>
              <div class="form-group full-w">
                <label class="form-label">{{ __('site.message_body') }}</label>
                <textarea
                  class="form-control"
                  name="body"
                  aria-label="message"
                  data-validation="required"
                >{{ old('body') }}</textarea>
              </div>
            </div>
            <button type="submit" class="submit-btn" aria-label="submit">
              {{ __('site.send') }}
            </button>
          </form>
        </div>
      </div>
    </section>


@endsection