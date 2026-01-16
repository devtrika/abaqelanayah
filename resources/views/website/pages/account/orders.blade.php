@extends('website.layouts.app')

@section('title', __('site.myAccount') . ' - ' . __('site.orders'))

@section('meta_description', __('site.orders'))

@section('content')

  <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('website.orders') }}" class="breadcrumb-link"> {{ __('site.orders') }} </a>
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
              <h2 class="account-title">{{ __('site.orders') }}</h2>
            </div>
            @php
              $statusColors = [
                'pending' => '#ffbe00',
                'processing' => '#34b7ea',
                'new' => '#34b7ea',
                'confirmed' => '#6667a7',
                'delivered' => '#66a61b',
                'problem' => '#9b9b9b',
                'cancelled' => '#ed1d24',
                'request_cancel' => '#ed1d24',
                // Refund lifecycle statuses
                'request_refund' => '#f59e0b',
                'out-for-delivery' => '#3b82f6',
                'refunded' => '#666',
                'request_rejected' => '#ed1d24',
              ];
              $typeLabels = ['gift' => __('site.gift'), 'ordinary' => __('site.ordinary')];
            @endphp
            <div class="nav account-tabs">
              <button
                type="button"
                class="{{ ($activeTab ?? 'ongoing') === 'ongoing' ? 'active' : '' }}"
                data-bs-toggle="tab"
                data-bs-target="#tab_1"
              >
                {{ __('site.tab_ongoing') }}
              </button>
              <button
                type="button"
                class="{{ ($activeTab ?? 'ongoing') === 'completed' ? 'active' : '' }}"
                data-bs-toggle="tab"
                data-bs-target="#tab_2"
              >
                {{ __('site.tab_completed') }}
              </button>
              <button
                type="button"
                class="{{ ($activeTab ?? 'ongoing') === 'cancelled' ? 'active' : '' }}"
                data-bs-toggle="tab"
                data-bs-target="#tab_3"
              >
                {{ __('site.tab_cancelled') }}
              </button>
            </div>

            <div class="tab-content">
              <div class="tab-pane fade {{ ($activeTab ?? 'ongoing') === 'ongoing' ? 'show active' : '' }}" id="tab_1">
                <div class="account-table-wrapper">
                  <table class="account-table less-padding">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>{{ __('site.order_number') }}</th>
                        <th>{{ __('site.order_date') }}</th>
                        <th>{{ __('site.type') }}</th>
                        <th>{{ __('site.delivery') }}</th>
                        <th>{{ __('site.order_status') }}</th>
                        <th>{{ __('site.total') }}</th>
                        <th>{{ __('site.invoice') }}</th>
                        <th>{{ __('site.view') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($ongoingOrders ?? [] as $order)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->created_at ? $order->created_at->format('Y/m/d - H:i') : '' }}</td>
                        <td>{{ $typeLabels[$order->order_type ?? 'ordinary'] ?? __('site.ordinary') }}</td>
                        <td>{{ __('admin.' . ($order->delivery_type ?? 'home_delivery')) }}</td>
                        <td>
                          @php $effectiveStatus = $order->refund_status ?: $order->status; @endphp
                          <span class="table-status" style="--color: {{ $statusColors[$effectiveStatus] ?? '#999' }}">
                            {{ __('admin.' . $effectiveStatus) }}
                          </span>
                        </td>
                        <td>
                          <span class="table-price">
                            {{ number_format($order->total ?? 0, 2) }}
                            <img
                              loading="lazy"
                              src="{{ asset('website/images/icons/sar.svg') }}"
                              alt="sar"
                              class="svg"
                            />
                          </span>
                        </td>
                        <td>
                          <a href="{{ route('website.orders.invoice', $order->id) }}" class="table-download">{{ __('site.download') }}</a>
                        </td>
                        <td>
                          <div class="table-actions">
                            <a href="{{ route('website.orders.show', $order->id) }}" class="table-action"><i class="fal fa-eye"></i></a>
                          </div>
                        </td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="9" class="text-center">{{ __('site.no_orders') }}</td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="tab-pane fade {{ ($activeTab ?? 'ongoing') === 'completed' ? 'show active' : '' }}" id="tab_2">
                <div class="account-table-wrapper">
                  <table class="account-table less-padding">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>{{ __('site.order_number') }}</th>
                        <th>{{ __('site.order_date') }}</th>
                        <th>{{ __('site.type') }}</th>
                        <th>{{ __('site.delivery') }}</th>
                        <th>{{ __('site.order_status') }}</th>
                        <th>{{ __('site.total') }}</th>
                        <th>{{ __('site.invoice') }}</th>
                        <th>{{ __('site.view') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($completedOrders ?? [] as $order)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->created_at ? $order->created_at->format('Y/m/d - H:i') : '' }}</td>
                        <td>{{ $typeLabels[$order->order_type ?? 'ordinary'] ?? __('site.ordinary') }}</td>
                        <td>{{ __('admin.' . ($order->delivery_type ?? 'home_delivery')) }}</td>
                        <td>
                          <span class="table-status" style="--color: {{ $statusColors[$order->status] ?? '#66a61b' }}">
                            {{ __('admin.' . $order->status) }}
                          </span>
                        </td>
                        <td>
                          <span class="table-price">
                            {{ number_format($order->total ?? 0, 2) }}
                            <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}" alt="sar" class="svg" />
                          </span>
                        </td>
                        <td>
                          <a href="{{ route('website.orders.invoice', $order->id) }}" class="table-download">{{ __('site.download') }}</a>
                        </td>
                        <td>
                          <div class="table-actions">
                            <a href="{{ route('website.orders.show', $order->id) }}" class="table-action"><i class="fal fa-eye"></i></a>
                          </div>
                        </td>
                     
                      </tr>
                      @empty
                      <tr>
                        <td colspan="10" class="text-center">{{ __('site.no_orders') }}</td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="tab-pane fade {{ ($activeTab ?? 'ongoing') === 'cancelled' ? 'show active' : '' }}" id="tab_3">
                <div class="account-table-wrapper">
                  <table class="account-table less-padding">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>{{ __('site.order_number') }}</th>
                        <th>{{ __('site.order_date') }}</th>
                        <th>{{ __('site.type') }}</th>
                        <th>{{ __('site.delivery') }}</th>
                        <th>{{ __('site.order_status') }}</th>
                        <th>{{ __('site.total') }}</th>
                        <th>{{ __('site.invoice') }}</th>
                        <th>{{ __('site.view') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($cancelledOrders ?? [] as $order)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->created_at ? $order->created_at->format('Y/m/d - H:i') : '' }}</td>
                        <td>{{ $typeLabels[$order->order_type ?? 'ordinary'] ?? __('site.ordinary') }}</td>
                        <td>{{ __('admin.' . ($order->delivery_type ?? 'home_delivery')) }}</td>
                        <td>
                          @php $effectiveStatus = $order->refund_status ?: $order->status; @endphp
                          <span class="table-status" style="--color: {{ $statusColors[$effectiveStatus] ?? '#ed1d24' }}">
                            {{ __('admin.' . $effectiveStatus) }}
                          </span>
                        </td>
                        <td>
                          <span class="table-price">
                            {{ number_format($order->total ?? 0, 2) }}
                            <img loading="lazy" src="{{ asset('website/images/icons/sar.svg') }}" alt="sar" class="svg" />
                          </span>
                        </td>
                        <td>
                          <a href="{{ route('website.orders.invoice', $order->id) }}" class="table-download">{{ __('site.download') }}</a>
                        </td>
                        <td>
                          <div class="table-actions">
                            <a href="{{ route('website.orders.show', $order->id) }}" class="table-action"><i class="fal fa-eye"></i></a>
                          </div>
                        </td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="9" class="text-center">{{ __('site.no_orders') }}</td>
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
    </section>

@endsection