     @if($order->branch)
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-info text-white py-3">
            <h5 class="card-title mb-0 text-white">
                <i class="feather icon-map-pin mr-2 text-white"></i>
                {{ __('admin.branch_information') }}
            </h5>
        </div>
        <div class="card-body p-3">
            <table class="table table-borderless mb-0">

                  <tr>
                    <th>{{ __('admin.name') }}:</th>
                    <td>{{ $order->branch->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>{{ __('admin.last_order_time') }}:</th>
                    <td>{{ $order->branch->last_order_time ?? '-' }}</td>
                </tr>
                <tr>
                    <th>{{ __('admin.expected_duration') }}:</th>
                    
                    <td>{{ $order->branch->expected_duration }} {{ __('admin.minute') }}
</td>
                </tr>

                
              
                @if($order->branch->latitude && $order->branch->longitude)
                <tr>
                    <th>{{ __('admin.location') }}:</th>
                    <td>
                        <a href="https://maps.google.com/?q={{ $order->branch->latitude }},{{ $order->branch->longitude }}"
                           target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="feather icon-map-pin"></i> {{ __('admin.view_on_map') }}
                        </a>
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>
    @endif