@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
@endsection

@section('content')
<!-- Provider Details Section -->
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card mb-2">
                <div class="card-body text-center">
                    <h5 class="card-title">{{ __('admin.provider_suborders_total_sum') }}</h5>
                    <h3 class="card-text">
                        {{ number_format($user->provider->providerSubOrders->sum('total'), 2) }} {{ __('admin.currency') }}
                    </h3>
                </div>
            </div>
           
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('admin.provider_details') }} - {{ $user->name }}</h4>
                    <div class="card-header-toolbar d-flex align-items-center">
                        @if($user->provider)
                            @switch($user->provider->status)
                                @case('in_review')
                                    <span class="badge badge-info mr-2">{{ __('admin.in_review') }}</span>
                                    @break
                                @case('pending')
                                    <span class="badge badge-warning mr-2">{{ __('admin.pending') }}</span>
                                    @break
                                @case('accepted')
                                    <span class="badge badge-success mr-2">{{ __('admin.accepted') }}</span>
                                    @break
                                @case('rejected')
                                    <span class="badge badge-danger mr-2">{{ __('admin.rejected') }}</span>
                                    @break
                                @case('blocked')
                                    <span class="badge badge-dark mr-2">{{ __('admin.blocked') }}</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary mr-2">{{ $user->provider->status }}</span>
                            @endswitch
                        @endif

                        <a href="{{ route('admin.providers.index') }}" class="btn btn-primary btn-sm mr-1">
                            <i class="feather icon-arrow-left"></i> {{ __('admin.back') }}
                        </a>

                        @if($user->provider && $user->provider->status === 'in_review')
                            <button class="btn btn-success btn-sm mr-1 approve-provider" data-id="{{ $user->id }}">
                                <i class="feather icon-check"></i> {{ __('admin.accept') }}
                            </button>
                            <button class="btn btn-danger btn-sm reject-provider" data-id="{{ $user->id }}">
                                <i class="feather icon-x"></i> {{ __('admin.reject') }}
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="basic-info-tab" data-toggle="tab" href="#basic-info" aria-controls="basic-info" role="tab" aria-selected="true">
                                        <i class="feather icon-user"></i> {{ __('admin.basic_information') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="provider-info-tab" data-toggle="tab" href="#provider-info" aria-controls="provider-info" role="tab" aria-selected="false">
                                        <i class="feather icon-briefcase"></i> {{ __('admin.provider_information') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents" aria-controls="documents" role="tab" aria-selected="false">
                                        <i class="feather icon-file-text"></i> {{ __('admin.documents') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="services-tab" data-toggle="tab" href="#services" aria-controls="services" role="tab" aria-selected="false">
                                        <i class="feather icon-list"></i> {{ __('admin.services') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="products-tab" data-toggle="tab" href="#products" aria-controls="products" role="tab" aria-selected="false">
                                        <i class="feather icon-package"></i> {{ __('admin.products') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="orders-tab" data-toggle="tab" href="#orders" aria-controls="orders" role="tab" aria-selected="false">
                                        <i class="feather icon-shopping-cart"></i> {{ __('admin.orders') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="salon-images-tab" data-toggle="tab" href="#salon-images" aria-controls="salon-images" role="tab" aria-selected="false">
                                        <i class="feather icon-image"></i> {{ __('admin.salon_images') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="working-hours-tab" data-toggle="tab" href="#working-hours" aria-controls="working-hours" role="tab" aria-selected="false">
                                        <i class="feather icon-clock"></i> {{ __('admin.working_hours') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="withdraw-request-tab" data-toggle="tab" href="#withdraw-request" aria-controls="working-hours" role="tab" aria-selected="false">
                                        <i class="feather icon-clock"></i> {{ __('admin.withdraw_request') }}
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <!-- Basic Information Tab -->
                                <div class="tab-pane active" id="basic-info" aria-labelledby="basic-info-tab" role="tabpanel">
                                    <div class="row mt-2">
                                        <div class="col-md-3 text-center">
                                            <div class="avatar avatar-xl">
                                                @if($user->getFirstMediaUrl('profile'))
                                                    <img src="{{ $user->getFirstMediaUrl('profile') }}" alt="{{ $user->name }}" class="round">
                                                @else
                                                    <img src="{{ asset('admin/app-assets/images/portrait/small/avatar-s-11.jpg') }}" alt="{{ $user->name }}" class="round">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>{{ __('admin.name') }}:</strong></td>
                                                    <td>{{ $user->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('admin.email') }}:</strong></td>
                                                    <td>{{ $user->email }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('admin.phone') }}:</strong></td>
                                                    <td>{{ $user->full_phone }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('admin.gender') }}:</strong></td>
                                                    <td>{{ $user->gender ? __('admin.' . $user->gender) : __('admin.not_set') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('admin.city') }}:</strong></td>
                                                    <td>{{ $user->city->name ?? __('admin.not_set') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('admin.region') }}:</strong></td>
                                                    <td>{{ $user->region->name ?? __('admin.not_set') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('admin.status') }}:</strong></td>
                                                    <td>
                                                        @if($user->provider)
                                                        @switch($user->provider->status)
                                                            @case('in_review')
                                                                <span class="badge badge-info mr-2">{{ __('admin.in_review') }}</span>
                                                                @break
                                                            @case('pending')
                                                                <span class="badge badge-warning mr-2">{{ __('admin.pending') }}</span>
                                                                @break
                                                            @case('accepted')
                                                                <span class="badge badge-success mr-2">{{ __('admin.accepted') }}</span>
                                                                @break
                                                            @case('rejected')
                                                                <span class="badge badge-danger mr-2">{{ __('admin.rejected') }}</span>
                                                                @break
                                                            @case('blocked')
                                                                <span class="badge badge-dark mr-2">{{ __('admin.blocked') }}</span>
                                                                @break
                                                            @default
                                                                <span class="badge badge-secondary mr-2">{{ $user->provider->status }}</span>
                                                        @endswitch
                                                    @endif                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('admin.created_at') }}:</strong></td>
                                                    <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Provider Information Tab -->
                                <div class="tab-pane" id="provider-info" aria-labelledby="provider-info-tab" role="tabpanel">
                                    @if($user->provider)
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td><strong>{{ __('admin.commercial_name') }}:</strong></td>
                                                        <td>{{ $user->provider->commercial_name ?? __('admin.not_set') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('admin.commercial_register_no') }}:</strong></td>
                                                        <td>{{ $user->provider->commercial_register_no ?? __('admin.not_set') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('admin.institution_name') }}:</strong></td>
                                                        <td>{{ $user->provider->institution_name ?? __('admin.not_set') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('admin.salon_type') }}:</strong></td>
                                                        <td>{{ $user->provider->salon_type ? __('admin.' . $user->provider->salon_type) : __('admin.not_set') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('admin.nationality') }}:</strong></td>
                                                        <td>
                                                            {{ $user->provider->nationality ? __('admin.' . $user->provider->nationality) : __('admin.not_set') }}
                                                        </td>
                                                                                                            </tr>
                                                    <tr>
                                                        <td><strong>{{ __('admin.sponsor_name') }}:</strong></td>
                                                        <td>{{ $user->provider->sponsor_name ?? __('admin.not_set') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('admin.sponsor_phone') }}:</strong></td>
                                                        <td>{{ $user->provider->sponsor_phone ?? __('admin.not_set') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td><strong>{{ __('admin.is_mobile') }}:</strong></td>
                                                        <td>
                                                            @if($user->provider->is_mobile)
                                                                <span class="badge badge-success">{{ __('admin.yes') }}</span>
                                                            @else
                                                                <span class="badge badge-secondary">{{ __('admin.no') }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('admin.mobile_service_fee') }}:</strong></td>
                                                        <td>{{ $user->provider->mobile_service_fee ?? __('admin.not_set') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('admin.in_home') }}:</strong></td>
                                                        <td>
                                                            @if($user->provider->in_home)
                                                                <span class="badge badge-success">{{ __('admin.yes') }}</span>
                                                            @else
                                                                <span class="badge badge-secondary">{{ __('admin.no') }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('admin.in_salon') }}:</strong></td>
                                                        <td>
                                                            @if($user->provider->in_salon)
                                                                <span class="badge badge-success">{{ __('admin.yes') }}</span>
                                                            @else
                                                                <span class="badge badge-secondary">{{ __('admin.no') }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('admin.home_fees') }}:</strong></td>
                                                        <td>{{ $user->provider->home_fees ?? __('admin.not_set') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('admin.cuurent_worth_balance') }}:</strong></td>
                                                        <td>{{ $user->provider->wallet_balance ?? 0 }} {{ __('admin.currency') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('admin.withdrawable_balance') }}:</strong></td>
                                                        <td>{{ $user->provider->withdrawable_balance ?? 0 }} {{ __('admin.currency') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        @if($user->provider->description)
                                            <div class="row mt-2">
                                                <div class="col-12">
                                                    <h5>{{ __('admin.description') }}</h5>
                                                    <p class="card-text">{{ $user->provider->description }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if($user->provider->rejection_reason)
                                            <div class="row mt-2">
                                                <div class="col-12">
                                                    <div class="alert alert-danger">
                                                        <h5>{{ __('admin.rejection_reason') }}</h5>
                                                        <p class="mb-0">{{ $user->provider->rejection_reason }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-warning">
                                            {{ __('admin.no_provider_information') }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Salon Images Tab -->
                                <div class="tab-pane" id="salon-images" aria-labelledby="salon-images-tab" role="tabpanel">
                                    @if($user->provider)
                                        <div class="row mt-2">
                                            
                                            <!-- Salon Images Section -->
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header bg-light-primary">
                                                        <h5>{{ __('admin.salon_images') }}</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        @if($user->provider->salon_images_urls && $user->provider->salon_images_urls->count() > 0)
                                                            <div class="row">
                                                                @foreach($user->provider->salon_images_urls as $imageUrl)
                                                                    <div class="col-md-6 mb-3">
                                                                        <img src="{{ $imageUrl }}" alt="{{ __('admin.salon_image') }}" class="img-fluid rounded" style="max-height: 150px; width: 100%; object-fit: cover;">
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <p class="text-muted text-center">{{ __('admin.no_salon_images_uploaded') }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            {{ __('admin.no_provider_information') }}
                                        </div>
                                    @endif
                                </div>
                                <!-- Documents Tab -->
                                <div class="tab-pane" id="documents" aria-labelledby="documents-tab" role="tabpanel">
                                    @if($user->provider)
                                        <div class="row mt-2">
                                            <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5>{{ __('admin.logo') }}</h5>
                                                    </div>
                                                    <div class="card-body text-center">
                                                        @if($user->provider->getFirstMediaUrl('logo'))
                                                            <img src="{{ $user->provider->getFirstMediaUrl('logo') }}" alt="{{ __('admin.logo') }}" class="img-fluid" style="max-height: 200px;">
                                                        @else
                                                            <p class="text-muted">{{ __('admin.no_image_uploaded') }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5>{{ __('admin.commercial_register_image') }}</h5>
                                                    </div>
                                                    <div class="card-body text-center">
                                                        @if($user->provider->getFirstMediaUrl('commercial_register_image'))
                                                            <img src="{{ $user->provider->getFirstMediaUrl('commercial_register_image') }}" alt="{{ __('admin.commercial_register_image') }}" class="img-fluid" style="max-height: 200px;">
                                                        @else
                                                            <p class="text-muted">{{ __('admin.no_image_uploaded') }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5>{{ __('admin.residence_image') }}</h5>
                                                    </div>
                                                    <div class="card-body text-center">
                                                        @if($user->provider->getFirstMediaUrl('residence_image'))
                                                            <img src="{{ $user->provider->getFirstMediaUrl('residence_image') }}" alt="{{ __('admin.residence_image') }}" class="img-fluid" style="max-height: 200px;">
                                                        @else
                                                            <p class="text-muted">{{ __('admin.no_image_uploaded') }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Salon Images -->
                                        @if($user->provider->getMedia('salon_images')->count() > 0)
                                            <div class="row mt-2">
                                                <div class="col-12">
                                                    <h5>{{ __('admin.salon_images') }}</h5>
                                                    <div class="row">
                                                        @foreach($user->provider->getMedia('salon_images') as $image)
                                                            <div class="col-md-3 mb-2">
                                                                <img src="{{ $image->getUrl() }}" alt="{{ __('admin.salon_image') }}" class="img-fluid rounded">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-warning">
                                            {{ __('admin.no_provider_information') }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Services Tab -->
                                <div class="tab-pane" id="services" aria-labelledby="services-tab" role="tabpanel">
                                    @if($user->provider && $user->provider->services && $user->provider->services->count() > 0)
                                        <div class="table-responsive mt-2">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('admin.name') }}</th>
                                                        <th>{{ __('admin.category') }}</th>
                                                        <th>{{ __('admin.price') }}</th>
                                                        <th>{{ __('admin.service_duration') }}</th>
                                                        <th>{{ __('admin.status') }}</th>
                                                        <th>{{ __('admin.created_at') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->provider->services as $service)
                                                        <tr>

                                                            <td>{{ $service->name }}</td>
                                                            <td>{{ $service->category->name ?? __('admin.not_set') }}</td>
                                                            <td>{{ $service->price }} {{ __('admin.currency') }}</td>
                                                            <td>{{ $service->duration }} {{ __('admin.minutes') }}</td>
                                                            <td>
                                                                @if($service->is_active)
                                                                    <span class="badge badge-success">{{ __('admin.active') }}</span>
                                                                @else
                                                                    <span class="badge badge-secondary">{{ __('admin.inactive') }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $service->created_at->format('Y-m-d') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info mt-2">
                                            <i class="feather icon-info"></i> {{ __('admin.no_services_found') }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Products Tab -->
                                <div class="tab-pane" id="products" aria-labelledby="products-tab" role="tabpanel">
                                    @if($user->provider && $user->provider->products && $user->provider->products->count() > 0)
                                        <div class="table-responsive mt-2">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('admin.image') }}</th>
                                                        <th>{{ __('admin.name') }}</th>
                                                        <th>{{ __('admin.category') }}</th>
                                                        <th>{{ __('admin.price') }}</th>
                                                        <th>{{ __('admin.stock_quantity') }}</th>
                                                        <th>{{ __('admin.status') }}</th>
                                                        <th>{{ __('admin.created_at') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->provider->products as $product)
                                                        <tr>
                                                            <td>
                                                                @if($product->getFirstMediaUrl('image'))
                                                                    <img src="{{ $product->getFirstMediaUrl('image') }}" alt="{{ $product->name }}" class="rounded" width="50" height="50">
                                                                @else
                                                                    <div class="avatar avatar-sm bg-light-secondary">
                                                                        <i class="feather icon-package"></i>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td>{{ $product->name }}</td>
                                                            <td>{{ $product->category->name ?? __('admin.not_set') }}</td>
                                                            <td>{{ $product->price }} {{ __('admin.currency') }}</td>
                                                            <td>
                                                                @if($product->quantity > 0)
                                                                    <span class="badge badge-success">{{ $product->quantity }}</span>
                                                                @else
                                                                    <span class="badge badge-danger">{{ __('admin.out_of_stock') }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($product->is_active)
                                                                    <span class="badge badge-success">{{ __('admin.active') }}</span>
                                                                @else
                                                                    <span class="badge badge-secondary">{{ __('admin.inactive') }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $product->created_at->format('Y-m-d') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info mt-2">
                                            <i class="feather icon-info"></i> {{ __('admin.no_products_found') }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Orders Tab -->
                                <div class="tab-pane" id="orders" aria-labelledby="orders-tab" role="tabpanel">
                                    @if($user->provider && $user->provider->providerSubOrders && $user->provider->providerSubOrders->count() > 0)
                                        <div class="table-responsive mt-2">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('admin.sub_order_number') }}</th>
                                                        <th>{{ __('admin.client') }}</th>
                                                        <th>{{ __('admin.total_amount') }}</th>
                                                        <th>{{ __('admin.status') }}</th>
                                                        <th>{{ __('admin.created_at') }}</th>
                                                        <th>{{ __('admin.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->provider->providerSubOrders->take(10) as $subOrder)
                                                        <tr>
                                                            <td>{{ $subOrder->sub_order_number }}</td>
                                                            <td>{{ $subOrder->order->user->name ?? __('admin.not_set') }}</td>
                                                            <td>{{ $subOrder->total }} {{ __('admin.currency') }}</td>
                                                            <td>
                                                                @switch($subOrder->status)
                                                                    @case('pending_payment')
                                                                        <span class="badge badge-warning">{{ __('admin.pending_payment') }}</span>
                                                                        @break
                                                                    @case('processing')
                                                                        <span class="badge badge-info">{{ __('admin.processing') }}</span>
                                                                        @break
                                                                    @case('confirmed')
                                                                        <span class="badge badge-primary">{{ __('admin.confirmed') }}</span>
                                                                        @break
                                                                    @case('completed')
                                                                        <span class="badge badge-success">{{ __('admin.completed') }}</span>
                                                                        @break
                                                                    @case('cancelled')
                                                                        <span class="badge badge-danger">{{ __('admin.cancelled') }}</span>
                                                                        @break
                                                                    @default
                                                                        <span class="badge badge-secondary">{{ $subOrder->status }}</span>
                                                                @endswitch
                                                            </td>
                                                            <td>{{ $subOrder->created_at->format('Y-m-d H:i') }}</td>
                                                            <td>
                                                                <a href="{{ route('admin.orders.show', $subOrder->order_id) }}" class="btn btn-sm btn-primary">
                                                                    <i class="feather icon-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if($user->provider->providerSubOrders->count() > 10)
                                            <div class="text-center mt-2">
                                                <a href="{{ route('admin.orders.index', ['provider_id' => $user->provider->id]) }}" class="btn btn-outline-primary">
                                                    {{ __('admin.view_all_orders') }}
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-info mt-2">
                                            <i class="feather icon-info"></i> {{ __('admin.no_orders_found') }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Working Hours Tab -->
                                <div class="tab-pane" id="working-hours" aria-labelledby="working-hours-tab" role="tabpanel">
                                    @if($user->provider && $user->provider->workingHours && $user->provider->workingHours->count() > 0)
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ __('admin.day') }}</th>
                                                                <th>{{ __('admin.status') }}</th>
                                                                <th>{{ __('admin.start_time') }}</th>
                                                                <th>{{ __('admin.end_time') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $days = [
                                                                    'sunday' => __('admin.sunday'),
                                                                    'monday' => __('admin.monday'),
                                                                    'tuesday' => __('admin.tuesday'),
                                                                    'wednesday' => __('admin.wednesday'),
                                                                    'thursday' => __('admin.thursday'),
                                                                    'friday' => __('admin.friday'),
                                                                    'saturday' => __('admin.saturday')
                                                                ];
                                                            @endphp
                                                            @foreach($days as $dayKey => $dayName)
                                                                @php
                                                                    $workingHour = $user->provider->workingHours->where('day', $dayKey)->first();
                                                                @endphp
                                                                <tr>
                                                                    <td><strong>{{ $dayName }}</strong></td>
                                                                    <td>
                                                                        @if($workingHour && $workingHour->is_working)
                                                                            <span class="badge badge-success">{{ __('admin.open') }}</span>
                                                                        @else
                                                                            <span class="badge badge-danger">{{ __('admin.closed') }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($workingHour && $workingHour->is_working)
                                                                            {{ $workingHour->start_time ?? __('admin.not_set') }}
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($workingHour && $workingHour->is_working)
                                                                            {{ $workingHour->end_time ?? __('admin.not_set') }}
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                    @else
                                        <div class="alert alert-info mt-2">
                                            <i class="feather icon-info"></i> {{ __('admin.no_working_hours_found') }}
                                        </div>
                                    @endif
                                </div>

                                 <div class="tab-pane" id="withdraw-request" aria-labelledby="withdraw-request-tab" role="tabpanel">
                                    @if($user->provider && $user->provider->withdrawRequests && $user->provider->withdrawRequests->count() > 0)
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>{{ __('admin.order_number') }}</th>
                                                                <th>{{ __('admin.provider') }}</th>
                                                                <th>{{ __('admin.phone') }}</th>
                                                                <th>{{ __('admin.bank_account') }}</th>
                                                                <th>{{ __('admin.amount') }}</th>
                                                                <th>{{ __('admin.created_at') }}</th>
                                                                <th>{{ __('admin.status') }}</th>
                                                                <th>{{ __('admin.image') }}</th>
                                                    
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($user->provider->withdrawRequests as $index => $withdrawRequest)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>
                                                                        <span class="badge badge-info">{{ $withdrawRequest->number }}</span>
                                                                    </td>
                                                                    <td>
                                                                        @if($withdrawRequest->provider)
                                                                            <div>
                                                                                <strong>{{ $withdrawRequest->provider->commercial_name }}</strong><br>
                                                                                <small class="text-muted">{{ $withdrawRequest->provider->user->phone ?? '-' }}</small>
                                                                            </div>
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $withdrawRequest->provider->user->phone ?? '-' }}</td>
                                                                    <td>
                                                                        @if($withdrawRequest->provider && $withdrawRequest->provider->bankAccount)
                                                                            <div>
                                                                                <strong>{{ $withdrawRequest->provider->bankAccount->bank_name }}</strong><br>
                                                                                <small>{{ $withdrawRequest->provider->bankAccount->account_number }}</small><br>
                                                                                <small>{{ $withdrawRequest->provider->bankAccount->iban }}</small>
                                                                            </div>
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ number_format($withdrawRequest->amount, 2) }} {{ __('admin.sar') }}</td>
                                                                    <td>
                                                                        <div>{{ $withdrawRequest->created_at->format('d/m/Y') }}</div>
                                                                        <small class="text-muted">{{ $withdrawRequest->created_at->format('H:i') }}</small>
                                                                    </td>
                                                                    <td>
                                                                        @php
                                                                            $statusClass = [
                                                                                'pending' => 'badge-warning',
                                                                                'accepted' => 'badge-success',
                                                                                'rejected' => 'badge-danger',
                                                                            ][$withdrawRequest->status] ?? 'badge-secondary';
                                                                        @endphp
                                                                        <span class="badge {{ $statusClass }}">{{ __('admin.' . $withdrawRequest->status) }}</span>
                                                                    </td>
                                                                    <td>
                                                                        @if($withdrawRequest->status === 'accepted')
                                                                            @php
                                                                                $imageUrl = $withdrawRequest->getFirstMediaUrl('withdraw_requests');
                                                                            @endphp
                                                                            @if($imageUrl)
                                                                                <a href="{{ $imageUrl }}" target="_blank" title="{{ __('admin.image') }}">
                                                                                    <i class="fa fa-image fa-lg"></i>
                                                                                </a>
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                    @else
                                        <div class="alert alert-info mt-2 text-center">
                                            <i class="feather icon-info"></i> {{ __('admin.no_data_found') }}
                                        </div>
                                    @endif
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
    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script>
        $(document).ready(function(){
            // Handle provider approval
            $(document).on('click','.approve-provider',function(e){
                e.preventDefault();
                let providerId = $(this).data('id');

                Swal.fire({
                    title: '{{ __('admin.are_you_sure') }}',
                    text: '{{ __('admin.approve_provider_request') }}',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __('admin.approve_request') }}',
                    cancelButtonText: '{{ __('admin.cancel') }}'
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: '{{ url('admin/providers') }}/' + providerId + '/approve',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: '{{ __('admin.success') }}',
                                    text: response.message,
                                    type: 'success',
                                    confirmButtonText: '{{ __('admin.close') }}'
                                });
                                setTimeout(function(){
                                    window.location.reload()
                                }, 1000);
                            },
                            error: function(xhr, status, error) {
                                var errorMessage = xhr.responseJSON?.message || '{{ __('admin.error_occurred') }}';
                                Swal.fire({
                                    title: '{{ __('admin.error') }}',
                                    text: errorMessage,
                                    type: 'error',
                                    confirmButtonText: '{{ __('admin.close') }}'
                                });
                            }
                        });
                    }
                });
            });

            // Handle provider rejection
            $(document).on('click','.reject-provider',function(e){
                e.preventDefault();
                let providerId = $(this).data('id');

                Swal.fire({
                    title: '{{ __('admin.rejection_reason') }}',
                    input: 'textarea',
                    inputPlaceholder: '{{ __('admin.enter_rejection_reason') }}',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ __('admin.reject_request') }}',
                    cancelButtonText: '{{ __('admin.cancel') }}',
                    inputValidator: function(value) {
                        if (!value) {
                            return '{{ __('admin.reason_required') }}'
                        }
                    }
                }).then(function(result) {
                    if (result.value) {
                        var rejectionReason = result.value;
                        $.ajax({
                            url: '{{ url('admin/providers') }}/' + providerId + '/reject',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                rejection_reason: rejectionReason
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: '{{ __('admin.success') }}',
                                    text: response.message,
                                    type: 'success',
                                    confirmButtonText: '{{ __('admin.close') }}'
                                });
                                setTimeout(function(){
                                    window.location.reload()
                                }, 1000);
                            },
                            error: function(xhr, status, error) {
                                var errorMessage = xhr.responseJSON?.message || '{{ __('admin.error_occurred') }}';
                                Swal.fire({
                                    title: '{{ __('admin.error') }}',
                                    text: errorMessage,
                                    type: 'error',
                                    confirmButtonText: '{{ __('admin.close') }}'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
