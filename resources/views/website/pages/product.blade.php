@extends('website.layouts.app')

@section('title', $product->name )

@section('meta_description', $product->description ?? __('site.product_details'))
@section('meta_og')
<meta property="og:title" content="{{ $product->name }} - Lia" />
<meta property="og:description" content="{{ $product->description ?? '' }}" />
<meta property="og:image" content="{{ $product->image_url }}" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:type" content="product" />
<meta property="og:site_name" content="Lia" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $product->name }} - Lia" />
<meta name="twitter:description" content="{{ $product->description ?? '' }}" />
<meta name="twitter:image" content="{{ $product->image_url }}" />
@section('content')
 <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          @if($product->category)
          <li class="breadcrumb-item">
            <a href="{{ route('website.category', $product->category->slug) }}" class="breadcrumb-link">
              {{ $product->category->name }}
            </a>
          </li>
          @endif
          <li class="breadcrumb-item">
            <a href="{{ route('website.product.show', $product->slug) }}" class="breadcrumb-link">
              {{ $product->name }}
            </a>
          </li>
        </ul>
      </div>
    </section>
    <!-- End Breadcrumb -->

    <section class="page-content product-page">
      <div class="container">
        <div class="product-content">
          @php($gallery = $product->getMedia('product-images'))
          <div class="product-gallery custom-slider">
            <div class="swiper">
              <div class="swiper-wrapper">
                @if($gallery && $gallery->count())
                  @foreach($gallery as $media)
                    <div class="swiper-slide">
                      <img loading="lazy" src="{{ $media->getUrl() }}" alt="{{ $product->name }}" class="img-contain" />
                    </div>
                  @endforeach
                @else
                  <div class="swiper-slide">
                    <img loading="lazy" src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-contain" />
                  </div>
                @endif
              </div>
              <div class="swiper-pagination"></div>
            </div>
          </div>
          <div class="product-information">
            <div class="product-categories">
              @if($product->category)
                @if($product->category->parent)
              <a href="{{ route('website.category', $product->category->parent->slug) }}" class="product-category">
                {{ $product->category->parent->name }}
              </a>
              @endif
              <a href="{{ route('website.category', $product->category->slug) }}" class="product-category">
                {{ $product->category->name }}
              </a>
            
              @endif
            </div>
            <button class="product-fav {{ $product->is_favourite ? 'is-favorited' : '' }}" data-favorite-toggle data-product-id="{{ $product->id }}">
              <i class="far fa-heart"></i>
            </button>
            <h1 class="product-title">
              {{ $product->name }}
            </h1>
            @if($product->description)
            <p class="product-desc">
              {{ $product->description }}
            </p>
            @endif
            <div class="product-features">
              <div class="product-feature">
                <strong class="title"> {{ __('site.product_status') }} </strong>
                <span class="status">
                  @if($product->quantity > 0)
                  <i class="far fa-check-circle"></i>
                  {{ __('site.available') }}
                  @else
                  <i class="far fa-times-circle"></i>
                  {{ __('site.not_available') }}
                  @endif
                </span>
              </div>
              <div class="product-feature">
                <strong class="title"> {{ __('site.refundable') }} </strong>
                <span class="value">{{ $product->is_refunded ? __('site.yes') : __('site.no') }}</span>
              </div>
        
              @if($product->brand)
              <div class="product-feature">
                <strong class="title"> {{ __('site.brand') }} </strong>
                <span class="value">{{ $product->brand->name }}</span>
              </div>
              @endif
            </div>
            <div class="product-share">
              <strong class="title"> {{ __('site.share_product') }} </strong>
              <div class="share-list">
                <a href="#!" class="share-link" onclick="navigator.clipboard.writeText(window.location.href); alert('{{ __('site.link_copied') }}'); return false;">
                  <i class="far fa-link"></i>
                </a>
                <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($product->name) }}" target="_blank" class="share-link">
                  <i class="fab fa-telegram"></i>
                </a>
                <a href="https://wa.me/?text={{ urlencode($product->name . ' - ' . url()->current()) }}" target="_blank" class="share-link">
                  <i class="fab fa-whatsapp"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($product->name) }}" target="_blank" class="share-link">
                  <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="share-link">
                  <i class="fab fa-facebook"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      @if($relatedProducts && $relatedProducts->count() > 0)
      <section class="specials-section">
        <div class="container">
          <div class="section-head">
            <h2 class="section-title">{{ __('site.similar_products') }}</h2>
          </div>
          <div class="specials-slider custom-slider">
            <div class="swiper">
              <div class="swiper-wrapper">
                @foreach($relatedProducts as $relatedProduct)
                <div class="swiper-slide auto-swiper-slide">
                  <div class="product-item">
                    <a
                      href="{{ route('website.product.show', $relatedProduct->slug) }}"
                      class="item-img"
                      aria-label="{{ $relatedProduct->name }}"
                    >
                      <img
                        loading="lazy"
                        src="{{ $relatedProduct->image_url }}"
                        class="img-contain"
                        alt="{{ $relatedProduct->name }}"
                      />
                    </a>
                    <div class="item-info">
                      <h3 class="item-title">
                        <a
                          href="{{ route('website.product.show', $relatedProduct->slug) }}"
                          aria-label="{{ $relatedProduct->name }}"
                        >
                          {{ $relatedProduct->name }}
                        </a>
                      </h3>
                      <div class="item-prices">
                        <strong class="price-current">
                          {{ number_format($relatedProduct->final_price, 2) }}
                          <span class="curreny">
                            <img
                              loading="lazy"
                              src="{{ asset('website/images/icons/sar.svg') }}"
                              alt="sar"
                              class="svg"
                            />
                          </span>
                        </strong>
                        @if($relatedProduct->discount_percentage > 0)
                        <span class="price-discount"> -{{ $relatedProduct->discount_percentage }}% </span>
                        <del class="price-old">
                          {{ number_format($relatedProduct->base_price, 2) }}
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
                            max="999"
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
                        <button class="item-button add-to-cart-btn {{ $relatedProduct->in_cart ? 'in-cart' : '' }}" data-product-id="{{ $relatedProduct->id }}" data-in-cart="{{ $relatedProduct->in_cart ? 1 : 0 }}" @disabled($relatedProduct->in_cart)
                        >
                          <i class="fal fa-shopping-cart"></i>
                        </button>
                        <button class="item-fav" data-favorite-toggle data-product-id="{{ $relatedProduct->id }}">
                          <i class="far fa-heart"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
            <div class="swiper-pagination"></div>
          </div>
        </div>
      </section>
      @endif
      <section class="product_sticky-section">
        <div class="container">
          <div class="product-sticky">
            <div class="product-prices">
              <strong>
                {{ number_format($product->final_price, 2) }}
                <i class="curreny">
                  <img
                    loading="lazy"
                    src="{{ asset('website/images/icons/sar.svg') }}"
                    alt="sar"
                    class="svg"
                  />
                </i>
              </strong>
              @if($product->discount_percentage > 0)
              <del>
                {{ number_format($product->base_price, 2) }}
                <i class="curreny">
                  <img
                    loading="lazy"
                    src="{{ asset('website/images/icons/sar.svg') }}"
                    alt="sar"
                    class="svg"
                  />
                </i>
              </del>
              <span> -{{ $product->discount_percentage }}% </span>
              @endif
            </div>
            <div class="product-tools">
              <div class="product-quantity">
                <input
                  type="number"
                  class="quantity-input"
                  min="1"
                  max="999"
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
              <button class="product-button add-to-cart-btn {{ $product->in_cart ? 'in-cart' : '' }}" data-product-id="{{ $product->id }}" data-in-cart="{{ $product->in_cart ? 1 : 0 }}" @disabled($product->in_cart)
              >
                <i class="fal fa-shopping-cart"></i>
                <span>{{ __('site.add_to_cart') }}</span>
              </button>
            </div>
          </div>
        </div>
      </section>
    </section>

