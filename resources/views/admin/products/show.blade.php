@extends('admin.layout.master')

@section('content')
<!-- // Basic multiple Column Form section start -->
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <form class="show form-horizontal">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        @include('admin.shared.mediaPicker', [
                                            'existing' => $product->getMedia('product-images'),
                                            'allowUpload' => false,
                                            'multiple' => false,
                                            'alt' => $product->name,
                                            'fallback' => $product->getImageUrlAttribute()
                                        ])
                                    </div>

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
                                                            <label for="first-name-column">{{__('admin.product_name')}} {{ $lang }}</label>
                                                            <div class="controls">
                                                                <input type="text" value="{{$product->getTranslations('name')[$lang] ?? ''}}" name="name[{{$lang}}]" class="form-control" disabled>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 col-12">
                                                        <div class="form-group">
                                                            <label for="first-name-column">{{__('admin.product_description')}} {{ $lang }}</label>
                                                            <div class="controls">
                                                                <textarea name="description[{{$lang}}]" class="form-control" rows="6" disabled>{{$product->getTranslations('description')[$lang] ?? ''}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>


                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="parent-category-column">{{ __('admin.parent_category') }}</label>
                                            <div class="controls">
                                                <input type="text" value="{{ $product->category && $product->category->parent ? $product->category->parent->name : __('admin.no_parent_category') }}" class="form-control" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="parent-category-column">{{ __('admin.brands') }}</label>
                                            <div class="controls">
                                                <input type="text" value="{{ $product->brand ? $product->brand->name : __('admin.no_brand') }}" class="form-control" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="category-column">{{ __('admin.product_category') }}</label>
                                            <div class="controls">
                                                <input type="text" value="{{ $product->category ? $product->category->name : __('admin.no_category') }}" class="form-control" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="base-price-column">{{ __('admin.base_price') }}</label>
                                            <div class="controls">
                                                <input type="number" step="0.01" name="base_price" value="{{$product->base_price}}" class="form-control" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="quantity-column">{{ __('admin.quantity') }}</label>
                                            <div class="controls">
                                                <input type="number" min="0" name="quantity" value="{{$product->quantity}}" class="form-control" disabled>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="discount-column">{{ __('admin.discount_percentage') }}</label>
                                            <div class="controls">
                                                <input type="number" step="0.01" name="discount_percentage" value="{{$product->discount_percentage ?? 0}}" class="form-control" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="final-price-column">{{ __('admin.final_price') }}</label>
                                            <div class="controls">
                                                <input type="number" step="0.01" value="{{$product->final_price}}" class="form-control" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">

                                        <div class="form-group">
                                            <label for="status-column">{{ __('admin.status') }}</label>
                                            <div class="controls">
                                                <input type="text" value="{{ $product->is_active ? __('admin.active') : __('admin.inactive') }}" class="form-control" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="is-refunded-column">{{ __('admin.is_refunded') }}</label>
                                            <div class="controls">
                                                <input type="text" value="{{ $product->is_refunded ? __('admin.refunded') : __('admin.not_refunded') }}" class="form-control" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Product Options --}}
                                    {{-- @if($product->options->count() > 0)
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">{{ __('admin.product_options') }}</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('admin.option_name') }}</th>
                                                                    <th>{{ __('admin.option_type') }}</th>
                                                                    <th>{{ __('admin.additional_price') }}</th>
                                                                    <th>{{ __('admin.is_default') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($product->options as $option)
                                                                    <tr>
                                                                        <td>{{ $option->name }}</td>
                                                                        <td>
                                                                            @switch($option->type)
                                                                                @case('weight')
                                                                                    {{ __('admin.weight') }}
                                                                                    @break
                                                                                @case('cutting')
                                                                                    {{ __('admin.cutting') }}
                                                                                    @break
                                                                                @case('packaging')
                                                                                    {{ __('admin.packaging') }}
                                                                                    @break
                                                                                @default
                                                                                    {{ $option->type }}
                                                                            @endswitch
                                                                        </td>
                                                                        <td>{{ number_format($option->additional_price, 2) }}</td>
                                                                        <td>
                                                                            @if($option->is_default)
                                                                                <span class="badge badge-success">{{ __('admin.yes') }}</span>
                                                                            @else
                                                                                <span class="badge badge-secondary">{{ __('admin.no') }}</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif --}}

                                    <div class="col-12 d-flex justify-content-center mt-3">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary mr-1 mb-1">{{ __('admin.edit') }}</a>
                                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-warning mr-1 mb-1">{{ __('admin.back') }}</a>
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
    <script>
        $('.show input').attr('disabled' , true)
        $('.show textarea').attr('disabled' , true)
        $('.show select').attr('disabled' , true)
    </script>
@endsection
