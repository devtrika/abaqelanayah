@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
@endsection

@section('content')
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{__('admin.show_service')}}</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">

                            {{-- Service Name --}}
                                @foreach (languages() as $lang)
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{__('admin.service_name')}} {{__('admin.in_' . $lang)}}</label>
                                            <p class="form-control-static">{{$service->getTranslations('name')[$lang] ?? __('admin.not_set')}}</p>
                                        </div>
                                    </div>
                                @endforeach
                          

                            {{-- Service Description --}}
                                @foreach (languages() as $lang)
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{__('admin.service_description')}} {{__('admin.in_' . $lang)}}</label>
                                            <p class="form-control-static">{{$service->getTranslations('description')[$lang] ?? __('admin.not_set')}}</p>
                                        </div>
                                    </div>
                                @endforeach
                            

                            {{-- Provider Information --}}
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{__('admin.service_provider')}}</label>
                                    @if($service->provider && $service->provider->user)
                                        <p class="form-control-static">
                                            {{$service->provider->user->name}}
                                            <br>
                                            <small class="text-muted">{{$service->provider->commercial_name}}</small>
                                        </p>
                                    @else
                                        <p class="form-control-static text-muted">{{__('admin.no_provider')}}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Category Information --}}
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{__('admin.service_category')}}</label>
                                    @if($service->category)
                                        <p class="form-control-static">{{$service->category->name}}</p>
                                    @else
                                        <p class="form-control-static text-muted">{{__('admin.no_category')}}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Price --}}
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{__('admin.service_price')}}</label>
                                    <p class="form-control-static">{{number_format($service->price, 2)}} {{__('admin.sar')}}</p>
                                </div>
                            </div>

                            {{-- Duration --}}
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{__('admin.service_duration')}}</label>
                                    <p class="form-control-static">{{$service->duration}} {{__('admin.minutes')}}</p>
                                </div>
                            </div>

                            {{-- Expected Time to Accept --}}
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{__('admin.expected_time_to_accept')}}</label>
                                    <p class="form-control-static">{{$service->expected_time_to_accept}} {{__('admin.minutes')}}</p>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{__('admin.service_status')}}</label>
                                    <p class="form-control-static">
                                        @if($service->is_active)
                                            <span class="badge badge-success">{{__('admin.active')}}</span>
                                        @else
                                            <span class="badge badge-secondary">{{__('admin.inactive')}}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Created At --}}
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{__('admin.created_at')}}</label>
                                    <p class="form-control-static">{{$service->created_at->format('Y-m-d H:i:s')}}</p>
                                </div>
                            </div>

                            {{-- Updated At --}}
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{__('admin.updated_at')}}</label>
                                    <p class="form-control-static">{{$service->updated_at->format('Y-m-d H:i:s')}}</p>
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-center mt-3">
                                <a href="{{ route('admin.services.edit', ['id' => $service->id]) }}" class="btn btn-primary mr-1 mb-1">
                                    <i class="feather icon-edit"></i> {{__('admin.edit')}}
                                </a>
                                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-warning mr-1 mb-1">
                                    <i class="feather icon-arrow-left"></i> {{__('admin.back')}}
                                </a>
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
    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js')}}"></script>
@endsection
