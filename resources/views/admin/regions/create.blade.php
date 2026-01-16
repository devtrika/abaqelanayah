@extends('admin.layout.master')
{{-- extra css files --}}
@section('css')
    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
@endsection
{{-- extra css files --}}

@section('content')
    <!-- // Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    {{-- <div class="card-header">
                        <h4 class="card-title">{{ __('admin.add') }}</h4>
                    </div> --}}
                    <div class="card-content">
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.regions.store') }}" class="store form-horizontal"
                                novalidate>
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            {{-- Remove tabs, show all language inputs in one column --}}
                                            @foreach (languages() as $lang)
                                                <div class="col-md-12 col-12">
                                                    <div class="form-group">
                                                        <label for="first-name-column">
                                                            {{__('admin.name')}} {{ $lang }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="controls">
                                                            <input type="text" name="name[{{$lang}}]" class="form-control"
                                                                placeholder="{{__('admin.write') . __('admin.name')}} {{ $lang }}"
                                                                required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="col-md-12 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">{{ __('admin.country') }}</label>
                                                    <div class="controls">
                                                        <select name="country_id" class="select2 form-control" required
                                                            data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                            <option value>{{ __('admin.choose_the_country') }}</option>
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-12 d-flex justify-content-center mt-3">
                                            <button type="submit"
                                                class="btn btn-primary mr-1 mb-1 submit_button">{{ __('admin.add') }}</button>
                                            <a href="{{ url()->previous() }}" type="reset"
                                                class="btn btn-outline-warning mr-1 mb-1">{{ __('admin.back') }}</a>
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
    <script src="{{ asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/forms/validation/form-validation.js') }}"></script>
    <script src="{{ asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js') }}"></script>

    {{-- show selected image script --}}
    @include('admin.shared.addImage')
    {{-- show selected image script --}}

    {{-- submit add form script --}}
    @include('admin.shared.submitAddForm')
    {{-- submit add form script --}}

    <script>
        $(document).ready(function() {
            // Try to find the first input with an error
            var $firstInvalid = $('.is-invalid').first();
            if (!$firstInvalid.length) {
                // Fallback: find the first visible invalid-feedback
                $firstInvalid = $('.invalid-feedback:visible').first().closest('.form-group').find('input, textarea, select');
            }
            if ($firstInvalid.length) {
                var $tabPane = $firstInvalid.closest('.tab-pane');
                if ($tabPane.length) {
                    var tabId = $tabPane.attr('id');
                    // Use Bootstrap's tab API to show the tab
                    $('.nav-tabs a[href="#' + tabId + '"]').tab('show');
                }
            }
        });
    </script>
@endsection
