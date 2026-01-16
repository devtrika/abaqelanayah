@extends('website.layouts.app')

@section('title', 'حسابي - تفاصيل المرتجع')

@section('meta_description', 'تفاصيل المرتجع')

@section('content')

   <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ route('website.home') }}" class="breadcrumb-link"> الرئيسية </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('website.refunds.index') }}" class="breadcrumb-link">
              المرتجعات
            </a>
          </li>
          <li class="breadcrumb-item">
            <span class="breadcrumb-link">
              طلب #{{ $order->order_number ?? $order->refund_number ?? $order->id }}
            </span>
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
            <div class="account-header order-header">
              <h2 class="account-title">طلب #{{ $order->order_number ?? $order->refund_number ?? $order->id }}</h2>
              @php
                $statusColors = [
                  'request_refund' => '#f5a623',
                  'new' => '#f5a623',
                  'confirmed' => '#f5a623',
                  'out-for-delivery' => '#f5a623',
                  'delivered' => '#66a61b',
                  'refunded' => '#66a61b',
                  'request_rejected' => '#ed1d24',
                  'cancelled' => '#ed1d24',
                  'pending' => '#999'
                ];
                $displayStatus = $order->refund_status ?? $order->status;
                $color = $statusColors[$displayStatus] ?? '#999';
              @endphp
              <span class="order-status" style="--color: {{ $color }}">
                {{ __('admin.' . $displayStatus) }}
              </span>
              <div class="order_header-buttons">
                <a href="{{ route('website.orders.invoice', $order->id) }}" class="order_header-btn" download>
                  طباعة الفاتورة
                </a>
              </div>
              <span class="order-date"> {{ optional($order->created_at)->format('Y/m/d - H:i') }} </span>
            </div>
            <div class="order-invoice">
              <div class="order-products">
                <div
                  class="order_product-item return_product-item order_product-head"
                >
                  <span class="order_product-col"> المنتج </span>
                  <span class="order_product-col"> السعر </span>
                  <span class="order_product-col">
                    المدة التي يمكن الارجاع خلالها
                  </span>
                </div>
                @php $refundWindowDays = (int) config('orders.refund_window_days', 7); @endphp
                @forelse($order->items as $orderItem)
                  @php
                    $product = $orderItem->product ?? ($orderItem->isProduct() ? $orderItem->item : null);
                  @endphp
                  <div class="order_product-item return_product-item">
                    <div class="order_product-col order_product-info">
                      <a href="#" class="order_product-img">
                        <img
                          loading="lazy"
                          src="{{ $product?->image_url }}"
                          alt="product"
                          class="img-cover"
                        />
                      </a>
                      <div>
                        <h3 class="order_product-name">
                          <a href="#">
                            {{ $product ? $product->getTranslation('name', app()->getLocale()) : '-' }}
                          </a>
                        </h3>
                      </div>
                    </div>
                    <span class="order_product-col order_product-quantity">
                      {{ number_format($orderItem->price, 2) }} {{ __('admin.sar') }}
                    </span>
                    <span class="order_product-col order_product-duration">
                      {{ $refundWindowDays }} يوم
                    </span>
                  </div>
                @empty
                  <div class="order_product-item return_product-item">
                    <div class="order_product-col">لا توجد عناصر للمرتجع</div>
                  </div>
                @endforelse
              </div>
            </div>
            <div class="order-information">
              <h3 class="order_info-title">معلومات الطلب</h3>
              <ul class="order_info-list">
                <li class="order_info-item">
                  <strong class="title"> سبب الارجاع </strong>
                  <span class="value"> {{ $order->refundReason?->getTranslation('reason', app()->getLocale()) ?? $order->refund_reason_text ?? '-' }} </span>
                </li>
                <li class="order_info-item">
                  <strong class="title"> الصور المرفقة </strong>
                  @php
                    $singleUrl = $order->getFirstMediaUrl('refund_image');
                    $images = $order->getMedia('refund_images');
                  @endphp
                  <span class="value">
                    @if($singleUrl)
                      <a href="{{ $singleUrl }}" class="link" target="_blank" download>تحميل</a>@if($images && $images->count()) ، @endif
                    @endif
                    @if($images && $images->count())
                      @foreach($images as $img)
                        <a href="{{ $img->getUrl() }}" class="link" target="_blank" download>تحميل</a>@if(!$loop->last) ، @endif
                      @endforeach
                    @endif
                    @if(!$singleUrl && (!$images || !$images->count()))
                      -
                    @endif
                  </span>
                </li>
              </ul>
              <h3 class="order_info-title">معلومات التوصيل</h3>
              <ul class="order_info-list">
                <li class="order_info-item">
                  <strong class="title"> العنوان </strong>
                  <span class="value"> {{ $order->address?->address_name ?? $order->gift_address_name ?? '-' }} </span>
                </li>
                <li class="order_info-item">
                  <strong class="title"> اسم المستلم </strong>
                  <span class="value"> {{ $order->address?->recipient_name ?? $order->reciver_name ?? $order->user?->name ?? '-' }} </span>
                </li>
                <li class="order_info-item">
                  <strong class="title"> رقم الجوال </strong>
                  <span class="value"> {{ $order->address?->phone ?? $order->reciver_phone ?? $order->user?->phone ?? '-' }} </span>
                </li>
                <li class="order_info-item">
                  <strong class="title"> المدينة </strong>
                  <span class="value"> {{ optional(optional($order->address)->city)->name ?? optional($order->city)->name ?? '-' }} </span>
                </li>
                <li class="order_info-item">
                  <strong class="title"> الحي </strong>
                  <span class="value"> {{ optional(optional($order->address)->district)->getTranslation('name', app()->getLocale()) ?? '-' }} </span>
                </li>
                <li class="order_info-item">
                  <strong class="title"> وصف العنوان </strong>
                  <span class="value"> {{ $order->address?->description ?? $order->message ?? '-' }} </span>
                </li>
                <li class="order_info-item">
                  <strong class="title"> الموقع على الخريطة </strong>
                  @php
                    $lat = $order->address?->latitude ?? $order->gift_latitude ?? $order->address_latitude ?? null;
                    $lng = $order->address?->longitude ?? $order->gift_longitude ?? $order->address_longitude ?? null;
                  @endphp
                  <span class="value">
                    @if($lat && $lng)
                      <a href="https://www.google.com/maps?q={{ $lat }},{{ $lng }}" target="_blank" class="link">
                        عرض على الخريطة
                      </a>
                    @else
                      -
                    @endif
                  </span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

@endsection