<td class="product-action">
    <span class="d-none d-md-inline">
        <a href="{{route('admin.clients.edit' , ['id' => $row->id])}}" class="btn btn-primary btn-sm"><i class="feather icon-edit"></i></a>
    </span>
    <span class="d-none d-md-inline">
        <button data-toggle="modal" data-target="#notify" class="btn btn-info btn-sm notify" data-id="{{$row->id}}" data-url="{{url('admins/clients/notify')}}"><i class="feather icon-bell"></i></button>
    </span>
    <span class="d-none d-md-inline">
        <button data-toggle="modal" data-target="#mail" class="btn btn-info btn-sm mail" data-id="{{$row->id}}" data-url="{{url('admins/clients/notify')}}"><i class="feather icon-mail"></i></button>
    </span>
    <span class="d-none d-md-inline">
        <button class="delete-row btn btn-danger btn-sm" data-url="{{url('admin/clients/'.$row->id)}}"><i class="feather icon-trash"></i></button>
    </span>

    <span class="actions-dropdown d-md-none">
        <div class="dropdown">
            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="actions-menu-{{ $row->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ __('admin.actions') }}
            </button>
            <div class="dropdown-menu" aria-labelledby="actions-menu-{{ $row->id }}">
                <a class="dropdown-item" href="{{route('admin.clients.edit' , ['id' => $row->id])}}">{{ __('admin.edit') }}</a>
                <button class="dropdown-item notify" data-toggle="modal" data-target="#notify" data-id="{{$row->id}}" data-url="{{url('admins/clients/notify')}}">{{ __('admin.notify') }}</button>
                <button class="dropdown-item mail" data-toggle="modal" data-target="#mail" data-id="{{$row->id}}" data-url="{{url('admins/clients/notify')}}">{{ __('admin.send_email') }}</button>
                <button class="dropdown-item delete-row" data-url="{{url('admin/clients/'.$row->id)}}">{{ __('admin.delete') }}</button>
            </div>
        </div>
    </span>
</td>