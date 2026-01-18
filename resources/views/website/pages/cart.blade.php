@extends('website.layouts.app')

@section('title', __('site.cart') . ' - Lia')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<style>
    /* Fix z-index issue: Select2 dropdown should appear above intlTelInput */
    .select2-container--open {
        z-index: 99999 !important;
    }

    .select2-dropdown {
        z-index: 99999 !important;
    }

    .select2-container {
        z-index: 99999 !important;
    }

    /* Ensure intlTelInput has much lower z-index */
    .iti {
        z-index: 1 !important;
        position: relative !important;
    }

    .iti__country-list {
        z-index: 9998 !important;
    }

    /* Make sure form-group with phone doesn't create stacking context issues */
    .form-group {
        position: relative;
        z-index: auto;
    }

    /* Specifically target the phone input container */
    .form-group:has(input[intlTelInput]) {
        z-index: 1 !important;
    }

    /* Force districts dropdown to be on top */
    .js-district + .select2-container,
    .js-city + .select2-container,
    .js-gift-district + .select2-container,
    .js-gift-city + .select2-container {
        z-index: 99999 !important;
    }
</style>
@endpush


@section('meta_description', __('site.cart'))
@section('content')
@php($giftFee = (float) (\App\Models\SiteSetting::where('key','gift_fee')->value('value') ?? 0))

<!-- Start Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <ul class="breadcrumb-list">
            <li class="breadcrumb-item">
                <a href="index.html" class="breadcrumb-link"> {{ __('site.home') }} </a>
            </li>
            <li class="breadcrumb-item">
                <a href="cart.html" class="breadcrumb-link"> {{ __('site.cart') }} </a>
            </li>
        </ul>
    </div>
</section>
<!-- End Breadcrumb -->

