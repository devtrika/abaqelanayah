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
            <th>{{ __('admin.provider') }}</th>
            <th>{{ __('admin.phone') }}</th>
            <th>{{ __('admin.bank_account') }}</th>
            <th>{{ __('admin.amount') }}</th>
            <th>{{ __('admin.created_at') }}</th>
            <th>{{ __('admin.status') }}</th>
            <th>{{ __('admin.image') }}</th>

            <th>{{ __('admin.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($withdrawRequests as $index => $withdrawRequest)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <span class="badge badge-info">{{ $withdrawRequest->number }}</span>
                </td>
                <td>
                    @if($withdrawRequest->provider)
                        <div>
                            <strong>{{ $withdrawRequest->provider->commercial_name }}</strong><br>
                            <small class="text-muted">{{ $withdrawRequest->provider->user->phone ?? '-' }}</small>
                        </div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ $withdrawRequest->provider->user->phone ?? '-' }}</td>
                <td>
                    @if($withdrawRequest->provider && $withdrawRequest->provider->bankAccount)
                        <div>
                            <strong>{{ $withdrawRequest->provider->bankAccount->bank_name }}</strong><br>
                            <small>{{ $withdrawRequest->provider->bankAccount->account_number }}</small><br>
                            <small>{{ $withdrawRequest->provider->bankAccount->iban }}</small>
                        </div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ number_format($withdrawRequest->amount, 2) }} {{ __('admin.sar') }}</td>
                <td>
                    <div>{{ $withdrawRequest->created_at->format('d/m/Y') }}</div>
                    <small class="text-muted">{{ $withdrawRequest->created_at->format('H:i') }}</small>
                </td>
                <td>
                    @php
                        $statusClass = [
                            'pending' => 'badge-warning',
                            'accepted' => 'badge-success',
                            'rejected' => 'badge-danger',
                        ][$withdrawRequest->status] ?? 'badge-secondary';
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ __('admin.' . $withdrawRequest->status) }}</span>
                </td>
                <td>
                    @if($withdrawRequest->status === 'accepted')
                        @php
                            $imageUrl = $withdrawRequest->getFirstMediaUrl('withdraw_requests');
                        @endphp
                        @if($imageUrl)
                            <a href="{{ $imageUrl }}" target="_blank" title="{{ __('admin.image') }}">
                                <i class="fa fa-image fa-lg"></i>
                            </a>
                        @endif
                    @endif
                </td>
                <td>
                    @if($withdrawRequest->status === 'pending')
                        <button class="btn btn-success btn-sm accept-withdraw-btn" data-id="{{ $withdrawRequest->id }}" data-amount="{{ $withdrawRequest->amount }}">
                            <i class="fa fa-check"></i> {{ __('admin.accept') }}
                        </button>
                        <button class="btn btn-danger btn-sm reject-withdraw-btn" data-id="{{ $withdrawRequest->id }}">
                            <i class="fa fa-times"></i> {{ __('admin.reject') }}
                        </button>
                    @endif
                </td>
             
            </tr>
        @endforeach
    </tbody>
</table>


    {{-- no data found div --}}
            @if ($withdrawRequests->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{ asset('admin/app-assets/images/pages/404.png') }}" alt="">
            <span class="mt-2" style="font-family: cairo">{{ __('admin.there_are_no_matches_matching') }}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>

{{-- pagination links div --}}
@if ($withdrawRequests->count() > 0 && $withdrawRequests instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{ $withdrawRequests->links() }}
    </div>
@endif
{{-- pagination links div --}}
