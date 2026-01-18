<!-- Start Header -->
<header class="main-header">
  <div class="container">
    <div class="header">
      <a href="{{ route('website.main') }}" class="logo">
        <img
          src="{{$settings['logo']}}"
          alt="logo"
          loading="lazy"
          class="img-contain"
        />
      </a>
      <div class="form-search-wrapper">
        <form action="#" method="GET" class="form-search">
          <input
            type="search"
            name="search"
            id="global-search-input"
            placeholder="{{ __('site.search_placeholder') }}"
            class="search-input"
            value="{{ request('search') }}"
            autocomplete="off"
          />
          <button class="search-btn" type="submit">
            <i class="fal fa-search"></i>
          </button>
        </form>
        <div class="search-dropdown" id="search-dropdown" style="display: none;">
          <div class="search-results" id="search-results"></div>
        </div>
      </div>
      <div class="header-tools">
        <div class="header-icons">
          <button type="button" class="header-icon menu-icon">
            <i class="fal fa-bars"></i>
          </button>
          
          @auth('web')
            {{-- Notifications --}}
            <a href="{{ route('website.notifications') }}" class="header-icon notifications-icon">
              <i class="fal fa-bell"></i>
              <span class="badge" id="notifications-badge">{{ $headerNotificationsUnreadCount ?? 0 }}</span>
            </a>
            
            {{-- User Menu --}}
            <div class="user_header-content">
              <button type="button" class="header-icon user-icon">
                <i class="fal fa-user"></i>
              </button>
              <div class="user_header-list">
                <a href="{{ route('website.account') }}"> {{ __('site.myAccount') }} </a>
                <a href="{{ route('website.orders') }}"> {{ __('site.orders') }} </a>
                <a href="{{ route('website.addresses.index') }}"> {{ __('site.addresses') }} </a>
                <a href="{{ route('website.wallet.index') }}"> {{ __('site.wallet') }} </a>
                <a href="{{ route('website.refunds.index') }}"> {{ __('site.refunds') }} </a>
                <a
                  data-bs-toggle="modal"
                  data-bs-target="#logoutModal"
                  class="logout"
                >
                  {{ __('site.logout') }}
                </a>
              </div>
            </div>
            
           
            
            {{-- Cart --}}
            <a href="{{ route('website.cart.index') }}" class="header-icon cart-icon">
              <i class="fal fa-shopping-cart"></i>
              <span class="badge" id="cart-badge">{{ $headerCartCount ?? 0 }}</span>
            </a>
          @else
            {{-- Guest User - Show Login/Register --}}
            <a href="{{ route('website.login') }}" class="header-icon user-icon">
              <i class="fal fa-user"></i>
            </a>
          @endauth
        </div>
        
        {{-- Language Switcher --}}
      <div class="lang-content">
      @php $targetLang = app()->getLocale() === 'ar' ? 'en' : 'ar'; @endphp
      <a href="{{ url('lang/' . $targetLang) }}" class="lang-link">{{ app()->getLocale() === 'ar' ? __('site.lang_en_short') : __('site.lang_ar_short') }}</a>
        </div>
      </div>
    </div>
  </div>
  
  <div class="overlay"></div>
  
  {{-- Mobile Navigation --}}
  <nav class="header-nav">
    <div class="container nav-content">
      <div class="nav-head">
    <div class="lang-content">
      @php $targetLang = app()->getLocale() === 'ar' ? 'en' : 'ar'; @endphp
      <a href="{{ url('lang/' . $targetLang) }}" class="lang-link">{{ app()->getLocale() === 'ar' ? __('site.english') : __('site.arabic') }}</a>
        </div>
        <button type="button" class="close-nav">
          <i class="fal fa-times"></i>
        </button>
      </div>
      <ul class="nav-list">
        <li class="nav-item">
          <a href="{{ route('website.main') }}" class="nav-link"> {{ __('site.home') }} </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('website.offers') }}" class="nav-link"> {{ __('site.offers') }} </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('website.latest') }}" class="nav-link"> {{ __('site.latest_products') }} </a>
        </li>

        {{-- Dynamic Categories --}}
        @if(isset($headerCategories))
          @foreach($headerCategories as $category)
            @if($category->children && $category->children->count() > 0)
              {{-- Category with subcategories --}}
              <li class="nav-item has-children">
                <a href="{{ route('website.category', $category->slug) }}" class="nav-link">
                  {{ $category->name }}
                </a>
                <ul class="children-list">
                  @foreach($category->children as $subCategory)
                    <li class="children-item">
                      <a href="{{ route('website.category', $subCategory->slug) }}" class="children-link">
                        {{ $subCategory->name }}
                      </a>
                    </li>
                  @endforeach
                </ul>
              </li>
            @else
              {{-- Category without subcategories --}}
              <li class="nav-item">
                <a href="{{ route('website.category', $category->slug) }}" class="nav-link">
                  {{ $category->name }}
                </a>
              </li>
            @endif
          @endforeach
        @endif
      </ul>
    </div>
  </nav>
