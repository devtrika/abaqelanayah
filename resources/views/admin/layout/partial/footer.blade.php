
<div class="sidenav-overlay"></div>
    <div class="drag-target"></div>
    
    {{-- <footer class="footer footer-static footer-light">
        <p class="clearfix blue-grey lighten-2 mb-0">
            <span class="float-md-left d-block d-md-inline-block mt-25">
                {{__('admin.Copyrights')}} &copy; {{\Carbon\Carbon::now()->year}}
                <a class="text-bold-800 grey darken-2" href="https://www.tocaan.com/" target="_blank">,</a>
                {{__('admin.all_rights_reserved')}}
            </span>
            <span class="float-md-right d-none d-md-block"> 
                <a href="https://www.tocaan.com/" rel="follow" target="_blank"> {{__('admin.tocaan')}}</a>
                <a href="mailto:support@tocaan.com" ><i class="feather icon-mail pink"></i></a> 
                <a href="tel:+96594971095" ><i class="feather icon-phone pink"></i></a> 
            </span>
        </p>
    </footer> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


    <script src="{{asset('admin/active.js')}}"></script>
    <script src="{{asset('admin/assets/js/flatpickr.js')}}"></script>
    <script src="{{asset('admin/app-assets/vendors/js/vendors.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/core/app.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/components.js')}}"></script>
    <script src="{{asset('admin/app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // input date js
        var $list = $(":input[type='date']");
        $(window).on('load', function () {
            if ($($list).length > 0) {
                $(document).find($list).addClass("custom-input-date");
                $(document).find($list).parents(".controls").addClass("parent-input-date");
                $($list).prop("type", "text");
                flatpickr($list, {
                    disableMobile: true,
                    // minDate: "today",
                });
            }
        })
        $(document).ready(function () {
            $(".select2").select2();  
        });
    </script>

    @yield('js')

    <x-admin.alert />
    {{-- <x-socket /> --}}
    @include('components.firebase')
</body>
</html>