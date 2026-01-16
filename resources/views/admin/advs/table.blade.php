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
                <th>{{__('admin.title')}}</th>
                <th>{{__('admin.description')}}</th>
                <th>{{__('admin.control')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($advs as $adv)
                <tr class="delete_row">
                    <td class="text-center">
                        <label class="container-checkbox">
                        <input type="checkbox" class="checkSingle" id="{{ $adv->id }}">
                        <span class="checkmark"></span>
                        </label>
                    </td>

                    <td><img src="{{$adv->image}}" width="30px" height="30px" alt=""></td>

                    <td>{{ $adv->title }}</td>
                    <td>{{ $adv->description }}</td>
                    
                 
                    
                    <td class="product-action">
                        <x-admin.actions
                            :id="$adv->id"
                            :show-url="route('admin.advs.show', ['id' => $adv->id])"
                            :edit-url="route('admin.advs.edit', ['id' => $adv->id])"
                            :delete-url="url('admin/advs/' . $adv->id)"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- table content --}}
    {{-- no data found div --}}
    @if ($advs->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($advs->count() > 0 && $advs instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$advs->links()}}
    </div>
@endif
{{-- pagination  links div --}}

