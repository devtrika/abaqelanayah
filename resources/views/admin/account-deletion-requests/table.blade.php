<div class="position-relative" style="overflow: auto">
   
  
    <table class="table " id="tab">
        <thead>
            <tr>
                <th>
                    <label class="container-checkbox">
                      <input type="checkbox" value="value1" name="name1" id="checkedAll">
                      <span class="checkmark"></span>
                    </label>
                  </th>
                  <th>{{ __('admin.date') }}</th>
                  <th>{{ __('admin.user') }}</th>
                  <th>{{ __('admin.user_type') }}</th>
                  <th>{{ __('admin.email') }}</th>
                  <th>{{ __('admin.phone') }}</th>
                  <th>{{ __('admin.status') }}</th>
                  <th>{{ __('admin.control') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($account_deletion_requests as $row)
                <tr class="delete_row">
                <td class="text-center">
                    <label class="container-checkbox">
                        <input type="checkbox" class="checkSingle" id="{{ $row->id }}">
                        <span class="checkmark"></span>
                    </label>
                </td>
                <td>{{ $row->created_at->format('d/m/Y') }}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm mr-2">
                            @if($row->user && $row->user->getFirstMediaUrl('profile'))
                                <img src="{{ $row->user->getFirstMediaUrl('profile') }}" alt="{{ $row->user->name }}" class="round" width="30px" height="30px">
                            @else
                                <div class="avatar-content bg-light-primary">
                                    <i class="feather icon-user"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $row->user->name ?? __('admin.deleted_user') }}</h6>
                            <small class="text-muted">ID: {{ $row->user_id }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    @if($row->user)
                        @switch($row->user->type)
                            @case('client')
                                <span class="badge badge-info">{{ __('admin.client') }}</span>
                                @break
                            @case('provider')
                                <span class="badge badge-warning">{{ __('admin.provider') }}</span>
                                @break
                            @case('delivery')
                                <span class="badge badge-secondary">{{ __('admin.delivery') }}</span>
                                @break
                            @default
                                <span class="badge badge-light">{{ $row->user->type }}</span>
                        @endswitch
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ $row->user->email ?? __('admin.not_set') }}</td>
                <td>{{ $row->user->full_phone ?? __('admin.not_set') }}</td>
                
                <td>
                    @switch($row->status)
                        @case('pending')
                            <span class="badge badge-warning">{{ __('admin.pending') }}</span>
                            @break
                        @case('approved')
                            <span class="badge badge-success">{{ __('admin.approved') }}</span>
                            @break
                        @case('rejected')
                            <span class="badge badge-danger">{{ __('admin.rejected') }}</span>
                            @break
                        @default
                            <span class="badge badge-secondary">{{ $row->status }}</span>
                    @endswitch
                </td>
              
                <td class="product-action">
                    <span class="d-none d-md-inline">
                        <a href="{{ route('admin.account-deletion-requests.show', $row->id) }}" class="btn btn-warning btn-sm">
                            <i class="feather icon-eye"></i> {{ __('admin.show') }}
                        </a>
                    </span>

                    @if($row->isPending())
                        <span class="d-none d-md-inline">
                            <button class="btn btn-success btn-sm approve-request" data-id="{{ $row->id }}">
                                <i class="feather icon-check"></i> {{ __('admin.accept') }}
                            </button>
                        </span>
                        <span class="d-none d-md-inline">
                            <button class="btn btn-danger btn-sm reject-request" data-id="{{ $row->id }}">
                                <i class="feather icon-x"></i> {{ __('admin.reject') }}
                            </button>
                        </span>
                    @else
                        <span class="text-muted d-none d-md-inline">
                            @if($row->isApproved())
                                <span class="btn btn-sm btn-outline-success">
                                    <i class="feather icon-check-circle"></i> {{ __('admin.approved') }}
                                </span>
                            @else
                                <span class="btn btn-sm btn-outline-danger">
                                    <i class="feather icon-x-circle"></i> {{ __('admin.rejected') }}
                                </span>
                            @endif
                        </span>
                    @endif

                    <span class="actions-dropdown d-md-none">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="actions-menu-{{ $row->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('admin.actions') }}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="actions-menu-{{ $row->id }}">
                                <a class="dropdown-item" href="{{ route('admin.account-deletion-requests.show', $row->id) }}">{{ __('admin.show') }}</a>
                                @if($row->isPending())
                                    <button class="dropdown-item approve-request" data-id="{{ $row->id }}">{{ __('admin.approve') }}</button>
                                    <button class="dropdown-item reject-request" data-id="{{ $row->id }}">{{ __('admin.reject') }}</button>
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
    @if ($account_deletion_requests->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($account_deletion_requests->count() > 0 && $account_deletion_requests instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$account_deletion_requests->links()}}
    </div>
@endif
{{-- pagination  links div --}}
