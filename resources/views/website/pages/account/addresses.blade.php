@extends('website.layouts.app')

@section('title', __('site.myAccount') . ' - ' . __('site.address_book'))

@section('meta_description', __('site.address_book'))

@section('content')

 <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('website.addresses.index') }}" class="breadcrumb-link">
              {{ __('site.address_book') }}
            </a>
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
              <h2 class="account-title">{{ __('site.address_book') }}</h2>
              <a href="{{ route('website.addresses.create') }}" class="account-btn"> {{ __('site.add_new') }} </a>
            </div>
            <div class="account-table-wrapper">
              <table class="account-table fixed">
                <thead>
                  <tr>
                    <th>{{ __('site.address_name') }}</th>
                    <th>{{ __('site.recipient_name') }}</th>
                    <th>{{ __('site.city') }}</th>
                    <th>{{ __('site.district') }}</th>
                    <th>{{ __('site.phone_number') }}</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($addresses as $address)
                  <tr>
                    <td>{{ $address->address_name }}</td>
                    <td>{{ $address->recipient_name }}</td>
                    <td>{{ optional($address->city)->name ?? '-' }}</td>
                    <td>{{ optional($address->district)->name ?? '-' }}</td>
                    <td>
                      <a href="tel:{{ $address->phone }}" class="table-contact">
                        {{ $address->phone }}
                      </a>
                    </td>
                    <td>
                      <div class="table-actions">
                        <a href="{{ route('website.addresses.edit', $address) }}" class="table-action">
                          <i class="fal fa-edit"></i>
                        </a>
                        <button
                          type="button"
                          class="table-action js-delete-address"
                          data-address-id="{{ $address->id }}"
                          data-address-name="{{ $address->address_name }}"
                          data-has-orders="{{ $address->hasOrders() ? 'true' : 'false' }}"
                        >
                          <i class="fal fa-trash-alt"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="text-center">{{ __('site.no_addresses_yet') }}</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

{{-- Delete Address Modal --}}
<div class="modal fade" id="deleteAddressModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="modal-close" data-bs-dismiss="modal">
          <i class="far fa-xmark"></i>
        </button>
      </div>
      <div class="modal-body">
        <h2 class="modal-head">{{ __('site.delete_address') }}</h2>
        <p class="modal-desc" id="deleteModalMessage">{{ __('site.delete_address_confirm') }}</p>
      </div>
      <div class="modal-footer">
        <button
          type="button"
          class="modal-btn modal_second-btn"
          data-bs-dismiss="modal"
        >
          {{ __('site.cancel') }}
        </button>
        <form id="deleteAddressForm" method="POST" style="width: 100%;">
          @csrf
          @method('DELETE')
          <button type="submit" class="modal-btn" id="confirmDeleteBtn">{{ __('site.yes_delete') }}</button>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Cannot Delete Modal --}}
<div class="modal fade" id="cannotDeleteAddressModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="modal-close" data-bs-dismiss="modal">
          <i class="far fa-xmark"></i>
        </button>
      </div>
      <div class="modal-body">
        <h2 class="modal-head">{{ __('site.cannot_delete_address') }}</h2>
        <p class="modal-desc">{{ __('site.cannot_delete_address_msg') }}</p>
      </div>
      <div class="modal-footer">
        <button
          type="button"
          class="modal-btn"
          data-bs-dismiss="modal"
        >
          {{ __('site.ok') }}
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  (function($){
    $(document).on('click', '.js-delete-address', function(){
      var $btn = $(this);
      var addressId = $btn.data('address-id');
      var addressName = $btn.data('address-name');
      var hasOrders = $btn.data('has-orders') === 'true';

      if(hasOrders){
        // Show cannot delete modal
        var cannotDeleteModal = new bootstrap.Modal(document.getElementById('cannotDeleteAddressModal'));
        cannotDeleteModal.show();
      } else {
        // Show delete confirmation modal
        var deleteUrl = "{{ route('website.addresses.destroy', ':id') }}".replace(':id', addressId);
        $('#deleteAddressForm').attr('action', deleteUrl);
        var tmpl = @js(__('site.delete_address_confirm_with_name'));
        $('#deleteModalMessage').text(tmpl.replace(':name', addressName));

        var deleteModal = new bootstrap.Modal(document.getElementById('deleteAddressModal'));
        deleteModal.show();
      }
    });
  })(jQuery);
</script>
@endpush