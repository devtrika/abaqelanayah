<div class="owl-carousel owl-theme owl-index" id="home">
    @foreach ($sliders as $slider)
        <div class="item">
            <div class="info_owl">
                <div class="container">
                    <div class="ads_wol">
                        <h3>{{$slider->title}}</h3>
                        <p>{{$slider->description}}</p>
                    </div>
                </div>
                <div class="img_owl_index">
                    <img loading="lazy" src="{{$slider->image}}">
                </div>
            </div>
        </div>
    @endforeach
</div>