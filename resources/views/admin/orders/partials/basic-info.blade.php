<!-- Basic Order Information & Customer Information -->
<div class="row">
    <!-- Basic Order Information -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-info text-white py-3">
                <h5 class="card-title mb-0 text-white">
                    <i class="feather icon-info mr-2 text-white"></i>
                    {{ __('admin.basic_information') }}
                </h5>
            </div>
            <div class="card-body p-3">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="40%">{{ __('admin.order_number') }}:</th>
                        <td><span class="badge badge-info">{{ $order->order_number ?? $order->order_num }}</span></td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.order_date') }}:</th>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    {{-- <tr>
                        <th>{{ __('admin.booking_type') }}:</th>
                        <td>
                            @if($order->booking_type)
                                <span class="badge badge-secondary">{{ __('admin.' . $order->booking_type) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr> --}}
                    {{-- <tr>
                        <th>{{ __('admin.city') }}:</th>
                        <td>
                            @if($order->user->city)
                                <span class="badge badge-secondary">{{  $order->user->city->name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr> --}}
                    <tr>
                        <th>{{ __('admin.delivery_type') }}:</th>
                        <td>
                            @if($order->delivery_type)
                                <span class="badge badge-secondary">{{ __('admin.' . $order->delivery_type) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @if($order->scheduled_at)
                    <tr>
                        <th>{{ __('admin.scheduled_at') }}:</th>
                        <td>{{ $order->scheduled_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                    @if($order->current_status === 'cancelled' && $order->cancelReason)
                    <tr>
                        <th>{{ __('admin.cancel_reason') }}:</th>
                        <td>
                            @php
                                $reasonData = json_decode($order->cancelReason->reason, true);
                                $reasonText = $reasonData[app()->getLocale()] ?? $reasonData['en'] ?? 'Unknown';
                            @endphp
                            <div class="alert alert-danger py-2 px-3 mb-0">
                                <i class="feather icon-x-circle mr-2"></i>
                                <span class="font-italic">"{{ $reasonText }}"</span>
                            </div>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-info text-white py-3">
                <h5 class="card-title mb-0 text-white">
                    <i class="feather icon-user mr-2 text-white"></i>
                    {{ __('admin.customer_information') }}
                </h5>
            </div>
            <div class="card-body p-3">
                @if($order->user)
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="40%">{{ __('admin.name') }}:</th>
                        <td>{{ $order->user->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.phone') }}:</th>
                        <td>{{ $order->user->phone }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.email') }}:</th>
                        <td>{{ $order->user->email ?? '-' }}</td>
                    </tr>
                    @if($order->user->wallet_balance)
                    <tr>
                        <th>{{ __('admin.wallet_balance') }}:</th>
                        <td>{{ number_format($order->user->wallet_balance, 2) }} {{ __('admin.sar') }}</td>
                    </tr>
                    @endif
                    @if($order->user->lat && $order->user->lng)
                    <tr>
                        <th>{{ __('admin.user_location') }}:</th>
                        <td>
                            <a href="https://maps.google.com/?q={{ $order->user->lat }},{{ $order->user->lng }}"
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="feather icon-map-pin"></i> {{ __('admin.view_on_map') }}
                            </a>
                        </td>
                    </tr>
                    @endif
                </table>
                @else
                <p class="text-muted">{{ __('admin.no_customer_info') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
