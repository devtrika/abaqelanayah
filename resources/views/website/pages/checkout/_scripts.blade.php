@push('scripts')
<script>
(function($){
  function toggleScheduleUI(){
    var type = $('input[name="delivery_type"]:checked').val();
    if(type === 'scheduled'){
      $('#immediate').hide();
      $('#schedule').show();
      $('input[name="schedule_date"]').attr('data-rules','required');
      $('input[name="schedule_time"]').attr('data-rules','required');
    } else {
      $('#immediate').show();
      $('#schedule').hide();
      $('input[name="schedule_date"], input[name="schedule_time"]').removeAttr('data-rules');
    }
  }
  $(document).on('change', 'input[name="delivery_type"]', toggleScheduleUI);
  $(toggleScheduleUI);

  // Helpers to render totals from different payloads
  function renderTotalsFromCart(cart){
    var subtotal = parseFloat(cart.subtotal || 0);
    var discount = parseFloat(cart.discount || 0);
    var coupon   = parseFloat(cart.coupon_value || 0);
    var wallet   = parseFloat(cart.wallet_deduction || 0);
    var vat      = parseFloat(cart.vat_amount || 0);
    var beforeDelivery = parseFloat(cart.total || 0); // total before delivery fee (already includes wallet deduction)
    // Use server-provided amount_before_vat to avoid frontend calculations
    var afterDiscount = parseFloat(cart.amount_before_vat || 0);

    $('.js-subtotal').text(subtotal.toFixed(2));
    $('.js-coupon').text(coupon.toFixed(2));
    $('.js-wallet').text(wallet.toFixed(2));
    $('.js-after-discount').text(afterDiscount.toFixed(2));
    $('.js-before-vat').text(afterDiscount.toFixed(2));
    $('.js-vat').text(vat.toFixed(2));
    $('.js-before-delivery').text(beforeDelivery.toFixed(2));

    // Update final total immediately with current delivery fee
    // cart.total already includes coupon, wallet deduction, and VAT
    var currentDeliveryFee = parseFloat($('.js-delivery-fee').text() || 0);
    var currentGiftFee = parseFloat($('.js-gift-fee').text() || 0);
    var final = beforeDelivery + currentDeliveryFee + currentGiftFee;
    $('.js-final').text(final.toFixed(2));
  }
  function renderTotalsFromCalc(totals, deliveryFee){
    var subtotal = parseFloat(totals.subtotal || 0);
    var discount = parseFloat(totals.discount || 0);
    var coupon   = parseFloat(totals.coupon_value || 0);
    var wallet   = parseFloat(totals.wallet_deduction || 0);
    var vat      = parseFloat(totals.vat_amount || 0);
    var beforeDelivery = parseFloat(totals.before_delivery || 0);
    var final    = parseFloat(totals.final || 0);
    // Use server-provided amount_before_vat to avoid frontend calculations
    var afterDiscount = parseFloat(totals.amount_before_vat || 0);

    $('.js-subtotal').text(subtotal.toFixed(2));
    $('.js-coupon').text(coupon.toFixed(2));
    $('.js-wallet').text(wallet.toFixed(2));
    $('.js-after-discount').text(afterDiscount.toFixed(2));
    $('.js-before-vat').text(afterDiscount.toFixed(2));
    $('.js-vat').text(vat.toFixed(2));
    $('.js-before-delivery').text(beforeDelivery.toFixed(2));
    $('.js-delivery-fee').text(parseFloat(deliveryFee || 0).toFixed(2));
    // gift fee will be set by caller when available
    $('.js-final').text(final.toFixed(2));
  }

  function currentSelection(){
    var data = {
      order_type: $('input[name="order_type"]').val() || 'ordinary',
      delivery_type: $('input[name="delivery_type"]:checked').val() || 'immediate'
    };
    if(data.order_type === 'gift'){
      var glat = $('input[name="gift_latitude"]').val();
      var glng = $('input[name="gift_longitude"]').val();
      if(glat && glng){
        data.gift_latitude = glat; data.gift_longitude = glng;
        data.lat = glat; data.lng = glng;
      }
    } else {
      var addrId = $('input[name="address_id"]').val();
      if(addrId){ data.address_id = addrId; }
      else {
        var lat = $('input[name="latitude"]').val() || $('input[name="lat"]').val();
        var lng = $('input[name="longitude"]').val() || $('input[name="lng"]').val();
        if(lat && lng){
          data.latitude = lat; data.longitude = lng;
          data.lat = lat; data.lng = lng;
        }
      }
    }
    return data;
  }

  var calcXhr = null;
  function triggerCalc(){
    var payload = currentSelection();

    if(!payload.address_id && (typeof payload.latitude === 'undefined' || typeof payload.longitude === 'undefined') && !(payload.order_type === 'gift' && payload.gift_latitude && payload.gift_longitude)){
      return; // nothing to calculate yet
    }
    if(calcXhr){ calcXhr.abort(); }
    calcXhr = $.ajax({
      url: "{{ route('website.checkout.calculate') }}",
      method: 'POST',
      data: payload,
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function(res){
      if(!res || !res.success){ toastr && toastr.error((res && res.message) || '{{ __('site.calculation_failed') }}'); return; }
      $('input[name="branch_id"]').val(res.branch_id || '');
      renderTotalsFromCalc(res.totals || {}, res.delivery_fee || 0);
      if(typeof res.gift_fee !== 'undefined'){
        var gf = parseFloat(res.gift_fee || 0).toFixed(2);
        $('.js-gift-fee').text(gf);
        $('.js-gift-fee-inline').text(gf);
      }
      if(res.immediate){
        $('.js-last-pickup').text(res.immediate.last_pickup_string);
        $('.js-expected-duration').text(res.immediate.expected_duration);
      }
    }).fail(function(xhr){
      var msg = (xhr.responseJSON && xhr.responseJSON.message) || '{{ __('site.something_went_wrong') }}';
      toastr && toastr.error(msg);
    });
  }

  // Events to trigger calculation
  $(document).on('change', 'input[name="delivery_type"]', triggerCalc);
  $(document).on('change', 'input[name="address_id"]', triggerCalc);
  $(document).on('change keyup', 'input[name="latitude"], input[name="longitude"], input[name="lat"], input[name="lng"]', triggerCalc);
  $(document).on('change', 'input[name="schedule_date"], input[name="schedule_time"]', triggerCalc);

  // Initial calculate on load
  $(triggerCalc);

  // Prevent Enter key from submitting the whole form inside coupon/wallet fields
  $(document).on('keydown', 'input[name="coupon_code"], input[name="wallet_amount"]', function(e){
    if(e.key === 'Enter') { e.preventDefault(); return false; }
  });

  // Helper function to toggle coupon UI state
  function toggleCouponUI(hasCoupon, couponCode){
    if(hasCoupon){
      // Coupon is applied - disable input, show remove button, hide apply button
      $('.js-coupon-input').prop('disabled', true).val(couponCode || '');
      $('.js-apply-coupon').hide();
      $('.js-remove-coupon').show();
    } else {
      // No coupon - enable input, show apply button, hide remove button
      $('.js-coupon-input').prop('disabled', false).val('');
      $('.js-apply-coupon').show();
      $('.js-remove-coupon').hide();
    }
  }

  // Apply coupon
  $(document).on('click', '.js-apply-coupon', function(e){
    e.preventDefault();
    var code = $.trim($('input[name="coupon_code"]').val());
    if(!code){ toastr && toastr.warning('{{ __('site.please_enter_coupon_code') }}'); return; }
    var $btn = $(this); $btn.prop('disabled', true).addClass('is-loading');
    $.ajax({
      url: "{{ route('website.checkout.apply-coupon') }}",
      method: 'POST',
      data: { coupon_code: code },
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function(res){
      if(!res || !res.success){ toastr && toastr.error((res && res.message) || '{{ __('site.coupon_apply_failed') }}'); return; }
      var cart = res.cart || {};
      renderTotalsFromCart(cart);
      toggleCouponUI(true, cart.coupon_code);
      toastr && toastr.success(res.message || '{{ __('site.coupon_applied_successfully') }}');
      triggerCalc();
    }).fail(function(xhr){
      if(xhr.status===401){ window.location.href = "{{ route('website.login') }}"; return; }
      var msg = (xhr.responseJSON && xhr.responseJSON.message) || '{{ __('site.coupon_apply_failed_generic') }}';
      toastr && toastr.error(msg);
    }).always(function(){
      $btn.prop('disabled', false).removeClass('is-loading');
    });
  });

  // Remove coupon
  $(document).on('click', '.js-remove-coupon', function(e){
    e.preventDefault();
    var $btn = $(this); $btn.prop('disabled', true).addClass('is-loading');
    $.ajax({
      url: "{{ route('website.checkout.remove-coupon') }}",
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function(res){
      if(!res || !res.success){ toastr && toastr.error((res && res.message) || '{{ __('site.coupon_remove_failed') }}'); return; }
      var cart = res.cart || {};
      renderTotalsFromCart(cart);
      toggleCouponUI(false, null);
      toastr && toastr.success(res.message || '{{ __('site.coupon_removed_successfully') }}');
      triggerCalc();
    }).fail(function(xhr){
      if(xhr.status===401){ window.location.href = "{{ route('website.login') }}"; return; }
      var msg = (xhr.responseJSON && xhr.responseJSON.message) || '{{ __('site.coupon_remove_failed_generic') }}';
      toastr && toastr.error(msg);
    }).always(function(){
      $btn.prop('disabled', false).removeClass('is-loading');
    });
  });

  // Helper function to toggle wallet UI state
  function toggleWalletUI(hasWallet, walletAmount){
    if(hasWallet){
      // Wallet deduction is applied - disable input, show remove button, hide apply button
      $('.js-wallet-inp').prop('disabled', true).val(walletAmount || '');
      $('.js-apply-wallet').hide();
      $('.js-remove-wallet').show();
    } else {
      // No wallet deduction - enable input, show apply button, hide remove button
      $('.js-wallet-inp').prop('disabled', false).val('');
      $('.js-apply-wallet').show();
      $('.js-remove-wallet').hide();
    }
  }

  // Apply wallet deduction
  $(document).on('click', '.js-apply-wallet', function(e){
    e.preventDefault();
    var amount = parseInt($('input[name="wallet_amount"]').val(), 10);
    if(isNaN(amount) || amount < 1){ toastr && toastr.warning('{{ __('site.please_enter_valid_wallet_amount') }}'); return; }
    var $btn = $(this); $btn.prop('disabled', true).addClass('is-loading');
    $.ajax({
      url: "{{ route('website.checkout.apply-wallet-deduction') }}",
      method: 'POST',
      data: { amount: amount },
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function(res){
      if(!res || !res.success){ toastr && toastr.error((res && res.message) || '{{ __('site.wallet_apply_failed') }}'); return; }
      var cart = res.cart || {};
      renderTotalsFromCart(cart);
      toggleWalletUI(true, cart.wallet_deduction);

      // Update wallet balance display (only the numeric value)
      if(typeof res.wallet_balance !== 'undefined'){
        $('.js-wallet-balance').text(parseFloat(res.wallet_balance).toFixed(2));
      }

      toastr && toastr.success(res.message || '{{ __('site.wallet_applied_successfully') }}');
      triggerCalc();
    }).fail(function(xhr){
      if(xhr.status===401){ window.location.href = "{{ route('website.login') }}"; return; }
      var msg = (xhr.responseJSON && xhr.responseJSON.message) || '{{ __('site.wallet_apply_failed_generic') }}';
      toastr && toastr.error(msg);
    }).always(function(){
      $btn.prop('disabled', false).removeClass('is-loading');
    });
  });

  // Remove wallet deduction
  $(document).on('click', '.js-remove-wallet', function(e){
    e.preventDefault();
    var $btn = $(this); $btn.prop('disabled', true).addClass('is-loading');
    $.ajax({
      url: "{{ route('website.checkout.remove-wallet-deduction') }}",
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function(res){
      if(!res || !res.success){ toastr && toastr.error((res && res.message) || '{{ __('site.wallet_remove_failed') }}'); return; }
      var cart = res.cart || {};
      renderTotalsFromCart(cart);
      toggleWalletUI(false, null);

      // Update wallet balance display (refunded amount)
      if(typeof res.wallet_balance !== 'undefined'){
        $('.js-wallet-balance').text(parseFloat(res.wallet_balance).toFixed(2));
      }

      toastr && toastr.success(res.message || '{{ __('site.wallet_removed_successfully') }}');
      triggerCalc();
    }).fail(function(xhr){
      if(xhr.status===401){ window.location.href = "{{ route('website.login') }}"; return; }
      var msg = (xhr.responseJSON && xhr.responseJSON.message) || '{{ __('site.wallet_remove_failed_generic') }}';
      toastr && toastr.error(msg);
    }).always(function(){
      $btn.prop('disabled', false).removeClass('is-loading');
    });
  });
})(jQuery);
</script>
@endpush

