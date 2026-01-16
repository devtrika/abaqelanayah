@extends('admin.layout.master')
{{-- extra css files --}}
@section('css')
    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
    <style>
        .btn{
            margin-top:-2px;
        }
        .btn-block{
            width:fit-content;
        }
    </style>
@endsection
{{-- extra css files --}}

@section('content')
@if(session()->has('errors'))
    <div class="alert alert-danger">
        <ul>
            @foreach(session()->get('errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<!-- // Basic multiple Column Form section start -->
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <form method="POST" action="{{route('admin.products.store')}}" class="store form-horizontal" enctype="multipart/form-data" novalidate>
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        @include('admin.shared.mediaPicker', [
                                            'existing' => [],
                                            'allowUpload' => true,
                                            'multiple' => true,
                                            'inputName' => 'images[]'
                                        ])
                                    </div>

                                    <!-- Product Name Fields -->
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>{{__('admin.product_name')}} (AR)<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                <input type="text" name="name[ar]" class="form-control" 
                                                    placeholder="{{__('admin.product_name')}} (AR)"
                                                    required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>{{__('admin.product_name')}} (EN)</label>
                                            <div class="controls">
                                                <input type="text" name="name[en]" class="form-control"
                                                    placeholder="{{__('admin.product_name')}} (EN)">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Product Description Fields -->
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>{{__('admin.product_description')}} (AR)</label>
                                            <div class="controls">
                                                <textarea name="description[ar]" class="form-control"
                                                    placeholder="{{__('admin.product_description')}} (AR)"
                                                    rows="6"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>{{__('admin.product_description')}} (EN)</label>
                                            <div class="controls">
                                                <textarea name="description[en]" class="form-control"
                                                    placeholder="{{__('admin.product_description')}} (EN)"
                                                    rows="6"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                        <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="parent-category-column">{{ __('admin.brands') }} <span class="text-danger">*</span></label>
                                            <div class="controls">
                                                <select name="brand_id" class="select2 form-control" required data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                    <option value="">{{ __('admin.brands') }}</option>
                                                    @foreach ($brands as $cat)
                                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Parent Category Selection --}}

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="parent-category-column">{{ __('admin.parent_category') }} <span class="text-danger">*</span></label>
                                            <div class="controls">
                                                <select id="parent_category_id" name="parent_category_id" class="select2 form-control" required data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                    <option value="">{{ __('admin.select_parent_category') }}</option>
                                                    @foreach ($categories as $cat)
                                                        @if($cat->parent_id === null)
                                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Subcategory Selection --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="category-column">{{ __('admin.Subcategory') }} <span class="text-danger">*</span></label>
                                            <div class="controls">
                                                <select name="category_id" id="category_id" class="select2 form-control" required data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                    <option value="">{{ __('admin.select_category') }}</option>
                                                    @foreach ($categories as $cat)
                                                        @if($cat->parent_id !== null)
                                                            <option value="{{ $cat->id }}" data-parent-id="{{ $cat->parent_id }}">{{ $cat->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Base Price --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="base-price-column">{{ __('admin.base_price') }} <span class="text-danger">*</span></label>
                                            <div class="controls">
                                                <input type="number" step="0.01" name="base_price" class="form-control"
                                                    placeholder="{{ __('admin.base_price') }}" required
                                                    data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                            </div>
                                        </div>
                                    </div>

                                                                        {{-- <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="quantity-column">{{ __('admin.quantity') }} <span class="text-danger">*</span></label>
                                            <div class="controls">
                                                <input type="number" min="0" name="quantity" class="form-control"
                                                    placeholder="{{ __('admin.quantity') }}" required
                                                    data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                            </div>
                                        </div>
                                    </div> --}}

                                    {{-- Discount Percentage --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="discount-column">{{ __('admin.discount_percentage') }}</label>
                                            <div class="controls">
                                                <input type="number" step="0.01" min="0" max="100" name="discount_percentage" class="form-control"
                                                    placeholder="{{ __('admin.discount_percentage') }}">
                                            </div>
                                        </div>
                                    </div>


                                    {{-- Status --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="is-active-column">{{ __('admin.status') }}</label>
                                            <div class="controls">
                                                <select name="is_active" class="select2 form-control">
                                                    <option value="1" selected>{{ __('admin.active') }}</option>
                                                    <option value="0">{{ __('admin.inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Is Refunded --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="is-refunded-column">{{ __('admin.is_refunded') }}</label>
                                            <div class="controls">
                                                    <select name="is_refunded" class="select2 form-control">
                                                        <option value="0" selected>{{ __('admin.not_refunded') }}</option>
                                                        <option value="1">{{ __('admin.refunded') }}</option>
                                                </select>
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

    @include('admin.shared.mediaPickerJs')

    {{-- submit add form script --}}
    @include('admin.shared.submitAddForm')
    {{-- submit add form script --}}

     @include('admin.shared.categoryDependentJs')





{{-- <script>
    $('.store').on('submit', function (e) {
        // تحقق من وجود وزن واحد على الأقل
        var weightOptions = document.querySelectorAll('#weight-options-container .option-row');
        if (weightOptions.length === 0) {
            Swal.fire({
                icon: 'warning',
title: "{{ __('admin.weight_required') }}",
                showConfirmButton: true,
            });
            e.preventDefault();
            return;
        }
    
        e.preventDefault();
    
        let form = $(this);
        let submitButton = $('.submit_button');
    
        submitButton.attr('disabled', true);
    
        let formData = new FormData(this);
    
        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                Swal.fire({
            position: 'top-start',
            type: 'success',
            title: '{{ __('admin.added_successfully') }}',
            showConfirmButton: false,
            timer: 1500,
            confirmButtonClass: 'btn btn-primary',
            buttonsStyling: false,
          })
                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = response.url;
                }, 2000);
            },
            error: function (xhr) {
                submitButton.attr('disabled', false);
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (const key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'danger',
                                title: errors[key][0],
                                showConfirmButton: false,
                                timer: 4000,
                                timerProgressBar: true
                            });
                        }
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("admin.something_went_wrong") }}',
                        text: xhr.responseJSON.message || '',
                    });
                }
            }
        });
    });
