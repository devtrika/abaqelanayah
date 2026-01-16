@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
@endsection

@section('content')
    <!-- Profile header -->
    {{-- <div class="profile-cover mb-3">
        <div class="profile-cover-img" style="background-image: url({{ $settings['profile_cover'] ?? '' }})"></div>
        <div class="media align-items-center text-center text-md-left flex-column flex-md-row m-0 p-3">
            <div class="mr-md-3 mb-2 mb-md-0">
                <a href="{{ $row->user_image }}" class="profile-thumb" data-fancybox="gallery" data-caption="{{ $row->name }}">
                    <img src="{{ $row->user_image }}" class="bg-white border-white rounded-circle" width="64" height="64" alt="{{ $row->name }}">
                </a>
            </div>
            <div class="media-body text-dark">
                <h3 class="mb-0">{{ $row->name }}</h3>
                <p class="mb-0 small text-muted">{{ $row->email ?? '-' }}</p>
            </div>
        </div>
    </div> --}}

    <!-- Full-width profile user_image above fields -->
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                {{-- <div class="text-center mb-3">
                    <a href="{{ $row->user_image }}" target="_blank">
                        <img src="{{ $row->user_image }}" alt="{{ $row->name }}" class="rounded-circle" style="width:120px;height:120px;object-fit:cover;">
                    </a>
                </div> --}}

                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <!-- Left column: profile picture and basic info -->
                                <div class="col-lg-4 col-md-5 col-12 mb-2">
                                    {{-- <div class="text-center mb-3">
                                        <a href="{{ $row->user_image }}" target="_blank">
                                            <img src="{{ $row->user_image }}" alt="{{ $row->name }}" class="rounded-circle" style="width:140px;height:140px;object-fit:cover;">
                                        </a>
                                    </div> --}}
                                    <h4 class="text-center">{{ $row->name }}</h4>
                                    <p class="text-center text-muted mb-0">{{ $row->email ?? '-' }}</p>
                                    <p class="text-center text-muted">{{ $row->full_phone ?? (($row->country_code ? '+'.$row->country_code : '') . ' ' . ($row->phone ?? '-')) }}</p>
                                </div>

                                <!-- Right column: details -->
                                <div class="col-lg-8 col-md-7 col-12">
                                    <div class="row">
                                        <div class="col-md-6 col-12 mb-2">
                                            <label class="font-weight-bold">{{ __('admin.City') }}</label>
                                            <div class="form-control-plaintext">{{ $row->city->name ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6 col-12 mb-2">
                                            <label class="font-weight-bold">{{ __('admin.District') }}</label>
                                            <div class="form-control-plaintext">{{ $row->district->name ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6 col-12 mb-2">
                                            <label class="font-weight-bold">{{ __('admin.type') }}</label>
                                            <div class="form-control-plaintext">{{ $row->type ? __('admin.' . $row->type) : '-' }}</div>
                                        </div>

                                        <div class="col-md-6 col-12 mb-2">
                                            <label class="font-weight-bold">{{ __('admin.created_at') }}</label>
                                            <div class="form-control-plaintext">{{ $row->created_at ? $row->created_at->toDayDateTimeString() : '-' }}</div>
                                        </div>

                                        <div class="col-md-6 col-12 mb-2">
                                            <label class="font-weight-bold">{{ __('admin.active') }}</label>
                                            <div class="form-control-plaintext">{{ $row->is_active ? __('admin.yes') : __('admin.no') }}</div>
                                        </div>

                                        <div class="col-md-6 col-12 mb-2">
                                            <label class="font-weight-bold">{{ __('admin.blocked') }}</label>
                                            <div class="form-control-plaintext">{{ $row->is_blocked ? __('admin.yes') : __('admin.no') }}</div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mt-3">
                                        <a href="{{ route('admin.clients.edit', ['id' => $row->id]) }}" class="btn btn-sm btn-primary mr-2">{{ __('admin.edit') }}</a>
                                        <a href="{{ route('admin.clients.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.back') }}</a>
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
    {{-- no JS required for readonly fields view --}}
@endsection
