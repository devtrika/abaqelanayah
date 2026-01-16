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
            <th>{{ __('admin.amount') }}</th>
            <th>{{ __('admin.created_at') }}</th>
            <th>{{ __('admin.platform_commission') }}</th>
            <th>{{ __('admin.booking_fees') }}</th>
            <th>{{ __('admin.cancel_fees') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $index => $order)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', ['id' => $order->id]) }}" target="_blank">
                        {{ $order->order_number }}
                    </a>
                   
                </td>
                <td>{{ number_format($order->total, 2) }} {{ __('admin.sar') }}</td>
                <td>
                    <div>{{ $order->created_at->format('d/m/Y') }}</div>
                </td>
                <td>{{ number_format($order->platform_commission ?? 0, 2) }} {{ __('admin.sar') }}</td>
                <td>{{ number_format($order->booking_fee ?? 0, 2) }} {{ __('admin.sar') }}</td>
                <td>{{ number_format($order->cancel_fees ?? 0, 2) }} {{ __('admin.sar') }}</td>
               
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
