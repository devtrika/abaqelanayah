@extends('website.layouts.app')

@section('title', __('site.order_success') )

@section('meta_description', __('site.order_success'))
@section('content')


    <section class="page-content">
      <div class="container">
        <div class="success-content">
          <span class="success-icon">
            <i class="fat fa-check-circle"></i>
          </span>
          <h1 class="success-title">{{ __('site.thank_you_for_purchase') }}</h1>
          <p class="success-order">
            <span>{{ __('site.your_order_number_is') }}</span>
            <strong>{{ $orderNumber ?? '' }}</strong>
          </p>
           <p class="success-desc">
           {{ __('site.order_confirmation_email_note') }}
          </p>
        
          <a href="{{ route('website.orders') }}" class="submit-btn"> {{ __('site.my_orders') }} </a>
          <a href="{{ route('website.home') }}" class="submit-btn second-btn"> {{ __('site.continue_shopping') }} </a>
        </div>
      </div>
    </section>
@endsection