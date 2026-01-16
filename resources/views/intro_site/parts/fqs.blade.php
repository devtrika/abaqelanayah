
    <div class="sec-padd bacg_section2" id="FAQ">
        <div class="container">
            <div class="the_title">
                <h3>{{__('intro_site.fqs')}}</h3>
                <p class="grey-color">
                   {{$settings['fqs_text']}}
                </p>
            </div>

            <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                @foreach ($fqsCategories as $key => $category)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{$key == 0 ? 'active' : ''}}" id="pills-{{$category->id}}-tab" data-toggle="pill" href="#pills-{{$category->id}}" role="tab" aria-controls="pills-{{$category->id}}" aria-selected="true">{{$category->title}}</a>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content" id="pills-tabContent">
                @foreach ($fqsCategories as $key => $category)
                    <div class="tab-pane fade {{$key == 0 ? 'show active' : ''}}" id="pills-{{$category->id}}" role="tabpanel" aria-labelledby="pills-{{$category->id}}-tab">

                        <div class="accordion" id="accordionExample">
                            @foreach ($category->questions as $fqsKey => $fqs)
                                <div class="card">
                                    <div class="card-header" id="headingOne">
                                        <button class="" type="button" data-toggle="collapse" data-target="#{{'collapse_'.$fqs->id}}" aria-expanded="true" aria-controls="collapseOne">
                                            {{$fqsKey + 1}} - {{$fqs ->title}}
                                        </button>
                                    </div>
                                    <div id="{{'collapse_'.$fqs->id}}" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                        <div class="card-body"> {{$fqs ->description}}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>