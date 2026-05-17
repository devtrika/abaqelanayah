@extends('website.layouts.app')

@section('title', __('site.track_order'))

@section('content')
<main class="page-content">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4 text-primary">{{ __('site.track_order') }}</h2>
                        <p class="text-center text-muted mb-4">{{ __('site.enter_order_number_to_track') }}</p>

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('website.track-order.track') }}" method="POST">
                            @csrf
                            <div class="form-group mb-4">
                                <label for="order_number" class="form-label fw-bold">{{ __('site.order_number') }}</label>
                                <input type="text" name="order_number" id="order_number" class="form-control form-control-lg @error('order_number') is-invalid @enderror" value="{{ old('order_number') }}" placeholder="{{ __('site.order_number_placeholder') }}" required>
                                @error('order_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill">{{ __('site.track_now') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
