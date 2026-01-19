@extends('website.layouts.app')

@section('title', __('site.home_page_title') )

@section('meta_description', __('site.home_meta_description'))
@section('content')
<!-- Start Main -->
<main class="main-section">
  <div class="main-slider custom-slider">
    <div class="swiper">
      <div class="swiper-wrapper">
        @forelse($sliders as $slider)
        <div class="swiper-slide">
          <a href="{{$silder->limk ?? '#'}}" target="_blank" class="slider-img" aria-label="{{ $slider->title ?? 'main slide' }}">
            <img
              loading="lazy"
              src="{{ app()->getLocale() === 'ar' ? $slider->image_ar : $slider->image_en }}"
              class="img-contain"
              alt="{{ $slider->title ?? 'main slide' }}"
            />
          </a>
        </div>
        @empty
        <div class="swiper-slide">
          <a href="#!" class="slider-img" aria-label="main slide">
            <img
              loading="lazy"
              src="{{ asset('website/images/main/1.jpg') }}"
              class="img-contain"
              alt="main slide"
            />
          </a>
        </div>
        @endforelse
      </div>
    </div>

    <div class="swiper-pagination"></div>
  </div>
</main>
<!-- End Main -->

<!-- Start Categories -->
<section class="categories-section">
  <div class="container">
    <div class="section-head">
      <h2 class="section-title">{{ __('site.what_would_you_like_to_order_today') }}</h2>
    </div>
    <div class="categories-slider custom-slider">
      <div class="swiper">
        <div class="swiper-wrapper">
          @forelse($categories as $category)
          <div class="swiper-slide auto-swiper-slide">
            <div class="category-item">
              <a
                href="{{ route('website.category', $category->slug) }}"
                class="item-img"
                aria-label="{{ $category->name }}"
              >
                <img
                  loading="lazy"
                  src="{{ $category->image_url }}"
                  class="img-contain"
                  alt="{{ $category->name }}"
                />
              </a>
              <h3 class="item-title">
                <a
                  href="{{ route('website.category', $category->slug) }}"
                  aria-label="{{ $category->name }}"
                >
                  {{ $category->name }}
                </a>
              </h3>
            </div>
          </div>
          @empty
          <div class="swiper-slide auto-swiper-slide">
            <div class="category-item">
              <p>{{ __('site.no_categories_available') }}</p>
            </div>
          </div>
          @endforelse
        </div>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
</section>
<!-- End Categories -->

<!-- Start Specials -->
<section class="specials-section">
  <div class="container">
    <div class="section-head">
      <h2 class="section-title">{{ __('site.latest_offers') }}</h2>
      <a href="{{ route('website.offers') }}" class="section-link"> {{ __('site.view_all') }} </a>
    </div>
    <div class="specials-slider custom-slider">
      <div class="swiper">
        <div class="swiper-wrapper">
          @forelse($specialOffers as $product)
          <div class="swiper-slide auto-swiper-slide">
            <div class="product-item">
              <a
                href="{{ route('website.product.show', $product->slug) }}"
                class="item-img"
                aria-label="{{ $product->name }}"
              >
                <img
                  loading="lazy"
                  src="{{ $product->image_url }}"
                  class="img-contain"
                  alt="{{ $product->name }}"
                />
              </a>
              <div class="item-info">
                <h3 class="item-title">
                  <a href="{{ route('website.product.show', $product->slug) }}" aria-label="{{ $product->name }}">
                    {{ $product->name }}
                  </a>
                </h3>
                <div class="item-prices">
                  <strong class="price-current">
                    {{ number_format($product->final_price, 2) }}
                    <span class="curreny">
                      <img
                        loading="lazy"
                        src="{{ asset('website/images/icons/sar.svg') }}"
                        alt="sar"
                        class="svg"
                      />
                    </span>
                  </strong>
                  @if($product->discount_percentage > 0)
                  <span class="price-discount"> -{{ $product->discount_percentage }}% </span>
                  <del class="price-old">
                    {{ number_format($product->base_price, 2) }}
                    <span class="curreny">
                      <img
                        loading="lazy"
                        src="{{ asset('website/images/icons/sar.svg') }}"
                        alt="sar"
                        class="svg"
                      />
                    </span>
                  </del>
                  @endif
                </div>
                <div class="item-tools">
                  <div class="item-quantity">
                    <input
                      type="number"
                      class="quantity-input"
                      min="1"
                      value="1"
                      readonly
                    />
                    <button
                      class="quantity-btn plus-btn"
                      type="button"
                    >
                      <i class="fal fa-plus"></i>
                    </button>
                    <button
                      class="quantity-btn minus-btn"
                      type="button"
                    >
                      <i class="fal fa-minus"></i>
                    </button>
                  </div>
                  <button class="item-button add-to-cart-btn {{ $product->in_cart ? 'in-cart' : '' }}" data-product-id="{{ $product->id }}" data-in-cart="{{ $product->in_cart ? 1 : 0 }}" @disabled($product->in_cart)>
                    <i class="fal fa-shopping-cart"></i>
                  </button>
                  <button class="item-fav" data-favorite-toggle data-product-id="{{ $product->id }}">
                    <i class="far fa-heart"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          @empty
          <div class="swiper-slide auto-swiper-slide">
            <p>{{ __('site.no_offers_available') }}</p>
          </div>
          @endforelse
        </div>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
