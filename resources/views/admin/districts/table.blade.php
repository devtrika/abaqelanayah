
<div class="position-relative">
    {{-- table loader  --}}
    {{-- <div class="table_loader" >
        {{__('admin.loading')}}
    </div> --}}
    {{-- table loader  --}}
    <table class="table" id="tab">
        <thead>
            <tr>
                <th>
                    <label class="container-checkbox">
                        <input type="checkbox" value="value1" name="name1" id="checkedAll">
                        <span class="checkmark"></span>
                    </label>
                </th>
                <th>{{ __('admin.id_num') }}</th>
                <th>{{__('admin.date')}}</th>
                <th>{{__('admin.name')}}</th>
                <th>{{__('admin.city')}}</th>
                {{-- <th>{{__('admin.users')}}</th> --}}
                <th>{{__('admin.status')}}</th>
                <th>{{__('admin.control')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($districts as $district)
                <tr class="delete_district">
                    <td class="text-center">
                        <label class="container-checkbox">
                            <input type="checkbox" class="checkSingle" id="{{$district->id}}">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{ $district->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($district->created_at)->format('d/m/Y') }}</td>
                    <td>{{ $district->name }}</td>
                    <td>{{ $district->city->name ?? '-' }}</td>
                    {{-- <td>
                        <a href="{{ route('admin.clients.index') }}?district_id={{ $district->id }}">
                            {{ $district->users_count ?? 0 }}
                        </a>
                    </td> --}}
                    <td>
                        @if($district->status)
                            <span class="badge badge-success">{{ __('admin.active') }}</span>
                        @else
                            <span class="badge badge-danger">{{ __('admin.inactive') }}</span>
                        @endif
                    </td>
                    <td class="product-action">
                        <span class="text-primary"><a href="{{ route('admin.districts.show', ['id' => $district->id]) }}" class="btn btn-warning btn-sm"><i class="feather icon-eye"></i> {{ __('admin.show') }}</a></span>
                        <span class="action-edit text-primary"><a href="{{ route('admin.districts.edit', ['id' => $district->id]) }}" class="btn btn-primary btn-sm"><i class="feather icon-edit"></i>{{ __('admin.edit') }}</a></span>
                        <span class="delete-row btn btn-danger btn-sm" data-url="{{ url('admin/districts/' . $district->id) }}"><i class="feather icon-trash"></i>{{ __('admin.delete') }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- no data found div --}}
    @if ($districts->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
</div>
{{-- pagination  links div --}}
@if ($districts->count() > 0 && $districts instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$districts->links()}}
    </div>
@endif
