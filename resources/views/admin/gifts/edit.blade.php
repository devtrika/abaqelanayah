@extends('admin.layout.master')
{{-- extra css files --}}
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/forms/select/select2.min.css')}}">
@endsection
{{-- extra css files --}}

@section('content')
<!-- // Basic multiple Column Form section start -->
<form method="POST" action="{{ route('admin.gifts.update', ['id' => $gift->id]) }}" class="store form-horizontal" novalidate>
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">

                                    {{-- Orders Count --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="orders_count">{{ __('admin.orders_count') }}</label>
                                            <input type="number" id="orders_count" class="form-control"
                                                   placeholder="{{ __('admin.orders_count') }}"
                                                   name="orders_count" min="1" value="{{ old('orders_count', $gift->orders_count) }}" required>
                                            @error('orders_count')
                                                <span class="alert alert-danger">
                                                    <small class="errorTxt">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Month --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="month">{{ __('admin.month') }}</label>
                                            <input type="date" id="month" class="form-control"
                                                   name="month" value="{{ old('month', $gift->month->format('Y-m-d')) }}" required>
                                            @error('month')
                                                <span class="alert alert-danger">
                                                    <small class="errorTxt">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Coupon Selection --}}
                                    <div class="col-md-6 col-6">
                                        <div class="form-group">
                                            <label for="coupon_id">{{ __('admin.coupon') }}</label>
                                            <select class="select2 form-control" name="coupon_id" id="coupon_id" required>
                                                <option value="">{{ __('admin.choose') }}</option>
                                                @foreach($coupons as $coupon)
                                                    <option value="{{ $coupon->id }}"
                                                        {{ $gift->coupon_id == $coupon->id ? 'selected' : '' }}>
                                                        {{ $coupon->coupon_num }} ({{ $coupon->discount }}{{ $coupon->type == 'ratio' ? '%' : ' ' . __('admin.currency') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('coupon_id')
                                                <span class="alert alert-danger">
                                                    <small class="errorTxt">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Is Active Toggle --}}
                                    <div class="col-md-6 col-6">
                                        <div class="form-group">
                                            <label for="is_active">{{ __('admin.is_active') }}</label>
                                            <div class="controls">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $gift->is_active) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="is_active">
                                                        <span class="switch-text-left">{{ __('admin.active') }}</span>
                                                        <span class="switch-text-right">{{ __('admin.inactive') }}</span>
                                                    </label>
                                                </div>
                                                @error('is_active')
                                                <span class="alert alert-danger">
                                                    <small class="errorTxt">{{ $message }}</small>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">{{ __('admin.edit') }}</button>
                                        <a href="{{ route('admin.gifts.index') }}" class="btn btn-outline-warning mr-1 mb-1 waves-effect waves-light">{{ __('admin.back') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</form>
@endsection

@section('js')
    <script src="{{asset('admin/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/forms/validation/form-validation.js')}}"></script>
    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js')}}"></script>
    <script src="{{asset('admin/app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/scripts/forms/select/form-select2.js')}}"></script>

    @include('admin.shared.submitEditForm')

    <script>
        $(document).ready(function() {
            $('#coupon_id').select2({
                placeholder: "{{ __('admin.choose') }}",
                allowClear: true,
                width: '100%',
                dir: "{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}",
                language: {
                    searching: function() {
                        return "{{ __('admin.searching') }}...";
                    },
                    noResults: function() {
                        return "{{ __('admin.no_results_found') }}";
                    },
                    inputTooShort: function() {
                        return "{{ __('admin.please_enter_more_characters') }}";
                    }
                },
                minimumInputLength: 0,
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(data) {
                    if (data.loading) {
                        return "{{ __('admin.searching') }}...";
                    }
                    return data.text;
                },
                templateSelection: function(data) {
                    return data.text;
                }
            });

            // Fix for RTL and styling issues
            $('.select2-container').css({
                'direction': "{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}",
                'text-align': "{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"
            });

            // Custom CSS fixes for single select
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    .select2-container--default .select2-selection--single {
                        border: 1px solid #d9dee3 !important;
                        border-radius: 0.357rem !important;
                        height: 38px !important;
                    }
                    .select2-container--default .select2-selection--single .select2-selection__rendered {
                        line-height: 36px !important;
                        padding-left: 12px !important;
                        padding-right: 20px !important;
                    }
                    .select2-container--default.select2-container--focus .select2-selection--single {
                        border: 1px solid #7367f0 !important;
                        box-shadow: 0 0 0 0.2rem rgba(115, 103, 240, 0.25) !important;
                    }
                    .select2-dropdown {
                        border: 1px solid #d9dee3 !important;
                        border-radius: 0.357rem !important;
                    }
                    .select2-container--default .select2-results__option--highlighted[aria-selected] {
                        background-color: #7367f0 !important;
                    }
                `)
                .appendTo('head');
        });
    </script>
@endsection