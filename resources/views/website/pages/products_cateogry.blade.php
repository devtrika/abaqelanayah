@extends('website.layouts.app')

@section('title', $category->name )

@section('meta_description', __('site.browse_products') . ' ' . $category->name)
@section('meta_og')
<meta property="og:title" content="{{ $category->name }} - Lia" />
<meta property="og:description" content="{{ __('site.browse_products') . ' ' . $category->name }}" />
<meta property="og:image" content="{{ $category->image_url }}" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:type" content="website" />
<meta property="og:site_name" content="Lia" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $category->name }} - Lia" />
<meta name="twitter:description" content="{{ __('site.browse_products') . ' ' . $category->name }}" />
<meta name="twitter:image" content="{{ $category->image_url }}" />
@section('content')
  <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('website.category', $category->slug) }}" class="breadcrumb-link">
              {{ $category->name }}
            </a>
          </li>
        </ul>
      </div>
    </section>
    <!-- End Breadcrumb -->

    <section class="page-content">
      <div class="container">
        <div class="category-content">
          <div class="filters-overlay"></div>
          <aside class="category-side">
            <div class="search-filters">
              <form id="filters-form" method="GET" action="{{ route('website.category', $category->slug) }}">
                <div class="filters-head">
                  <h3 class="filters-title">{{ __('site.filter_search') }}</h3>
                  <button class="filters-reset" type="button" id="reset-btn">
                    <i class="fal fa-rotate-right"></i>
                  </button>
                </div>
                <div class="filters-list">
                  @if(count($category->children ?? []) > 0)
                  <div class="filter-item">
                    <label class="filter-label">{{ __('site.subcategories') }}</label>
                    <div class="filter-values">
                      @foreach(($category->children ?? []) as $child)
                        @if(($child->is_active ?? 1) == 1)
                        <div class="checkbox">
                          <label>
                            <input type="checkbox" name="category_ids[]" value="{{ $child->id }}" {{ in_array($child->id, (array)request()->input('category_ids', [])) ? 'checked' : '' }} />
                            <span class="mark"><i class="fa-regular fa-check"></i></span>
                            <span class="text"> {{ $child->name }} </span>
                          </label>
                        </div>
                        @endif
                      @endforeach
                    </div>
                  </div>
                  @endif
                  <div class="filter-item">
                    <label class="filter-label">{{ __('site.brands') }}</label>
                    <div class="filter-values">
                      @foreach($brands as $brand)
                        <div class="checkbox">
                          <label>
                            <input type="checkbox" name="brand_ids[]" value="{{ $brand->id }}" {{ in_array($brand->id, (array)request()->input('brand_ids', [])) ? 'checked' : '' }} />
                            <span class="mark"><i class="fa-regular fa-check"></i></span>
                            <span class="text"> {{ $brand->name }} </span>
                          </label>
                        </div>
                      @endforeach
                    </div>
                  </div>
                  <div class="filter-item">
                    <label class="filter-label">{{ __('site.price') }}</label>
                    <div class="filter-values">
                      <div class="price-filter">
                        <div
                          id="price-slider"
                          class="price-slider"
                          data-min-price="0"
                          data-max-price="5000"
                        ></div>
                        <div class="tooltips">
                          <p>
                            <span id="max-tooltip"> </span>
                            <i>
                              <img
                                src="{{ asset('website/images/icons/sar.svg') }}"
                                alt="curreny"
                                class="svg"
                              />
                            </i>
                          </p>
                          <p>
                            <span id="min-tooltip"> </span>
                            <i>
                              <img
                                src="{{ asset('website/images/icons/sar.svg') }}"
                                alt="curreny"
                                class="svg"
                              />
                            </i>
                          </p>
                        </div>
                        <div class="values-inputs">
                          <input
                            id="min-price"
                            type="number"
                            class="min-price"
                            name="min_price"
                            value="{{ request('min_price') }}"
                          />
                          <input
                            id="max-price"
                            type="number"
                            class="max-price"
                            name="max_price"
                            value="{{ request('max_price') }}"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <button class="filters-btn" type="submit">{{ __('site.filter') }}</button>
              </form>
            </div>
          </aside>
          <div>
            <div class="section-head page-head">
              <h1 class="section-title">{{ $category->name }}</h1>
              <div class="archive-tools">
                <div class="archive-sort">
                  <span>{{ __('site.sort_by') }}</span>
                  <select class="form-control" name="sort_by">
                    <option value="latest" {{ request('sort_by') === 'latest' ? 'selected' : '' }}>{{ __('site.latest') }}</option>
                    <option value="price_low" {{ request('sort_by') === 'price_low' ? 'selected' : '' }}>{{ __('site.price_low') }}</option>
                    <option value="price_high" {{ request('sort_by') === 'price_high' ? 'selected' : '' }}>{{ __('site.price_high') }}</option>
                  </select>
                </div>
                <button class="filter-trigger">
                  <i class="fal fa-filter"></i>
                </button>
              </div>
            </div>
            <div id="products-list">
              @include('website.partials.category_products_list', ['products' => $products])
            </div>

          </div>
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
                const resetBtn = document.getElementById('reset-btn');
                
                if (!form || !sort || !container) { return; }

                if (resetBtn) {
                  resetBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Clear all checkboxes
                    form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                      cb.checked = false;
                    });

                    // Reset Price Slider
                    const priceSlider = document.getElementById('price-slider');
                    if (priceSlider && priceSlider.noUiSlider) {
                        const min = priceSlider.getAttribute('data-min-price');
                        const max = priceSlider.getAttribute('data-max-price');
                        priceSlider.noUiSlider.set([min, max]);
                    }

                    // Reset URL params (keep sort)
                    const params = new URLSearchParams();
                    if (sort.value) {
                         params.set('sort_by', sort.value);
                    }
                    params.set('ajax', '1');
                    
                    const url = form.action + '?' + params.toString();
                    fetchAndRender(url);

                    // Update Browser URL
                    params.delete('ajax');
                    const newUrl = form.action + (params.toString() ? '?' + params.toString() : '');
                    window.history.replaceState({}, '', newUrl);
                  });
                }

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
