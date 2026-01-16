<div class="position-relative">
    <table class="table " id="tab">
        <thead>
            <tr>
                <th>
                    <label class="container-checkbox">
                        <input type="checkbox" id="checkedAll">
                        <span class="checkmark"></span>
                    </label>
                </th>
                <th>{{ __('admin.date') }}</th>
                <th>{{ __('admin.coupon_name') }}</th>
                <th>{{ __('admin.coupon_number') }}</th>
                <th>{{ __('admin.discount_type') }}</th>
                <th>{{ __('admin.discount_value') }}</th>
                <th>{{ __('admin.start_date') }}</th>
                <th>{{ __('admin.expiry_date') }}</th>
                <th>{{ __('admin.used_times') }}</th>
                <th>{{ __('admin.orders_count') }}</th>
                <th>{{ __('admin.expired') }}</th>
        
                <th>{{ __('admin.status') }}</th>
                <th>{{ __('admin.control') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($coupons as $coupon)
                <tr class="delete_coupon">
                    <td class="text-center">
                        <label class="container-checkbox">
                            <input type="checkbox" class="checkSingle" id="{{$coupon->id}}">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($coupon->created_at)->format('d/m/Y') }}</td>
                    <td>{{ $coupon->coupon_name ?? __('admin.not_set') }}</td>
                    <td>{{ $coupon->coupon_num }}</td>
                    <td>
                        <span class="badge badge-{{ $coupon->type == 'ratio' ? 'warning' : 'primary' }}">
                            {{ $coupon->type == 'ratio' ? __('admin.percentage') : __('admin.fixed_number') }}
                        </span>
                    </td>
                    <td>
                        {{ $coupon->discount }}{{ $coupon->type == 'ratio' ? '%' : '' }}
                        @if($coupon->max_discount && $coupon->type == 'ratio')
                            <small class="text-muted">({{ __('admin.max') }}: {{ $coupon->max_discount }})</small>
                        @endif
                    </td>
                    <td>{{ $coupon->start_date ? date('d-m-Y', strtotime($coupon->start_date)) : __('admin.not_set') }}</td>
                    <td>{{ $coupon->expire_date ? date('d-m-Y', strtotime($coupon->expire_date)) : __('admin.no_expiry') }}</td>
                    <td>
                        {{ $coupon->usage_time }}
                    </td>
                    <td>
                        {{-- <a href="{{ route('admin.coupons.orders', ['coupon' => $coupon->id]) }}"> --}}
                            {{ $coupon->orders_count ?? 0 }}
                        {{-- </a> --}}
                    </td>
                    <td>
                        @php
                            // Determine expiry purely from dates (ignore is_active)
                            $isExpiredByDate = false;
                            if ($coupon->expire_date) {
                                $isExpiredByDate = \Carbon\Carbon::parse($coupon->expire_date)->lt(\Carbon\Carbon::now());
                            }
                        @endphp

                        @if (! $isExpiredByDate)
                            <span class="badge badge-success">{{ __('admin.valid') }}</span>
                        @else
                            <span class="badge badge-danger">{{ __('admin.expired') }}</span>
                        @endif
                    </td>
                    <td>
                        {!! toggleBooleanView($coupon , route('admin.model.active' , ['model' =>'Coupon' , 'id' => $coupon->id , 'action' => 'is_active'])) !!}
                    </td>
                    <td class="product-action">
                        <a href="{{ route('admin.coupons.show', ['id' => $coupon->id]) }}" class="btn btn-warning btn-sm">
                            <i class="feather icon-eye"></i> {{ __('admin.show') }}
                        </a>
                        <a href="{{ route('admin.coupons.edit', ['id' => $coupon->id]) }}" class="btn btn-primary btn-sm">
                            <i class="feather icon-edit"></i> {{ __('admin.edit') }}
                        </a>
                        <span class="delete-row btn btn-danger btn-sm" data-url="{{ url('admin/coupons/' . $coupon->id) }}">
                            <i class="feather icon-trash"></i> {{ __('admin.delete') }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- لا توجد بيانات --}}
    @if ($coupons->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{ asset('admin/app-assets/images/pages/404.png') }}" alt="">
            <span class="mt-2" style="font-family: cairo">{{ __('admin.there_are_no_matches_matching') }}</span>
        </div>
    @endif

</div>

{{-- روابط الصفحات --}}
@if ($coupons->count() > 0 && $coupons instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="d-flex justify-content-center mt-3">
        {{ $coupons->links() }}
    </div>
@endif
