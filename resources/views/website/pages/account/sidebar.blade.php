<aside class="account-list">
            <a href="{{ route('website.account') }}" class="account-item {{ request()->routeIs('website.account') ? 'active' : '' }}">
              <i class="fal fa-user"></i>
              {{ __('site.edit_profile') }}
            </a>
            <a href="{{ route('website.password') }}" class="account-item {{ request()->routeIs('website.password') ? 'active' : '' }}">
              <i class="fal fa-lock"></i>
              {{ __('site.change_password') }}
            </a>
            <a href="{{ route('website.orders') }}" class="account-item {{ request()->routeIs('website.orders') ? 'active' : '' }}">
              <i class="fal fa-shopping-cart"></i>
              {{ __('site.orders') }}
            </a>
            <a href="{{ route('website.refunds.index') }}" class="account-item {{ request()->routeIs('website.refunds.*') ? 'active' : '' }}">
              <i class="fal fa-redo"></i>
              {{ __('site.refunds') }}
            </a>
            <a href="{{ route('website.favourits') }}" class="account-item {{ request()->routeIs('website.favourits') ? 'active' : '' }}">
              <i class="fal fa-heart"></i>
              {{ __('site.favorite') }}
            </a>
            <a href="{{ route('website.addresses.index') }}" class="account-item {{ request()->routeIs('website.addresses.*') ? 'active' : '' }}">
              <i class="fal fa-address-book"></i>
              {{ __('site.address_book') }}
            </a>
            <a href="{{ route('website.notifications') }}" class="account-item {{ request()->routeIs('website.notifications') ? 'active' : '' }}">
              <i class="fal fa-bell"></i>
              {{ __('site.notifications') }}
            </a>
            <a href="{{ route('website.wallet.index') }}"  class="account-item {{ request()->routeIs('website.wallet.*') ? 'active' : '' }}">
              <i class="fal fa-wallet"></i>
              {{ __('site.wallet') }}
            </a>
            <a
              data-bs-toggle="modal"
              data-bs-target="#logoutModal"
              class="account-item modal-item"
            >
              <i class="fal fa-sign-out-alt"></i>
              {{ __('site.logout') }}
            </a>
            <a
              data-bs-toggle="modal"
              data-bs-target="#deleteModal"
              class="account-item modal-item"
            >
              <i class="fal fa-trash-alt"></i>
              {{ __('site.delete_account') }}
            </a>
          </aside>