@extends('admin.layout.master')

@section('content')

<section id="image-details-section">
    <div class="row">
        <!-- Image Preview Card -->
        <div class="col-md-4">
            <div class="card shadow-lg border-primary">
                <div class="card-header bg-primary text-white text-center">
                    <i class="feather icon-image" style="font-size:2rem;"></i>
                    <h5 class="mt-1">{{__('admin.image')}}</h5>
                </div>
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center align-items-center" style="background:#f8f9fa; border-radius:10px; min-height:230px;">
                        @if($image->image_ar)
                            <img src="{{$image->image_ar}}" alt="{{ $image->getTranslation('name', 'ar') }}" class="img-fluid rounded shadow" style="max-height:220px; border:3px solid #007bff; background:#fff;">
                        @else
                            <img src="{{ asset('storage/images/default.png') }}" alt="default" class="img-fluid rounded shadow" style="max-height:220px; border:3px solid #ccc; background:#fff;">
                        @endif
                        @if($image->image_en)
                            <img src="{{$image->image_en}}" alt="{{ $image->getTranslation('name', 'en') }}" class="img-fluid rounded shadow ml-2" style="max-height:220px; border:3px solid #28a745; background:#fff;">
                        @endif
                    </div>
                    <div>
                        <span class="badge badge-pill badge-info px-3 py-2" style="font-size:1rem;"><i class="feather icon-type"></i> {{ __('admin.' . $image->type) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Details Card -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="feather icon-info"></i> {{__('admin.image_details')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
         
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="feather icon-link mr-2 text-success"></i>
                                <strong>{{__('admin.link')}}:</strong>
                                <span class="ml-2"><a href="{{ $image->link }}" target="_blank">{{ $image->link }}</a></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="feather icon-globe mr-2 text-info"></i>
                                <strong>{{__('admin.image_type')}}:</strong>
                                <span class="ml-2">{{ __('admin.' . $image->type) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="feather icon-flag mr-2 text-danger"></i>
                                <strong>{{__('admin.status')}}:</strong>
                                <span class="ml-2">
                                    @if($image->is_active)
                                        <span class="badge badge-success">{{__('admin.active')}}</span>
                                    @else
                                        <span class="badge badge-danger">{{__('admin.inactive')}}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="feather icon-type mr-2 text-secondary"></i>
                                <strong>{{__('admin.name')}} ({{__('admin.ar')}}):</strong>
                                <span class="ml-2">{{ $image->getTranslation('name', 'ar') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="feather icon-type mr-2 text-secondary"></i>
                                <strong>{{__('admin.name')}} ({{__('admin.en')}}):</strong>
                                <span class="ml-2">{{ $image->getTranslation('name', 'en') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-center mt-3">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-warning btn-lg px-5"><i class="feather icon-arrow-left"></i> {{ __('admin.back') }}</a>
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
        $('.store input').attr('disabled' , true)
        $('.store textarea').attr('disabled' , true)
        $('.store select').attr('disabled' , true)

    </script>
@endsection