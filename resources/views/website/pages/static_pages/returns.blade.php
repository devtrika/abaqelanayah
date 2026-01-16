@extends('website.layouts.app')

@section('title', __('site.return_policy') . ' - Lia')

@section('meta_description', __('site.return_policy'))
@section('content')

 <!-- Start Breadcrumb -->
    <section class="breadcrumb-section">
      <div class="container">
        <ul class="breadcrumb-list">
          <li class="breadcrumb-item">
            <a href="{{ route('website.home') }}" class="breadcrumb-link"> {{ __('site.home') }} </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('website.returns') }}" class="breadcrumb-link"> {{ __('site.return_policy') }} </a>
          </li>
        </ul>
      </div>
    </section>
    <!-- End Breadcrumb -->

    <section class="page-content">
      <div class="container">
        <div class="section-head page-head">
          <h1 class="section-title">{{ __('site.return_policy') }}</h1>
        </div>

        <div class="info-content">
          {!! $content !!}
        </div>
      </div>
    </section>

@endsection