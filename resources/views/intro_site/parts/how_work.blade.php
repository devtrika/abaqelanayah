<div class="sec-padd" id="how_work">
    <div class="container">
        <div class="the_title">
            <h3>{{__('intro_site.how_work')}} {{$settings['intro_name']}}</h3>
            <p class="grey-color">{{$settings['how_work_text']}} </p>
        </div>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <ul class="how_work">
                    @foreach ($howWorks as $howWork)     
                        <li>
                            <img  loading="lazy"  src="{{$howWork->image}}">
                            <div>{{$howWork->title}}</div>
                        </li>
                    @endforeach
                </ul>

            </div>
        </div>
    </div>
</div>