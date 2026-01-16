<div class="position-relative">
    <table class="table" id="tab">
        <thead>
            <tr>
                <th>
                    <label class="container-checkbox">
                        <input type="checkbox" id="checkedAll">
                        <span class="checkmark"></span>
                    </label>
                </th>
                <th>{{ __('admin.order_number') }}</th>
                <th>{{ __('admin.user_name') }}</th>
                <th>{{ __('admin.stage_video') }}</th>
                <th>{{ __('admin.video_id') }}</th>
                <th>{{ __('admin.order_rate_id') }}</th>
                <th>{{ __('admin.status') }}</th>
                <th>{{ __('admin.created_at') }}</th>
                <th>{{ __('admin.control') }}</th>
            </tr>
        </thead>
        
        <tbody>
            @foreach ($shortvideos as $shortvideo)
                <tr class="delete_row">
                    <td class="text-center">
                        <label class="container-checkbox">
                            <input type="checkbox" class="checkSingle" id="{{ $shortvideo->id }}">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{ $shortvideo->orderRate->order->order_number ?? '-' }}</td>
                    <td>{{ $shortvideo->orderRate->user->name ?? $shortvideo->client_name }}</td>
                    <td>
                        @php
                            $videoMedia = $shortvideo->getFirstMediaUrl('short_video');
                        @endphp
                        @if ($videoMedia)
                            <a href="javascript:void(0);" onclick="showVideoModal('{{ $videoMedia }}')">
                                <i class="feather icon-video text-primary" style="font-size: 24px;"></i>
                            </a>
                        @else
                            <span class="text-muted">{{ __('admin.no_video') }}</span>
                        @endif
                    </td>
                    <td>{{ $shortvideo->video_id }}</td>
                    <td>{{ $shortvideo->order_rate_id }}</td>
                    <td>
                        {!! toggleBooleanView($shortvideo , route('admin.model.active' , ['model' =>'ShortVideo' , 'id' => $shortvideo->id , 'action' => 'is_active'])) !!}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($shortvideo->published_at)->format('Y-m-d') }}</td>
                    <td class="product-action">
                        <span class="d-none d-md-inline">
                            <a href="{{ route('admin.shortvideos.show', $shortvideo->id) }}" class="btn btn-info btn-sm">
                                <i class="feather icon-eye"></i> {{ __('admin.show') }}
                            </a>
                            <a href="{{ route('admin.shortvideos.edit', $shortvideo->id) }}" class="btn btn-primary btn-sm">
                                <i class="feather icon-edit"></i> {{ __('admin.edit') }}
                            </a>
                            <button class="delete-row btn btn-danger btn-sm" data-url="{{ url('admin/shortvideos/' . $shortvideo->id) }}">
                                <i class="feather icon-trash"></i> {{ __('admin.delete') }}
                            </button>
                        </span>
                        <span class="actions-dropdown d-md-none">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="actions-menu-{{ $shortvideo->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ __('admin.actions') }}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="actions-menu-{{ $shortvideo->id }}">
                                    <a class="dropdown-item" href="{{ route('admin.shortvideos.show', $shortvideo->id) }}">{{ __('admin.show') }}</a>
                                    <button class="dropdown-item delete-row" data-url="{{ url('admin/shortvideos/' . $shortvideo->id) }}">{{ __('admin.delete') }}</button>
                                </div>
                            </div>
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        
    </table>

    @if ($shortvideos->isEmpty())
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{ asset('admin/app-assets/images/pages/404.png') }}" alt="">
            <span class="mt-2" style="font-family: cairo">{{ __('admin.there_are_no_matches_matching') }}</span>
        </div>
    @endif
</div>

@if ($shortvideos->count() > 0 && $shortvideos instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="d-flex justify-content-center mt-3">
        {{ $shortvideos->links() }}
    </div>
@endif

{{-- Video Modal --}}
<div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <video id="modalVideo" width="100%" height="auto" controls></video>
            </div>
        </div>
    </div>
</div>

{{-- JS to trigger video modal --}}
<script>
    function showVideoModal(videoUrl) {
        const modalVideo = document.getElementById('modalVideo');
        modalVideo.src = videoUrl;
        $('#videoModal').modal('show');
    }

    $('#videoModal').on('hidden.bs.modal', function () {
        document.getElementById('modalVideo').pause();
    });
</script>
