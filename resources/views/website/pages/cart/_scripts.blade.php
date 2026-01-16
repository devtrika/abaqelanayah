@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
(function($){
  // Re-enable checkout button on page load (in case user navigated back)
  $(document).ready(function(){
    $('.cart-btns .second-btn').prop('disabled', false).removeClass('is-loading');
  });

  // Header cart badge update
  function updateHeaderCart(count){ $('.cart-icon .badge').text(count); }
  function putJson(url, data){ return $.ajax({url: url, method: 'PUT', data: data}); }
  function deleteJson(url, data){ return $.ajax({url: url, method: 'DELETE', data: data}); }

  // Page blocker while updating cart
  var cartBusy = false;
  function blockCart(on){
    var $c = $('#cart-content');
    $c.addClass('block-relative');
    if(on){
      if(!$c.find('.page-blocker').length){ $c.append('<div class="page-blocker"><div class="spinner"></div></div>'); }
      $c.find('.page-blocker').show();
      $c.find('.quantity-btn, .cart_product-del').prop('disabled', true).addClass('is-loading');
    } else {
      $c.find('.page-blocker').remove();
      $c.find('.quantity-btn, .cart_product-del').prop('disabled', false).removeClass('is-loading');
    }
  }

  // Toggle gift vs ordinary address sections
  function applyOrderTypeUI(){
    var type = $('input[name="order_type"]:checked').val();
    if(type === 'gift'){
      $('.gift-fields').show();
      $('.address-content').hide();
    } else {
      $('.gift-fields').hide();
      $('.address-content').show();
    }
  }
  function applyGiftFeeToCartTotal(){
    var isGift = ($('input[name="order_type"]:checked').val() === 'gift');
    var giftFee = parseFloat($('#gift-fee-value').val() || 0);
    var wrap = $('#cart-content .cart-final_total');
    if(!wrap.length) return;
    var base = parseFloat(wrap.attr('data-base-total') || 0);
    var final = base + (isGift ? giftFee : 0);
    wrap.find('.js-cart-total').text(final.toFixed(2));
  }

  $(document).on('change', '.js-order-type', function(){ applyOrderTypeUI(); applyGiftFeeToCartTotal(); });
  $(function(){ applyOrderTypeUI(); applyGiftFeeToCartTotal(); });

  // City -> Districts dependent dropdown (for ordinary orders)
  function loadDistricts(cityId){
    var $district = $('.js-district');
    $district.prop('disabled', true).empty().append($('<option hidden>').text("{{ __('site.loading_ellipsis') }}"));
    if(!cityId){ $district.empty().append($('<option hidden>').text("{{ __('site.choose_from_list') }}").val('')); $district.prop('disabled', false); return; }
    $.getJSON('/api/city/' + cityId + '/districts')
      .done(function(resp){
        $district.empty().append($('<option hidden>').text("{{ __('site.choose_from_list') }}").val(''));
        (resp && resp.data ? resp.data : []).forEach(function(d){
          $district.append($('<option>').val(d.id).text(d.name));
        });
      })
      .fail(function(){ if(window.toastr){ toastr.error("{{ __('site.failed_to_load_districts') }}"); } })
      .always(function(){ $district.prop('disabled', false); });
  }
  $(document).on('change', '.js-city', function(){ loadDistricts($(this).val()); });

  // City -> Districts dependent dropdown (for gift orders)
  function loadGiftDistricts(cityId){
    var $district = $('.js-gift-district');
    $district.prop('disabled', true).empty().append($('<option>').text("{{ __('site.loading_ellipsis') }}").val(''));
    if(!cityId){ $district.empty().append($('<option>').text("{{ __('site.choose_district') }}").val('')); $district.prop('disabled', false); return; }
    $.getJSON('/api/city/' + cityId + '/districts')
      .done(function(resp){
        $district.empty().append($('<option>').text("{{ __('site.choose_district') }}").val(''));
        (resp && resp.data ? resp.data : []).forEach(function(d){
          $district.append($('<option>').val(d.id).text(d.name));
        });
      })
      .fail(function(){ if(window.toastr){ toastr.error("{{ __('site.failed_to_load_districts') }}"); } })
      .always(function(){ $district.prop('disabled', false); });
  }
  $(document).on('change', '.js-gift-city', function(){ loadGiftDistricts($(this).val()); });

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
      geocoder = L.Control.geocoder({ defaultMarkGeocode: false, placeholder: "{{ __('site.search_location_placeholder') }}" })
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
    if(!modalEl){ if(window.toastr) toastr.error("{{ __('site.map_not_available') }}"); return; }
    var bsModal = new bootstrap.Modal(modalEl);
    bsModal.show();
    setTimeout(function(){ ensureMap(); if(map){ map.invalidateSize(); } }, 200);
  }
  // Handle current location button
  $(document).on('click', '.js-locate-me', function(){
    if(!navigator.geolocation){ if(window.toastr) toastr.error("{{ __('site.browser_geolocation_unsupported') }}"); return; }
    navigator.geolocation.getCurrentPosition(function(pos){
      ensureMap();
      var latlng = { lat: pos.coords.latitude, lng: pos.coords.longitude };
      map.setView(latlng, 15);
      setPoint(latlng);
    }, function(){ if(window.toastr) toastr.error("{{ __('site.unable_to_get_location') }}"); }, { enableHighAccuracy: true, timeout: 10000 });
  });
  $(document).on('click', '.js-open-map', function(){
    var $input = $(this);
    if($input.attr('name') === 'gift_address_map'){
      openMapPicker($input, $('input[name="gift_latitude"]'), $('input[name="gift_longitude"]'));
    } else {
      openMapPicker($input, $('input[name="lat"]'), $('input[name="lng"]'));
    }
  });

  // Proceed to checkout: validate and submit data via POST
  $(document).on('click', '.cart-btns .second-btn', function(e){
    e.preventDefault();
    var errors = [];
    function req(name){ return ($('[name="'+name+'"]').val()||'').trim(); }
    var type = $('input[name="order_type"]:checked').val() || 'ordinary';

    var payload = {
      order_type: type
    };

    if(type === 'gift'){
      if(req('reciver_name').length < 3) errors.push("{{ __('site.please_enter_recipient_name_min') }}");

      // Validate Saudi phone number for gift (9 digits starting with 5)
      var giftPhone = req('reciver_phone').replace(/\D/g, ''); // Remove non-digits
      if(giftPhone.length !== 9 || !giftPhone.startsWith('5')) {
        errors.push("{{ __('site.invalid_saudi_phone') }}");
      }

      if(!req('gift_city_id')) errors.push("{{ __('site.please_select_city') }}");
      if(!req('gift_districts_id')) errors.push("{{ __('site.please_select_district') }}");
      if(req('gift_address_name').length < 3) errors.push("{{ __('site.please_enter_delivery_address_name') }}");
      if(!req('gift_latitude') || !req('gift_longitude')) errors.push("{{ __('site.please_select_gift_location') }}");

      if(!errors.length){
        payload.reciver_name = req('reciver_name');
        payload.reciver_phone = req('reciver_phone');
        payload.gift_city_id = req('gift_city_id');
        payload.gift_districts_id = req('gift_districts_id');
        payload.gift_address_name = req('gift_address_name');
        payload.gift_latitude = req('gift_latitude');
        payload.gift_longitude = req('gift_longitude');
        if(req('message')) payload.message = req('message');
        payload.whatsapp = $('input[name="whatsapp"]').is(':checked') ? '1' : '0';
        payload.hide_sender = $('input[name="hide_sender"]').is(':checked') ? '1' : '0';
      }
    } else {
      var addActive = $('#add').hasClass('active') && $('#add').hasClass('show');
      if(addActive){
        // Validate required fields
        if(req('address_name').length < 3) errors.push("{{ __('site.please_enter_address_name') }}");
        if(req('recipient_name').length < 3) errors.push("{{ __('site.please_enter_recipient_name') }}");
        if(!req('city_id')) errors.push("{{ __('site.please_select_city') }}");
        if(!req('districts_id')) errors.push("{{ __('site.please_select_district') }}");

        // Validate Saudi phone number (9 digits starting with 5)
        var phone = req('phone').replace(/\D/g, ''); // Remove non-digits
        if(phone.length !== 9 || !phone.startsWith('5')) {
          errors.push("{{ __('site.invalid_saudi_phone') }}");
        }

        if(!req('lat') || !req('lng')) errors.push("{{ __('site.please_select_location_on_map') }}");

        if(!errors.length){
          payload.address_name = req('address_name');
          payload.recipient_name = req('recipient_name');
          payload.phone = req('phone');
          if(req('country_code')) payload.country_code = req('country_code');
          payload.city_id = req('city_id');
          payload.districts_id = req('districts_id');
          if(req('description')) payload.description = req('description');
          payload.latitude = req('lat');
          payload.longitude = req('lng');
        }
      } else {
        var chosen = $('input[name="address_id"]:checked').val();
        if(!chosen) errors.push("{{ __('site.please_select_saved_address') }}");
        else { payload.address_id = chosen; }
      }
    }

    if(errors.length){ if(window.toastr){ toastr.error(errors[0]); } return false; }

    // Submit via POST to prepare endpoint, then redirect to checkout
    var $btn = $(this);
    $btn.prop('disabled', true).addClass('is-loading');

    $.ajax({
      url: "{{ route('website.checkout.prepare') }}",
      method: 'POST',
      data: payload,
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function(res){
      if(res && res.success && res.redirect_url){
        window.location.href = res.redirect_url;
      } else {
        if(window.toastr){ toastr.error(res.message || "{{ __('site.something_went_wrong') }}"); }
        $btn.prop('disabled', false).removeClass('is-loading');
      }
    }).fail(function(xhr){
      if(xhr.status === 401){
        window.location.href = "{{ route('website.login') }}";
        return;
      }
      var msg = (xhr.responseJSON && xhr.responseJSON.message) || "{{ __('site.something_went_wrong') }}";
      if(window.toastr){ toastr.error(msg); }
      $btn.prop('disabled', false).removeClass('is-loading');
    });
  });

  // Cart quantity + remove handlers
  $(document).on('click', '#cart-content .plus-btn, #cart-content .minus-btn', function(){
    if(cartBusy) return; 
    var wrap = $(this).closest('.cart-product');
    var input = wrap.find('.quantity-input');
    var currentVal = parseInt(input.val(), 10) || 1;
    var max = parseInt(input.attr('max'), 10);
    if(isNaN(max)) max = 999;

    var q = currentVal;
    if($(this).hasClass('plus-btn')) {
      q = Math.min(max, currentVal + 1);
    } else {
      q = Math.max(1, currentVal - 1);
    }

    if(q === currentVal) return;

    cartBusy = true; blockCart(true);
    var payload = { quantity: q };
    var id = wrap.data('cart-item-id');
    var key = wrap.data('cart-item-key');
    if(id) payload.cart_item_id = id; else payload.cart_item_key = key;
    putJson("{{ route('website.cart.update') }}", payload)
      .done(function(res){ if(res && res.html){ $('#cart-content').html(res.html); applyGiftFeeToCartTotal(); } updateHeaderCart(res.count || 0); if(window.toastr){ toastr.success(res.message || "{{ __('site.cart_updated') }}"); } })
      .fail(function(xhr){ if(xhr.status===401){ window.location.href = "{{ route('website.login') }}"; return; } if(window.toastr){ toastr.error((xhr.responseJSON && xhr.responseJSON.message) || "{{ __('site.something_went_wrong') }}"); } })
      .always(function(){ cartBusy = false; blockCart(false); });
  });

  $(document).on('click', '#cart-content .cart_product-del', function(){
    if(cartBusy) return; cartBusy = true; blockCart(true);
    var wrap = $(this).closest('.cart-product');
    var payload = {};
    var productId = wrap.data('product-id');
    var key = wrap.data('cart-item-key');
    if(productId) payload.product_id = productId; else payload.cart_item_key = key;

    // Check if this is the last item in cart
    var cartItemsCount = $('#cart-content .cart-product').length;

    deleteJson("{{ route('website.cart.remove') }}", payload)
      .done(function(res){
        // If this was the last item, reload the page to show empty state
        if(cartItemsCount === 1 || (res.count !== undefined && res.count === 0)){
          window.location.reload();
          return;
        }

        // Otherwise, update cart content normally
        if(res && res.html){ $('#cart-content').html(res.html); applyGiftFeeToCartTotal(); }
        updateHeaderCart(res.count || 0);
        if(window.toastr){ toastr.success(res.message || "{{ __('site.item_removed_from_cart') }}"); }
      })
      .fail(function(xhr){ if(xhr.status===401){ window.location.href = "{{ route('website.login') }}"; return; } if(window.toastr){ toastr.error((xhr.responseJSON && xhr.responseJSON.message) || "{{ __('site.something_went_wrong') }}"); } })
      .always(function(){ cartBusy = false; blockCart(false); });
  });
})(jQuery);
</script>
@endpush

