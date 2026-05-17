@extends('website.layouts.app')

@section('title', __('site.track_order'))

@section('content')
<main class="page-content">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-5 d-flex flex-column justify-content-center align-items-center text-center">
                        <h2 class="mb-4" style="color: #000000e9;">{{ __('site.track_order') }}</h2>
                        <p class="mb-4" style="color: #000000e9;">{{ __('site.enter_order_number_to_track') }}</p>

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('website.track-order.track') }}" method="POST" class="w-100">
                            @csrf
                            <div class="form-group mb-4">
                                <label for="order_number" class="form-label fw-bold" style="color: #000000e9;">{{ __('site.order_number') }}</label>
                                <input type="text" name="order_number" id="order_number" class="form-control form-control-lg border-warning @error('order_number') is-invalid @enderror" value="{{ old('order_number') }}" placeholder="{{ __('site.order_number_placeholder') }}" required>
                                @error('order_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-lg rounded-pill" style="background-color: #FFDD04; border-color: #FFDD04; color: #000;">{{ __('site.track_now') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
