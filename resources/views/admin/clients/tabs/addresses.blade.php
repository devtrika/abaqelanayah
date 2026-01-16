<div class="tab-pane fade" id="addresses">
    @if($row->addresses->count() > 0)
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('admin.addresses') }}</h5>
                <div class="header-elements">
                    <span class="badge badge-primary">
                        {{ $row->addresses->count() }} {{ __('admin.addresses') }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                {{-- <th>{{ __('admin.details') }}</th> --}}
                                <th>{{ __('admin.latitude') }}</th>
                                <th>{{ __('admin.longitude') }}</th>
                                <th>{{ __('admin.phone') }}</th>
                                <th>{{ __('admin.country_code') }}</th>
                                <th>{{ __('admin.is_default') }}</th>
                                {{-- <th>{{ __('admin.created_at') }}</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($row->addresses as $index => $address)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    {{-- <td>{{ $address->details ?? __('admin.not_available') }}</td> --}}
                                    <td>{{ $address->latitude }}</td>
                                    <td>{{ $address->longitude }}</td>
                                    <td>{{ $address->phone }}</td>
                                    <td>{{ $address->country_code }}</td>
                                    <td>
                                        @if($address->is_default)
                                            <span class="badge badge-success">{{ __('admin.yes') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('admin.no') }}</span>
                                        @endif
                                    </td>
                                    {{-- <td>{{ $address->created_at->format('Y-m-d H:i') }}</td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <div class="empty-state">
                <img src="{{ asset('admin/app-assets/images/pages/404.png') }}"
                     alt="{{ __('admin.no_addresses_found') }}"
                     class="img-fluid mb-3"
                     style="max-width: 200px;">
                <h5 class="text-muted mb-2">{{ __('admin.no_addresses_found') }}</h5>
                <p class="text-muted" style="font-family: cairo">
                    {{ __('admin.there_are_no_matches_matching') }}
                </p>
            </div>
        </div>
    @endif
</div>
