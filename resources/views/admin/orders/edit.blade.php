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
                    <h4 class="card-title">{{__('admin.update') . ' ' . __('admin.product')}}</h4>
                </div> --}}
                <div class="card-content">
                    <div class="card-body">
                        <form  method="POST" action="{{route('admin.products.update' , ['id' => $product->id])}}" class="store form-horizontal" enctype="multipart/form-data" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">

                                    {{-- to create languages tabs uncomment that --}}
                                    {{-- <div class="col-12">
                                        <div class="col-12">
                                            <ul class="nav nav-tabs  mb-3">
                                                    @foreach (languages() as $lang)
                                                        <li class="nav-item">
                                                            <a class="nav-link @if($loop->first) active @endif"  data-toggle="pill" href="#first_{{$lang}}" aria-expanded="true">{{  __('admin.data') }} {{ $lang }}</a>
                                                        </li>
                                                    @endforeach
                                            </ul>
                                        </div>  --}}

                                        <div class="col-12">
                                            <div class="imgMontg col-12 text-center">
                                                <div class="dropBox">
                                                    <div class="textCenter">
                                                        <div class="imagesUploadBlock">
                                                            <label class="uploadImg">
                                                                <span><i class="feather icon-image"></i></span>
                                                                <input type="file" accept="image/*" name="image" class="imageUploader">
                                                            </label>
                                                            <div class="uploadedBlock">
                                                                <img src="{{$product->image}}">
                                                                <button class="close"><i class="feather icon-x"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div class="tab-content">
                                                @foreach (languages() as $lang)
                                                    <div role="tabpanel" class="tab-pane fade @if($loop->first) show active @endif " id="first_{{$lang}}" aria-labelledby="first_{{$lang}}" aria-expanded="true">
                                                        <div class="col-md-12 col-12">
                                                            <div class="form-group">
                                                                <label for="first-name-column">{{__('admin.name')}} {{ $lang }}</label>
                                                                <div class="controls">
                                                                    <input type="text" value="{{$product->getTranslations('name')[$lang]??''}}" name="name[{{$lang}}]" class="form-control" placeholder="{{__('admin.write') . __('admin.name')}} {{ $lang }}" required data-validation-required-message="{{__('admin.this_field_is_required')}}" >
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div> --}}

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
                                                                    {{__('admin.title')}} {{ $lang }}
                                                                    @if($lang == config('app.locale'))
                                                                        <span class="text-danger">*</span>
                                                                    @endif
                                                                </label>
                                                                <div class="controls">
                                                                    <input type="text" value="{{$product->getTranslations('title')[$lang] ?? ''}}"
                                                                        name="title[{{$lang}}]" class="form-control"
                                                                        placeholder="{{__('admin.write_title')}} {{ $lang }}"
                                                                        @if($lang == config('app.locale')) required data-validation-required-message="{{__('admin.this_field_is_required')}}" @endif>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 col-12">
                                                            <div class="form-group">
                                                                <label for="first-name-column">
                                                                    {{__('admin.description')}} {{ $lang }}
                                                                    @if($lang == config('app.locale'))
                                                                        <span class="text-danger">*</span>
                                                                    @endif
                                                                </label>
                                                                <div class="controls">
                                                                    <textarea name="description[{{$lang}}]" class="form-control"
                                                                        placeholder="{{__('admin.write_description')}} {{ $lang }}"
                                                                        @if($lang == config('app.locale')) required data-validation-required-message="{{__('admin.this_field_is_required')}}" @endif
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
                                                <label for="price-column">{{ __('admin.price') }}</label>
                                                <div class="controls">
                                                    <input type="number" step="0.01" name="price" value="{{$product->price}}" class="form-control"
                                                        placeholder="{{ __('admin.price') }}" required
                                                        data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="discount-price-column">{{ __('admin.discount_price') }}</label>
                                                <div class="controls">
                                                    <input type="number" step="0.01" name="discount_price" value="{{$product->discount_price}}" class="form-control"
                                                        placeholder="{{ __('admin.discount_price') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="category-column">{{ __('admin.category') }}</label>
                                                <div class="controls">
                                                    <select name="category_id" class="select2 form-control" required
                                                        data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                        <option value="">{{ __('admin.select_category') }}</option>
                                                        @foreach (\App\Models\Category::where('is_active', 1)->get() as $category)
                                                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="stock-column">{{ __('admin.stock') }}</label>
                                                <div class="controls">
                                                    <input type="number" name="stock" value="{{$product->stock}}" class="form-control"
                                                        placeholder="{{ __('admin.stock') }}" required
                                                        data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-12">
                                            <div class="form-group">
                                                <label for="is-active-column">{{ __('admin.is_active') }}</label>
                                                <div class="controls">
                                                    <select name="is_active" class="select2 form-control">
                                                        <option value="1" {{ $product->is_active ? 'selected' : '' }}>{{ __('admin.active') }}</option>
                                                        <option value="0" {{ !$product->is_active ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div class="col-md-12 col-12">
                                            <div class="form-group">
                                                <label for="first-name-column">{{__('admin.Validity')}}</label>
                                                <div class="controls">
                                                    <select name="role_id" class="select2 form-control" required data-validation-required-message="{{__('admin.this_field_is_required')}}" >
                                                        <option value>{{__('admin.Select_the_validity')}}</option>
                                                        @foreach ($roles as $role)
                                                            <option {{$role->id == $product->role_id ? 'selected' : ''}} value="{{$role->id}}">{{$role->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div> --}}

                                    {{--  to create languages tabs uncomment that --}}
                                    {{-- </div> --}}

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