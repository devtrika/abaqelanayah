<div class="tab-pane fade" id="notifications">
    @if($row->notifications->count() > 0)
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('admin.notifications') }}</h5>
                <div class="header-elements">
                    <span class="badge badge-primary">
                        {{ $row->notifications->count() }} {{ __('admin.notifications') }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="contain-table text-center">
                        <table class="table datatable-button-init-basic text-center table-hover">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th class="text-center">#</th>
                                    <th class="text-center">{{__('admin.title')}}</th>
                                    <th class="text-center">{{__('admin.message')}}</th>
                                    <th class="text-center">{{__('admin.type')}}</th>
                                    <th class="text-center">{{__('admin.read_status')}}</th>
                                    <th class="text-center">{{__('admin.created_at')}}</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @forelse($row->notifications->take(50) as $key => $notification)
                                    <tr class="delete_row text-center">
                                        <td class="text-center align-middle">{{ $key + 1 }}</td>
                                        <td class="text-center align-middle">
                                            @if(isset($notification->data['title_' . app()->getLocale()]))
                                                {{ $notification->data['title_' . app()->getLocale()] }}
                                            @elseif(isset($notification->data['title']))
                                                {{ $notification->data['title'] }}
                                            @else
                                                {{ $notification->type }}
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            @if(isset($notification->data['body_' . app()->getLocale()]))
                                                {{ Str::limit($notification->data['body_' . app()->getLocale()], 50) }}
                                            @elseif(isset($notification->data['message']))
                                                {{ Str::limit($notification->data['message'], 50) }}
                                            @elseif(isset($notification->data['body']))
                                                {{ Str::limit($notification->data['body'], 50) }}
                                            @else
                                                {{ __('admin.no_message') }}
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-info">{{ class_basename($notification->type) }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($notification->read_at)
                                                <span class="badge badge-success">{{ __('admin.read') }}</span>
                                                <small class="d-block text-muted">{{ $notification->read_at->format('Y-m-d H:i') }}</small>
                                            @else
                                                <span class="badge badge-warning">{{ __('admin.unread') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <div class="empty-state">
                <img src="{{ asset('admin/app-assets/images/pages/404.png') }}"
                     alt="{{ __('admin.no_notifications_found') }}"
                     class="img-fluid mb-3"
                     style="max-width: 200px;">
                <h5 class="text-muted mb-2">{{ __('admin.no_notifications_found') }}</h5>
                <p class="text-muted" style="font-family: cairo">
                    {{ __('admin.there_are_no_matches_matching') }}
                </p>
            </div>
        </div>
    @endif
</div>
