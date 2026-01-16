<!-- Provider Sub-Orders -->
@if($order->providerSubOrders && $order->providerSubOrders->count() > 0)
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-info text-white py-3">
        <h5 class="card-title mb-0 text-white">
            <i class="feather icon-users mr-2 text-white"></i>
            {{ __('admin.order_items') }}
            <span class="badge badge-warning ml-2">{{ $order->providerSubOrders->count() }}</span>
        </h5>
        @if($order->hasMultipleProviders())
            <small class="text-white-75 mt-1 d-block">{{ __('admin.multi_provider_order_note') }}</small>
        @endif
    </div>
    <div class="card-body p-0">
        @foreach($order->providerSubOrders as $subOrder)
        <div class="provider-sub-order border-bottom">
            <div class="row no-gutters">
                <!-- Provider Info -->
                <div class="col-md-4 border-right">
                    <div class="p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="provider-avatar mr-3">
                                @php
                                    $providerLogo = $subOrder->provider->getFirstMediaUrl('logo', 'thumb');
                                @endphp
                                @if($providerLogo)
                                    <img src="{{ $providerLogo }}" alt="{{ $subOrder->provider->commercial_name ?? $subOrder->provider->user->name }}" class="rounded-circle" style="width:45px;height:45px;object-fit:cover;">
                                @else
                                    <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:45px;height:45px;border-radius:50%;">
                                        {{ substr($subOrder->provider->commercial_name ?? $subOrder->provider->user->name, 0, 2) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-1 font-weight-bold">{{ $subOrder->provider->commercial_name ?? $subOrder->provider->user->name }}</h6>
                                <small class="text-muted">{{ $subOrder->sub_order_number }}</small>
                            </div>
                        </div>
                        <div class="provider-details">
                            <p class="mb-2 small"><i class="feather icon-phone mr-2 text-primary"></i> {{ $subOrder->provider->user->phone }}</p>
                            <p class="mb-2 small"><i class="feather icon-mail mr-2 text-info"></i> {{ $subOrder->provider->user->email ?? '-' }}</p>
                            <div class="provider-type">
                                @if($subOrder->provider->in_home && $subOrder->provider->in_salon)
                                    <span class="badge badge-success badge-sm">{{ __('admin.home_and_salon') }}</span>
                                @elseif($subOrder->provider->in_home)
                                    <span class="badge badge-info badge-sm">{{ __('admin.home_service') }}</span>
                                @elseif($subOrder->provider->in_salon)
                                    <span class="badge badge-primary badge-sm">{{ __('admin.salon_service') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="col-md-5 border-right">
                    <div class="p-3">
                        <h6 class="mb-2">{{ __('admin.order_items') }}</h6>
                        @php
                            // Get items for this provider - try relationship first, then fallback to filtering main order items
                            $providerItems = $subOrder->orderItems;
                            if ($providerItems->isEmpty()) {
                                $providerItems = $order->items->filter(function($item) use ($subOrder) {
                                    if ($item->item && isset($item->item->provider_id)) {
                                        return $item->item->provider_id == $subOrder->provider_id;
                                    }
                                    return false;
                                });
                            }
                        @endphp

                        @if($providerItems && $providerItems->count() > 0)
                            <div class="items-list">
                                @foreach($providerItems as $item)
                                <div class="item-row mb-3 p-2 border rounded">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="item-info flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                @if($item->item_type === 'App\\Models\\Product' && $item->item)
                                                    <img src="{{ $item->item->getFirstMediaUrl('product-images', 'thumb') }}" alt="{{ $item->name }}" class="mr-2 rounded" style="width:40px;height:40px;object-fit:cover;">
                                                @endif
                                                <div>
                                                    <span class="item-name font-weight-medium d-block">{{ $item->name }}</span>
                                                    @if($item->item_type === 'App\\Models\\Service')
                                                        <span class="badge badge-info badge-sm">{{ __('admin.service') }}</span>
                                                    @else
                                                        <span class="badge badge-success badge-sm">{{ __('admin.product') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="item-details">
                                                <small class="text-muted d-block">{{ __('admin.quantity') }}: {{ $item->quantity }} × {{ number_format($item->price, 2) }}</small>

                                                @if($item->item_type === 'App\\Models\\Service' && $item->item && $item->item->expected_time_to_accept)
                                                    <small class="text-info d-block mt-1">
                                                        <i class="feather icon-clock mr-1"></i>
                                                        {{ __('admin.expected_time_to_accept') }}: {{ $item->item->expected_time_to_accept }} {{ __('admin.minutes') }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="item-total text-right">
                                            <strong class="text-primary">{{ number_format($item->total, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small">{{ __('admin.no_items') }}</p>
                            {{-- Debug info --}}
                            <small class="text-danger">Debug: Provider ID {{ $subOrder->provider_id }}, Total Order Items: {{ $order->items->count() }}</small>
                        @endif
                    </div>
                </div>

                <!-- Status & Actions -->
                <div class="col-md-3">
                    <div class="p-3">
                        <div class="status-section mb-3">
                            @php
                                $statusColors = [
                                    'pending_payment' => 'warning',
                                    'processing' => 'info',
                                    'confirmed' => 'primary',
                                    'completed' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $color = $statusColors[$subOrder->status] ?? 'secondary';
                            @endphp
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge badge-{{ $color }}">
                                    {{ __('admin.' . $subOrder->status) }}
                                </span>
                            </div>

                            @if($subOrder->status === 'cancelled' && $order->cancelReason)
                            <div class="cancellation-reason mb-2">
                                <small class="text-danger d-block">
                                    <i class="feather icon-info mr-1"></i>
                                    {{ __('admin.cancel_reason') }}:
                                </small>
                                @php
                                    $reasonData = json_decode($order->cancelReason->reason, true);
                                    $reasonText = $reasonData[app()->getLocale()] ?? $reasonData['en'] ?? 'Unknown';
                                @endphp
                                <small class="text-muted font-italic">
                                    "{{ $reasonText }}"
                                </small>
                            </div>
                            @endif

                            <!-- Provider Total -->
                            <div class="provider-total mb-2">
                                <small class="text-muted">{{ __('admin.provider_total') }}:</small>
                                <div class="font-weight-bold text-success">{{ number_format($subOrder->total, 2) }} {{ __('admin.sar') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
