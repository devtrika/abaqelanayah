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
                <th>{{__('admin.created_at')}}</th>
                <th>{{ __('admin.id_num') }}</th>
                {{-- <th>{{__('admin.image')}}</th> --}}
                <th>{{__('admin.product_name')}}</th>
                <th>{{__('admin.Subcategory')}}</th>
                <th>{{__('admin.base_price')}}</th>
                <th>{{__('admin.discount_percentage')}}</th>
                <th>{{__('admin.final_price')}}</th>
                {{-- <th>{{__('admin.quantity')}}</th> --}}
                {{-- <th>{{__('admin.options_count')}}</th> --}}
                @if (auth()->guard('admin')->user()->role_id == 1)
                <th>{{__('admin.status')}}</th>
               @endif
                <th>{{__('admin.control')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr class="delete_row">
                    <td class="text-center">
                        <label class="container-checkbox">
                        <input type="checkbox" class="checkSingle" id="{{ $product->id }}">
                        <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{$product->created_at->format('Y-m-d H:i:s')}}</td>
                    <td>{{ $product->id }}</td>
                    {{-- <td>
                        @if($product->getFirstMediaUrl('product-images'))
                            <img src="{{$product->getFirstMediaUrl('product-images')}}" width="30px" height="30px" alt="{{$product->display_name}}">
                        @else
                            <img src="{{asset('storage/images/default.png')}}" width="30px" height="30px" alt="No Image">
                        @endif
                    </td> --}}
                    <td>{{$product->name}}</td>
                    <td>
                        @if($product->category)
                            {{$product->category->name}}
                            @if($product->parentCategory)
                                <br>
                                <small class="text-muted">{{$product->parentCategory->name}}</small>
                            @endif
                        @else
                            <span class="text-muted">{{__('admin.no_category')}}</span>
                        @endif
                    </td>
                    <td>{{number_format($product->base_price, 2)}} {{__('admin.sar')}}</td>
                    <td>
                        @if($product->discount_percentage)
                            {{number_format($product->discount_percentage, 2)}}%
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{number_format($product->final_price, 2)}} {{__('admin.sar')}}</td>
                    {{-- <td>
                        @php
                            $displayQty = 0;
                            if (isset($isBranchManager) && $isBranchManager && isset($managerBranchIds) && $managerBranchIds->count() > 0) {
                                // For branch managers, show sum of quantities from their branches
                                $displayQty = \App\Models\BranchProduct::where('product_id', $product->id)
                                    ->whereIn('branch_id', $managerBranchIds)
                                    ->sum('qty');
                            } else {
                                // For super admin, show total quantity
                                $displayQty = $product->qty ?? 0;
                            }
                        @endphp
                        <span class="badge badge-{{ $displayQty > 0 ? 'success' : 'danger' }}">{{ $displayQty }}</span>
                    </td> --}}
                    {{-- <td>
                        @if($product->options->count() > 0)
                            <span class="badge badge-info">{{$product->options->count()}}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td> --}}
                    @if (auth()->guard('admin')->user()->role_id == 1)

                    <td>
                        {!! toggleBooleanView($product , route('admin.model.active' , ['model' =>'Product' , 'id' => $product->id , 'action' => 'is_active'])) !!}
                    </td>
                    @endif

                    <td class="product-action">
                        <x-admin.actions
                            :id="$product->id"
                            :show-url="route('admin.products.show', ['id' => $product->id])"
                            :edit-url="route('admin.products.edit', ['id' => $product->id])"
                            :delete-url="url('admin/products/' . $product->id)"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- table content --}}
    {{-- no data found div --}}
    @if ($products->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($products->count() > 0 && $products instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$products->links()}}
    </div>
@endif
{{-- pagination  links div --}}

