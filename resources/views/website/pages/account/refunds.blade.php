@extends('website.layouts.app')

@section('title', __('site.myAccount') . ' - ' . __('site.refunds'))

@section('meta_description', __('site.refunds'))

@section('content')


    <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('website.refunds.index') }}" class="breadcrumb-link">
              {{ __('site.refunds') }}
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
              <h2 class="account-title">{{ __('site.refunds') }}</h2>
            </div>
            <div class="account-table-wrapper">
              <table class="account-table fixed">
                <thead>
                  <tr>
                    <th colspan="1">#</th>
                    <th colspan="3">{{ __('site.order_number') }}</th>
                    <th colspan="3">{{ __('site.order_date') }}</th>
                    <th colspan="3">{{ __('site.order_status') }}</th>
                    <th colspan="2" class="text-center">{{ __('site.view') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $statusColors = [
                      'request_refund' => '#f5a623',
                      'new' => '#f5a623',
                      'confirmed' => '#f5a623',
                      'delivered' => '#66a61b',
                      'refunded' => '#66a61b',
                      'request_rejected' => '#ed1d24',
                    ];
                  @endphp

                  @forelse($refundOrders as $idx => $order)
                    @php 
                      $displayStatus = $order->refund_status ?? $order->status;
                      $color = $statusColors[$displayStatus] ?? '#999'; 
                    @endphp
                    <tr>
                      <td colspan="1">{{ $idx + 1 }}</td>
                      <td colspan="3">{{ $order->order_number ?? $order->refund_number }}</td>
                      <td colspan="3">{{ optional($order->created_at)->format('Y/m/d - H:i') }}</td>
                      <td colspan="3">
                        <span class="table-status" style="--color: {{ $color }}">
                          {{ __('admin.' . $displayStatus) }}
                        </span>
                      </td>
                      <td colspan="2">
                        <div class="table-actions">
                          <a href="{{ route('website.refunds.show', $order->id) }}" class="table-action">
                            <i class="fal fa-eye"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="12" class="text-center"> {{ __('site.no_refunds_yet') }} </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

@endsection