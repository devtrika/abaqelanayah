<div class="sec-padd" id="connect_us">
    <div class="container">
        <div class="the_title">
            <h3>{{__('intro_site.cantact_us')}}</h3>
            <p class="grey-color">
                {{$settings['contact_text']}}
            </p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-content">
                    <form action="{{url('send-message')}}" enctype="multipart/form-data" method="POST" class="send-message">
                        @csrf
                        <div class="form-group">
                            <input type="text" name="name" class="form-control" placeholder="{{__('site.name')}}">
                        </div>
                        <div class="form-group">
                            <input type="number" name="phone" class="form-control" placeholder="{{__('site.phone')}}">
                        </div>

                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="{{__('site.email')}}">
                        </div>

                        <div>
                            <textarea class="form-control" name="message" placeholder="{{__('site.write_here')}}"></textarea>
                        </div>
                        
                        <button class="btn-main">{{__('site.send_message')}} <i class="fas fa-long-arrow-alt-left"></i></button>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div> 