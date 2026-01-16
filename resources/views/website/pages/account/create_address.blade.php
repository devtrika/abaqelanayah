@extends('website.layouts.app')

@section('title', __('site.myAccount') . ' - ' . __('site.address_book'))

@section('meta_description', __('site.address_book'))

@section('content')

  <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('website.addresses.index') }}" class="breadcrumb-link">
              {{ __('site.address_book') }}
            </a>
          </li>
          <li class="breadcrumb-item">
            <span class="breadcrumb-link"> {{ __('site.add_new') }} </span>
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
              <h2 class="account-title">{{ __('site.add_new') }}</h2>
            </div>
            <form id="addressForm" method="POST" action="{{ route('website.addresses.store') }}" data-validate>
              @csrf
              <div class="form-grid grid-3">
                <div class="form-group">
                  <label for="name" class="form-label">{{ __('site.address_name') }}</label>
                  <input type="text" class="form-control" name="address_name" data-rules="required|minLength:2|maxLength:100" />
                </div>
                <div class="form-group">
                  <label for="name" class="form-label">{{ __('site.recipient_name') }}</label>
                  <input type="text" class="form-control" name="name" data-rules="required|minLength:2|maxLength:100" />
                </div>
                <div class="form-group">
                  <label for="phone" class="form-label">{{ __('site.mobile_number') }}</label>
                  <input
                    type="tel"
                    class="form-control"
                    name="phone"
                    intlTelInput
                    data-rules="required|saudiPhone"
                  />
                </div>
                <div class="form-group">
                  <label for="city" class="form-label"> {{ __('site.city') }} </label>
                  <select class="form-control" name="city" select2 data-rules="required">
                    <option value="" hidden>{{ __('site.choose_from_list') }}</option>
                    @isset($cities)
                      @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                      @endforeach
                    @endisset
                  </select>
                </div>
                <div class="form-group">
                  <label for="district" class="form-label"> {{ __('site.district') }} </label>
                  <select class="form-control" name="district" select2 data-rules="required">
                    <option value="" hidden>{{ __('site.choose_from_list') }}</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="address_desc" class="form-label">
                    {{ __('site.address_description') }}
                  </label>
                  <input type="text" class="form-control" name="address_desc" data-rules="maxLength:500" />
                </div>
                <div class="form-group">
                  <label for="address_map" class="form-label">
                    {{ __('site.location_on_map') }}
                  </label>
                  <div class="map-input">
                    <span class="icon">
                      <i class="fal fa-map"></i>
                    </span>
                    <input
                      type="text"
                      class="form-control js-open-map"
                      name="address_map"
                      placeholder="{{ __('site.choose_location') }}"
                      readonly
                    />
                    <input type="hidden" name="latitude" />
                    <input type="hidden" name="longitude" />
                  </div>
                </div>
              </div>
              <button type="submit" class="submit-btn">{{ __('site.add') }}</button>
            </form>
          </div>
        </div>
      </div>
    </section>
@endsection

@include('website.pages.cart._map_modal')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
  (function($){
    function populateDistricts(cityId){
      var $district = $('select[name="district"]');
      $district.prop('disabled', true).addClass('is-loading');
      $.get("{{ url('/api/city') }}/" + cityId + "/districts")
        .done(function(res){
          var list = Array.isArray(res) ? res : (res.data || []);
          $district.empty();
          $district.append('<option value="" hidden>'+ @js(__('site.choose_from_list')) +'</option>');
          list.forEach(function(d){ $district.append('<option value="'+ d.id +'">'+ (d.name || d.title || d.label) +'</option>'); });
        })
        .always(function(){ $district.prop('disabled', false).removeClass('is-loading'); });
    }

    $('select[name="city"]').on('change', function(){
      var cid = $(this).val();
      if (cid){ populateDistricts(cid); }
    });

    // Leaflet map picker with Geocoder + Geolocation
    var map, marker, mapInited = false, target = null, geocoder;
    function setPoint(latlng){
      if(marker) { map.removeLayer(marker); }
      marker = L.marker(latlng).addTo(map);
      if(target){
        $(target.lat).val(latlng.lat.toFixed(6));
        $(target.lng).val(latlng.lng.toFixed(6));
        $(target.display).val(latlng.lat.toFixed(6)+', '+latlng.lng.toFixed(6));
      }
    }
    function ensureMap(){
      if(mapInited) return;
      if(typeof L === 'undefined'){ return; }
      map = L.map('mapPicker').setView([24.7136, 46.6753], 11);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

      // Add search control (Leaflet Control Geocoder)
      try {
        geocoder = L.Control.geocoder({ defaultMarkGeocode: false, placeholder: @js(__('site.search_location_placeholder')) })
          .on('markgeocode', function(e){
            var center = e.geocode.center;
            map.setView(center, 15);
            setPoint(center);
          })
          .addTo(map);
      } catch(err) { /* no-op if not loaded */ }

      // Map click sets point
      map.on('click', function(e){ setPoint(e.latlng); });
      mapInited = true;
    }
    function openMapPicker($display, $lat, $lng){
      target = { display: $display, lat: $lat, lng: $lng };
      var modalEl = document.getElementById('mapPickerModal');
      if(!modalEl){ if(window.toastr) toastr.error(@js(__('site.map_not_available'))); return; }
      var bsModal = new bootstrap.Modal(modalEl);
      bsModal.show();
      setTimeout(function(){ ensureMap(); if(map){ map.invalidateSize(); } }, 200);
    }
    // Handle current location button
    $(document).on('click', '.js-locate-me', function(){
      if(!navigator.geolocation){ if(window.toastr) toastr.error(@js(__('site.browser_geolocation_unsupported'))); return; }
      navigator.geolocation.getCurrentPosition(function(pos){
        ensureMap();
        var latlng = { lat: pos.coords.latitude, lng: pos.coords.longitude };
        map.setView(latlng, 15);
        setPoint(latlng);
      }, function(){ if(window.toastr) toastr.error(@js(__('site.unable_to_get_location'))); }, { enableHighAccuracy: true, timeout: 10000 });
    });
    $(document).on('click', '.js-open-map', function(){
      var $input = $(this);
      openMapPicker($input, $('input[name="latitude"]'), $('input[name="longitude"]'));
    });

    // Add hardcoded country code to form
    function updateCountryCode(){
      // Remove existing country_code field if any
      $('input[name="country_code"]').remove();
      // Add country_code as hidden field with hardcoded value
      $('<input>').attr({
        type: 'hidden',
        name: 'country_code',
        value: '966'
      }).appendTo('#addressForm');
    }

    // Update country code when phone country changes
    $(document).on('countrychange', 'input[name="phone"]', function(){
      updateCountryCode();
    });

    // Update country code on form submit (before validation)
    $('#addressForm').on('submit', function(e){
      updateCountryCode();
    });

    // Initialize country code on page load
    $(document).ready(function(){
      // Wait longer for intlTelInput to initialize
      setTimeout(function(){
        updateCountryCode();
        // If still no country code, try again
        if(!$('input[name="country_code"]').length){
          setTimeout(updateCountryCode, 1000);
        }
      }, 1000);
    });
  })(jQuery);
</script>
@endpush