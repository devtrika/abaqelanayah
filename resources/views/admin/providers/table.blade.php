<div class="position-relative">
    {{-- table loader  --}}
    {{-- <div class="table_loader" >
        {{__('admin.loading')}}
    </div> --}}
    {{-- table loader  --}}

    {{-- table content --}}
    <table class="table " id="tab">
        <thead>
            <tr>
                <th>
                    <label class="container-checkbox">
                        <input type="checkbox" value="value1" name="name1" id="checkedAll">
                        <span class="checkmark"></span>
                    </label>
                </th>
                <th>{{ __('admin.id_num') }}</th>
                <th>{{ __('admin.commercial_name') }}</th>
                <th>{{ __('admin.email') }}</th>
                <th>{{ __('admin.phone') }}</th>
                <th>{{ __('admin.phone_status') }}</th>
                <th>{{ __('admin.provider_status') }}</th>
                <th>{{ __('admin.wallet_balance') }}</th>
                <th>{{ __('admin.accept_order') }}</th>

                <th>{{ __('admin.created_at') }}</th>
                <th>{{ __('admin.control') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr class="delete_provider">
                    <td>
                        <label class="container-checkbox">
                            <input type="checkbox" value="{{ $row->id }}" name="delete_select" id="delete_select">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{ $row->id }}</td>

                    <td>
                        @if ($row->provider && $row->provider->commercial_name)
                            {{ $row->provider->commercial_name }}
                        @else
                            <span class="text-muted">{{ __('admin.not_set') }}</span>
                        @endif
                    </td>
                    <td>{{ $row->email ?? __('admin.not_set') }}</td>
                    <td>{{ $row->full_phone }}</td>
                    <td>
                        @if ($row->is_active == 1)
                            <span class="badge badge-success">{{ __('admin.active') }}</span>
                        @else
                            <span class="badge badge-secondary">{{ __('admin.inactive') }}</span>
                        @endif
                    </td>
                    <td>
                        {{ __('admin.' . $row->provider?->status) }}
                    
                    </td>
                    <td>{{ number_format($row->wallet_balance, 2) }} {{ __('admin.sar') }}</td>
                <td>
                    @if ($row->provider->accept_orders == 1)
                    <span class="badge badge-success">{{ __('admin.yes') }}</span>
                @else
                    <span class="badge badge-secondary">{{ __('admin.no') }}</span>
                @endif
                </td>
                
                    <td>{{ $row->created_at->format('Y-m-d H:i') }}</td>

                    <td class="row-action">

                        <span class="text-primary"><a href="{{ route('admin.providers.show', ['id' => $row->id]) }}"
                                class="btn btn-warning btn-sm"><i class="feather icon-eye"></i>
                                {{ __('admin.show') }}</a></span>

                        <span class="action-edit text-primary"><a
                                href="{{ route('admin.providers.edit', ['id' => $row->id]) }}"
                                class="btn btn-primary btn-sm"><i
                                    class="feather icon-edit"></i>{{ __('admin.edit') }}</a></span>





                                    <span class="delete-row btn btn-danger btn-sm" data-url="{{ url('admin/providers/' . $row->id) }}"><i class="feather icon-trash"></i>{{ __('admin.delete') }}</span>


                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- table content --}}
    {{-- no data found div --}}
    @if ($rows->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{ asset('admin/app-assets/images/pages/404.png') }}" alt="">
            <span class="mt-2" style="font-family: cairo">{{ __('admin.there_are_no_matches_matching') }}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($rows->count() > 0 && $rows instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="d-flex justify-content-center mt-3">
        {{ $rows->links() }}
    </div>
@endif
{{-- pagination  links div --}}
