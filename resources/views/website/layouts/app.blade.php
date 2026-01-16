<!DOCTYPE html>
@php
  $currentLocale = app()->getLocale();
  $rtlLocales = json_decode($settings['rtl_locales'] ?? '[]', true);
  if (!is_array($rtlLocales) || empty($rtlLocales)) { $rtlLocales = ['ar']; }
  $isRtl = in_array($currentLocale, $rtlLocales);
@endphp
<html dir="{{ $isRtl ? 'rtl' : 'ltr' }}" lang="{{ $currentLocale }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="@yield('meta_description', 'هذا النص هو مثال لنص يمكن أن يستبدل في نفس المساحة، لقد تم توليد هذا النص من مولد النص العربى، حيث يمكنك أن تولد مثل هذا النص أو العديد من النصوص الأخرى إضافة إلى زيادة عدد الحروف التى يولدها التطبيق.')" />
  <meta name="keywords" content="@yield('meta_keywords', '')" />
  <meta name="author" content="@yield('meta_author', 'Lia')" />
  @yield('meta_og')

  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Lia')</title>

  <link rel="shortcut icon" type="img/png" href="{{ asset('website/images/favicon.png') }}" />
  @if($isRtl)
    <link rel="stylesheet" href="{{ asset('website/css/bootstrap.rtl.min.css') }}" />
  @else
    <link rel="stylesheet" href="{{ asset('website/css/bootstrap.min.css') }}" />
  @endif
  <link rel="stylesheet" href="{{ asset('website/css/fontawesome.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('website/css/swiper-bundle.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('website/css/main.css') }}?v={{ time() }}" />
  <link rel="stylesheet" href="{{ asset('website/css/nouislider.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('website/css/intlTelInput.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('website/css/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('website/css/auth.css') }}" />

  <!-- Toastr CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

  <style>
    .block-relative{ position: relative; }
    .page-blocker{ position: absolute; inset: 0; background: rgba(255,255,255,0.6); display: flex; align-items: center; justify-content: center; z-index: 50; }
    .spinner{ width: 28px; height: 28px; border: 3px solid #eee; border-top: 3px solid #999; border-radius: 50%; animation: spin .8s linear infinite; }
    @keyframes spin{ 0%{ transform: rotate(0deg);} 100%{ transform: rotate(360deg);} }
    .is-loading{ pointer-events: none; opacity: .6; }
    .item-fav.is-favorited i.fa-heart,
    .product-fav.is-favorited i.fa-heart { color: #e53935 !important; }

    /* Custom Pagination Styles */
    .pagination {
      justify-content: center;
      margin-bottom: 0;
      gap: 4px;
    }
    .pagination .page-item .page-link {
      font-size: 14px;
      padding: 0;
      border-radius: 4px !important;
      color: #333;
      border: 1px solid #dee2e6;
      background-color: #fff;
      transition: all 0.3s ease;
      min-width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: none !important;
      margin: 0;
    }
    .pagination .page-item.active .page-link {
      background-color: var(--main-color) !important;
      border-color: var(--main-color) !important;
      /* color: #fff !important; */
    }
    .pagination .page-item.disabled .page-link {
      color: #6c757d;
      pointer-events: none;
      background-color: #f8f9fa;
      border-color: #dee2e6;
      opacity: 0.6;
    }
    .pagination .page-item:hover:not(.active):not(.disabled) .page-link {
      background-color: #e9ecef;
      color: var(--main-color);
      border-color: #dee2e6;
    }
    /* Mobile Pagination */
    @media (max-width: 576px) {
      .pagination .page-item .page-link {
        font-size: 12px;
        min-width: 28px;
        height: 28px;
      }
      .pagination {
        gap: 2px;
        flex-wrap: wrap;
      }
    }
  </style>

  @stack('styles')
</head>

<body>
  {{-- Header --}}
  @include('website.partials.header')

  {{-- Main Content --}}
  <main>
    @yield('content')
  </main>

  {{-- Footer --}}
  @include('website.partials.footer')

  {{-- Toast Notifications --}}
  @include('website.shared.toast')

  {{-- Delete Account Modal --}}
  @auth('web')
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="modal-close" data-bs-dismiss="modal">
            <i class="far fa-xmark"></i>
          </button>
        </div>
        <div class="modal-body">
          <h2 class="modal-head">{{ __('site.delete_account') }}</h2>
          <p class="modal-desc">{{ __('site.delete_account_confirmation') }}</p>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="modal-btn modal_second-btn"
            data-bs-dismiss="modal"
          >
            {{ __('site.cancel') }}
          </button>
          <form id="deleteAccountForm" class="modal-btn" action="{{ route('website.account.delete') }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="modal-btn">{{ __('site.yes') }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  @endauth

  {{-- Scripts --}}
  <script src="{{ asset('website/js/jquery.min.js') }}"></script>
  <script src="{{ asset('website/js/popper.min.js') }}"></script>
  <script src="{{ asset('website/js/form-validator.js') }}"></script>
  <script src="{{ asset('website/js/intlTelInput.min.js') }}"></script>
  <script src="{{ asset('website/js/select2.min.js') }}"></script>
  <script src="{{ asset('website/js/form.js') }}"></script>
  <script src="{{ asset('website/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('website/js/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('website/js/main.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('website/js/nouislider.min.js') }}"></script>
  <!-- Toastr JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script>
    // Toastr defaults
    if (typeof toastr !== 'undefined') {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: '{{ $isRtl ? 'toast-top-left' : 'toast-top-right' }}',
        timeOut: 3000,
        extendedTimeOut: 2000,
        newestOnTop: true,
        preventDuplicates: true,
      };
    }
  </script>

  <script src="{{ asset('website/js/favorites.js') }}?v={{ time() }}"></script>
  <script>
    window.App = window.App || {};
    window.App.routesClientProductShowBase = "{{ url('/client/products') }}/";
    window.App.routes = {
      websiteProductShow: "{{ route('website.product.show', ['slug' => '__SLUG__']) }}",
      websiteFavoritesAdd: "{{ route('website.favourites.add') }}",
      websiteFavoritesRemoveBase: "{{ route('website.favourites.remove', ['product' => '__ID__']) }}",
      websiteFavoritesIds: "{{ route('website.favourites.ids') }}",
      login: "{{ route('website.login') }}"
    };
    window.App.assetsSarIcon = "{{ asset('website/images/icons/sar.svg') }}";

    // Delete account form handler
    @auth('web')
    $(document).ready(function() {
      $('#deleteAccountForm').on('submit', function(e) {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).addClass('is-loading');
      });
    });
    @endauth
  </script>

  
    <script>
      /************************************ price range ************************************/
      var priceSlider = document.getElementById("price-slider");
      if (priceSlider) {
        var minPrice = parseInt($("#price-slider").data("min-price"));
        var maxPrice = parseInt($("#price-slider").data("max-price"));
        
        // Use requested values if available, otherwise default to min/max
        var currentMin = document.getElementById("min-price").value ? parseInt(document.getElementById("min-price").value) : minPrice;
        var currentMax = document.getElementById("max-price").value ? parseInt(document.getElementById("max-price").value) : maxPrice;

        var sliderStart = [currentMin, currentMax];
        var range = {
          min: minPrice,
          max: maxPrice,
        };

        var valuesinputs = [
          document.getElementById("min-price"),
          document.getElementById("max-price"),
        ];
        noUiSlider.create(priceSlider, {
          start: sliderStart,
          connect: true,
          step: 1,
          range: range,

        });

        var skipValues = [
          document.getElementById("min-tooltip"),
          document.getElementById("max-tooltip"),
        ];

        priceSlider.noUiSlider.on("update", function (values, handle) {
          skipValues[handle].innerHTML = ~~values[handle];
          valuesinputs[handle].value = values[handle];
        });
      }
    </script>
  <script>
    (function(){
      var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      if (window.jQuery && token) {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });
      }
    })();
  </script>
  @auth('web')
  {{-- Firebase scripts: initialize messaging for authenticated users --}}
  @include('components.firebase')
  <script>
    (function(){
      // Register service worker for background Firebase messaging
      if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/firebase-messaging-sw.js')
          .then(function(reg){ console.log('Firebase SW registered:', reg.scope); })
          .catch(function(err){ console.warn('Firebase SW registration failed:', err); });
      }

      function sendTokenToServer(token){
        if (!token || !window.jQuery) { return; }
        $.ajax({
          url: "{{ route('website.device-token.store') }}",
          method: 'POST',
          data: { device_token: token, device_type: 'web', device_id: token }
        })
        .done(function(){ console.log('Device token registered'); })
        .fail(function(xhr){ console.warn('Failed to register device token', xhr && xhr.status); });
      }

      // Try localStorage first
      var stored = localStorage.getItem('device_id');
      if (stored) { sendTokenToServer(stored); }

      // Ask Firebase messaging for current token as well (refresh-safe)
      if (window.fcmMessageing && typeof window.fcmMessageing.getToken === 'function') {
        window.fcmMessageing.getToken()
          .then(function(t){ if (t) { localStorage.setItem('device_id', t); sendTokenToServer(t); } })
          .catch(function(err){ console.warn('FCM getToken error', err); });
      }
    })();
  </script>
  @endauth
  <script>
    (function($){
      // Global cart badge updater
      window.updateCartBadge = function(count){ $('#cart-badge').text(count); };

      // Global Add To Cart handler (delegated) with button-level loader/lock
      $(document).on('click', '.add-to-cart-btn', function(e){
        e.preventDefault();
        var btn = $(this);
        if (btn.prop('disabled') || btn.hasClass('in-cart') || btn.data('in-cart') === 1) {
          if (typeof toastr !== 'undefined') { toastr.info('المنتج موجود بالفعل في السلة'); }
          return;
        }
        if (btn.data('loading')) { return; }
        var pid = btn.data('product-id');
        if(!pid){ return; }
        var q = 1;
        // find nearest context that may include quantity input
        var wrap = btn.closest('.product-sticky, .product-item, .swiper-slide, .cart-product');
        var input = wrap.find('.quantity-input');
        if(input.length){ 
            q = parseInt(input.val(), 10) || 1; 
            var max = parseInt(input.attr('max'), 10);
            if(!isNaN(max) && q > max) q = max;
        }

        // lock and show spinner
        var origHtml = btn.data('orig-html');
        if (!origHtml) { btn.data('orig-html', btn.html()); }
        btn.data('loading', true).prop('disabled', true).addClass('is-loading').html('<i class="fal fa-spinner fa-spin"></i>');

        $.ajax({ url: "{{ route('website.cart.add') }}", method: 'POST', data: { product_id: pid, quantity: q } })
          .done(function(res){
            if (typeof toastr !== 'undefined') { 
                toastr.success(res.message || '\u062a\u0645\u062a \u0627\u0644\u0625\u0636\u0627\u0641\u0629 \u0625\u0644\u0649 \u0627\u0644\u0633\u0644\u0629'); }
            updateCartBadge(res.count || 0);
            // Disable all add-to-cart buttons for this product now that it's in the cart
            $('.add-to-cart-btn[data-product-id="'+pid+'"]').data('in-cart', 1).addClass('in-cart').prop('disabled', true);
          })
          .fail(function(xhr){
            if (xhr.status === 401) { window.location.href = "{{ route('website.login') }}"; return; }
            if (typeof toastr !== 'undefined') { toastr.error((xhr.responseJSON && xhr.responseJSON.message) || '\u062d\u062f\u062b \u062e\u0637\u0623 \u0645\u0627'); }
          })
          .always(function(){
            var html = btn.data('orig-html');
            if (html) { btn.html(html); }
            btn.data('loading', false).removeClass('is-loading');
            if (!btn.hasClass('in-cart')) { btn.prop('disabled', false); }
          });
      });

      // Sync badge from DB on load (auth only)
      @auth('web')
      $(function(){
        $.ajax({ url: "{{ route('website.cart.summary') }}", method: 'GET', headers: {'X-Requested-With':'XMLHttpRequest'} })
          .done(function(res){ if (res && typeof res.count !== 'undefined') { updateCartBadge(res.count); } });
      });
      @endauth

      // Global Quantity Handler for read-only inputs (Home, Category List, etc.)
      $(document).on('click', '.quantity-btn', function(e){
        e.preventDefault();
        var btn = $(this);
        // Skip if handled by specific page logic (Product page has its own handler, Cart page has its own)
        if(btn.closest('.product-page').length || btn.closest('#cart-content').length) return;

        var wrap = btn.closest('.item-quantity');
        var input = wrap.find('.quantity-input');
        if(!input.length) return;
        
        var min = parseInt(input.attr('min'), 10) || 1;
        var max = parseInt(input.attr('max'), 10) || 999;
        var val = parseInt(input.val(), 10) || min;
        
        if(btn.hasClass('plus-btn')){
          val = Math.min(max, val + 1);
        } else {
          val = Math.max(min, val - 1);
        }
        input.val(val);
      });

    })(jQuery);
  </script>
  <style>
    .add-to-cart-btn.in-cart,
    .add-to-cart-btn:disabled{
      opacity: 0.6;
      pointer-events: none;
      cursor: not-allowed;
    }
  </style>
  @stack('scripts')
</body>
</html>

