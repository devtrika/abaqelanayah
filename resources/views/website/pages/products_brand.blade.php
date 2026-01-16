@extends('website.layouts.app')

@section('title', $brand->name . ' - Lia')

@section('meta_description', __('site.browse_products') . ' ' . $brand->name)

@section('content')

<section class="breadcrumb-section">
  <div class="container">
    <ul class="breadcrumb-list">
      <li class="breadcrumb-item">
        <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{ route('website.brand', $brand->id) }}" class="breadcrumb-link">
          {{ $brand->name }}
        </a>
      </li>
    </ul>
  </div>
</section>

<section class="page-content">
  <div class="container">
    <form id="filters-form" method="GET" action="{{ route('website.brand', $brand->id) }}">
      <div class="section-head page-head">
        <h1 class="section-title">{{ $brand->name }}</h1>
        <div class="archive-tools">
          <div class="archive-sort">
            <span>{{ __('site.sort_by') }}</span>
            <select class="form-control" name="sort_by">
              <option value="latest" {{ request('sort_by') === 'latest' ? 'selected' : '' }}>{{ __('site.latest') }}</option>
              <option value="price_low" {{ request('sort_by') === 'price_low' ? 'selected' : '' }}>{{ __('site.price_low') }}</option>
              <option value="price_high" {{ request('sort_by') === 'price_high' ? 'selected' : '' }}>{{ __('site.price_high') }}</option>
            </select>
          </div>
        </div>
      </div>
    </form>

    <div id="products-list">
      @include('website.partials.category_products_list', ['products' => $products])
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filters-form');
    const sort = document.querySelector('select[name="sort_by"]');
    const container = document.getElementById('products-list');
    if (!form || !sort || !container) { return; }

    async function fetchAndRender(url) {
      try {
        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) return;
        const html = await res.text();
        container.innerHTML = html;
      } catch (e) {
        console.error('Failed to update products list:', e);
      }
    }

    sort.addEventListener('change', function () {
      const params = new URLSearchParams(new FormData(form));
      params.set('sort_by', sort.value);
      params.set('ajax', '1');
      const url = form.action + '?' + params.toString();
      fetchAndRender(url);

      // Update URL (without ajax=1) to keep current state navigable
      params.delete('ajax');
      window.history.replaceState({}, '', form.action + '?' + params.toString());
    });
  });
</script>
@endpush

