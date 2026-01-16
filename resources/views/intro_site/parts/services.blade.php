<div class="sec-padd bacg_section" id="our_service">
    <div class="container">
        <div class="the_title">
            <h3>{{__('intro_site.our_services')}}</h3>
            <p>
                {{$settings['services_text']}}
            </p>
        </div>
        <div class="row">
            @foreach ($services as $service)
                <div class="col-md-4">
                    <div class="box-servess">
                        <img  loading="lazy"  src="{{asset('intro_site/imgs/ser.png')}}">
                        <h6 class="f-b">{{$service->title}}</h6>
                        <p class="grey-color">
                            {{$service->description}}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>