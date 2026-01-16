<!DOCTYPE html>
<html dir="rtl" lang="ar">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta
      name="description"
      content="هذا النص هو مثال لنص يمكن أن يستبدل في نفس المساحة، لقد تم توليد هذا النص من مولد النص العربى، حيث يمكنك أن تولد مثل هذا النص أو العديد من النصوص الأخرى إضافة إلى زيادة عدد الحروف التى يولدها التطبيق."
    />
    <title>@yield('title', 'Lia')</title>
    <link rel="shortcut icon" type="img/png" href="{{ asset('website/images/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('website/css/bootstrap.rtl.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('website/css/fontawesome.min.css') }}" />
    @stack('styles')
  </head>

  <body>
    @yield('header')

    @yield('content')

    {{-- Toast Notifications --}}
    @include('website.shared.toast')

    <script src="{{ asset('website/js/jquery.min.js') }}"></script>
    <script src="{{ asset('website/js/popper.min.js') }}"></script>
    <script src="{{ asset('website/js/bootstrap.min.js') }}"></script>

    {{-- Firebase on login page: request permission and cache token locally --}}
    @include('components.firebase')
    <script>
      (function(){
        // Register service worker so background messages work post-login
        if ('serviceWorker' in navigator) {
          navigator.serviceWorker.register('/firebase-messaging-sw.js')
            .then(function(reg){ console.log('Firebase SW registered (auth layout):', reg.scope); })
            .catch(function(err){ console.warn('Firebase SW registration failed (auth layout):', err); });
        }
        // Token is stored in localStorage by the included component; backend submission happens after login
      })();
    </script>

    @stack('scripts')

    <!-- Load form-validator.js AFTER page-specific scripts (intlTelInput, form.js, etc.) -->
    <script src="{{ asset('website/js/form-validator.js') }}?v={{ time() }}"></script>
  </body>
</html>
