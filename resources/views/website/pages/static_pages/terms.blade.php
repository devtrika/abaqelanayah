@extends('website.layouts.app')

@section('title', __('site.terms_and_conditions') )
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
            <a href="{{ route('website.terms') }}" class="breadcrumb-link"> {{ __('site.terms_and_conditions') }} </a>
          </li>
        </ul>
      </div>
    </section>
    <!-- End Breadcrumb -->

    <section class="page-content">
      <div class="container">
        <div class="section-head page-head">
          <h1 class="section-title">{{ __('site.terms_and_conditions') }}</h1>
        </div>
        <div class="info-content">
          {!! $content !!}
        </div>
      </div>
    </section>
@endsection