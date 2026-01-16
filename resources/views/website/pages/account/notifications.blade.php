@extends('website.layouts.app')

@section('title', __('site.myAccount') . ' - ' . __('site.notifications'))

@section('meta_description', __('site.notifications'))

@section('content')
 <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('website.notifications') }}" class="breadcrumb-link"> {{ __('site.notifications') }} </a>
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
              <h2 class="account-title">{{ __('site.notifications') }}</h2>
              <form method="POST" action="{{ route('website.notifications.delete') }}">
                @csrf
                <button type="submit" class="notifications-btn"> {{ __('site.delete_all_notifications') }} </button>
              </form>
            </div>
            <div class="notifications-list">
              @forelse ($notifications as $notification)
                @php
                  $isUnread = empty($notification->read_at);
                  $data = is_array($notification->data) ? $notification->data : [];
                  $goUrl = route('website.notifications.go', $notification->id);
                @endphp
                <a href="{{ $goUrl }}" class="notification-item borderd {{ $isUnread ? 'unread' : 'read' }}">
                  <span class="item-icon">
                    <img
                      src="{{ asset('website/images/icons/notifications.png') }}"
                      alt="notification"
                      class="img-contain"
                    />
                  </span>
                  <div class="item-info">
                    <p class="item-title">{{ $notification->body ?? ($data['body'] ?? '') }}</p>
                    <span class="item-date">{{ $notification->created_at ? $notification->created_at->format('Y/m/d - H:i') : '' }}</span>
                  </div>
                </a>
              @empty
                <div class="notification-item borderd">
                  <div class="item-info">
                    <p class="item-title">{{ __('site.no_notifications') }}</p>
                  </div>
                </div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </section>

@endsection