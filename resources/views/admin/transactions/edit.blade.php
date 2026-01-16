@extends('admin.layout.master')
{{-- extra css files --}}
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <style>
        .btn{
            margin-top:-2px;
        }
        .btn-block{
            width:fit-content;
        }
        /* hide inline validation helper messages inside this form (we use popup instead) */
        form.store .help-block,
        form.store .validation-errors,
        form.store .invalid-feedback,
        form.store .text-danger.validation-message,
        form.store small.text-danger,
        form.store .jqvError {
            display: none !important;
        }

        /* sweetalert2 tweaks for nicer look */
        .swal2-popup {
            font-size: 1rem;
            direction: rtl; /* keep RTL for Arabic if needed */
        }
        .swal2-html-container ul { padding-left: 1.2rem; text-align: left; }
        .swal2-confirm.btn-primary {
            background-color: #0069d9 !important;
            border-color: #0062cc !important;
            color: #fff !important;
        }
        /* highlight invalid inputs when popup shows */
        .is-invalid {
            border: 1px solid #dc3545 !important;
            box-shadow: 0 0 0 .15rem rgba(220,53,69,.15) !important;
        }
        .missing-item { cursor: pointer; padding: .35rem .25rem; border-radius: .25rem; }
        .missing-item:hover { background: rgba(0,0,0,0.03); }
    </style>
@endsection
{{-- extra css files --}}

