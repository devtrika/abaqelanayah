<!-- Location Maps -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-info text-white py-3">
        <h5 class="card-title mb-0 text-white">
            <i class="feather icon-map mr-2 text-white"></i>
            {{ __('admin.location_maps') }}
        </h5>
    </div>
    <div class="card-body p-3">
        <div class="row">
            <!-- User Location Map -->
            @if($order->user && $order->user->lat && $order->user->lng)
            <div class="col-md-12 mb-3">
                <div class="border rounded p-3">
                    <h6 class="mb-3">
                        <i class="feather icon-user mr-2 text-primary"></i>
                        {{ __('admin.user_location') }}
                    </h6>
                    <div id="userMap" style="height: 250px; border-radius: 8px; border: 1px solid #e9ecef;"></div>
                    <div class="mt-3 pt-2 border-top">
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1 small text-muted">{{ __('admin.name') }}</p>
                                <p class="mb-2 font-weight-medium">{{ $order->user->name }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 small text-muted">{{ __('admin.phone') }}</p>
                                <p class="mb-2 font-weight-medium">{{ $order->user->phone }}</p>
                            </div>
                        </div>
                        <p class="mb-0 small text-muted">
                            <i class="feather icon-map-pin mr-1"></i>
                            {{ $order->user->lat }}, {{ $order->user->lng }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Client Address Location Map -->
            @if($order->address && $order->address->latitude && $order->address->longitude)
            <div class="col-md-6 mb-3">
                <div class="border rounded p-3">
                    <h6 class="mb-3">
                        <i class="feather icon-map-pin mr-2 text-danger"></i>
                        {{ __('admin.delivery_address') }}
                    </h6>
                    <div id="clientMap" style="height: 250px; border-radius: 8px; border: 1px solid #e9ecef;"></div>
                    <div class="mt-3 pt-2 border-top">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <p class="mb-1 small text-muted">{{ __('admin.address') }}</p>
                                <p class="mb-0 font-weight-medium">{{ $order->address->details ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 small text-muted">{{ __('admin.phone') }}</p>
                                <p class="mb-2 font-weight-medium">{{ $order->address->phone ?? '-' }}</p>
                            </div>
                        </div>
                        <p class="mb-0 small text-muted">
                            <i class="feather icon-map-pin mr-1"></i>
                            {{ $order->address->latitude }}, {{ $order->address->longitude }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

        </div>

        <!-- Provider Location Map (Full Width) -->
        @if($order->providerSubOrders && $order->providerSubOrders->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="border rounded p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">
                            <i class="feather icon-users mr-2 text-success"></i>
                            {{ __('admin.provider_location') }}
                            @if($order->providerSubOrders->count() > 1)
                                <span class="badge badge-info ml-2">{{ $order->providerSubOrders->count() }}</span>
                            @endif
                        </h6>
                    </div>
                    @if($order->providerSubOrders->count() > 1)
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">{{ __('admin.filter_by_provider') }}</label>
                        <select id="providerFilter" class="form-control">
                            <option value="">{{ __('admin.show_all_providers') }}</option>
                            @foreach($order->providerSubOrders as $index => $subOrder)
                                @if($subOrder->provider && $subOrder->provider->lat && $subOrder->provider->lng)
                                <option value="{{ $index }}"
                                        data-lat="{{ $subOrder->provider->lat }}"
                                        data-lng="{{ $subOrder->provider->lng }}"
                                        data-name="{{ $subOrder->provider->commercial_name ?? $subOrder->provider->user->name }}"
                                        data-phone="{{ $subOrder->provider->user->phone }}"
                                        data-address="{{ $subOrder->provider->map_desc ?? '' }}">
                                    {{ $subOrder->provider->commercial_name ?? $subOrder->provider->user->name }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div id="providerMap" style="height: 300px; border-radius: 8px; border: 1px solid #e9ecef;"></div>

                    <!-- Dynamic Provider Info (for filtered selection) -->
                    <div id="providerInfo" class="mt-3 pt-2 border-top" style="display: none;">
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1 small text-muted">{{ __('admin.provider_name') }}</p>
                                <p class="mb-2 font-weight-medium" id="providerName"></p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 small text-muted">{{ __('admin.phone') }}</p>
                                <p class="mb-2 font-weight-medium" id="providerPhone"></p>
                            </div>
                            <div class="col-12">
                                <p class="mb-1 small text-muted">{{ __('admin.address') }}</p>
                                <p class="mb-2 font-weight-medium" id="providerAddress"></p>
                            </div>
                        </div>
                        <p class="mb-0 small text-muted">
                            <i class="feather icon-map-pin mr-1"></i>
                            <span id="providerCoords"></span>
                        </p>
                    </div>

                    <!-- Single Provider Info (always visible for single provider) -->
                    @if($order->providerSubOrders->count() === 1)
                        @php $provider = $order->providerSubOrders->first()->provider; @endphp
                        @if($provider && $provider->lat && $provider->lng)
                        <div class="mt-3 pt-2 border-top">
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-1 small text-muted">{{ __('admin.provider_name') }}</p>
                                    <p class="mb-2 font-weight-medium">{{ $provider->commercial_name ?? $provider->user->name }}</p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1 small text-muted">{{ __('admin.phone') }}</p>
                                    <p class="mb-2 font-weight-medium">{{ $provider->user->phone }}</p>
                                </div>
                                <div class="col-12">
                                    <p class="mb-1 small text-muted">{{ __('admin.address') }}</p>
                                    <p class="mb-2 font-weight-medium">{{ $provider->map_desc ?? '-' }}</p>
                                </div>
                            </div>
                            <p class="mb-0 small text-muted">
                                <i class="feather icon-map-pin mr-1"></i>
                                {{ $provider->lat }}, {{ $provider->lng }}
                            </p>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