</header>
<!-- End Header -->

{{-- Logout Modal --}}
@auth('web')
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="modal-close" data-bs-dismiss="modal">
          <i class="far fa-xmark"></i>
        </button>
      </div>
      <div class="modal-body">
        <h2 class="modal-head">{{ __('site.logout_confirm_title') }}</h2>
        <p class="modal-desc">{{ __('site.logout_confirm_desc') }}</p>
      </div>
      <div class="modal-footer">
        <button
          type="button"
          class="modal-btn modal_second-btn"
          data-bs-dismiss="modal"
        >
          {{ __('site.cancel') }}
        </button>
        <form action="{{ route('website.logout') }}" class="modal-btn" method="POST">
          @csrf
          <button type="submit" class="modal-btn">{{ __('site.yes') }}</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endauth

{{-- Live Search Styles and Script --}}
<style>
  .form-search-wrapper {
    position: relative;
    flex: 1;
  }

  .search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-top: none;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    max-height: 400px;
    overflow-y: auto;
    z-index: 1000;
    margin-top: -1px;
  }

  .search-results {
    padding: 0;
  }

  .search-result-item {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background-color 0.2s;
    text-decoration: none;
    color: inherit;
  }

  .search-result-item:hover {
    background-color: #f8f8f8;
  }

  .search-result-item:last-child {
    border-bottom: none;
  }

  .search-result-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
    margin-inline-end: 12px;
    flex-shrink: 0;
  }

  .search-result-info {
    flex: 1;
    min-width: 0;
  }

  .search-result-name {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin: 0 0 4px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .search-result-price {
    font-size: 13px;
    color: #666;
    margin: 0;
  }

  .search-no-results {
    padding: 20px;
    text-align: center;
    color: #999;
    font-size: 14px;
  }

  .search-loading {
    padding: 20px;
    text-align: center;
    color: #999;
    font-size: 14px;
  }
</style>

<script>
(function() {
  let searchTimeout;
  const searchInput = document.getElementById('global-search-input');
  const searchDropdown = document.getElementById('search-dropdown');
  const searchResults = document.getElementById('search-results');

  if (!searchInput || !searchDropdown || !searchResults) return;

  // Handle input changes
  searchInput.addEventListener('input', function() {
    const query = this.value.trim();

    clearTimeout(searchTimeout);

    if (query.length < 2) {
      searchDropdown.style.display = 'none';
      return;
    }

    // Show loading state
    searchResults.innerHTML = '<div class="search-loading"><i class="fal fa-spinner fa-spin"></i> {{ __("site.searching") }}...</div>';
    searchDropdown.style.display = 'block';

    // Debounce search
    searchTimeout = setTimeout(function() {
      performSearch(query);
    }, 300);
  });

  // Perform AJAX search
  function performSearch(query) {
    fetch("{{ route('website.product.search') }}?q=" + encodeURIComponent(query))
      .then(response => response.json())
      .then(data => {
        displayResults(data.results);
      })
      .catch(error => {
        console.error('Search error:', error);
        searchResults.innerHTML = '<div class="search-no-results">{{ __("site.search_error") }}</div>';
      });
  }

  // Display search results
  function displayResults(results) {
    if (!results || results.length === 0) {
      searchResults.innerHTML = '<div class="search-no-results">{{ __("site.no_results_found") }}</div>';
      return;
    }

    let html = '';
    results.forEach(function(product) {
      html += `
        <a href="${product.url}" class="search-result-item">
          <img src="${product.image_url}" alt="${product.name}" class="search-result-image" onerror="this.src='{{ asset('website/images/placeholder.png') }}'">
          <div class="search-result-info">
            <p class="search-result-name">${product.name}</p>
            <p class="search-result-price">${product.price} {{ __("site.currency") }}</p>
          </div>
        </a>
      `;
    });

    searchResults.innerHTML = html;
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
      searchDropdown.style.display = 'none';
    }
  });

  // Show dropdown when focusing on input if there are results
  searchInput.addEventListener('focus', function() {
    if (searchResults.innerHTML && this.value.trim().length >= 2) {
      searchDropdown.style.display = 'block';
    }
  });
})();
</script>

