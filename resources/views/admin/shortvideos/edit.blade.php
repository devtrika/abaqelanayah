@extends('admin.layout.master')
{{-- extra css files --}}
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
@endsection
{{-- extra css files --}}

@section('content')
<!-- // Basic multiple Column Form section start -->
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                {{-- <div class="card-header">
                    <h4 class="card-title">{{__('admin.update') . ' ' . __('admin.shortvideo')}}</h4>
                </div> --}}
                <div class="card-content">
                    <div class="card-body">
                        <form  method="POST" action="{{route('admin.shortvideos.update' , ['id' => $shortvideo->id])}}" class="store form-horizontal" novalidate enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">

                                    {{-- Video Upload Section --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{__('admin.current_video')}}</label>
                                            <div class="current-video mb-3">
                                                @php
                                                    $videoMedia = $shortvideo->getFirstMediaUrl('short_video');
                                                @endphp
                                                @if ($videoMedia)
                                                    <div class="video-preview">
                                                        <video width="300" height="200" controls>
                                                            <source src="{{ $videoMedia }}" type="video/mp4">
                                                            Your browser does not support the video tag.
                                                        </video>
                                                        <p class="text-muted mt-2">{{__('admin.current_video_file')}}</p>
                                                    </div>
                                                @else
                                                    <p class="text-muted">{{__('admin.no_video_uploaded')}}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="video">{{__('admin.upload_new_video')}} ({{__('admin.optional')}})</label>
                                            <div class="controls">
                                                <input type="file" name="video" class="form-control" accept="video/*">
                                                <small class="text-muted">{{__('admin.supported_formats')}}: MP4, MOV, AVI, WMV, FLV, WEBM ({{__('admin.max_size')}}: 100MB)</small>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Video ID --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="video_id">{{__('admin.video_id')}}</label>
                                            <div class="controls">
                                                <input type="text" name="video_id" value="{{$shortvideo->video_id}}" class="form-control" placeholder="{{__('admin.video_id')}}" required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Client Name --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="client_name">{{__('admin.client_name')}}</label>
                                            <div class="controls">
                                                <input type="text" name="client_name" value="{{$shortvideo->client_name}}" class="form-control" placeholder="{{__('admin.client_name')}}" required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Order Rate --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="order_rate_id">{{__('admin.order_rate')}}</label>
                                            <div class="controls">
                                                <select name="order_rate_id" class="form-control select2" required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                                    <option value="">{{__('admin.choose')}}</option>
                                                    @foreach(\App\Models\OrderRate::with(['order', 'user'])->get() as $orderRate)
                                                        <option value="{{$orderRate->id}}" {{ $shortvideo->order_rate_id == $orderRate->id ? 'selected' : '' }}>
                                                            {{__('admin.order')}} #{{$orderRate->order->order_number ?? $orderRate->id}} - {{$orderRate->user->name ?? 'N/A'}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Rate (Optional) --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="rate_id">{{__('admin.rate')}} ({{__('admin.optional')}})</label>
                                            <div class="controls">
                                                <select name="rate_id" class="form-control select2">
                                                    <option value="">{{__('admin.choose')}}</option>
                                                    @foreach(\App\Models\Rate::all() as $rate)
                                                        <option value="{{$rate->id}}" {{ $shortvideo->rate_id == $rate->id ? 'selected' : '' }}>
                                                            {{$rate->rate}} {{__('admin.stars')}} - {{$rate->comment}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- User (Optional) --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="user_id">{{__('admin.user')}} ({{__('admin.optional')}})</label>
                                            <div class="controls">
                                                <select name="user_id" class="form-control select2">
                                                    <option value="">{{__('admin.choose')}}</option>
                                                    @foreach(\App\Models\User::all() as $user)
                                                        <option value="{{$user->id}}" {{ $shortvideo->user_id == $user->id ? 'selected' : '' }}>
                                                            {{$user->name}} ({{$user->email}})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Published At --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="published_at">{{__('admin.published_at')}}</label>
                                            <div class="controls">
                                                <input type="datetime-local" name="published_at" value="{{$shortvideo->published_at ? \Carbon\Carbon::parse($shortvideo->published_at)->format('Y-m-d\TH:i') : ''}}" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Expired At --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="expired_at">{{__('admin.expired_at')}}</label>
                                            <div class="controls">
                                                <input type="datetime-local" name="expired_at" value="{{$shortvideo->expired_at ? \Carbon\Carbon::parse($shortvideo->expired_at)->format('Y-m-d\TH:i') : ''}}" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="is_active">{{__('admin.status')}}</label>
                                            <div class="controls">
                                                <select name="is_active" class="form-control" required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                                    <option value="1" {{ $shortvideo->is_active ? 'selected' : '' }}>{{__('admin.active')}}</option>
                                                    <option value="0" {{ !$shortvideo->is_active ? 'selected' : '' }}>{{__('admin.inactive')}}</option>
                                                </select>
                                            </div>
                                        </div>

                                    <div class="col-12 d-flex justify-content-center mt-3">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{__('admin.update')}}</button>
                                        <a href="{{ url()->previous() }}" type="reset" class="btn btn-outline-warning mr-1 mb-1">{{__('admin.back')}}</a>
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
    <script src="{{asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/forms/validation/form-validation.js')}}"></script>
    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js')}}"></script>
    
    {{-- show selected image script --}}
        @include('admin.shared.addImage')
    {{-- show selected image script --}}

    {{-- submit edit form script --}}
        @include('admin.shared.submitEditForm')
    {{-- submit edit form script --}}
    
@endsection