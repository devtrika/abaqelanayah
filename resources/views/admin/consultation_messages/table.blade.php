

<div class="position-relative">
    {{-- table loader  --}}
    {{-- <div class="table_loader">
        {{ __('admin.loading') }}
    </div> --}}
    {{-- table loader  --}}

    {{-- table content --}}
    
 <table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ __('admin.name') }}</th>
            <th>{{ __('admin.email') }}</th>
            <th>{{ __('admin.phone') }}</th>
            <th>{{ __('admin.last_message') }}</th>
            <th>{{ __('admin.last_message_date') }}</th>
            <th>{{ __('admin.control') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clients as $client)
            @php
                $lastMessage = $client->consultationMessages()->latest()->first();
            @endphp
            <tr>
                <td>{{ $client->id }}</td>
                <td>{{ $client->name }}</td>
                <td>{{ $client->email }}</td>
                <td>{{ $client->phone }}</td>
                <td>
                    @if($lastMessage)
                        {{ Str::limit($lastMessage->message, 50) }}
                    @else
                        <span class="text-muted">{{ __('admin.no_messages') }}</span>
                    @endif
                </td>
                <td>
                    @if($lastMessage)
                        {{ $lastMessage->created_at->format('Y-m-d H:i') }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.consultations.show', $client->id) }}" class="btn btn-primary btn-sm">{{ __('admin.show') }}</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


    {{-- no data found div --}}
    @if ($clients->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{ asset('admin/app-assets/images/pages/404.png') }}" alt="">
            <span class="mt-2" style="font-family: cairo">{{ __('admin.there_are_no_matches_matching') }}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>

{{-- pagination links div --}}
        @if ($clients->count() > 0 && $clients instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{ $clients->links() }}
    </div>
@endif
{{-- pagination links div --}}