<section class="page-content">
    <div class="container">
        @if(empty($cartData['items']) || count($cartData['items']) === 0)
        {{-- Empty Cart State --}}
        <div class="empty-state" style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 80px; color: #e0e0e0; margin-bottom: 20px;">
                <i class="fal fa-shopping-cart"></i>
            </div>
            <h2 style="font-size: 24px; color: #3F132C; margin-bottom: 15px;">{{ __('site.no_cart_items') }}</h2>
            <p style="font-size: 16px; color: #808080; margin-bottom: 30px;">{{ __('site.cart_empty_message') }}</p>
            <a href="{{ route('website.home') }}" class="default-btn">
                <i class="fal fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                {{ __('site.continue_shopping') }}
            </a>
        </div>
        @else
        <div class="cart-content">
            <div class="cart-main">
                <div class="cart-info">
                    <h3 class="cart_info-title">{{ __('site.order_type') }}</h3>
                    <div class="request-type" style="display: flex; flex-direction: column; gap: 10px;">
                        <div class="radio">
                            <label>
                                <input type="radio" name="order_type" value="ordinary" class="js-order-type" checked />
                                <span class="mark"> </span>
                                <span class="text"> {{ __('site.ordinary') }} </span>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="order_type" value="gift" class="js-order-type" />
                                <span class="mark"> </span>
                                <span class="text">{{ __('site.gift') }} <span
                                        style="color: red;">({{ __('site.delivery_to_another_recipient') }} + <span class="">{{ number_format($giftFee, 2) }}</span>
                                        <i class="curreny">
                                            <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}" alt="sar" class="svg" />
                                        </i>)</span>
                                </span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="gift-fields" style="display:none; margin-top:15px;">
                        <h3 class="cart_info-title">{{ __('site.gift_details') }}</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">{{ __('site.recipient_name') }}</label>
                                <input type="text" class="form-control" name="reciver_name" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('site.phone_number') }}</label>
                                <input type="tel" class="form-control" name="reciver_phone" intlTelInput />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('site.city') }}</label>
                                <select class="form-control js-gift-city" name="gift_city_id">
                                    <option value="">{{ __('site.choose_city') }}</option>
                                    @foreach($cities ?? [] as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('site.district') }}</label>
                                <select class="form-control js-gift-district" name="gift_districts_id">
                                    <option value="">{{ __('site.choose_district') }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('site.delivery_address') }}</label>
                                <input type="text" class="form-control" name="gift_address_name" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('site.location_on_map') }}</label>
                                <div class="map-input">
                                    <span class="icon"><i class="fal fa-map"></i></span>
                                    <input type="text" class="form-control js-open-map" name="gift_address_map"
                                           placeholder="{{ __('site.choose_location') }}" readonly />
                                </div>
                                <input type="hidden" name="gift_latitude" />
                                <input type="hidden" name="gift_longitude" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('site.gift_message_optional') }}</label>
                                <textarea class="form-control" name="message" maxlength="255"></textarea>
                            </div>
                            <div class="form-group mt-5">
                                <label class="form-check">
                                    <input type="checkbox" name="whatsapp" value="1" class="form-check-input" />
                                    <span class="form-check-label">{{ __('site.send_via_whatsapp') }}</span>
                                </label>
                                <label class="form-check">
                                    <input type="checkbox" name="hide_sender" value="1" class="form-check-input" />
                                    <span class="form-check-label">{{ __('site.hide_sender_name') }}</span>
                                </label>
                            </div>

                        </div>
                    </div>

                    <div class="address-content">
                        <h3 class="cart_info-title">{{ __('site.delivery_address') }}</h3>
                        <div class="nav cart_address-tabs">
                            <button type="button" data-bs-toggle="tab" data-bs-target="#choose">
                                <i class="fal fa-address-book"></i>
                                {{ __('site.choose_from_address_book') }}
                            </button>
                            <button type="button" class="active" data-bs-toggle="tab" data-bs-target="#add">
                                <i class="fal fa-plus"></i>
                                {{ __('site.add_new_address') }}
                            </button>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="add">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="address_name" class="form-label">
                                            {{ __('site.address_name') }}
                                        </label>
                                        <input type="text" class="form-control" name="address_name" />
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient_name" class="form-label">
                                            {{ __('site.recipient_name') }}
                                        </label>
                                        <input type="text" class="form-control" name="recipient_name"
                                               data-rules="minLength:3|maxLength:191" />
                                    </div>
                                    <div class="form-group">
                                        <label for="city_id" class="form-label"> {{ __('site.city') }} </label>
                                        <select class="form-control js-city" name="city_id" select2 data-rules="required">
                                            <option value="" hidden>{{ __('site.choose_from_list') }}</option>
                                            @foreach(($cities ?? collect()) as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="districts_id" class="form-label"> {{ __('site.district') }} </label>
                                        <select class="form-control js-district" name="districts_id" select2 data-rules="required">
                                            <option value="" hidden>{{ __('site.choose_from_list') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="description" class="form-label">
                                            {{ __('site.address_description') }}
                                        </label>
                                        <input type="text" class="form-control" name="description" data-rules="maxLength:255" />
                                    </div>
                                    <div class="form-group">
                                        <label for="phone" class="form-label">{{ __('site.phone_number') }}</label>
                                        <input type="tel" class="form-control" name="phone" intlTelInput />
                                    </div>
                                    <div class="form-group">
                                        <label for="address_map" class="form-label">
                                            {{ __('site.location_on_map') }}
                                        </label>
                                        <div class="map-input">
                                            <span class="icon">
                                                <i class="fal fa-map"></i>
                                            </span>
                                            <input type="text" class="form-control js-open-map" name="address_map"
                                                   placeholder="{{ __('site.choose_location') }}" readonly />
                                            <input type="hidden" name="lat" />
                                            <input type="hidden" name="lng" />
                                            <input type="hidden" name="country_code" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="choose">
                                <div class="cart_radio-list">
                                    @forelse(($addresses ?? collect()) as $address)
                                    <div class="cart_radio-item">
                                        <label>
                                            <input type="radio" name="address_id" value="{{ $address->id }}" {{ $loop->first ? 'checked' : '' }} />
                                            <!-- Map Picker Modal -->
                                            <div class="modal fade" id="mapPickerModalOld" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">          اختر الموقع</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div id="mapPicker" style="height: 420px;"></div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="default-btn second-btn"
                                                                    data-bs-dismiss="modal">        إغلاق</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <span class="mark"> </span>
                                            <span
                                                class="text">{{ $address->address_name ?? $address->recipient_name ?? (__('site.address_label') . " #{$address->id}") }}</span>
                                        </label>
                                    </div>
                                    @empty
                                    <p>{{ __('site.no_addresses_yet') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <aside class="cart-side">
                <h2 class="cart-title">{{ __('site.cart') }}</h2>
                <input type="hidden" id="gift-fee-value" value="{{ (float) (\App\Models\SiteSetting::where('key','gift_fee')->value('value') ?? 0) }}" />
                <div id="cart-content">
                    @include('website.partials.cart_items_list', ['cartData' => $cartData])
                </div>
                <div class="cart-btns">
                    <a href="{{ route('website.home') }}" class="default-btn">
                        {{ __('site.continue_shopping') }}
                    </a>
                    <a href="{{ route('website.checkout') }}" class="default-btn second-btn" id="checkout-btn">
                        {{ __('site.checkout') }}
                    </a>
                </div>
            </aside>
        </div>
        @endif
    </div>
</section>

@endsection
@include('website.pages.cart._map_modal')
@include('website.pages.cart._scripts')