@section('content')
<!-- // Basic multiple Column Form section start -->
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <form method="POST" action="{{route('admin.products.update' , ['id' => $product->id])}}" class="store form-horizontal" enctype="multipart/form-data" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="imgMontg col-12 text-center">
                                            <div class="dropBox">
                                                <div class="textCenter">
                                                   <div class="imagesUploadBlock">
                                <label class="uploadImg">
                                    <span><i class="feather icon-image"></i></span>
                                    <input type="file" accept="image/*" name="images[]" class="imageUploader" multiple>
                                </label>

                                <div class="existingImages mt-2" style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;">
                                        @foreach($product->getMedia('product-images') as $media)
                                            <div class="uploadedBlock existing" data-media-id="{{ $media->id }}" style="position:relative;display:inline-block;">
                                                <img src="{{ $media->getUrl() }}" alt="{{$product->name}}" style="max-width:150px;max-height:150px;object-fit:cover;" />
                                                <span class="remove-image" data-media-id="{{ $media->id }}" style="position:absolute;top:6px;right:6px;cursor:pointer;background:#fff;border-radius:50%;padding:4px;box-shadow:0 0 4px #ccc;display:flex;align-items:center;justify-content:center;">
                                                    <i class="feather icon-trash-2" style="color:#dc3545;font-size:20px;"></i>
                                                </span>
                                            </div>
                                        @endforeach
                                        <div id="newImagesPreview" style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;"></div>
                                </div>
                                <div id="deleted-media-inputs"></div>
                            </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    @php($isBranchManager = auth('admin')->check() && auth('admin')->user()->role_id == 2)
                                    @if($isBranchManager)
                                        <div class="col-12">
                                            <div class="alert alert-info" role="alert" style="margin-top:10px;">
                                                {{ __('admin.note') }}: سيتم تعديل كمية المنتج على مستوى الفرع فقط، ولن يتم تعديل بيانات المنتج الأساسية.
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="branch_id_select">{{ __('admin.branch') }}</label>
                                                <div class="controls">
                                                    @php($branches = isset($managerBranches) && $managerBranches ? $managerBranches : collect())
                                                    @if($branches->count() > 1)
                                                        <select id="branch_id_select" name="branch_id" class="select2 form-control">
                                                            @foreach($branches as $b)
                                                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @elseif($branches->count() === 1)
                                                        <input type="hidden" name="branch_id" id="branch_id_select" value="{{ $branches->first()->id }}">
                                                        <input type="text" class="form-control" value="{{ $branches->first()->name }}" disabled>
                                                    @else
                                                        <div class="text-danger small">لا يوجد فروع مرتبطة بك.</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="branch_qty_input">{{ __('admin.quantity') }}</label>
                                                <div class="controls">
                                                    <input type="number" min="0" name="qty" id="branch_qty_input" class="form-control" placeholder="{{ __('admin.quantity') }}" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-12">
                                        <div class="col-12">
                                            <ul class="nav nav-tabs mb-3">
                                                @foreach (languages() as $lang)
                                                    <li class="nav-item">
                                                        <a class="nav-link @if($loop->first) active @endif" data-toggle="pill" href="#first_{{$lang}}" aria-expanded="true">{{ __('admin.data') }} {{ $lang }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <div class="tab-content">
                                            @foreach (languages() as $lang)
                                                <div role="tabpanel" class="tab-pane fade @if($loop->first) show active @endif" id="first_{{$lang}}" aria-labelledby="first_{{$lang}}" aria-expanded="true">
                                                    <div class="col-md-12 col-12">
                                                        <div class="form-group">
                                                            <label for="first-name-column">
                                                                {{__('admin.product_name')}} {{ $lang }}
                                                                @if($lang == config('app.locale'))
                                                                    <span class="text-danger">*</span>
                                                                @endif
                                                            </label>
                                                            <div class="controls">
                                                                <input type="text" value="{{$product->getTranslations('name')[$lang] ?? ''}}"
                                                                    name="name[{{$lang}}]" class="form-control"
                                                                    placeholder="{{__('admin.product_name')}} {{ $lang }}"
                                                                    @if($lang == config('app.locale')) required data-validation-required-message="{{__('admin.this_field_is_required')}}" @endif>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 col-12">
                                                        <div class="form-group">
                                                            <label for="first-name-column">
                                                                {{__('admin.product_description')}} {{ $lang }}
                                                            </label>
                                                            <div class="controls">
                                                                <textarea name="description[{{$lang}}]" class="form-control"
                                                                    placeholder="{{__('admin.product_description')}}"
                                                                    rows="6">{{$product->getTranslations('description')[$lang] ?? ''}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                        <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="parent-category-column">{{ __('admin.brands') }} <span class="text-danger">*</span></label>
                                            <div class="controls">
                                                <select name="brand_id" class="select2 form-control" required data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                    <option value="">{{ __('admin.brands') }}</option>
                                                    @foreach ($brands as $cat)

                              <option value="{{ $cat->id }}"
                                @if(old('brand_id', $product->brand_id ?? null) == $cat->id) selected @endif>
                                                                {{ $cat->name }}
                            </option>                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                     {{-- Parent Category Selection --}}

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="parent-category-column">{{ __('admin.parent_category') }} <span class="text-danger">*</span></label>
                                            <div class="controls">
                                                <select id="parent_category_id" class="select2 form-control" required data-validation-required-message="{{ __('admin.this_field_is_required') }}" name="parent_category_id">
                                                    <option value="">{{ __('admin.select_parent_category') }}</option>
                                                    @foreach ($categories as $cat)
                                                        @if($cat->parent_id === null)
                                                            <option value="{{ $cat->id }}"
                                                                @if(old('parent_category_id', optional($product->category->parent)->id ?? null) == $cat->id) selected @endif>
                                                                {{ $cat->name }}
                                                            </option>
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
                                                            <option value="{{ $cat->id }}" data-parent-id="{{ $cat->parent_id }}" @if(old('category_id', isset($product->category_id) ? $product->category_id : null) == $cat->id) selected @endif>
                                                                {{ $cat->name }}
                                                            </option>
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
                                                    data-validation-required-message="{{ __('admin.this_field_is_required') }}"
                                                    value="{{ $product->base_price }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="quantity-column">{{ __('admin.quantity') }} <span class="text-danger">*</span></label>
                                            <div class="controls">
                                                <input type="number" min="0" name="quantity" class="form-control"
                                                    placeholder="{{ __('admin.quantity') }}" required
                                                    data-validation-required-message="{{ __('admin.this_field_is_required') }}"
                                                    value="{{ $product->quantity }}">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Discount Percentage --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="discount-column">{{ __('admin.discount_percentage') }}</label>
                                            <div class="controls">
                                                <input type="number" step="0.01" min="0" max="100" name="discount_percentage" class="form-control"
                                                    placeholder="{{ __('admin.discount_percentage') }}"
                                                    value="{{ $product->discount_percentage }}">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Status --}}

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="is-active-column">{{ __('admin.status') }}</label>
                                            <div class="controls">
                                                <select name="is_active" class="select2 form-control">
                                                    <option value="1" {{ $product->is_active ? 'selected' : '' }}>{{ __('admin.active') }}</option>
                                                    <option value="0" {{ !$product->is_active ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="is-refunded-column">{{ __('admin.is_refunded') }}</label>
                                            <div class="controls">
                                                <select name="is_refunded" class="select2 form-control">
                                                    <option value="0" {{ !$product->is_refunded ? 'selected' : '' }}>{{ __('admin.not_refunded') }}</option>
                                                    <option value="1" {{ $product->is_refunded ? 'selected' : '' }}>{{ __('admin.refunded') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-12 d-flex justify-content-center mt-3">
                                        <button type="submit"
                                            class="btn btn-primary mr-1 mb-1 submit_button">{{ __('admin.update') }}</button>
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
    </section>
@endsection

@section('js')
    <script src="{{ asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/forms/validation/form-validation.js') }}"></script>
    <script src="{{ asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js') }}"></script>

    {{-- show selected image script --}}
    @include('admin.shared.addImage')

    @include('admin.shared.categoryDependentJs')
    @include('admin.shared.submitEditForm')

    {{-- حذف صورة المنتج نهائياً عند التعديل --}}
    <script>
    $(document).on('click', '.remove-image', function() {
        var mediaId = $(this).data('media-id');
        var block = $(this).closest('.uploadedBlock');
        Swal.fire({
            title: 'تأكيد الحذف',
            text: 'هل أنت متأكد من حذف هذه الصورة نهائياً؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/products/delete-image/' + mediaId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if(res.success) {
                            block.remove();
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف',
                                text: 'تم حذف الصورة بنجاح',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: res.message || 'حدث خطأ أثناء الحذف'
                            });
                        }
                    },
                    error: function(xhr) {
                        let msg = 'حدث خطأ أثناء الحذف';
                        if(xhr.status === 403) msg = 'ليس لديك صلاحية الحذف';
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: msg
                        });
                    }
                });
            }
        });
    });

    const imageUploader = document.querySelector('.imageUploader');
    const newImagesPreview = document.getElementById('newImagesPreview');
    if(imageUploader && newImagesPreview) {
        imageUploader.addEventListener('change', function(e) {
            newImagesPreview.innerHTML = '';
            Array.from(e.target.files).forEach(file => {
                if(file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        const img = document.createElement('img');
                        img.src = ev.target.result;
                        img.style.maxWidth = '150px';
                        img.style.maxHeight = '150px';
                        img.style.objectFit = 'cover';
                        img.style.margin = '2px';
                        newImagesPreview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }
    </script>

    @php($isBranchManagerJs = auth('admin')->check() && auth('admin')->user()->role_id == 2)
    @if($isBranchManagerJs)
    <script>
        (function() {
            var $form = $('form.store');
            var branchQtys = @json(($branchQtys ?? collect())->toArray());

            // Disable all inputs except qty, branch_id, csrf/method, and submit button
            $form.find('input, select, textarea, button').each(function() {
                var $el = $(this);
                var name = $el.attr('name');
                if (name === '_token' || name === '_method' || name === 'qty' || name === 'branch_id') {
                    return; // keep enabled
                }
                if ($el.hasClass('submit_button')) {
                    return; // keep submit enabled
                }
                $el.prop('disabled', true);
            });

            // Hide image remove buttons and uploader interactions
            $('.remove-image').hide();
            $('.imageUploader').prop('disabled', true).closest('.uploadImg').css({ 'pointer-events': 'none', 'opacity': .5 });

            // Update submit button text
            $('.submit_button').text('حفظ كمية الفرع');

            function getSelectedBranchId() {
                var $select = $('#branch_id_select');
                return $select.val();
            }
            function setQtyForBranch(branchId) {
                var qty = 0;
                if (branchId && branchQtys && branchQtys[branchId] !== undefined) {
                    qty = branchQtys[branchId] || 0;
                }
                $('#branch_qty_input').val(qty);
            }

            var initialBranchId = getSelectedBranchId();
            setQtyForBranch(initialBranchId);

            $('#branch_id_select').on('change', function() {
                setQtyForBranch($(this).val());
            });
        })();
    </script>
    @endif

@endsection
