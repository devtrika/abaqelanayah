
    <div class="sec-padd">
        <div class="container">
            <div class="the_title">
                <h3>{{__('intro_site.parteners')}}</h3>
                <p class="grey-color">
                    {{$settings['parteners_text']}}
                </p>
            </div>
            
            <div class="owl-carousel owl-theme owl-brands">
                @foreach ($parteners as $partener)
                    <div class="item">
                        <img  loading="lazy"  src="{{$partener->image}}">
                    </div>
                @endforeach
            </div>                
        </div>
    </div>
    