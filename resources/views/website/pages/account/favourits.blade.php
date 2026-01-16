@extends('website.layouts.app')

@section('title', __('site.myAccount') . ' - ' . __('site.favorite'))

@section('meta_description', __('site.favorite'))

@section('content')

  <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('website.favourits') }}" class="breadcrumb-link"> {{ __('site.favorite') }} </a>
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
            <div class="account-header">
              <h2 class="account-title">{{ __('site.favorite') }}</h2>
            </div>
            <div class="products-grid" id="favorites-page-grid" data-empty-msg="{{ __('site.favorites_empty') }}">
              @php($count = isset($favourites) ? $favourites->count() : 0)
              @forelse($favourites as $fav)
                @php($product = $fav->product)
                @if($product)
                <div class="product-item addedToWishlist">
                  <a href="{{ route('website.product.show', $product->slug) }}" class="item-img" aria-label="{{ $product->name }}">
                    <img loading="lazy" src="{{ $product->image_url }}" class="img-contain" alt="{{ $product->name }}" />
                  </a>
                  <div class="item-info">
                    <h3 class="item-title">
                      <a href="{{ route('website.product.show', $product->slug) }}" aria-label="{{ $product->name }}">{{ $product->name }}</a>
                    </h3>
                    <div class="item-prices">
                      <strong class="price-current">
                        {{ number_format($product->final_price, 2) }}
                        <span class="curreny">
                          <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}" alt="sar" class="svg" />
                        </span>
                      </strong>
                      @if($product->discount_percentage)
                        <span class="price-discount"> -{{ $product->discount_percentage }}% </span>
                        <del class="price-old">
                          {{ number_format($product->base_price, 2) }}
                          <span class="curreny">
                            <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}" alt="sar" class="svg" />
                          </span>
                        </del>
                      @endif
                    </div>
                    <div class="item-tools">
                      <div class="item-quantity">
                        <input type="number" class="quantity-input" readonly min="1" value="1" />
                        <button class="quantity-btn plus-btn" type="button">
                          <i class="fal fa-plus"></i>
                        </button>
                        <button class="quantity-btn minus-btn" type="button">
                          <i class="fal fa-minus"></i>
                        </button>
                      </div>
                      <button class="item-button add-to-cart-btn" data-product-id="{{ $product->id }}" @disabled($product->in_cart)>
                        <i class="fal fa-shopping-cart"></i>
                      </button>
                      <button class="item-fav is-favorited" data-favorite-toggle data-product-id="{{ $product->id }}">
                        <i class="far fa-heart"></i>
                      </button>
                    </div>
                  </div>
                </div>
                @endif
              @empty
                <div class="alert alert-info text-center">{{ __('site.favorites_empty') }}</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </section>
@endsection
