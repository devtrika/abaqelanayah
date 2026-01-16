
<div class="row">
    <!-- Product Information -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-info text-white py-3">
                <h5 class="card-title mb-0 text-white">
                    <i class="feather icon-package mr-2 text-white"></i>
                    {{ __('admin.product_information') }}
                </h5>
            </div>
            <div class="card-body p-3">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th>{{ __('admin.name') }}:</th>
                        <td>{{ $product->getTranslation('name', app()->getLocale()) }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.description') }}:</th>
                        <td>{{ $product->getTranslation('description', app()->getLocale()) ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.category') }}:</th>
                        <td>{{ $product->category->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.base_price') }}:</th>
                        <td>{{ number_format($product->base_price, 2) }} {{ __('admin.sar') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.discount') }}:</th>
                        <td>{{ $product->discount_percentage }}%</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.final_price') }}:</th>
                        <td>
                            <span class="badge badge-success">
                                {{ number_format($product->final_price, 2) }} {{ __('admin.sar') }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.status') }}:</th>
                        <td>
                            @if($product->is_active)
                                <span class="badge badge-success">{{ __('admin.active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('admin.inactive') }}</span>
                            @endif
                        </td>
                    </tr>
                         <tr>
                        <th>{{ __('admin.is_refunded') }}:</th>
                        <td>
                            @if($product->is_refunded)
                                <span class="badge badge-warning">{{ __('admin.refunded') }}</span>
                            @else
                                <span class="badge badge-success">{{ __('admin.not_refunded') }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Product Image -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100 text-center">
            <div class="card-header bg-info text-white py-3">
                <h5 class="card-title mb-0 text-white">
                    <i class="feather icon-image mr-2 text-white"></i>
                    {{ __('admin.product_image') }}
                </h5>
            </div>
            <div class="card-body">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                     class="img-fluid rounded shadow-sm" style="max-height:300px">
            </div>
        </div>
    </div>
</div>

<!-- Selected Item Options -->
@php
    $selectedOptions = collect();
    if(isset($item)) {
        if($item->weightOption) {
            $selectedOptions->push($item->weightOption);
        }
        if($item->cuttingOption) {
            $selectedOptions->push($item->cuttingOption);
        }
        if($item->packagingOption) {
            $selectedOptions->push($item->packagingOption);
        }
    }
@endphp

@if($selectedOptions->count())
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-info text-white py-3">
                <h5 class="card-title mb-0 text-white">
                    <i class="feather icon-list mr-2 text-white"></i>
                    {{ __('admin.selected_options') }}
                </h5>
            </div>
            <div class="card-body p-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('admin.type') }}</th>
                            <th>{{ __('admin.name') }}</th>
                            <th>{{ __('admin.additional_price') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedOptions as $option)
                        <tr>
                            <td>{{ __("admin." . $option->type) != "admin." . $option->type ? __("admin." . $option->type) : ucfirst($option->type) }}</td>
                            <td>{{ $option->name }}</td>
                            <td>{{ number_format($option->additional_price, 2) }} {{ __('admin.sar') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif
