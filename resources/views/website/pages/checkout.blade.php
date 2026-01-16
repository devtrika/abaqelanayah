@extends('website.layouts.app')

@section('title', __('site.cart') . ' - Lia')

@section('meta_description', __('site.cart'))
@section('content')
@php($addresses = $addresses ?? collect())
@php($paymentMethods = $paymentMethods ?? collect())
@php($giftFee = (float) (\App\Models\SiteSetting::where('key','gift_fee')->value('value') ?? 0))
@php($OrdinaryDeliveryFee = (float) (\App\Models\SiteSetting::where('key','ordinary_delivery_fee')->value('value') ?? 0))
@php($ScheculedDeliveryFee = (float) (\App\Models\SiteSetting::where('key','scheduled_delivery_fee')->value('value') ?? 0))

<form method="POST" action="{{ route('website.checkout.store') }}">
  @csrf

  <!-- Hidden fields populated from session data coming from cart page -->
  @php($checkoutData = session('checkout.temp', []))
  <input type="hidden" name="order_type" value="{{ $checkoutData['order_type'] ?? 'ordinary' }}" />
  <input type="hidden" name="address_id" value="{{ $checkoutData['address_id'] ?? '' }}" />
  <input type="hidden" name="latitude" value="{{ $checkoutData['latitude'] ?? $checkoutData['lat'] ?? '' }}" />
  <input type="hidden" name="longitude" value="{{ $checkoutData['longitude'] ?? $checkoutData['lng'] ?? '' }}" />
  <input type="hidden" name="branch_id" value="{{ $checkoutData['branch_id'] ?? '' }}" />
  <!-- Gift fields -->
  <input type="hidden" name="reciver_name" value="{{ $checkoutData['reciver_name'] ?? '' }}" />
  <input type="hidden" name="reciver_phone" value="{{ $checkoutData['reciver_phone'] ?? '' }}" />
  <input type="hidden" name="gift_city_id" value="{{ $checkoutData['gift_city_id'] ?? '' }}" />
  <input type="hidden" name="gift_districts_id" value="{{ $checkoutData['gift_districts_id'] ?? '' }}" />
  <input type="hidden" name="gift_address_name" value="{{ $checkoutData['gift_address_name'] ?? '' }}" />
  <input type="hidden" name="gift_latitude" value="{{ $checkoutData['gift_latitude'] ?? '' }}" />
  <input type="hidden" name="gift_longitude" value="{{ $checkoutData['gift_longitude'] ?? '' }}" />
  <input type="hidden" name="message" value="{{ $checkoutData['message'] ?? '' }}" />
  <input type="hidden" name="whatsapp" value="{{ $checkoutData['whatsapp'] ?? '' }}" />
  <input type="hidden" name="hide_sender" value="{{ $checkoutData['hide_sender'] ?? '' }}" />

  <!-- Carry-over ordinary address fields from cart (when no address_id) -->
  <input type="hidden" name="address_name" value="{{ $checkoutData['address_name'] ?? '' }}" />
  <input type="hidden" name="recipient_name" value="{{ $checkoutData['recipient_name'] ?? '' }}" />
  <input type="hidden" name="phone" value="{{ $checkoutData['phone'] ?? '' }}" />
  <input type="hidden" name="country_code" value="{{ $checkoutData['country_code'] ?? '' }}" />
  <input type="hidden" name="city_id" value="{{ $checkoutData['city_id'] ?? '' }}" />
  <input type="hidden" name="districts_id" value="{{ $checkoutData['districts_id'] ?? '' }}" />
  <input type="hidden" name="description" value="{{ $checkoutData['description'] ?? '' }}" />


  <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="index.html" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          <li class="breadcrumb-item">
            <a href="checkout.html" class="breadcrumb-link">
              {{ __('site.checkout_summary') }}
            </a>
          </li>
        </ul>
      </div>
    </section>
    <!-- End Breadcrumb -->

    <section class="page-content">
      <div class="container">
        <div class="cart-content">
          <div class="cart-main">
          <div class="cart-info">
              <h3 class="cart_info-title">{{ __('site.delivery_type') }}
                  
              </h3>
              <div class="cart_radio-list">
                <div class="cart_radio-item">
                  <label>
                    <input type="radio" name="delivery_type" value="immediate" checked data-rules="required" />
                    <span class="mark"> </span>
                    <span class="text"> {{ __('site.delivery_type_immediate') }}
                      <small style="color:#dc3545; margin-inline-start:6px;">(+ <span class="">{{ number_format($OrdinaryDeliveryFee, 2) }}</span>
                        <i class="curreny">
                          <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}" alt="sar" class="svg" />
                        </i>)
                      </small>
                    </span>
                  </label>
                </div>
                <div class="cart_radio-item">
                  <label>
                    <input type="radio" name="delivery_type" value="scheduled" data-rules="required" />
                    <span class="mark"> </span>
                    <span class="text"> {{ __('site.delivery_type_scheduled') }}
                      <small style="color:#dc3545; margin-inline-start:6px;">(+ <span class="">{{ number_format($ScheculedDeliveryFee, 2) }}</span>
                        <i class="curreny">
                          <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}" alt="sar" class="svg" />
                        </i>)
                      </small>
                    </span>
                  </label>
                </div>
              </div>
              <div class="delivery_type-content">
                <div class="delivery_type-item" id="immediate">
                  <h4 class="delivery_type-title">{{ __('site.last_pickup_deadline') }}</h4>
                  <p class="delivery_type-desc js-last-pickup">اليوم - {{ now()->format('Y/m/d') }} - 20:00</p>
                  <span class="delivery_type-hint">
                    {{ __('site.pickup_deadline_note') }}
                  </span>
                  <h4 class="delivery_type-title">{{ __('site.expected_delivery_duration') }}</h4>
                  <p class="delivery_type-desc js-expected-duration">3-4 ساعات</p>
                </div>
                <div
                  class="delivery_type-item"
                  id="schedule"
                  style="display: none"
                >
                  <div class="form-grid">
                    <div class="form-group">
                      <label for="date" class="form-label">
                        {{ __('site.choose_suitable_date') }}
                      </label>
                      <div class="date-content">
                        <span class="icon">
                          <i class="fal fa-calendar-alt"></i>
                        </span>
                        <input type="date" class="form-control" name="schedule_date" />
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="time" class="form-label">
                        {{ __('site.choose_suitable_time') }}
                      </label>
                      <div class="date-content">
                        <span class="icon">
                          <i class="fal fa-clock"></i>
                        </span>
                        <input type="time" class="form-control" name="schedule_time" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>


           <div class="cart-info">
              <h3 class="cart_info-title">{{ __('site.discount_coupon') }}</h3>
              <div class="coupon-content">
                <input
                  type="text"
                  name="coupon_code"
                  placeholder="{{ __('site.enter_coupon_code') }}"
                  class="coupon-input js-coupon-input"
                  value="{{ $cartData['coupon_code'] ?? '' }}"
                  dir="rtl"
                  {{ !empty($cartData['coupon_code']) ? 'disabled' : '' }}
                />
                <button type="button" class="coupon-btn js-apply-coupon" {{ !empty($cartData['coupon_code']) ? 'style=display:none;' : '' }}>{{ __('site.apply') }}</button>
                <button type="button" class="coupon-btn js-remove-coupon" style="display:{{ !empty($cartData['coupon_code']) ? 'flex' : 'none' }}; background-color: #dc3545;">{{ __('site.remove_coupon') }}</button>
              </div>
            </div>
            <div class="cart-info">
              <h3 class="cart_info-title">
                {{ __('site.current_wallet_balance') }}
                <span>
                  <span class="js-wallet-balance">{{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</span>
                  <i>
                    <img
                      loading="lazy"
                      src="{{ asset('website/images/icons/sar.svg') }}"
                      alt="sar"
                      class="svg"
                    />
                  </i>
                </span>
              </h3>
              <div class="coupon-content">
                <input
                  type="number"
                  name="wallet_amount"
                  placeholder="{{ __('site.enter_wallet_deduction_amount') }}"
                  class="coupon-input js-wallet-inp"
                  min="1"
                  step="1"
                  dir="rtl"
                  value="{{ $cartData['wallet_deduction'] > 0 ? $cartData['wallet_deduction'] : '' }}"
                  {{ $cartData['wallet_deduction'] > 0 ? 'disabled' : '' }}
                  style="direction: rtl";
                />
                <button type="button" class="coupon-btn js-apply-wallet" {{ $cartData['wallet_deduction'] > 0 ? 'style=display:none;' : '' }}>{{ __('site.apply') }}</button>
                <button type="button" class="coupon-btn js-remove-wallet" style="display:{{ $cartData['wallet_deduction'] > 0 ? 'flex' : 'none' }}; background-color: #dc3545;">{{ __('site.remove_deduction') }}</button>
              </div>
            </div>
                  <div class="cart-info">
              <h3 class="cart_info-title">{{ __('site.payment_method') }}</h3>
              <div class="cart_radio-list">
                @forelse($paymentMethods as $method)
                  <div class="cart_radio-item">
                    <label>
                      <input type="radio" name="payment_method_id" value="{{ $method->id }}" {{ $loop->first ? 'checked' : '' }} data-rules="required" />
                      <span class="mark"> </span>
                      <span class="text"> {{ $method->name }} </span>
                    </label>
                  </div>
                @empty
                  <p>{{ __('site.no_payment_methods_available') }}</p>
                @endforelse
              </div>
            </div>
          </div>
          <aside class="cart-side">
            <h2 class="cart-title">{{ __('site.total') }}</h2>
            <div class="cart-totals">
              <div class="total-item">
                <span class="title"> {{ __('site.subtotal_without_tax') }} </span>
                <strong class="value">
                  <span class="js-subtotal">{{ number_format($cartData['subtotal'] ?? 0, 2) }}</span>
                  <i class="curreny">
                    <img
                      loading="lazy"
                      src="{{ asset('website/images/icons/sar.svg') }}"
                      alt="sar"
                      class="svg"
                    />
                  </i>
                </strong>
              </div>
              <div class="total-item">
                <span class="title"> {{ __('site.coupon_discount', ['code' => $cartData['coupon_code'] ?? '']) }} </span>
                <strong class="value">
                  <span class="js-coupon">{{ number_format($cartData['coupon_value'] ?? 0, 2) }}</span>
                  <i class="curreny">
                    <img
                      loading="lazy"
                      src="{{ asset('website/images/icons/sar.svg') }}"
                      alt="sar"
                      class="svg"
                    />
                  </i>
                </strong>
              </div>
              <div class="total-item">
                <span class="title"> {{ __('site.subtotal_after_discount') }} </span>
                <strong class="value">
                  <span class="js-after-discount">{{ number_format(($cartData['amount_before_vat'] ?? 0), 2) }}</span>
                  <i class="curreny">
                    <img
                      loading="lazy"
                      src="{{ asset('website/images/icons/sar.svg') }}"
                      alt="sar"
                      class="svg"
                    />
                  </i>
                </strong>
              </div>
              <div class="total-item">
                <span class="title"> {{ __('site.delivery_fee') }} </span>
                <strong class="value">
                  <span class="js-delivery-fee">0.00</span>
                  <i class="curreny">
                    <img
                      loading="lazy"
                      src="{{ asset('website/images/icons/sar.svg') }}"
                      alt="sar"
                      class="svg"
                    />
                  </i>
                </strong>
              </div>
              <div class="total-item">
                <span class="title"> {{ __('site.gift_fee') }} </span>
                <strong class="value">
                  <span class="js-gift-fee">0.00</span>
                  <i class="curreny">
                    <img
                      loading="lazy"
                      src="{{ asset('website/images/icons/sar.svg') }}"
                      alt="sar"
                      class="svg"
                    />
                  </i>
                </strong>
              </div>
              <div class="total-item">
                <span class="title"> {{ __('site.due_without_tax') }} </span>
                <strong class="value">
                  <span class="js-before-vat">{{ number_format(($cartData['amount_before_vat'] ?? 0), 2) }}</span>
                  <i class="curreny">
                    <img
                      loading="lazy"
                      src="{{ asset('website/images/icons/sar.svg') }}"
                      alt="sar"
                      class="svg"
                    />
                  </i>
                </strong>
              </div>
              <div class="total-item">
                <span class="title"> {{ __('site.vat_amount_percent', ['percent' => 15]) }} </span>
                <strong class="value">
                  <span class="js-vat">{{ number_format($cartData['vat_amount'] ?? 0, 2) }}</span>
                  <i class="curreny">
                    <img
                      loading="lazy"
                      src="{{ asset('website/images/icons/sar.svg') }}"
                      alt="sar"
                      class="svg"
                    />
                  </i>
                </strong>
              </div>
              <div class="total-item">
                <span class="title"> {{ __('site.due_after_tax') }} </span>
                <strong class="value">
                  <span class="js-before-delivery">{{ number_format($cartData['total'] ?? 0, 2) }}</span>
                  <i class="curreny">
                    <img
                      loading="lazy"
                      src="{{ asset('website/images/icons/sar.svg') }}"
                      alt="sar"
                      class="svg"
                    />
                  </i>
                </strong>
              </div>
              <div class="total-item">
                <span class="title"> {{ __('site.wallet_deduction') }} </span>
                <strong class="value">
                  <span class="js-wallet">{{ number_format($cartData['wallet_deduction'] ?? 0, 2) }}</span>
                  <i class="curreny">
                    <img
                      loading="lazy"
                      src="{{ asset('website/images/icons/sar.svg') }}"  
                      alt="sar"
                      class="svg"
                    />
                  </i>
                </strong>
              </div>
            </div>
            <div class="cart-final_total">
              <span class="title">{{ __('site.total_due') }}</span>
              <strong class="value">
                <span class="js-final">{{ number_format(($cartData['total'] ?? 0), 2) }}</span>
                <i class="curreny">
                  <img
                    loading="lazy"
                    src="{{ asset('website/images/icons/sar.svg') }}"
                    alt="sar"
                    class="svg"
                  />
                </i>
              </strong>
            </div>
            <div class="cart-btns">
              <button type="submit" class="default-btn"> {{ __('site.complete_order') }} </button>
            </div>
          </aside>

        </div>
      </div>
    </section>
  </form>

    @include('website.pages.checkout._scripts')


@endsection
