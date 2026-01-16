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
                <th>{{__('admin.user_name')}}</th>
                <th>{{__('admin.rate_type')}}</th>
                <th>{{__('admin.rateable_name')}}</th>
                <th>{{__('admin.status')}}</th>
                <th>{{__('admin.control')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rates as $rate)
                <tr class="delete_row">
                    <td class="text-center">
                        <label class="container-checkbox">
                        <input type="checkbox" class="checkSingle" id="{{ $rate->id }}">
                        <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{ $rate->user->name ?? 'N/A' }}</td>
                    <td>
                        @php
                            $rateType = class_basename($rate->rateable_type);
                        @endphp
                        <span class="badge badge-info">{{ __('admin.' . strtolower($rateType)) }}</span>
                    </td>
                    <td>{{ $rate->rateable->commercial_name ?? $rate->rateable->name ?? 'N/A' }}</td>
                   
                    <td>
                        @if ($rate->status == 'rejected')
                        <span class="btn btn-sm round btn-outline-danger">
                            {{ __('admin.rejected') }} <i class="la la-close font-medium-2"></i>
                        </span>
                        @elseif ($rate->status == 'approved')
                        <span class="btn btn-sm round btn-outline-success">
                            {{ __('admin.approved') }} <i class="la la-check font-medium-2"></i>
                        </span>
                        @elseif ($rate->status == 'pending')
                        <span class="btn btn-sm round btn-outline-warning">
                                {{ __('admin.pending') }} <i class="la la-clock font-medium-2"></i>
                        </span>
                        @endif
                    </td>
                    
                    <td class="product-action"> 
                        <span class="text-primary"><a href="{{ route('admin.rates.show', ['id' => $rate->id]) }}" class="btn btn-warning btn-sm"><i class="feather icon-eye"></i> {{ __('admin.show') }}</a></span>
                        <span class="delete-row btn btn-danger btn-sm" data-url="{{ url('admin/rates/' . $rate->id) }}"><i class="feather icon-trash"></i>{{ __('admin.delete') }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- table content --}}
    {{-- no data found div --}}
    @if ($rates->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($rates->count() > 0 && $rates instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$rates->links()}}
    </div>
@endif
{{-- pagination  links div --}}

