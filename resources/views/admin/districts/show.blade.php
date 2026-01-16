@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <style>
        .district-show-card {
            background: linear-gradient(135deg, #f8fafc 60%, #e3e7ed 100%);
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 2.5rem 2rem;
            margin-top: 30px;
        }
        .district-icon {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 1rem;
        }
        .district-info {
            font-size: 1.2rem;
            margin-bottom: 1.2rem;
        }
        .district-label {
            font-weight: bold;
            color: #495057;
        }
        .district-value {
            color: #212529;
            background: #f1f3f6;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
            display: inline-block;
        }
        .district-status {
            font-size: 1.1rem;
            padding: 0.4rem 1.2rem;
            border-radius: 20px;
        }
        .district-actions {
            margin-top: 2rem;
        }
    </style>
@endsection

@section('content')
@section('content')
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <form class="store form-horizontal" >
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <ul class="nav nav-tabs mb-3">
                                            @foreach (languages() as $lang)
                                                <li class="nav-item">
                                                    <a class="nav-link @if($loop->first) active @endif"  data-toggle="pill" href="#first_{{$lang}}" aria-expanded="true">{{  __('admin.data') }} {{ $lang }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div class="tab-content">
                                            @foreach (languages() as $lang)
                                                <div role="tabpanel" class="tab-pane fade @if($loop->first) show active @endif " id="first_{{$lang}}" aria-labelledby="first_{{$lang}}" aria-expanded="true">
                                                    <div class="col-md-12 col-12">
                                                        <div class="form-group">
                                                            <label for="first-name-column">{{__('admin.name')}} {{ $lang }}</label>
                                                            <div class="controls">
                                                                <input type="text" value="{{$district->getTranslations('name')[$lang]??''}}" name="name[{{$lang}}]" class="form-control" placeholder="{{__('admin.write') . __('admin.name')}} {{ $lang }}" required data-validation-required-message="{{__('admin.this_field_is_required')}}" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-md-12 col-12">
                                            <div class="form-group">
                                                <label for="city-column">{{__('admin.city')}}</label>
                                                <div class="controls">
                                                    <select name="city_id" class="select2 form-control" required data-validation-required-message="{{__('admin.this_field_is_required')}}" disabled>
                                                        <option value="">{{__('admin.select_city')}}</option>
                                                        @foreach ($cities as $city)
                                                            <option value="{{$city->id}}" @if($district->city_id == $city->id) selected @endif>{{$city->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-12">
                                            <div class="form-group">
                                                <label for="status-column">{{__('admin.status')}}</label>
                                                <div class="controls">
                                                    <select name="status" class="form-control" required data-validation-required-message="{{__('admin.this_field_is_required')}}" disabled>
                                                        <option value="1" @if($district->status == '1') selected @endif>{{__('admin.active')}}</option>
                                                        <option value="0" @if($district->status == '0') selected @endif>{{__('admin.inactive')}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-center mt-3">
                                        <a href="{{ route('admin.districts.index') }}" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
    <script>
        $('.store input').attr('disabled' , true)
        $('.store textarea').attr('disabled' , true)
        $('.store select').attr('disabled' , true)
    </script>
@endsection
