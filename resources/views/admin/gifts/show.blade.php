@extends('admin.layout.master')

@section('content')
<!-- // Basic multiple Column Form section start -->
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                {{-- <div class="card-header">
                    <h4 class="card-title">{{__('admin.view') . ' ' . __('admin.gift')}}</h4>
                </div> --}}
                <div class="card-content">
                    <div class="card-body">
                        <form class="show form-horizontal">
                            <div class="form-body">
                                <div class="row">

                                    {{-- Orders Count --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="orders_count">{{ __('admin.orders_count') }}</label>
                                            <input type="number" id="orders_count" class="form-control"
                                                   value="{{ $gift->orders_count }}" disabled>
                                        </div>
                                    </div>

                                    {{-- Month --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="month">{{ __('admin.month') }}</label>
                                            <input type="date" id="month" class="form-control"
                                                   value="{{ $gift->month->format('Y-m-d') }}" disabled>
                                        </div>
                                    </div>

                                    {{-- Created Date --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="created_at">{{ __('admin.created_at') }}</label>
                                            <input type="text" id="created_at" class="form-control"
                                                   value="{{ $gift->created_at->format('d/m/Y H:i') }}" disabled>
                                        </div>
                                    </div>

                                    {{-- Updated Date --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="updated_at">{{ __('admin.updated_at') }}</label>
                                            <input type="text" id="updated_at" class="form-control"
                                                   value="{{ $gift->updated_at->format('d/m/Y H:i') }}" disabled>
                                        </div>
                                    </div>

                                    {{-- Is Active Status --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="is_active">{{ __('admin.status') }}</label>
                                            <div class="mt-2">
                                                @if ($gift->is_active)
                                                <span class="badge badge-success badge-lg">
                                                    <i class="la la-check font-medium-2"></i> {{ __('admin.active') }}
                                                </span>
                                                @else
                                                <span class="badge badge-danger badge-lg">
                                                    <i class="la la-close font-medium-2"></i> {{ __('admin.inactive') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Coupon Section --}}
                                    <div class="col-12 mt-3">
                                        <h5 class="mb-3">{{ __('admin.coupon') }}</h5>
                                        @if($gift->coupon)
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <strong>{{ __('admin.coupon_num') }}:</strong><br>
                                                            {{ $gift->coupon->coupon_num }}
                                                        </div>
                                                        <div class="col-md-2">
                                                            <strong>{{ __('admin.type') }}:</strong><br>
                                                            {{ $gift->coupon->type }}
                                                        </div>
                                                        <div class="col-md-2">
                                                            <strong>{{ __('admin.discount') }}:</strong><br>
                                                            {{ $gift->coupon->discount }}{{ $gift->coupon->type == 'ratio' ? '%' : ' ' . __('admin.currency') }}
                                                        </div>
                                                        <div class="col-md-2">
                                                            <strong>{{ __('admin.max_discount') }}:</strong><br>
                                                            {{ $gift->coupon->max_discount ?? '-' }}
                                                        </div>
                                                        <div class="col-md-3">
                                                            <strong>{{ __('admin.status') }}:</strong><br>
                                                            <span class="badge badge-{{ $gift->coupon->status == 'available' ? 'success' : 'danger' }}">
                                                                {{ $gift->coupon->status }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                {{ __('admin.no_coupons_assigned') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-12 mt-3">
                                        <a href="{{ route('admin.gifts.edit', $gift->id) }}" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">{{ __('admin.edit') }}</a>
                                        <a href="{{ route('admin.gifts.index') }}" class="btn btn-outline-warning mr-1 mb-1 waves-effect waves-light">{{ __('admin.back') }}</a>
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
        $('.show input').attr('disabled' , true)
        $('.show textarea').attr('disabled' , true)
        $('.show select').attr('disabled' , true)
    </script>
@endsection