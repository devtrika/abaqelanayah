<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>{{ __('admin.image') }}</th>
                <th>{{ __('admin.commercial_name') }}</th>
                <th>{{ __('admin.email') }}</th>
                <th>{{ __('admin.phone') }}</th>
                <th>{{ __('admin.commercial_register_no') }}</th>
                <th>{{ __('admin.created_at') }}</th>
                <th>{{ __('admin.control') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row) 
                <tr>
                    <td>
                        <img src="{{ $row->image }}" alt="{{ $row->name }}" class="rounded-circle" width="50" height="50">
                    </td>
                    <td>
                        @if($row->provider && $row->provider->commercial_name)
                            {{ $row->provider->commercial_name }}
                        @else
                            <span class="text-muted">{{ __('admin.not_set') }}</span>
                        @endif
                    </td>
                    <td>{{ $row->email ?? __('admin.not_set') }}</td>
                    <td>{{ $row->full_phone }}</td>
                    <td>
                        @if($row->provider && $row->provider->commercial_register_no)
                            {{ $row->provider->commercial_register_no }}
                        @else
                            <span class="text-muted">{{ __('admin.not_set') }}</span>
                        @endif
                    </td>
                    <td>{{ $row->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <div class="btn-group" role="group" aria-label="Provider Actions">
                            <a href="{{ route('admin.providers.show', $row->id) }}" class="action-edit text-primary" title="{{ __('admin.show') }}">
                                <i class="feather icon-eye"></i>
                            </a>
                        @if($row->status != 'rejected')
                            <span class="action-edit text-success approve-provider" data-id="{{ $row->id }}" title="{{ __('admin.approve') }}">
                                <i class="feather icon-check"></i>
                            </span>

                            <span class="action-edit text-danger reject-provider" data-id="{{ $row->id }}" title="{{ __('admin.reject') }}">
                                <i class="feather icon-x"></i>
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            @if($rows->count() == 0)
            <div class="d-flex flex-column w-100 align-center mt-4">
                <img src="{{ asset('admin/app-assets/images/pages/404.png') }}" alt="">
                <span class="mt-2" style="font-family: cairo">{{ __('admin.there_are_no_matches_matching') }}</span>
            </div>
            @endif
        </tbody>
    </table>
</div>

@if($rows->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $rows->links() }}
    </div>
@endif


