@extends('admin.layout.master')

@section('content')
<section class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <!-- Left column: Order & Customer Info -->
                        <div class="col-12 col-md-6 mb-3">
                            <h5 class="mb-3"><i class="feather icon-file-text mr-1 text-info"></i> {{ __('admin.order_information') }}</h5>
                            <div class="mb-2">
                                <small class="text-muted">{{ __('admin.order_number') }}</small>
                                <div>{{ $orderrate->order?->order_number ?? __('admin.not_available') }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">{{ __('admin.order_date') }}</small>
                                <div>{{ $orderrate->order && $orderrate->order->created_at ? $orderrate->order->created_at->format('Y-m-d H:i') : __('admin.not_available') }}</div>
                            </div>

                            <hr />

                            <h5 class="mb-3"><i class="feather icon-user mr-1 text-primary"></i> {{ __('admin.customer_information') }}</h5>
                            <div class="mb-2">
                                <small class="text-muted">{{ __('admin.customer_name') }}</small>
                                <div>{{ $orderrate->user?->name ?? __('admin.not_available') }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">{{ __('admin.review_date') }}</small>
                                <div>{{ $orderrate->created_at ? $orderrate->created_at->format('Y-m-d H:i') : __('admin.not_available') }}</div>
                            </div>
                        </div>

                        <!-- Right column: Rating & Comment -->
                        <div class="col-12 col-md-6 mb-3">
                            <h5 class="mb-3"><i class="feather icon-star mr-1 text-warning"></i> {{ __('admin.rating') }}</h5>
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $orderrate->rating)
                                            <span class="text-warning h4">★</span>
                                        @else
                                            <span class="text-secondary h4">★</span>
                                        @endif
                                    @endfor
                                </div>
                                <div class="ml-3 font-weight-bold">{{ $orderrate->rating }}/5</div>
                            </div>

                            @if($orderrate->comment)
                            <h6 class="mb-2"><i class="feather icon-message-circle mr-1 text-secondary"></i> {{ __('admin.comment') }}</h6>
                            <div class="border rounded p-3 bg-light text-break">{{ $orderrate->comment }}</div>
                            @else
                            <div class="text-muted">{{ __('admin.no_comment_provided') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.orderrates.index') }}" class="btn btn-secondary">
                        <i class="feather icon-arrow-left mr-1"></i> {{ __('admin.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <style>
        /* Responsive tweaks */
        .card .h5, .card h5 { font-size: 1.05rem; }
        .card .h6, .card h6 { font-size: 0.95rem; }
        .text-break { word-break: break-word; }
        @media (max-width: 576px) {
            .card .h5 { font-size: 1rem; }
        }
    </style>
</section>
@endsection
