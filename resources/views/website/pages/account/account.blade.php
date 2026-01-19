@extends('website.layouts.app')

@section('title', __('site.myAccount') )
@push('styles')
<link rel="stylesheet" href="{{ asset('website/css/auth.css') }}" />
@endpush

@section('meta_description', __('site.edit_profile'))
@section('content')


    <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('website.account') }}" class="breadcrumb-link">
              {{ __('site.edit_profile') }}
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
              <h2 class="account-title">{{ __('site.edit_profile') }}</h2>
            </div>
            <form id="account-form" data-validate method="POST" action="{{ route('website.account.update') }}">
              @csrf
              <input type="hidden" name="country_code" id="country_code" value="{{ auth()->user()->country_code }}">
              <div class="form-grid grid-3">
                <div class="form-group">
                  <label for="name" class="form-label">{{ __('site.username') }}</label>
                  <input
                    type="text"
                    class="form-control"
                    name="name"
                    value="{{auth()->user()->name}}"
                    data-rules="required|minLength:3|maxLength:100"
                  />
                </div>
                <div class="form-group">
                  <label for="phone" class="form-label">{{ __('site.mobile_number') }}</label>
                  <input
                    type="tel"
                    class="form-control"
                    disabled
                    name="phone"
                    value="{{auth()->user()->phone}}"
                    intlTelInput
                    data-rules="required|minLength:6|maxLength:20"
                  />
                </div>
                <div class="form-group">
                  <label for="email" class="form-label">
                    {{ __('site.email') }}
                  </label>
                  <input
                    type="email"
                    class="form-control"
                    name="email"
                    value="{{auth()->user()->email}}"
                  />
                </div>
                <div class="form-group">
                  <label for="gender" class="form-label"> {{ __('site.gender') }} </label>
                  <select class="form-control" name="gender" select2 data-rules="required">
                    <option hidden>{{ __('site.choose_from_list') }}</option>
                    <option value="male" {{auth()->user()->gender == 'male' ? 'selected' : ''}}>{{ __('site.male') }}</option>
                    <option value="female" {{auth()->user()->gender == 'female' ? 'selected' : ''}}>{{ __('site.female') }}</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="city_id" class="form-label"> {{ __('site.city') }} </label>
                  <select class="form-control" name="city_id" select2 data-rules="required" data-current-city="{{ auth()->user()->city_id }}">
                    <option hidden>{{ __('site.choose_from_list') }}</option>
                    @foreach(($cities ?? []) as $city)
                      <option value="{{ $city->id }}" {{ (auth()->user()->city_id == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label for="district_id" class="form-label"> {{ __('site.district') }} </label>
                  <select class="form-control" name="district_id" select2 data-rules="required" data-current-district="{{ auth()->user()->district_id }}">
                    <option hidden>{{ __('site.choose_from_list') }}</option>
                    @if(isset($districts) && $districts->count())
                      @foreach($districts as $district)
                        <option value="{{ $district->id }}" {{ (auth()->user()->district_id == $district->id) ? 'selected' : '' }}>{{ $district->name }}</option>
                      @endforeach
                    @endif
                  </select>
                </div>
              </div>
              <button type="submit" class="submit-btn">{{ __('site.save_changes') }}</button>
            </form>
          </div>
        </div>
      </div>
    </section>
@endsection

@push('scripts')
<script>
  (function($){
    var telInput = $("input[type=tel][intlTelInput]");
    if (telInput.length && window.intlTelInputGlobals) {
      var iti = window.intlTelInputGlobals.getInstance(telInput.get(0));
      // If user has a saved country_code (like +966), set the selected country accordingly
      var savedCode = $('#country_code').val();
      if (!iti && telInput.get(0)) {
        // In case init ran before, ensure instance exists
        iti = window.intlTelInputGlobals.getInstance(telInput.get(0));
      }
      if (!iti && typeof window.intlTelInput === 'function') {
        iti = window.intlTelInput(telInput.get(0), { separateDialCode: true, utilsScript: '/website/js/utils.js' });
      }
      if (iti && savedCode) {
        var dial = savedCode.replace('+','');
        var data = window.intlTelInputGlobals.getCountryData();
        var match = data.find(function(c){ return String(c.dialCode) === String(dial); });
        if (match) { iti.setCountry(match.iso2); }
      }
      // Keep hidden country_code in sync
      telInput.on('countrychange', function(){
        if (!iti) return;
        var c = iti.getSelectedCountryData();
        $('#country_code').val('+' + (c && c.dialCode ? c.dialCode : ''));
      });
      // Before submit: strip country code into phone, and sync country_code
      $('#account-form').on('submit', function(){
        if (!iti) return true;
        var c = iti.getSelectedCountryData();
        $('#country_code').val('+' + (c && c.dialCode ? c.dialCode : ''));
        var full = iti.getNumber();
        var dial = (c && c.dialCode) ? c.dialCode : '';
        // remove leading + and dial code from full number to get national part
        if (full && dial && full.indexOf('+'+dial) === 0) {
          var national = full.replace('+'+dial, '').replace(/^\s+/, '');
          telInput.val(national);
        }
      });
    }
  })(jQuery);
</script>

{{-- Include City/District AJAX Handler --}}
@include('website.shared.cityDistrictDropdown')
@endpush
