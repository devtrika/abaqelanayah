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
                <th>{{__('admin.image')}}</th>
                <th>{{__('admin.name')}}</th>
                <th>{{__('admin.ban_status')}}</th>
                <th>{{__('admin.on_boarding')}}</th>
                <th>{{__('admin.control')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($brands as $brand)
                <tr class="delete_row">
                    <td class="text-center">
                        <label class="container-checkbox">
                        <input type="checkbox" class="checkSingle" id="{{ $brand->id }}">
                        <span class="checkmark"></span>
                        </label>
                    </td>
                    <td><img src="{{$brand->getFirstMediaUrl('brands')}}" width="30px" height="30px" alt=""></td>
                    <td>{{ $brand->name }}</td>
                 
                    <td>
                        {!! toggleBooleanView($brand , route('admin.model.active' , ['model' =>'Brand' , 'id' => $brand->id , 'action' => 'is_active'])) !!}
                    </td>

                          <td>
                        {!! toggleBooleanView($brand , route('admin.model.active' , ['model' =>'Brand' , 'id' => $brand->id , 'action' => 'onboarding'])) !!}
                    </td>
                    

                    
                    
                    <td class="product-action">
                        {{-- <span class="text-primary"><a href="{{ route('admin.brands.show', ['id' => $brand->id]) }}" class="btn btn-warning btn-sm"><i class="feather icon-eye"></i> {{ __('admin.show') }}</a></span> --}}
                        @if(adminCan('admin.brands.edit'))
                            <span class="action-edit text-primary"><a href="{{ route('admin.brands.edit', ['id' => $brand->id]) }}" class="btn btn-primary btn-sm"><i class="feather icon-edit"></i>{{ __('admin.edit') }}</a></span>
                        @endif
                        @if(adminCan('admin.brands.delete'))
                            <span class="delete-row btn btn-danger btn-sm" data-url="{{ url('admin/brands/' . $brand->id) }}"><i class="feather icon-trash"></i>{{ __('admin.delete') }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- table content --}}
    {{-- no data found div --}}
    @if ($brands->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($brands->count() > 0 && $brands instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$brands->links()}}
    </div>
@endif
{{-- pagination  links div --}}

