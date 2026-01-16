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
                <td>{{ __('admin.id_num') }}</td>
                <th>{{__('admin.date')}}</th>
                <th>{{__('admin.name')}}</th>
                {{-- <th>{{__('admin.users')}}</th> --}}
                {{-- <th>{{__('admin.providers')}}</th> --}}

                <th>{{__('admin.control')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($regions as $region)
                <tr class="delete_row">

                    <td class="text-center">
                        <label class="container-checkbox">
                            <input type="checkbox" class="checkSingle" id="{{$region->id}}">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{ $region->id }}</td>
                    <td>{{\Carbon\Carbon::parse($region->created_at)->format('d/m/Y')}}</td>
                    <td>{{ $region->name }}</td>
                    {{-- <td>
                        <a href="{{ route('admin.clients.index') }}?region_id={{ $region->id }}">
                            {{ $region->users_count }}
                        </a>
                    </td> --}}
                    {{-- <td>
                        <a href="{{ route('admin.providers.index') }}?region_id={{ $region->id }}">
                            {{ $region->providers_count }}
                        </a>
                    </td> --}}

                    
                    
                    <td class="product-action">
                        <x-admin.actions
                            :id="$region->id"
                            :show-url="route('admin.regions.show', ['id' => $region->id])"
                            :edit-url="route('admin.regions.edit', ['id' => $region->id])"
                            :delete-url="url('admin/regions/' . $region->id)"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- table content --}}
    {{-- no data found div --}}
    @if ($regions->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($regions->count() > 0 && $regions instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$regions->links()}}
    </div>
@endif
{{-- pagination  links div --}}

@section('js')
{{-- No JS needed for region filter links --}}
@endsection