@push('scripts')
<script>
(function($){
  $(document).on('click', '.product-page .quantity-btn', function(){
    var $btn = $(this);
    var $wrap = $btn.closest('.item-quantity, .product-quantity');
    var $input = $wrap.find('input[type=number]');
    if(!$input.length) return;

    var min = parseInt($input.attr('min'), 10); if (isNaN(min)) min = 1;
    var maxAttr = $input.attr('max');
    var max = maxAttr ? parseInt(maxAttr, 10) : Infinity;
    var val = parseInt($input.val(), 10); if (isNaN(val)) val = min;

    if($btn.hasClass('plus-btn')){
      val = Math.min(max, val + 1);
    } else {
      val = Math.max(min, val - 1);
    }

    $input.val(val);
  });
})(jQuery);
</script>
@endpush
@endsection

@push('scripts')
<script>
(function(){
  if (typeof Swiper !== 'undefined') {
    new Swiper('.product-gallery .swiper', {
      spaceBetween: 10,
      slidesPerView: 1,
      loop: true,
      autoplay: { delay: 4000, disableOnInteraction: false },
      pagination: { el: '.product-gallery .swiper-pagination', clickable: true },
    });
  }
})();
</script>
@endpush

@push('styles')
<style>
  .product-gallery{ width:100%; aspect-ratio:358/325; border-radius:15px; overflow:hidden; background-color:#fff9f9; }
  .product-gallery .swiper, .product-gallery .swiper-wrapper, .product-gallery .swiper-slide{ height:100%; }
  .product-gallery img{ width:100%; height:100%; object-fit:contain; }
</style>
@endpush
