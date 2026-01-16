<footer>
    <div class="container">
        <div>
            <div class="row">
                <div class="col-md-4">
                    <a href="" class="mb-20">
                        <img  loading="lazy"  src="{{$settings['intro_logo']}}" class="logo_footer">
                    </a>
                    <ul class="social-m d-flex">
                        @foreach ($socials as $social)
                            <li><a target="_blank" href="{{$social->url}}"><i class="{{$social->icon}}"></i></a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-4">
                    <div>
                        <h6 class="Tfooter">{{__('intro_site.quiek_links')}}</h6>
                        <ul class="link-footer">
                            <li><i class="fas fa-angle-double-left"></i> <a href="#home" data-scroll="home">{{__('intro_site.home')}}</a></li>
                            <li><i class="fas fa-angle-double-left"></i> <a href="#who_we" data-scroll="who_we">{{__('intro_site.who_us')}}</a></li>
                            <li><i class="fas fa-angle-double-left"></i> <a href="#our_service" data-scroll="our_service">{{__('intro_site.our_services')}}</a></li>
                            <li><i class="fas fa-angle-double-left"></i> <a href="#how_work" data-scroll="how_work">{{__('intro_site.who_we_work')}}</a></li>
                            <li><i class="fas fa-angle-double-left"></i> <a href="#FAQ" data-scroll="FAQ">{{__('intro_site.faq')}}</a></li>
                            <li><i class="fas fa-angle-double-left"></i> <a href="#connect_us" data-scroll="connect_us">{{__('intro_site.contact_us')}}</a></li>

                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <h6 class="Tfooter">{{__('intro_site.contact_us')}}</h6>
                    <ul class="link-footer">
                        <li><i class="fas fa-map-marker-alt"></i> <a>{{$settings['intro_address']}}</a></li>
                        <li><i class="fas fa-phone"></i> <a href="tel:{{$settings['intro_phone']}}">{{$settings['intro_phone']}}</a></li>
                        <li><i class="far fa-envelope"></i> <a href="mailto: {{$settings['intro_email']}}">{{$settings['intro_email']}}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="under_footer">
        <div class="row">
            <div class="col-md-8">
                {{__('intro_site.recives')}} {{\Carbon\Carbon::now()->year}} {{__('intro_site.site')}} <a href="">{{$settings['intro_name_'.lang()]}}</a>
            </div>
            <div class="col">
                <a href="{{route('IntroPrivacyPolicy')}}">{{__('intro_site.privacy')}}</a>
            </div>
        </div>
    </div>
</footer>

  
    <!--========================== Start Loading Page ======================-->

    <div class="loader">
        <img  loading="lazy"  src="{{$settings['intro_loader']}}" alt="">
    </div>

    <!--========================= End Loading Page =========================-->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    {!! $settings['google_analytics'] !!}
    <script src="{{asset('intro_site/js/jquery-3.2.1.min.js')}}"></script>
    <script src="{{asset('intro_site/js/popper.min.js')}}"></script>
    <script src="{{asset('intro_site/js/bootstrap.min.js')}}"></script>
    <!-- plugins JS -->
    <script src="{{asset('intro_site/plugins/owl-carousel/js/owl.carousel.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- my JS -->
    <script src="{{asset('intro_site/js/main.js')}}"></script>

    <script> 
        $(document).on('submit','.send-message',function (e) {
            e.preventDefault()
            var url = $(this).attr('action')
            $.ajax({
                url: url,
                method: 'post',
                data: new FormData($(this)[0]),
                dataType:'json',
                processData: false,
                contentType: false,
                success: function(response){
                    $('.error_meassages').remove();
                        toastr.success(response.message)
                        $('.send-message')[0].reset()
                },
                error: function (xhr) {
                    $('.error_meassages').remove();
                    $.each(xhr.responseJSON.errors, function(key,value) {
                        $('.send-message input[name=' + key + ']').after('<small class="form-text error_meassages text-danger">' + value + '</small>');
                        $('.send-message textarea[name=' + key + ']').after('<small class="form-text error_meassages text-danger">' + value + '</small>');
                    });
                },
            });
        })
    </script>

</body>

</html>