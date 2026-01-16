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
                <th>{{__('admin.service_name') }}</th>
                <th>{{__('admin.service_provider') }}</th>
                <th>{{__('admin.service_category') }}</th>
                <th>{{__('admin.service_price') }}</th>
                <th>{{__('admin.service_duration') }}</th>
                <th>{{__('admin.service_status') }}</th>
                <th>{{__('admin.control') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($services as $service)
                <tr class="delete_row">
                    <td class="text-center">
                        <label class="container-checkbox">
                            <input type="checkbox" class="checkSingle" id="{{$service->id}}">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{ $service->id }}</td>
                    <td>{{$service->name}}</td>
                    <td>
                        @if($service->provider && $service->provider->user)
                            {{$service->provider->user->name}}
                            <br>
                            <small class="text-muted">{{$service->provider->commercial_name}}</small>
                        @else
                            <span class="text-muted">{{__('admin.no_provider')}}</span>
                        @endif
                    </td>
                    <td>
                        @if($service->category)
                            {{$service->category->name}}
                        @else
                            <span class="text-muted">{{__('admin.no_category')}}</span>
                        @endif
                    </td>
                    <td>{{number_format($service->price, 2)}} {{__('admin.sar')}}</td>
                    <td>{{$service->duration}} {{__('admin.minutes')}}</td>
                    <td>
                        {!! toggleBooleanView($service , route('admin.model.active' , ['model' =>'Service' , 'id' => $service->id , 'action' => 'is_active'])) !!}
                    </td>
                    <td class="product-action">
                        <x-admin.actions
                            :id="$service->id"
                            :show-url="route('admin.services.show', ['id' => $service->id])"
                            :edit-url="route('admin.services.edit', ['id' => $service->id])"
                            :delete-url="url('admin/services/' . $service->id)"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- table content --}}
    {{-- no data found div --}}
    @if ($services->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}"alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($services->count() > 0 && $services instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$services->links()}}
    </div>
@endif
{{-- pagination  links div --}}
