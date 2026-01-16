@extends('website.layouts.app')

@section('title', __('site.about') . ' - Lia')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
@endpush    


@section('meta_description', __('site.cart'))
@section('content')
 <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('website.about') }}" class="breadcrumb-link"> {{ __('site.about') }} </a>
          </li>
        </ul>
      </div>
    </section>
    <!-- End Breadcrumb -->

    <section class="page-content">
      <div class="container">
        <div class="about-content">
          <div class="about-info">
            <div class="section-head page-head">
              <h1 class="section-title">{{ __('site.about') }}</h1>
            </div>

            <div class="info-content">
              {!! $content !!}
            </div>
          </div>
          <div class="about-img">
            <img src="{{ asset('website/images/about.svg') }}" alt="{{ __('site.about') }}" class="img-cover" />
          </div>
        </div>
      </div>
    </section>

@endsection