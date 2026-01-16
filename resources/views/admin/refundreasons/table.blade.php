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
                <th>{{__('admin.number')}}</th>
                <th>{{__('admin.reason')}}</th>
               
                <th>{{__('admin.control')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($refundreasons as $refundreason)
                <tr class="delete_row">
                    <td class="text-center">
                        <label class="container-checkbox">
                        <input type="checkbox" class="checkSingle" id="{{ $refundreason->id }}">
                        <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{ $refundreason->id }}</td>
                    <td>{{ $refundreason->reason }}</td>
                   
                    
                    <td class="product-action">
                        <span class="action-edit text-primary"><a href="{{ route('admin.refundreasons.edit', ['id' => $refundreason->id]) }}" class="btn btn-primary btn-sm p-1" title="{{ __('admin.edit') }}"><i class="feather icon-edit"></i></a></span>
                        <span class="delete-row btn btn-danger btn-sm p-1" data-url="{{ url('admin/refundreasons/' . $refundreason->id) }}" title="{{ __('admin.delete') }}"><i class="feather icon-trash"></i></span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- table content --}}
    {{-- no data found div --}}
    @if ($refundreasons->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($refundreasons->count() > 0 && $refundreasons instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$refundreasons->links()}}
    </div>
@endif
{{-- pagination  links div --}}

