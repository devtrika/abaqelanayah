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
                <th>{{__('admin.status')}}</th>
                <th>{{__('admin.control')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paymentmethods as $paymentmethod)
                <tr class="delete_row">
                    <td class="text-center">
                        <label class="container-checkbox">
                        <input type="checkbox" class="checkSingle" id="{{ $paymentmethod->id }}">
                        <span class="checkmark"></span>
                        </label>
                    </td>
                    
                    <td>
                        @php
                            $img = $paymentmethod->image;
                            $src = null;
                            if ($img) {
                                // absolute URL
                                if (preg_match('#^https?://#i', $img)) {
                                    $src = $img;
                                } elseif (file_exists(public_path($img))) {
                                    // file placed directly under public/
                                    $src = asset($img);
                                } else {
                                    // assume stored in storage/app/public and served via storage symlink
                                    $src = asset('storage/' . ltrim($img, '/'));
                                }
                            }
                        @endphp

                        @if ($src)
                            <img src="{{ $src }}" alt="{{ $paymentmethod->name ?? '' }}" style="height:40px; width:auto; object-fit:cover;" />
                        @else
                            <span class="text-muted">{{ __('admin.no_image') ?? '—' }}</span>
                        @endif
                    </td>
                    <td>{{ $paymentmethod->name }}</td>
                    <td>
                        {!! toggleBooleanView($paymentmethod, route('admin.model.active', ['model' => 'PaymentMethod', 'id' => $paymentmethod->id, 'action' => 'is_active'])) !!}
                    </td>
                    <td class="product-action">
                        <span class="action-edit text-primary">
                            <a href="{{ route('admin.paymentmethods.edit', ['id' => $paymentmethod->id]) }}" class="btn btn-primary btn-sm"><i class="feather icon-edit"></i> {{ __('admin.edit') }}</a>
                        </span>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- table content --}}
    {{-- no data found div --}}
    @if ($paymentmethods->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($paymentmethods->count() > 0 && $paymentmethods instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$paymentmethods->links()}}
    </div>
@endif
{{-- pagination  links div --}}

