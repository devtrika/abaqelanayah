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
            <th>{{ __('admin.order_number') }}</th>
            <th>{{ __('admin.user') }}</th>
            <th>{{ __('admin.problem_reason') }}</th>
            <th>{{ __('admin.payment_method') }}</th>
            <th>{{ __('admin.total_amount') }}</th>
            <th>{{ __('admin.request_date') }}</th>
            <th>{{ __('admin.control') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>
                    <span class="badge badge-warning">{{ $order->order_number ?? $order->order_num }}</span>
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
                    @php
                        // Compute a human-friendly problem reason with precedence:
                        // 1) order->notes (free text from reporter)
                        // 2) OrderStatus.map_desc reason (newer format)
                        // 3) $order->problem->problem (predefined problem)
                        $problemReasonText = null;

                        if (!empty($order->notes)) {
                            $problemReasonText = $order->notes;
                        } else {
                            $problemStatus = $order->statusChanges->firstWhere('status', 'problem');
                            if ($problemStatus && $problemStatus->map_desc) {
                                $md = json_decode($problemStatus->map_desc, true);
                                if (is_array($md) && !empty($md['reason'])) {
                                    $problemReasonText = $md['reason'];
                                }
                            }
                        }

                        if ($problemReasonText === null && $order->problem) {
                            $problemReasonText = $order->problem->problem;
                        }

                        if (is_string($problemReasonText) && strlen($problemReasonText) > 0 && $problemReasonText[0] === '{') {
                            $decoded = json_decode($problemReasonText, true);
                            if (is_array($decoded)) {
                                $problemReasonText = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $problemReasonText;
                            }
                        }

                        $problemReasonText = $problemReasonText ?? '-';
                        $displayProblemReason = Str::limit($problemReasonText, 30);
                    @endphp

                    <span>{{ $displayProblemReason }}</span>
                </td>
                <td>
                    @if($order->paymentMethod)
                        <div class="d-flex align-items-center">
                            @if($order->payment_method_id == 1)
                                <i class="feather icon-credit-card text-info mr-1"></i>
                            @elseif($order->payment_method_id == 5)
                                <i class="feather icon-send text-primary mr-1"></i>
                            @else
                                <i class="feather icon-credit-card text-secondary mr-1"></i>
                            @endif
                            <span>{{ $order->paymentMethod->name }}</span>
                        </div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <div>
                        <strong class="text-success">{{ number_format($order->total ?? $order->final_total, 2) }} {{ __('admin.sar') }}</strong>
                    </div>
                    @if($order->services_total > 0 || $order->products_total > 0)
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
                    <div>{{ $order->updated_at->format('d/m/Y') }}</div>
                    <small class="text-muted">{{ $order->updated_at->format('H:i') }}</small>
                    <br>
                    <small class="text-warning">
                        <i class="feather icon-clock mr-1"></i>
                        {{ $order->updated_at->diffForHumans() }}
                    </small>
                </td>
                <td class="order-action">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.problem_orders.show', ['id' => $order->id]) }}"
                           class="btn btn-info btn-sm" title="{{ __('admin.view_details') }}">
                            <i class="feather icon-eye"></i>
                        </a>

                        @php
                            $cancelReasonText = __('admin.no_reason_provided');
                            if ($order->cancelReason) {
                                $cancelModel = $order->cancelReason;
                                if (method_exists($cancelModel, 'getTranslation')) {
                                    $cancelReasonText = $cancelModel->getTranslation('reason', app()->getLocale()) ?: $cancelModel->getTranslation('reason', 'en') ?: __('admin.unknown');
                                } else {
                                    $decoded = json_decode($cancelModel->reason, true);
                                    if (is_array($decoded)) {
                                        $cancelReasonText = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $cancelModel->reason;
                                    } else {
                                        $cancelReasonText = $cancelModel->reason ?: __('admin.unknown');
                                    }
                                }
                            }
                        @endphp
                      
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


    {{-- no data found div --}}
    @if ($orders->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{ asset('admin/app-assets/images/pages/404.png') }}" alt="">
            <span class="mt-2" style="font-family: cairo">{{ __('admin.no_problem_orders_found') }}</span>
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
