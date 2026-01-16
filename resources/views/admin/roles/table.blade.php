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
                <th>#</th>
                <th>{{__('admin.name')}}</th>
                <th>{{__('admin.control')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
                <tr class="delete_role">
                    <td>{{$loop->iteration}}</td>
                    <td>{{$role->name}}</td>
                    <td class="product-action">
                        <span class="d-none d-md-inline">
                            <a href="{{ route('admin.roles.edit', ['id' => $role->id]) }}" class="btn btn-primary btn-sm"><i class="feather icon-edit"></i>{{ __('admin.edit') }}</a>
                        </span>
                        @if(auth()->guard('admin')->user()->role->id != $role->id && $role->id != 2)
                            <span class="d-none d-md-inline">
                                <button class="delete-row btn btn-danger btn-sm" data-url="{{ url('admin/roles/' . $role->id) }}"><i class="feather icon-trash"></i>{{ __('admin.delete') }}</button>
                            </span>
                        @endif

                        <span class="actions-dropdown d-md-none">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="actions-menu-{{ $role->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ __('admin.actions') }}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="actions-menu-{{ $role->id }}">
                                    <a class="dropdown-item" href="{{ route('admin.roles.edit', ['id' => $role->id]) }}">{{ __('admin.edit') }}</a>
                                    @if(auth()->guard('admin')->user()->role->id != $role->id && $role->id != 2)
                                        <button class="dropdown-item delete-row" data-url="{{ url('admin/roles/' . $role->id) }}">{{ __('admin.delete') }}</button>
                                    @endif
                                </div>
                            </div>
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- table content --}}
    {{-- no data found div --}}
    @if ($roles->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($roles->count() > 0 && $roles instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$roles->links()}}
    </div>
@endif
{{-- pagination  links div --}}
