<div class="products-grid">
  @forelse($products as $product)
    <div class="product-item">
      <a href="{{ route('website.product.show', $product->slug) }}" class="item-img" aria-label="product item">
        <img loading="lazy" src="{{ $product->image_url }}" class="img-contain" alt="{{ $product->name }}" />
      </a>
      <div class="item-info">
        <h3 class="item-title">
          <a href="{{ route('website.product.show', $product->slug) }}" aria-label="category item">{{ $product->name }}</a>
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
            <input type="number" class="quantity-input" readonly min="1" max="999" value="1" />
            <button class="quantity-btn plus-btn" type="button">
              <i class="fal fa-plus"></i>
            </button>
            <button class="quantity-btn minus-btn" type="button">
              <i class="fal fa-minus"></i>
            </button>
          </div>
          <button class="item-button add-to-cart-btn {{ $product->in_cart ? 'in-cart' : '' }}" data-product-id="{{ $product->id }}" data-in-cart="{{ $product->in_cart ? 1 : 0 }}" @disabled($product->in_cart)>
            <i class="fal fa-shopping-cart"></i>
          </button>
          <button class="item-fav {{ $product->is_favourite ? 'is-favorited' : '' }}" data-favorite-toggle data-product-id="{{ $product->id }}">
            <i class="far fa-heart"></i>
          </button>
        </div>
      </div>
    </div>
  @empty
    <p>{{ __('site.no_products_matching_filters') }}</p>
  @endforelse
</div>
<div class="mt-5 w-100 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
  <div class="text-dark fw-bold order-2 order-md-1">
    <span>{{ __('site.product_count') }}: {{ $products->total() }}</span>
  </div>
  <div class="flex-grow-1 d-flex justify-content-center order-1 order-md-2">
      {{ $products->withQueryString()->links() }}
  </div>
   <div style="width: 100px;" class="d-none d-md-block order-3"></div>
</div>

