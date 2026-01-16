<div class="position-relative">
    {{-- table loader  --}}
    {{-- <div class="table_loader">
        {{ __('admin.loading') }}
    </div> --}}
    {{-- table loader  --}}

    {{-- table content --}}
    
 <table class="table" id="tab">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ __('admin.order_number') }}</th>
            <th>{{ __('admin.user') }}</th>
            <th>{{ __('admin.final_total') }}</th>
            <th>{{ __('admin.created_at') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $index => $order)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="badge badge-info" target="_blank">
                        {{ $order->order_number ?? $order->order_num }}
                    </a>
                </td>
                <td>
                    @if($order->user)
                        <div>
                            <strong>{{ $order->user->name }}</strong><br>
                            <small class="text-muted">{{ $order->user->phone }}</small>
                        </div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                
                <td>
                    <strong>{{ number_format($order->final_total ?? $order->total, 2) }} {{ __('admin.sar') }}</strong>
                    @if($order->services_total > 0 || $order->products_total > 0)
                        <br>
                        <small class="text-muted">
                            @if($order->services_total > 0)
                                {{ __('admin.services') }}: {{ number_format($order->services_total, 2) }}
                            @endif
                            @if($order->products_total > 0)
                                @if($order->services_total > 0) | @endif
                                {{ __('admin.products') }}: {{ number_format($order->products_total, 2) }}
                            @endif
                        </small>
                    @endif
                </td>
                <td>
                    <div>{{ $order->created_at->format('d/m/Y') }}</div>
                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                </td>
             
            </tr>
        @endforeach
    </tbody>
</table>


    {{-- no data found div --}}
    @if ($orders->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{ asset('admin/app-assets/images/pages/404.png') }}" alt="">
            <span class="mt-2" style="font-family: cairo">{{ __('admin.there_are_no_matches_matching') }}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>

{{-- pagination links div --}}
@if ($orders->count() > 0 && $orders instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{ $orders->links() }}
    </div>
@endif
{{-- pagination links div --}}
