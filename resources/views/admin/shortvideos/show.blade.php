@extends('admin.layout.master')

@section('content')
<!-- // Basic multiple Column Form section start -->
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                {{-- <div class="card-header">
                    <h4 class="card-title">{{__('admin.view') . ' ' . __('admin.shortvideo')}}</h4>
                </div> --}}
                <div class="card-content">
                    <div class="card-body">
                        <div class="show-content">
                            <div class="row">
                                {{-- Video Display --}}
                                <div class="col-12 mb-4">
                                    @php
                                        $videoMedia = $shortvideo->getFirstMediaUrl('short_video');
                                    @endphp
                                    @if ($videoMedia)
                                        <div class="video-display">
                                            <video width="100%" height="400" controls>
                                                <source src="{{ $videoMedia }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="feather icon-info"></i> {{__('admin.no_video_uploaded')}}
                                        </div>
                                    @endif
                                </div>

                                {{-- Video Information --}}
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label>{{__('admin.video_id')}}</label>
                                        <input type="text" value="{{$shortvideo->video_id}}" class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label>{{__('admin.client_name')}}</label>
                                        <input type="text" value="{{$shortvideo->client_name}}" class="form-control" readonly>
                                    </div>
                                </div>

                              
                              
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label>{{__('admin.published_at')}}</label>
                                        <input type="text" value="{{$shortvideo->published_at ? (\Carbon\Carbon::parse($shortvideo->published_at)->format('Y-m-d H:i:s')) : __('admin.not_set')}}" class="form-control" readonly>
                                    </div>
                                </div>

                              
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label>{{__('admin.status')}}</label>
                                        <input type="text" value="{{$shortvideo->is_active ? __('admin.active') : __('admin.inactive')}}" class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label>{{__('admin.created_at')}}</label>
                                        <input type="text" value="{{\Carbon\Carbon::parse($shortvideo->created_at)->format('Y-m-d H:i:s')}}" class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label>{{__('admin.updated_at')}}</label>
                                        <input type="text" value="{{\Carbon\Carbon::parse($shortvideo->updated_at)->format('Y-m-d H:i:s')}}" class="form-control" readonly>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="col-12 d-flex justify-content-center mt-3">
                                    <a href="{{route('admin.shortvideos.edit', $shortvideo->id)}}" class="btn btn-primary mr-1 mb-1">
                                        <i class="feather icon-edit"></i> {{__('admin.edit')}}
                                    </a>
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-warning mr-1 mb-1">
                                        <i class="feather icon-arrow-left"></i> {{__('admin.back')}}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('js')
    <script>
        $('.show input').attr('disabled' , true)
        $('.show textarea').attr('disabled' , true)
        $('.show select').attr('disabled' , true)
    </script>
@endsection