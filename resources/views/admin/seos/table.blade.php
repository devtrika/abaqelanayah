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
                <th>{{__('admin.key')}}</th>
                <th>{{__('admin.address')}}</th>
                <th>{{__('admin.control')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($seos as $seo)
                <tr class="delete_seo">
                    <td class="text-center">
                        <label class="container-checkbox">
                            <input type="checkbox" class="checkSingle" id="{{$seo->id}}">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{$seo->key}}</td>
                    <td>{{$seo->meta_title}}</td>
                    <td class="product-action">
                        <x-admin.actions
                            :id="$seo->id"
                            :show-url="route('admin.seos.show', ['id' => $seo->id])"
                            :edit-url="route('admin.seos.edit', ['id' => $seo->id])"
                            :delete-url="url('admin/seos/' . $seo->id)"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- table content --}}
    {{-- no data found div --}}
    @if ($seos->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($seos->count() > 0 && $seos instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$seos->links()}}
    </div>
@endif
{{-- pagination  links div --}}