</script>     --}}

    {{-- <script>
        let weightOptionIndex = 0;
        let cuttingOptionIndex = 0;
        let packagingOptionIndex = 0;

        function addWeightOption() {
            const container = document.getElementById('weight-options-container');
            const optionHtml = `
                <div class="row option-row" data-type="weight" data-index="${weightOptionIndex}">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('admin.option_name') }}</label>
                            <input type="text" name="options[weight_${weightOptionIndex}][name]" class="form-control" required data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                            <input type="hidden" name="options[weight_${weightOptionIndex}][type]" value="weight">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('admin.additional_price') }}</label>
                            <input type="number" step="0.01" name="options[weight_${weightOptionIndex}][additional_price]" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('admin.is_default') }}</label>
                            <select name="options[weight_${weightOptionIndex}][is_default]" class="form-control" onchange="checkDefault(this, 'weight')">
                                <option value="0">{{ __('admin.no') }}</option>
                                <option value="1">{{ __('admin.yes') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-block" onclick="removeWeightOption(${weightOptionIndex})">
                                <i class="feather icon-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', optionHtml);
            weightOptionIndex++;
        }

        function removeWeightOption(index) {
            const optionRow = document.querySelector(`[data-type="weight"][data-index="${index}"]`);
            if (optionRow) {
                optionRow.remove();
            }
        }

        function addCuttingOption() {
            const container = document.getElementById('cutting-options-container');
            const optionHtml = `
                <div class="row option-row" data-type="cutting" data-index="${cuttingOptionIndex}">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('admin.option_name') }}</label>
                            <input type="text" name="options[cutting_${cuttingOptionIndex}][name]" class="form-control" required data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                            <input type="hidden" name="options[cutting_${cuttingOptionIndex}][type]" value="cutting">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('admin.additional_price') }}</label>
                            <input type="number" step="0.01" name="options[cutting_${cuttingOptionIndex}][additional_price]" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('admin.is_default') }}</label>
                            <select name="options[cutting_${cuttingOptionIndex}][is_default]" class="form-control" onchange="checkDefault(this, 'cutting')">
                                <option value="0">{{ __('admin.no') }}</option>
                                <option value="1">{{ __('admin.yes') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-block" onclick="removeCuttingOption(${cuttingOptionIndex})">
                                <i class="feather icon-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', optionHtml);
            cuttingOptionIndex++;
        }

        function removeCuttingOption(index) {
            const optionRow = document.querySelector(`[data-type="cutting"][data-index="${index}"]`);
            if (optionRow) {
                optionRow.remove();
            }
        }

        function addPackagingOption() {
            const container = document.getElementById('packaging-options-container');
            const optionHtml = `
                <div class="row option-row" data-type="packaging" data-index="${packagingOptionIndex}">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('admin.option_name') }}</label>
                            <input type="text" name="options[packaging_${packagingOptionIndex}][name]" class="form-control" required data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                            <input type="hidden" name="options[packaging_${packagingOptionIndex}][type]" value="packaging">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('admin.additional_price') }}</label>
                            <input type="number" step="0.01" name="options[packaging_${packagingOptionIndex}][additional_price]" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('admin.is_default') }}</label>
                            <select name="options[packaging_${packagingOptionIndex}][is_default]" class="form-control" onchange="checkDefault(this, 'packaging')">
                                <option value="0">{{ __('admin.no') }}</option>
                                <option value="1">{{ __('admin.yes') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-block" onclick="removePackagingOption(${packagingOptionIndex})">
                                <i class="feather icon-trash"></i> 
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', optionHtml);
            packagingOptionIndex++;
        }

        function removePackagingOption(index) {
            const optionRow = document.querySelector(`[data-type="packaging"][data-index="${index}"]`);
            if (optionRow) {
                optionRow.remove();
            }
        }

        function checkDefault(select, type) {
            let containerId = '';
            if (type === 'weight') containerId = 'weight-options-container';
            if (type === 'cutting') containerId = 'cutting-options-container';
            if (type === 'packaging') containerId = 'packaging-options-container';
            if (select.value == "1") {
                document.querySelectorAll(`#${containerId} select`).forEach(function(s) {
                    if (s !== select) {
                        s.value = "0";
                    }
                });
            }
        }
    </script> --}}
@endsection
