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
                <th>{{__('admin.image')}}</th>
                <th>{{__('admin.category_name')}}</th>
                <th>{{__('admin.products_count')}}</th>
                <th>{{__('admin.is_active')}}</th>
                <th>{{__('admin.control')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productcategories as $productcategory)
                <tr class="delete_row">
                    <td class="text-center">
                        <label class="container-checkbox">
                            <input type="checkbox" class="checkSingle" id="{{$productcategory->id}}">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{ $productcategory->id }}</td>
                    <td><img src="{{$productcategory->image_url}}" width="30px" height="30px" alt=""></td>
                    <td>{{ $productcategory->getTranslations('name')[app()->getLocale()] ?? $productcategory->getTranslations('name')[array_key_first($productcategory->getTranslations('name'))] }}</td>
                    <td>{{ $productcategory->products_count ?? 0 }}</td>
                    <td>
                        {!! toggleBooleanView($productcategory , route('admin.model.active' , ['model' =>'ProductCategory' , 'id' => $productcategory->id , 'action' => 'is_active'])) !!}
                    </td>
                    <td class="product-action">
                        <x-admin.actions
                            :id="$productcategory->id"
                            :show-url="route('admin.product-categories.show', ['id' => $productcategory->id])"
                            :edit-url="route('admin.product-categories.edit', ['id' => $productcategory->id])"
                            :delete-url="url('admin/product-categories/' . $productcategory->id)"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- table content --}}
    {{-- no data found div --}}
    @if ($productcategories->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($productcategories->count() > 0 && $productcategories instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$productcategories->links()}}
    </div>
@endif
{{-- pagination  links div --}}