</section>
<!-- End Specials -->

<!-- Start Brands -->
<section class="brands-section">
  <div class="container">
    <div class="section-head">
      <h2 class="section-title">{{ __('site.brands') }}</h2>
    </div>
    <div class="brands-slider custom-slider">
      <div class="swiper">
        <div class="swiper-wrapper">
          @forelse($brands as $brand)
          <div class="swiper-slide auto-swiper-slide">
            <a href="{{ route('website.brand', $brand->id) }}" class="brand-item" aria-label="{{ $brand->name }}">
              <img
                loading="lazy"
                src="{{ $brand->image }}"
                alt="{{ $brand->name }}"
                class="img-contain"
              />
            </a>
          </div>
          @empty
          <div class="swiper-slide auto-swiper-slide">
            <p>{{ __('site.no_brands_available') }}</p>
          </div>
          @endforelse
        </div>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
</section>



<!-- Start latest -->
<section class="specials-section">
  <div class="container">
    <div class="section-head">
      <h2 class="section-title">{{ __('site.latest_products') }}</h2>
      <a href="{{ route('website.latest') }}" class="section-link"> {{ __('site.view_all') }} </a>
    </div>
    <div class="specials-slider custom-slider">
      <div class="swiper">
        <div class="swiper-wrapper">
          @forelse($latestProducts as $product)
          <div class="swiper-slide auto-swiper-slide">
            <div class="product-item">
              <a
                href="{{ route('website.product.show', $product->slug) }}"
                class="item-img"
                aria-label="{{ $product->name }}"
              >
                <img
                  loading="lazy"
                  src="{{ $product->image_url }}"
                  class="img-contain"
                  alt="{{ $product->name }}"
                />
              </a>
              <div class="item-info">
                <h3 class="item-title">
                  <a href="{{ route('website.product.show', $product->slug) }}" aria-label="{{ $product->name }}">
                    {{ $product->name }}
                  </a>
                </h3>
                <div class="item-prices">
                  <strong class="price-current">
                    {{ number_format($product->final_price, 2) }}
                    <span class="curreny">
                      <img
                        loading="lazy"
                        src="{{ asset('website/images/icons/sar.svg') }}"
                        alt="sar"
                        class="svg"
                      />
                    </span>
                  </strong>
                  @if($product->discount_percentage > 0)
                  <span class="price-discount"> -{{ $product->discount_percentage }}% </span>
                  <del class="price-old">
                    {{ number_format($product->base_price, 2) }}
                    <span class="curreny">
                      <img
                        loading="lazy"
                        src="{{ asset('website/images/icons/sar.svg') }}"
                        alt="sar"
                        class="svg"
                      />
                    </span>
                  </del>
                  @endif
                </div>
                <div class="item-tools">
                  <div class="item-quantity">
                    <input
                      type="number"
                      class="quantity-input"
                      min="1"
                      value="1"
                      readonly
                    />
                    <button
                      class="quantity-btn plus-btn"
                      type="button"
                    >
                      <i class="fal fa-plus"></i>
                    </button>
                    <button
                      class="quantity-btn minus-btn"
                      type="button"
                    >
                      <i class="fal fa-minus"></i>
                    </button>
                  </div>
                  <button class="item-button add-to-cart-btn {{ $product->in_cart ? 'in-cart' : '' }}" data-product-id="{{ $product->id }}" data-in-cart="{{ $product->in_cart ? 1 : 0 }}" @disabled($product->in_cart)>
                    <i class="fal fa-shopping-cart"></i>
                  </button>
                  <button class="item-fav" data-favorite-toggle data-product-id="{{ $product->id }}">
                    <i class="far fa-heart"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          @empty
          <div class="swiper-slide auto-swiper-slide">
            <p>{{ __('site.no_offers_available') }}</p>
          </div>
          @endforelse
        </div>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
</section>
<!-- End Specials -->

<!-- End Brands -->
@endsection
