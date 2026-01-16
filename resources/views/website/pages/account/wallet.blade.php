@extends('website.layouts.app')

@section('title', __('site.myAccount') . ' - ' . __('site.wallet'))

@section('meta_description', __('site.wallet'))


@section('content')


 <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('website.wallet.index') }}" class="breadcrumb-link"> {{ __('site.wallet') }} </a> 
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
              <h2 class="account-title">{{ __('site.wallet') }}</h2>
              <div class="wallet-actions">
                <button
                  class="wallet-btn"
                  data-bs-toggle="modal"
                  data-bs-target="#addCreditModal"
                >
                  {{ __('site.recharge_wallet') }}
                </button>
                <button
                  class="wallet-btn"
                  data-bs-toggle="modal"
                  data-bs-target="#withdrawModal"
                >
                  {{ __('site.withdraw_request') }}
                </button>
              </div>
            </div>
            <div class="wallet-info">
              <div class="wallet-item">
                <span class="item-icon">
                  <i class="fal fa-coins"></i>
                </span>
                <div class="item-info">
                  <strong class="total">
                    {{ number_format($balance, 2) }}
                    <span class="curreny">
                      <img
                        loading="lazy"
                        src="{{ asset('website/images/icons/sar.svg') }}"
                        alt="sar"
                        class="svg"
                      />
                    </span>
                  </strong>
                  <span class="title">{{ __('site.total_wallet') }}</span>
                </div>
              </div>
              <div class="wallet-item">
                <span class="item-icon">
                  <i class="fal fa-check-circle"></i>
                </span>
                <div class="item-info"> 
                  <strong class="total">
                    {{ number_format($withdrawableBalance, 2) }}
                    <span class="curreny">
                      <img
                        loading="lazy"
                        src="{{ asset('website/images/icons/sar.svg') }}"
                        alt="sar"
                        class="svg"
                      />
                    </span>
                  </strong>
                  <span class="title">{{ __('site.withdrawable_wallet') }}</span>
                </div>
              </div>
              <div class="wallet-item">
                <span class="item-icon">
                  <i class="fal fa-exclamation-circle"></i>
                </span>
                <div class="item-info">
                  <strong class="total">
                    {{ number_format($nonWithdrawableBalance, 2) }}
                    <span class="curreny">
                      <img
                        loading="lazy"
                        src="{{ asset('website/images/icons/sar.svg') }}"
                        alt="sar"
                        class="svg"
                      />
                    </span>
                  </strong>
                  <span class="title">{{ __('site.non_withdrawable_wallet') }}</span>
                </div>
              </div>
            </div>
            <div class="wallet-tables">
              <h2 class="account-title">{{ __('site.wallet_history') }}</h2>
              <div class="nav account-tabs">
                <button
                  type="button"
                  class="active"
                  data-bs-toggle="tab"
                  data-bs-target="#tab_1"
                >
                  {{ __('site.payments') }}
                </button>
                <button
                  type="button"
                  data-bs-toggle="tab"
                  data-bs-target="#tab_2"
                >
                  {{ __('site.credits') }}
                </button>
                <button
                  type="button"
                  data-bs-toggle="tab"
                  data-bs-target="#tab_3"
                >
                  {{ __('site.withdraw_request') }}
                </button>
              </div>

              <div class="tab-content">
                <div class="tab-pane fade show active" id="tab_1">
                  <div class="account-table-wrapper">
                    <table class="account-table fixed">
                      <thead>
                        <tr>
                          <th colspan="1">#</th>
                          <th colspan="3">{{ __('site.reference_number') }}</th>
                          <th colspan="3">{{ __('site.order_number') }}</th>
                          <th colspan="3">{{ __('site.payment_date') }}</th>
                          <th colspan="3">{{ __('site.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($paymentTransactions as $index => $transaction)
                        <tr>
                          <td colspan="1">{{ $index + 1 }}</td>
                          <td colspan="3">{{ $transaction->reference }}</td>
                          <td colspan="3">{{ $transaction->order?->order_number ?? '-' }}</td>
                          <td colspan="3">{{ $transaction->created_at->format('Y/m/d - H:i') }}</td>
                          <td colspan="3">
                            <span class="table-price">
                              {{ number_format($transaction->amount, 2) }}
                              <img
                                loading="lazy"
                                src="{{ asset('website/images/icons/sar.svg') }}"
                                alt="sar"
                                class="svg"
                              />
                            </span>
                          </td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="13" style="text-align: center;">{{ __('site.no_payment_transactions') }}</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="tab-pane fade" id="tab_2">
                  <div class="account-table-wrapper">
                    <table class="account-table fixed">
                      <thead>
                        <tr>
                          <th colspan="1">#</th>
                          <th colspan="3">{{ __('site.reference_number') }}</th>
                          <th colspan="3">{{ __('site.payment_date') }}</th>
                          <th colspan="3">{{ __('site.amount') }}</th>
                          <th colspan="3">{{ __('site.notes') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($addonTransactions as $index => $transaction)
                        <tr>
                          <td colspan="1">{{ $index + 1 }}</td>
                          <td colspan="3">{{ $transaction->reference }}</td>
                          <td colspan="3">{{ $transaction->created_at->format('Y/m/d - H:i') }}</td>
                          <td colspan="3">
                            <span class="table-price">
                              {{ number_format($transaction->amount, 2) }}
                              <img
                                loading="lazy"
                                src="{{ asset('website/images/icons/sar.svg') }}"
                                alt="sar"
                                class="svg"
                              />
                            </span>
                          </td>
                          <td colspan="3">
                              {{ $transaction->parseNote() ?? '-' }}
                              </td>
                        </tr>
                        @empty
                        <tr>
                  <td colspan="13" style="text-align: center;">{{ __('site.no_credits') }}</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="tab-pane fade" id="tab_3">
                  <div class="account-table-wrapper">
                    <table class="account-table">
                      <thead>
                        <tr>
                          <th colspan="1">#</th>
                          <th colspan="3">{{ __('site.reference_number') }}</th>
                          <th colspan="3">{{ __('site.order_creation_date') }}</th>
                          <th colspan="3">{{ __('site.amount') }}</th>
                          <th colspan="3">{{ __('site.order_status') }}</th>
                          <th colspan="3">{{ __('site.notes') }}</th>
                          <th colspan="3">{{ __('site.bank_account_data') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($withdrawTransactions as $index => $transaction)
                        <tr>
                          <td colspan="1">{{ $index + 1 }}</td>
                          <td colspan="3">{{ $transaction->reference }}</td>
                          <td colspan="3">{{ $transaction->created_at->format('Y/m/d - H:i') }}</td>
                          <td colspan="3">
                            <span class="table-price">
                              {{ number_format($transaction->amount, 2) }}
                              <img
                                loading="lazy"
                                src="{{ asset('website/images/icons/sar.svg') }}"
                                alt="sar"
                                class="svg"
                              />
                            </span>
                          </td>
                          <td colspan="3">
                            @if($transaction->status == 'pending')
                              <span class="badge bg-warning">{{ __('site.pending') }}</span>
                            @elseif($transaction->status == 'accepted')
                              <span class="badge bg-success">{{ __('site.accepted') }}</span>
                            @elseif($transaction->status == 'rejected')
                              <span class="badge bg-danger">{{ __('site.rejected') }}</span>
                            @else
                              {{ $transaction->status }}
                            @endif
                          </td>
                          <td colspan="3">
                            @if(is_array($transaction->note))
                              {{ $transaction->note['ar'] ?? $transaction->note['en'] ?? '-' }}
                            @else
                              {{ $transaction->note ?? '-' }}
                            @endif
                          </td>
                          <td colspan="3">
                            {{ $transaction->bank_name ?? '-' }}<br>
                            {{ $transaction->account_holder_name ?? '-' }}<br>
                            {{ $transaction->account_number ?? '-' }}<br>
                            {{ $transaction->iban ?? '-' }}
                          </td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="19" style="text-align: center;">{{ __('site.no_withdraw_transactions') }}</td>   
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>


    
    <!-- Start Add Credit Modal -->
    <div
      class="modal fade"
      id="addCreditModal"
      tabindex="-1"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <form action="{{ route('website.wallet.recharge') }}" method="POST" class="modal-form" id="rechargeForm">
            @csrf
            <div class="modal-header">
              <button type="button" class="modal-close" data-bs-dismiss="modal">
                <i class="far fa-xmark"></i>
              </button>
            </div>
            <div class="modal-body">
              <h2 class="modal-head">{{ __('site.recharge_wallet') }}</h2>

              <div class="form-group">
                <label class="form-label">{{ __('site.recharge_amount') }}</label>
                <input
                  type="number"
                  name="amount"
                  class="form-control"
                  data-rules="required|min:10|max:10000"
                  data-messages='{"required":"{{ __('site.amount_required') }}","min":"{{ __('site.min_amount_10') }}","max":"{{ __('site.max_amount_10000') }}"}'
                  step="0.01"
                />
              </div>

              <div class="form-group">
                <label class="form-label">{{ __('site.payment_method') }}</label>
                <div class="cart_radio-list">
                  <div class="cart_radio-item">
                       <label>
                      <input type="radio" name="gateway" value="visa_master" checked />
                      <span class="mark"> </span>
                      <span class="text"> فيزا/ ماستركارد </span>
                      <div class="images">
                        <img
                          src="{{ asset('website/images/payments/1.png') }}"
                          alt="1"
                          loading="lazy"
                        />
                        <img
                          src="{{ asset('website/images/payments/2.png') }}"
                          alt="2"
                          loading="lazy"
                        />
                      </div>
                    </label>
                  
                  </div>
                  <div class="cart_radio-item">
                        <label>
                      <input type="radio" name="gateway" value="mada" />
                      <span class="mark"> </span>
                      <span class="text"> مدى </span>
                      <div class="images">
                        <img
                          src="{{ asset('website/images/payments/3.png') }}"
                          alt="3"
                          loading="lazy"
                        />
                      </div>
                    </label>
                
                  </div>
                  <div id="gatewayError" class="text-danger mt-1" style="display:none;">{{ __('site.required_field') }}</div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="modal-btn">
                {{ __('site.complete_payment_recharge') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- End Add Credit Modal -->

    <!-- Start Withdraw Modal -->
    <div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <form action="{{ route('website.wallet.withdraw') }}" method="POST" class="modal-form" id="withdrawForm">
            @csrf
            <div class="modal-header">
              <button type="button" class="modal-close" data-bs-dismiss="modal">
                <i class="far fa-xmark"></i>
              </button>
            </div>
            <div class="modal-body">
              <h2 class="modal-head">{{ __('site.withdraw_request_title') }}</h2>

              <div class="form-group">
                <label class="form-label">{{ __('site.withdraw_amount') }}</label>
                <input
                  type="number"
                  name="amount"
                  class="form-control"
                  data-rules="required|min:10|max:{{ $withdrawableBalance }}"
                  data-messages='{"required":"{{ __('site.amount_required') }}","min":"{{ __('site.min_withdraw_amount') }}","max":"{{ __('site.max_withdraw_amount', ['amount' => number_format($withdrawableBalance, 2)]) }}"}'
                  step="0.01"
                  max="{{ $withdrawableBalance }}"
                />
                <small class="form-text text-muted">
                  {{ __('site.available_balance') }}: {{ number_format($withdrawableBalance, 2) }} {{ __('site.sar') }}
                </small>
              </div>

              <div class="form-group">
                <label class="form-label">{{ __('site.bank_name') }}</label>
                <input
                  type="text"
                  name="bank_name"
                  class="form-control"
                  data-rules="required"
                  data-messages='{"required":"{{ __('site.bank_name_required') }}"}'
                />
              </div>

              <div class="form-group">
                <label class="form-label">{{ __('site.account_holder_name') }}</label>
                <input
                  type="text"
                  name="account_holder_name"
                  class="form-control"
                  data-rules="required"
                  data-messages='{"required":"{{ __('site.account_holder_name_required') }}"}'
                />
              </div>

              <div class="form-group">
                <label class="form-label">{{ __('site.account_number') }}</label>
                <input
                  type="text"
                  name="account_number"
                  class="form-control"
                  data-rules="requiredWithout:iban|digits:10,18"
                  data-messages='{"requiredWithout":"{{ __('site.account_or_iban_required') }}","digits":"{{ __('site.account_number_digits') }}"}'
                />
              </div>

              <div class="form-group">
                <label class="form-label">{{ __('site.iban_number') }}</label>
                <input
                  type="text"
                  name="iban"
                  class="form-control"
                  data-rules="requiredWithout:account_number|regex:^SA[0-9]{2}[A-Z0-9]{20}$"
                  data-messages='{"requiredWithout":"{{ __('site.iban_or_account_required') }}","regex":"{{ __('site.iban_format_error') }}"}'
                  maxlength="24"
                  style="text-transform: uppercase;"
                />
              </div>

              <div class="form-group">
                <label class="form-label">{{ __('site.notes') }}</label>
                <textarea
                  name="note"
                  class="form-control"
                  rows="3"
                ></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="modal-btn">{{ __('site.send_request') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- End Withdraw Modal -->

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize form validator
    if (typeof window.TaraValidator !== 'undefined') {
      // Add custom validator for requiredWithout
      window.TaraValidator.addValidator('requiredWithout', function(value, otherField) {
        const form = document.querySelector('#withdrawForm');
        if (!form) return true;

        const otherInput = form.querySelector(`[name="${otherField}"]`);
        if (!otherInput) return true;

        const otherValue = otherInput.value.trim();

        // If other field has value, this field is optional
        if (otherValue) return true;

        // If other field is empty, this field is required
        return value && value.trim() !== '';
      }, 'هذا الحقل مطلوب');

      // Add custom validator for digits range
      window.TaraValidator.addValidator('digits', function(value, range) {
        if (!value) return true; // Skip if empty (handled by required)

        const [min, max] = range.split(',').map(Number);
        const digits = value.replace(/\D/g, ''); // Remove non-digits
        const length = digits.length;

        return length >= min && length <= max;
      }, 'يجب أن يكون الرقم بين {min} و {max} رقم');

      // Initialize both forms
      window.TaraValidator.initForm('#rechargeForm');
      window.TaraValidator.initForm('#withdrawForm');

      // Add real-time validation for IBAN uppercase
      const ibanInput = document.querySelector('#withdrawForm input[name="iban"]');
      if (ibanInput) {
        ibanInput.addEventListener('input', function() {
          this.value = this.value.toUpperCase();
        });
      }

      // Handle requiredWithout validation on both fields
      const accountNumberInput = document.querySelector('#withdrawForm input[name="account_number"]');
      const ibanInputField = document.querySelector('#withdrawForm input[name="iban"]');

      if (accountNumberInput && ibanInputField) {
        accountNumberInput.addEventListener('input', function() {
          // Revalidate IBAN when account number changes
          setTimeout(() => window.TaraValidator.validateField(ibanInputField), 100);
        });

        ibanInputField.addEventListener('input', function() {
          // Revalidate account number when IBAN changes
          setTimeout(() => window.TaraValidator.validateField(accountNumberInput), 100);
        });
      }
    }

    // Grouped validation for payment methods (gateway)
    const rechargeForm = document.querySelector('#rechargeForm');
    if (rechargeForm) {
      const gatewayInputs = rechargeForm.querySelectorAll('input[name="gateway"]');
      const gatewayError = rechargeForm.querySelector('#gatewayError');

      const hideGatewayError = () => {
        if (gatewayError) gatewayError.style.display = 'none';
      };
      const showGatewayError = () => {
        if (gatewayError) gatewayError.style.display = 'block';
      };

      gatewayInputs.forEach((input) => {
        input.addEventListener('change', hideGatewayError);
      });

      rechargeForm.addEventListener('submit', function (e) {
        const hasSelection = Array.from(gatewayInputs).some((i) => i.checked);
        if (!hasSelection) {
          e.preventDefault();
          showGatewayError();
        }
      });
    }
  });
</script>
@endpush