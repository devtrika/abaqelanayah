@extends('website.layouts.app')

@section('title', __('site.myAccount') . ' - ' . __('site.orders'))

@section('meta_description', __('site.orders'))

@section('content')

    <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
        <div class="container">
            <ul class="breadcrumb-list">
                <li class="breadcrumb-item">
                    <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('website.orders') }}" class="breadcrumb-link"> {{ __('site.orders') }} </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('website.orders.show', $order->id) }}" class="breadcrumb-link">
                        {{ __('site.order_number') }} #{{ $order->order_number }}
                    </a>
                </li>
            </ul>
        </div>
    </section>
    <!-- End Breadcrumb -->

    <section class="page-content account-page">
        <div class="container">
            <div class="account-content">
                <div class="account-overlay"></div>
                <button class="account-trigger">
                    <i class="fal fa-user-gear"></i>
                </button>
                @include('website.pages.account.sidebar')
                <div class="account-main">
                    @php
                        use App\Enums\OrderStatus;
                        $effectiveStatus = $order->refund_status ?: $order->status;
                        $statusEnum = in_array($effectiveStatus, OrderStatus::values())
                            ? OrderStatus::from($effectiveStatus)
                            : null;
                        $statusLabel = $statusEnum ? $statusEnum->label() : __('admin.' . $effectiveStatus);
                        $statusColorName = $statusEnum ? $statusEnum->color() : 'info';
                        $statusHexMap = [
                            'warning' => '#f59e0b',
                            'info' => '#34b7ea',
                            'primary' => '#3b82f6',
                            'success' => '#22c55e',
                            'danger' => '#ef4444',
                            'secondary' => '#6b7280',
                        ];
                        $statusColorHex = $statusHexMap[$statusColorName] ?? '#34b7ea';
                    @endphp
                    @php
                        $problems = \App\Models\Problem::all();
                        $cancelReasons = \App\Models\CancelReason::all();
                    @endphp
                    <div class="account-header order-header">
                        <h2 class="account-title">طلب #{{ $order->order_number }}</h2>
                        <span class="order-status" id="orderStatus" style="--color: {{ $statusColorHex }}">
                            {{ $statusLabel }}
                        </span>
                        @if($order->refund_status)
                            @php
                                $refundEnum = in_array($order->refund_status, OrderStatus::values())
                                    ? OrderStatus::from($order->refund_status)
                                    : null;
                                $refundColorName = $refundEnum ? $refundEnum->color() : 'warning';
                                $refundHexMap = [
                                    'warning' => '#f59e0b',
                                    'info' => '#34b7ea',
                                    'primary' => '#3b82f6',
                                    'success' => '#22c55e',
                                    'danger' => '#ef4444',
                                    'secondary' => '#6b7280',
                                ];
                                $refundColorHex = $refundHexMap[$refundColorName] ?? '#f59e0b';
                            @endphp
                            <span class="order-status" style="--color: {{ $refundColorHex }}">
                                {{ __('admin.' . $order->refund_status) }}
                            </span>
                        @endif
                        <div class="order_header-buttons">
                            @if ($order->payment_status === 'pending' && !empty($order->payment_url))
                                <a href="{{ $order->payment_url }}" class="order_header-btn" target="_blank">
                                    {{ __('site.print_invoice') }}
                                </a>
                            @endif
                            <a href="{{ route('website.orders.invoice', $order->id) }}" class="order_header-btn">
                                {{ __('site.download') }} {{ __('site.invoice') }}
                            </a>
                        </div>
                        <span class="order-date"> {{ $order->created_at->format('Y/m/d - H:i') }} </span>
                    </div>
                    <div class="order-invoice">
                        <div class="order-products">
                            <div class="order_product-item order_product-head">
                                <span class="order_product-col"> {{ __('site.product') }} </span>
                                <span class="order_product-col"> {{ __('site.price') }} </span>
                                <span class="order_product-col"> {{ __('site.quantity') }} </span>
                                <span class="order_product-col"> {{ __('site.total') }} </span>
                                <span class="order_product-col"> {{ __('site.return') }} </span>
                            </div>
                            @foreach ($order->items as $item)
                                <div class="order_product-item">
                                    <div class="order_product-col order_product-info">
                                        <a href="#" class="order_product-img">
                                            <img loading="lazy"
                                                src="{{ $item->item_image ?? (optional($item->product)->image_url ?? asset('storage/images/default.png')) }}"
                                                alt="product" class="img-cover" />
                                        </a>
                                        <div>
                                            <h3 class="order_product-name">
                                                <a
                                                    href="#">{{ $item->item->name ?? (optional($item->product)->name ?? optional($item->service)->name) }}</a>
                                            </h3>
                                            <span class="order_product-hint">
                                                {{ __('site.refundable') }}:
                                                {{ optional($item->product)->is_refunded ? __('site.yes') : __('site.no') }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="order_product-col order_product-price">
                                        {{ number_format($item->product->final_price, 2) }}
                                        <i class="curreny">
                                            <img src="{{ asset('website/images/icons/sar.svg') }}" alt="sar"
                                                class="svg" />
                                        </i>
                                    </span>
                                    <span class="order_product-col order_product-quantity">
                                        {{ $item->quantity }}
                                    </span>
                                    <span class="order_product-col lg-col order_product-price">
                                        {{ number_format($item->total, 2) }}
                                        <i class="curreny">
                                            <img src="{{ asset('website/images/icons/sar.svg') }}" alt="sar"
                                                class="svg" />
                                        </i>
                                    </span>

                                    <span class="order_product-col lg-col order_product-price">
                                        @if($item->request_refund == 1)
                                            <span class="text-success">مسترجع</span>
                                        @elseif($order->status === 'delivered' && optional($item->product)->is_refunded == 1)
                                            <button 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#returnModal" 
                                                class="table-action js-return-btn"
                                                data-product-id="{{ $item->product_id }}"
                                                data-product-name="{{ $item->product->name ?? '' }}">
                                                <i class="fal fa-redo"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </span>

                                </div>
                            @endforeach
                        </div>
                        <div class="order-totals">
                            <div class="total-item">
                                <span class="title"> {{ __('site.subtotal_without_tax') }} </span>
                                <strong class="value">
                                    {{ number_format($order->subtotal, 2) }}
                                    <i class="curreny">
                                        <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}"
                                            alt="sar" class="svg" />
                                    </i>
                                </strong>
                            </div>
                            <div class="total-item">
                                <span class="title">
                                    {{ __('site.coupon_discount', ['code' => $order->coupon_code ?? '']) }} </span>
                                <strong class="value">
                                    {{ number_format($order->coupon_amount ?? ($order->discount_amount ?? 0), 2) }}
                                    <i class="curreny">
                                        <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}"
                                            alt="sar" class="svg" />
                                    </i>
                                </strong>
                            </div>
                            <div class="total-item">
                                <span class="title"> {{ __('site.subtotal_after_discount') }} </span>
                                <strong class="value">
                                    {{ number_format($order->subtotal - ($order->coupon_amount ?? ($order->discount_amount ?? 0)), 2) }}
                                    <i class="curreny">
                                        <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}"
                                            alt="sar" class="svg" />
                                    </i>
                                </strong>
                            </div>
                            <div class="total-item">
                                <span class="title"> {{ __('site.delivery_fee') }} </span>
                                <strong class="value">
                                    {{ number_format($order->delivery_fee ?? 0, 2) }}
                                    <i class="curreny">
                                        <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}"
                                            alt="sar" class="svg" />
                                    </i>
                                </strong>
                            </div>
                            @if(($order->gift_fee ?? 0) > 0)
                            <div class="total-item">
                                <span class="title"> {{ __('site.gift_fee') }} </span>
                                <strong class="value">
                                    {{ number_format($order->gift_fee, 2) }}
                                    <i class="curreny">
                                        <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}"
                                            alt="sar" class="svg" />
                                    </i>
                                </strong>
                            </div>
                            @endif
                            <div class="total-item">
                                <span class="title"> {{ __('site.due_without_tax') }} </span>
                                <strong class="value">
                                    {{ number_format($order->subtotal - ($order->coupon_amount ?? ($order->discount_amount ?? 0)) + ($order->delivery_fee ?? 0) + ($order->gift_fee ?? 0), 2) }}
                                    <i class="curreny">
                                        <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}"
                                            alt="sar" class="svg" />
                                    </i>
                                </strong>
                            </div>
                            <div class="total-item">
                                <span class="title">
                                    {{ __('site.vat_amount_percent', ['percent' => $order->vat_percent ?? 15]) }} </span>
                                <strong class="value">
                                    {{ number_format($order->vat_amount ?? 0, 2) }}
                                    <i class="curreny">
                                        <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}"
                                            alt="sar" class="svg" />
                                    </i>
                                </strong>
                            </div>
                            <div class="total-item">
                                <span class="title"> {{ __('site.due_after_tax') }} </span>
                                <strong class="value">
                                    {{ number_format($order->subtotal - ($order->coupon_amount ?? ($order->discount_amount ?? 0)) + ($order->delivery_fee ?? 0) + ($order->gift_fee ?? 0) + ($order->vat_amount ?? 0), 2) }}
                                    <i class="curreny">
                                        <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}"
                                            alt="sar" class="svg" />
                                    </i>
                                </strong>
                            </div>
                            <div class="total-item">
                                <span class="title"> {{ __('site.wallet_deduction') }} </span>
                                <strong class="value">
                                    {{ number_format($order->wallet_deduction ?? 0, 2) }}
                                    <i class="curreny">
                                        <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}"
                                            alt="sar" class="svg" />
                                    </i>
                                </strong>
                            </div>
                        </div>
                        <div class="order-final_total">
                            <span class="title"> {{ __('site.total_due') }} </span>
                            <strong class="value">
                                {{ number_format($order->total, 2) }}
                                <i class="curreny">
                                    <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}" alt="sar"
                                        class="svg" />
                                </i>
                            </strong>
                        </div>
                    </div>
                    <div class="order-information">
                        <h3 class="order_info-title">{{ __('site.order_information') }}</h3>
                        <ul class="order_info-list">
                            <li class="order_info-item">
                                <strong class="title"> {{ __('site.order_type') }} </strong>
                                <span class="value">
                                    @if ($order->original_order_id)
                                        {{ __('site.return') }}
                                    @elseif($order->order_type === 'gift')
                                        {{ __('site.gift') }}
                                    @else
                                        {{ __('site.ordinary') }}
                                    @endif
                                </span>
                            </li>
                            <li class="order_info-item">
                                <strong class="title"> {{ __('site.delivery_type') }} </strong>
                                <span class="value">
                                    {{ $order->delivery_type === 'immediate'
                                        ? __('site.delivery_type_immediate')
                                        : __('site.delivery_type_scheduled') }}
                                </span>
                            </li>

                            {{-- <li class="order_info-item">
                                <strong class="title"> {{ __('site.expected_preparation_duration') }} </strong>
                                <span class="value">
                                    {{ optional($order->branch)->expected_duration ? optional($order->branch)->expected_duration . ' ' . __('site.minute') : '-' }}
                                </span>
                            </li> --}}

                            
                        </ul>
                        <h3 class="order_info-title">{{ __('site.delivery_information') }}</h3>
                        <ul class="order_info-list">
                            <li class="order_info-item">
                                <strong class="title"> {{ __('site.address_name') }} </strong>
                                <span class="value">
                                    {{ $order->address?->address_name ?? ($order->gift_address_name ?? '-') }} </span>
                            </li>
                            <li class="order_info-item">
                                <strong class="title"> {{ __('site.recipient_name') }} </strong>
                                <span class="value">
                                    {{ $order->address?->recipient_name ?? ($order->recipient_name ?? ($order->reciver_name ?? '-')) }}
                                </span>
                            </li>
                            <li class="order_info-item">
                                <strong class="title"> {{ __('site.phone_number') }} </strong>
                                <span class="value">
                                    <a href="tel:{{ $order->address?->recipient_phone ?? ($order->recipient_phone ?? ($order->reciver_phone ?? '')) }}"
                                        class="en">{{ $order->address?->phone ?? ($order->recipient_phone ?? ($order->reciver_phone ?? '-')) }}</a>
                                </span>
                            </li>
                            <li class="order_info-item">
                                <strong class="title"> {{ __('site.city') }} </strong>
                                <span class="value">
                                    {{ optional(optional($order->address)->city)->name ?? (optional($order->giftCity)->name ?? (optional($order->city)->name ?? '-')) }}
                                </span>
                            </li>
                            <li class="order_info-item">
                                <strong class="title"> {{ __('site.district') }} </strong>
                                <span class="value">
                                    {{ optional($order->address?->district)->getTranslation('name', app()->getLocale()) ?? (optional($order->giftDistrict)->getTranslation('name', app()->getLocale()) ?? '-') }}
                                </span>
                            </li>
                            @if ($order->order_type === 'gift' && $order->hide_sender == 0)
                                <li class="order_info-item">
                                    <strong class="title"> {{ __('site.sender_name') }} </strong>
                                    <span class="value"> {{ $order->user?->name ?? '-' }} </span>
                                </li>
                            @endif
                            <li class="order_info-item">
                                <strong class="title"> {{ __('site.address_description') }} </strong>
                                <span class="value">
                                    {{ $order->address?->description ?? ($order->message ?? '-') }}
                                </span>
                            </li>
                            <li class="order_info-item">
                                <strong class="title"> {{ __('site.location_on_map') }} </strong>
                                <span class="value">
                                    @php
                                        $lat = null;
                                        $lng = null;

                                        // Prefer gift coordinates when available
                                        if (isset($order->gift_latitude) && isset($order->gift_longitude)) {
                                            $lat = $order->gift_latitude;
                                            $lng = $order->gift_longitude;
                                        }
                                        // Fallback to address relation coordinates
                                        elseif (
                                            optional($order->address)->latitude &&
                                            optional($order->address)->longitude
                                        ) {
                                            $lat = optional($order->address)->latitude;
                                            $lng = optional($order->address)->longitude;
                                        }
                                        // Fallback to order-level stored address coordinates
                                        elseif (isset($order->address_latitude) && isset($order->address_longitude)) {
                                            $lat = $order->address_latitude;
                                            $lng = $order->address_longitude;
                                        }
                                    @endphp

                                    @if ($lat !== null && $lng !== null)
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $lat }},{{ $lng }}"
                                            target="_blank" class="link">
                                            {{ __('site.view_on_map') }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </span>
                            </li>
                        </ul>
                        <h3 class="order_info-title">{{ __('site.payment_information') }}</h3>
                        <ul class="order_info-list">
                            <li class="order_info-item">
                                <strong class="title"> {{ __('site.payment_method') }} </strong>
                                <span class="value"> {{ $order->paymentMethod?->name ?? '-' }} </span>
                            </li>
                            <li class="order_info-item">
                                <strong class="title"> {{ __('site.payment_date_time') }} </strong>
                                <span class="value"> {{ optional($order->payment_date)->format('Y/m/d - H:i') ?? '-' }}
                                </span>
                            </li>
                            <li class="order_info-item">
                                <strong class="title"> {{ __('site.invoice_link') }} </strong>
                                <span class="value">
                                    <a href="{{ route('website.orders.invoice', $order->id) }}"
                                        class="link">{{ __('site.download') }}</a>
                                </span>
                            </li>
                            @if ($order->payment_status === 'pending' && !empty($order->payment_url))
                                <li class="order_info-item">
                                    <strong class="title"> {{ __('site.pay_now') }} </strong>
                                    <span class="value">
                                        <a href="{{ $order->payment_url }}" target="_blank"
                                            class="btn btn-sm btn-primary">{{ __('site.pay_now') }}</a>
                                    </span>
                                </li>
                            @endif
                        </ul>

                        @if ($order->status === 'cancelled' || $order->status === 'request_cancel')
                            <h3 class="order_info-title">{{ __('site.cancellation_reason') }}</h3>
                            <p class="order_info-desc">
                                {{ $order->cancelReason ? $order->cancelReason->getTranslation('reason', app()->getLocale()) : $order->notes ?? '-' }}
                            </p>
                        @endif

                        @if ($order->status === 'problem')
                            <h3 class="order_info-title">{{ __('site.problem_reason') }}</h3>
                            <p class="order_info-desc">
                                {{ $order->problem?->getTranslation('problem', app()->getLocale()) ?? '-' }}
                                @if (!empty($order->notes))
                                    <br>{{ $order->notes }}
                                @endif
                            </p>
                        @endif

                        @if (in_array($order->status, ['request_refund', 'request_rejected', 'refunded']))
                            <h3 class="order_info-title">{{ __('site.refund_reason') }}</h3>
                            <p class="order_info-desc">
                                {{ $order->refundReason?->getTranslation('reason', app()->getLocale()) ?? ($order->refund_reason_text ?? '-') }}
                                @if (!empty($order->notes))
                                    <br>{{ $order->notes }}
                                @endif
                            </p>
                        @endif
                    </div>
                    <div class="order-buttons">
                        @if (!in_array($order->status, ['cancelled', 'problem', 'request_cancel']))
                            <button data-bs-toggle="modal" data-bs-target="#reportModal" class="order-btn">
                                {{ __('site.report_problem') }}
                            </button>
                        @endif
                        @if (in_array($order->status, ['processing', 'pending', 'new']))
                            <button data-bs-toggle="modal" data-bs-target="#cancelModal" class="order-btn">
                                {{ __('site.cancel_order') }}
                            </button>
                        @endif
                        @if (!empty($order->whatsapp))
                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $order->whatsapp) }}?text={{ urlencode(__('site.order_number') . ': ' . ($order->order_number ?? $order->id)) }}"
                                target="_blank" class="order-btn" rel="noopener noreferrer">
                                {{ __('site.send_via_whatsapp') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Start Report Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form action="{{ route('website.orders.report', $order->id) }}" method="POST" class="modal-form"
                    id="reportForm">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="modal-close" data-bs-dismiss="modal">
                            <i class="far fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h2 class="modal-head">{{ __('site.specify_problem_reason') }}</h2>
                        <div class="form-group">
                            <div class="form-radios">
                                @foreach ($problems as $problem)
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="problem_id" value="{{ $problem->id }}" />
                                            <span class="mark"> </span>
                                            <span class="text">
                                                {{ $problem->getTranslation('problem', app()->getLocale()) }} </span>
                                        </label>
                                    </div>
                                @endforeach
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="problem_id" data-id="other" />
                                        <span class="mark"> </span>
                                        <span class="text"> {{ __('site.other_reason') }} </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group other-reason d-none">
                            <textarea name="notes" placeholder="{{ __('site.enter_problem_description_min_chars', ['min' => 10]) }}"
                                class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="form-modal-btns">
                            <button type="submit" class="modal-btn">{{ __('site.send') }}</button>
                            <button type="button" data-bs-dismiss="modal" class="modal-btn modal_second-btn">
                                {{ __('site.cancel') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Report Modal -->

    <!-- Start Cancel Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form action="{{ route('website.orders.cancel', $order->id) }}" method="POST" class="modal-form"
                    id="cancelForm">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="modal-close" data-bs-dismiss="modal">
                            <i class="far fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h2 class="modal-head">{{ __('site.specify_cancellation_reason') }}</h2>
                        <div class="form-group">
                            <div class="form-radios">
                                @foreach ($cancelReasons as $reason)
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="cancel_reason_id"
                                                value="{{ $reason->id }}" />
                                            <span class="mark"> </span>
                                            <span class="text">
                                                {{ $reason->getTranslation('reason', app()->getLocale()) }} </span>
                                        </label>
                                    </div>
                                @endforeach
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="cancel_reason_id" data-id="other" />
                                        <span class="mark"> </span>
                                        <span class="text"> {{ __('site.other_reason') }} </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group other-reason d-none">
                            <textarea name="notes" placeholder="{{ __('site.enter_cancellation_reason_min_chars', ['min' => 10]) }}"
                                class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="form-modal-btns">
                            <button type="submit" class="modal-btn">{{ __('site.send') }}</button>
                            <button type="button" data-bs-dismiss="modal" class="modal-btn modal_second-btn">
                                {{ __('site.cancel') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Start Return Modal -->
    <div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form action="{{ route('website.orders.refund') }}" method="POST" class="modal-form" id="refundForm">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}" />
                    <input type="hidden" name="items[]" id="refundItemProductId" />
                    <div class="modal-header">
                        <button type="button" class="modal-close" data-bs-dismiss="modal">
                            <i class="far fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h2 class="modal-head">{{ __('site.specify_refund_reason') }}</h2>
                        <div class="form-group">
                            <div class="form-radios">
                                @foreach(($refundReasons ?? []) as $reason)
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="refund_reason_id" value="{{ $reason->id }}" />
                                            <span class="mark"> </span>
                                            <span class="text">{{ $reason->getTranslation('reason', app()->getLocale()) }}</span>
                                        </label>
                                    </div>
                                @endforeach
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="refund_reason_id" data-id="other" />
                                        <span class="mark"> </span>
                                        <span class="text"> {{ __('site.other_reason') }} </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group other-reason d-none">
                            <textarea name="notes" placeholder="{{ __('site.enter_refund_description_min_chars', ['min' => 10]) }}" class="form-control"></textarea>
                        </div>
                      
                    </div>
                    <div class="modal-footer">
                        <div class="form-modal-btns">
                            <button type="submit" class="modal-btn">{{ __('site.send') }}</button>
                            <button type="button" data-bs-dismiss="modal" class="modal-btn modal_second-btn">
                                {{ __('site.cancel') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Return Modal -->

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Report modal: toggle other reason visibility
            const reportForm = document.getElementById('reportForm');
            const reportOtherGroup = document.querySelector('#reportModal .other-reason');
            const reportRadios = document.querySelectorAll('#reportModal input[name="problem_id"]');

            function updateReportOtherVisibility() {
                const selected = document.querySelector('#reportModal input[name="problem_id"]:checked');
                if (selected && selected.dataset.id === 'other') {
                    reportOtherGroup && reportOtherGroup.classList.remove('d-none');
                } else {
                    reportOtherGroup && reportOtherGroup.classList.add('d-none');
                }
            }

            reportRadios.forEach(r => r.addEventListener('change', updateReportOtherVisibility));
            const reportModalEl = document.getElementById('reportModal');
            if (reportModalEl) {
                reportModalEl.addEventListener('shown.bs.modal', updateReportOtherVisibility);
            }

            if (reportForm) {
                reportForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const selected = reportForm.querySelector('input[name="problem_id"]:checked');
                    const isOther = selected && selected.dataset.id === 'other';
                    const formData = new FormData(reportForm);
                    if (isOther) {
                        formData.delete('problem_id');
                        const notesEl = reportForm.querySelector('textarea[name="notes"]');
                        const notes = notesEl ? notesEl.value.trim() : '';
                        if (!notes || notes.length < 10) {
                            alert(
                                '{{ __('site.please_enter_description_min_chars', ['min' => 10]) }}'
                                );
                            return;
                        }
                    } else {
                        formData.delete('notes');
                    }
                    try {
                        const res = await fetch(reportForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        const json = await res.json();
                        if (json.success) {
                            window.location.reload();
                        } else {
                            alert(json.message || '{{ __('site.failed_to_send_report') }}');
                        }
                    } catch (err) {
                        alert('{{ __('site.submission_error') }}');
                    }
                });
            }

            // Cancel modal: toggle other reason visibility
            const cancelForm = document.getElementById('cancelForm');
            const cancelOtherGroup = document.querySelector('#cancelModal .other-reason');
            const cancelRadios = document.querySelectorAll('#cancelModal input[name="cancel_reason_id"]');

            function updateCancelOtherVisibility() {
                const selected = document.querySelector('#cancelModal input[name="cancel_reason_id"]:checked');
                if (selected && selected.dataset.id === 'other') {
                    cancelOtherGroup && cancelOtherGroup.classList.remove('d-none');
                } else {
                    cancelOtherGroup && cancelOtherGroup.classList.add('d-none');
                }
            }

            cancelRadios.forEach(r => r.addEventListener('change', updateCancelOtherVisibility));
            const cancelModalEl = document.getElementById('cancelModal');
            if (cancelModalEl) {
                cancelModalEl.addEventListener('shown.bs.modal', updateCancelOtherVisibility);
            }

            if (cancelForm) {
                cancelForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const selected = cancelForm.querySelector('input[name="cancel_reason_id"]:checked');
                    const isOther = selected && selected.dataset.id === 'other';
                    const formData = new FormData(cancelForm);
                    if (isOther) {
                        formData.delete('cancel_reason_id');
                        const notesEl = cancelForm.querySelector('textarea[name="notes"]');
                        const notes = notesEl ? notesEl.value.trim() : '';
                        if (!notes || notes.length < 10) {
                            alert(
                                '{{ __('site.please_enter_cancellation_reason_min_chars', ['min' => 10]) }}'
                                );
                            return;
                        }
                    } else {
                        formData.delete('notes');
                    }
                    try {
                        const res = await fetch(cancelForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        const json = await res.json();
                        if (json.success) {
                            window.location.reload();
                        } else {
                            alert(json.message || '{{ __('site.failed_to_send_request') }}');
                        }
                    } catch (err) {
                        alert('{{ __('site.submission_error') }}');
                    }
                });
            }

            // Refund modal: set product id and toggle other reason
            const refundForm = document.getElementById('refundForm');
            const refundOtherGroup = document.querySelector('#returnModal .other-reason');
            const refundRadios = document.querySelectorAll('#returnModal input[name="refund_reason_id"]');
            const refundItemInput = document.getElementById('refundItemProductId');

            // When clicking return button, set product id into hidden input
            document.querySelectorAll('.js-return-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const pid = btn.getAttribute('data-product-id');
                    if (refundItemInput) refundItemInput.value = pid || '';
                });
            });

            function updateRefundOtherVisibility() {
                const selected = document.querySelector('#returnModal input[name="refund_reason_id"]:checked');
                if (selected && selected.dataset.id === 'other') {
                    refundOtherGroup && refundOtherGroup.classList.remove('d-none');
                } else {
                    refundOtherGroup && refundOtherGroup.classList.add('d-none');
                }
            }

            refundRadios.forEach(r => r.addEventListener('change', updateRefundOtherVisibility));
            const refundModalEl = document.getElementById('returnModal');
            if (refundModalEl) {
                refundModalEl.addEventListener('shown.bs.modal', updateRefundOtherVisibility);
            }

            if (refundForm) {
                refundForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const selected = refundForm.querySelector('input[name="refund_reason_id"]:checked');
                    const isOther = selected && selected.dataset.id === 'other';
                    const formData = new FormData(refundForm);
                    if (isOther) {
                        formData.delete('refund_reason_id');
                        const notesEl = refundForm.querySelector('textarea[name="notes"]');
                        const notes = notesEl ? notesEl.value.trim() : '';
                        if (!notes || notes.length < 10) {
                            alert(
                                '{{ __('site.please_enter_description_min_chars', ['min' => 10]) }}'
                            );
                            return;
                        }
                    }
                    try {
                        const res = await fetch(refundForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        const json = await res.json();
                        if (json.success) {
                            window.location.reload();
                        } else {
                            alert(json.message || '{{ __('site.failed_to_send_request') }}');
                        }
                    } catch (err) {
                        alert('{{ __('site.submission_error') }}');
                    }
                });
            }


            function getCookie(name) {
                const m = document.cookie.match(new RegExp('(^|; )' + name + '=([^;]*)'));
                return m ? decodeURIComponent(m[2]) : null;
            }
        });
    </script>
@endpush
