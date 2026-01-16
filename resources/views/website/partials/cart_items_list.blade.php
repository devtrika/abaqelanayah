@php($data = $cartData)
<div class="cart-products">
  @forelse($data['items'] as $item)
    <div class="cart-product" data-cart-item-id="{{ $item['id'] }}" data-cart-item-key="{{ $item['key'] }}" data-product-id="{{ $item['product_id'] }}">
      <a href="{{ route('website.product.show', $item['slug'] ?? $item['product_id']) }}" class="cart_product-img">
        <img loading="lazy" src="{{ $item['image_url'] }}" alt="product" class="img-cover" />
      </a>
      <div class="cart_product-info">
        <h3 class="cart_product-title">
          <a href="{{ route('website.product.show', $item['slug'] ?? $item['product_id']) }}">{{ $item['name'] }}</a>
        </h3>
        <div class="cart_product-refund" style="margin: 5px 0; font-size: 14px; color: #666;">
          <strong>{{ __('site.refund_availability') }}:</strong>
          <span style="color: {{ $item['is_refunded'] ? '#28a745' : '#dc3545' }};">
            {{ $item['is_refunded'] ? __('site.yes') : __('site.no') }}
          </span>
        </div>
        <div class="cart_product-tools">
          <div class="cart_product-quantity">
            <input type="number" class="quantity-input" min="1" max="999" value="{{ $item['quantity'] }}" readonly />
            <button class="quantity-btn plus-btn" type="button">
              <i class="fal fa-plus"></i>
            </button>
            <button class="quantity-btn minus-btn" type="button">
              <i class="fal fa-minus"></i>
            </button>
          </div>
          <strong class="cart_product-price">
            {{ number_format($item['total'], 2) }}
            <i class="curreny">
              <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}" alt="sar" class="svg" />
            </i>
          </strong>
          <button class="cart_product-del" type="button">
            <i class="fal fa-trash-alt"></i>
          </button>
        </div>
      </div>
    </div>
  @empty
    <p>{{ __('site.no_cart_items') }}</p>
  @endforelse
</div>
<div class="cart-final_total" data-base-total="{{ (float) ($data['subtotal'] ?? 0) }}">
  <span class="title">{{ __('site.total') }}</span>
  <strong class="value">
    <span class="js-cart-total">{{ number_format($data['subtotal'] ?? 0, 2) }}</span>
    <i class="curreny">
      <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}" alt="sar" class="svg" />
    </i>
  </strong>
</div>

