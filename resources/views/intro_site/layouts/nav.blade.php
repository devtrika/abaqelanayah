
<body>
    <header>
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex justify-content-between align-items-center flex-grow-1">
                    <a href="{{route('intro')}}"><img  loading="lazy" src="{{$settings['intro_logo']}}" class="the-logo"></a>
                    <ul class="d-flex align-items-center justify-content-around nav_bar flex-grow-1">
                        <li><a href="{{route('intro')}}#home" >{{__('intro_site.home')}}</a></li>
                        <li><a href="{{route('intro')}}#who_we" >{{__('intro_site.who_us')}}</a></li>
                        <li><a href="{{route('intro')}}#our_service" >{{__('intro_site.our_services')}}</a></li>
                        <li><a href="{{route('intro')}}#how_work" >{{__('intro_site.who_we_work')}}</a></li>
                        <li><a href="{{route('intro')}}#FAQ" >{{__('intro_site.faq')}}</a></li>
                        <li><a href="{{route('intro')}}#connect_us" >{{__('intro_site.contact_us')}}</a></li>
                    </ul>
                </div>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{lang() == 'ar' ? 'English' : 'عربي'}} <i class="fas fa-globe"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <a href="{{url('lang/ar')}}" class="dropdown-item" type="button">عربي</a>
                            <a href="{{url('lang/en')}}" class="dropdown-item" type="button">English</a>
                        </div>
                    </div> 
                    {{-- <a href="{{url('provider/register')}}" class="btn-main">{{__('intro_site.regsteras_provider')}}</a> --}}
                    <div class="close-open-nav">
                        <div>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>