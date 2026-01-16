@extends('admin.layout.master')

@section('css')
<style>
    .rating-display {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    .rating-stars {
        display: flex;
        gap: 2px;
    }
    .star {
        color: #ddd;
        font-size: 20px;
    }
    .star.filled {
        color: #ffc107;
    }
    .star.half {
        background: linear-gradient(90deg, #ffc107 50%, #ddd 50%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .rating-number {
        font-weight: bold;
        font-size: 18px;
        color: #495057;
    }
    .rating-card {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .rating-title {
        font-weight: 600;
        color: #495057;
        margin-bottom: 10px;
    }
    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 12px;
    }
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    .status-approved {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .image-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    .image-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .image-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .image-item:hover img {
        transform: scale(1.05);
    }
</style>
@endsection

@section('content')
<!-- Rate Details -->
<section id="rate-details">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{__('admin.rate_details')}}</h4>
                    <div class="d-flex align-items-center gap-3">
                        <span class="status-badge status-{{ $rate->status }}">
                            {{ ucfirst(__('admin.' . $rate->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">

                        <!-- Rateable and User Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">{{__('admin.rated_item_information')}}</h6>
                                <p><strong>{{__('admin.rate_type')}}:</strong> {{ __('admin.' . strtolower(class_basename($rate->rateable_type))) }}</p>
                                <p><strong>{{__('admin.rateable_name')}}:</strong> {{ $rate->rateable->commercial_name ?? $rate->rateable->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">{{__('admin.customer_information')}}</h6>
                                <p><strong>{{__('admin.customer_name')}}:</strong> {{ $rate->user->name ?? 'N/A' }}</p>
                                <p><strong>{{__('admin.review_date')}}:</strong> {{ $rate->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>

                        <!-- Rating Section -->
                        <div class="rating-card">
                            <h5 class="mb-3">{{__('admin.rating')}}</h5>

                            <!-- Main Rating -->
                            <div class="rating-display">
                                <div class="rating-title" style="min-width: 120px;">{{__('admin.overall_rating')}}:</div>
                                <div class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $rate->rate)
                                            <span class="star filled">★</span>
                                        @elseif($i - 0.5 <= $rate->rate)
                                            <span class="star half">★</span>
                                        @else
                                            <span class="star">★</span>
                                        @endif
                                    @endfor
                                </div>
                                <span class="rating-number">{{ $rate->rate }}/5</span>
                            </div>
                        </div>

                        <!-- Review Body -->
                        @if($rate->body)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{__('admin.review_body')}}</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $rate->body }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Images Section -->
                        @php
                            $images = $rate->getMedia('rate-media')->filter(function($media) {
                                return str_starts_with($media->mime_type, 'image/');
                            });
                        @endphp
                        @if($images->count() > 0)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{__('admin.images')}} ({{ $images->count() }})</h5>
                                </div>
                                <div class="card-body">
                                    <div class="image-gallery">
                                        @foreach($images as $image)
                                            <div class="image-item">
                                                <img src="{{ $image->getUrl() }}" alt="{{__('admin.rate_image')}}" onclick="openImageModal('{{ $image->getUrl() }}', '{{ $image->name }}')">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Videos Section -->
                        @php
                            $videos = $rate->getMedia('rate-media')->filter(function($media) {
                                return str_starts_with($media->mime_type, 'video/');
                            });
                        @endphp
                        @if($videos->count() > 0)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ __('admin.videos') }} ({{ $videos->count() }})</h5>
                            </div>
                            <div class="card-body">
                                <div class="image-gallery">
                                    @foreach($videos as $video)
                                    <div class="image-item">
                                        <video width="100%" height="200" controls onclick="openVideoInNewTab('{{ $video->getUrl() }}')" style="cursor: pointer;">
                                            <source src="{{ $video->getUrl() }}" type="{{ $video->mime_type }}">
                                        </video>
                                        <div class="mt-2">
                                            <a href="{{ $video->getUrl() }}" target="_blank" class="btn btn-primary btn-sm">
                                                <i class="feather icon-eye"></i> {{ __('admin.view_full') }}
                                            </a>
                                            @if($rate->status == 'approved')
                                            <button class="btn btn-success btn-sm" onclick="publishVideo({{ $rate->id }}, {{ $video->id }})">
                                                <i class="feather icon-share"></i> {{ __('admin.publish_video') }}
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 col-12">
            <!-- Status Management -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{__('admin.status_management')}}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="status-select">{{__('admin.current_status')}}</label>
                        <select id="status-select" class="form-control" onchange="updateStatus()">
                            <option value="pending" {{ $rate->status == 'pending' ? 'selected' : '' }}>
                                {{__('admin.pending')}}
                            </option>
                            <option value="approved" {{ $rate->status == 'approved' ? 'selected' : '' }}>
                                {{__('admin.approved')}}
                            </option>
                            <option value="rejected" {{ $rate->status == 'rejected' ? 'selected' : '' }}>
                                {{__('admin.rejected')}}
                            </option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <i class="feather icon-info"></i>
                            {{__('admin.status_change_note')}}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{__('admin.quick_stats')}}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{__('admin.rating_value')}}:</span>
                        <strong>{{ $rate->rate }}/5</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{__('admin.review_date')}}:</span>
                        <strong>{{ $rate->created_at->format('M d, Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{__('admin.customer')}}:</span>
                        <strong>{{ $rate->user->name ?? 'N/A' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{__('admin.images_count')}}:</span>
                        <strong>{{ $images->count() ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{__('admin.videos_count')}}:</span>
                        <strong>{{ $videos->count() ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">{{__('admin.image_preview')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="{{__('admin.rate_image')}}">
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Image modal functionality
    function openImageModal(imageUrl, imageName) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('imageModalLabel').textContent = imageName || '{{__("admin.image_preview")}}';
        $('#imageModal').modal('show');
    }

    // Video new tab functionality
    function openVideoInNewTab(videoUrl) {
        window.open(videoUrl, '_blank');
    }

    // Status update functionality
    function updateStatus() {
        const statusSelect = document.getElementById('status-select');
        const newStatus = statusSelect.value;
        const rateId = {{ $rate->id }};

        Swal.fire({
            title: '{{__("admin.confirm_status_change")}}',
            text: '{{__("admin.are_you_sure_change_status")}}',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{__("admin.yes_change")}}',
            cancelButtonText: '{{__("admin.cancel")}}'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: '{{__("admin.updating")}}',
                    text: '{{__("admin.please_wait")}}',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Make AJAX request
                fetch(`/admin/rates/${rateId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '{{__("admin.success")}}',
                            text: '{{__("admin.status_updated_successfully")}}',
                            icon: 'success',
                            confirmButtonText: '{{__("admin.ok")}}'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        throw new Error(data.message || '{{__("admin.error_occurred")}}');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: '{{__("admin.error")}}',
                        text: error.message || '{{__("admin.error_occurred")}}',
                        icon: 'error',
                        confirmButtonText: '{{__("admin.ok")}}'
                    });
                    // Reset select to original value
                    statusSelect.value = '{{ $rate->status }}';
                });
            } else {
                // Reset select to original value if cancelled
                statusSelect.value = '{{ $rate->status }}';
            }
        });
    }

    function publishVideo(rateId, mediaId) {
        Swal.fire({
            title: '{{ __("admin.confirm_publish_title") }}',
            text: '{{ __("admin.confirm_publish_text") }}',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '{{ __("admin.yes_publish") }}',
            cancelButtonText: '{{ __("admin.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/rates/${rateId}/publish-video`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ media_id: mediaId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('{{ __("admin.success") }}', data.message, 'success');
                        // Disable the publish button for this video
                        document.querySelector(`button[onclick="publishVideo(${rateId}, ${mediaId})"]`).disabled = true;
                        document.querySelector(`button[onclick="publishVideo(${rateId}, ${mediaId})"]`).textContent = '{{ __("admin.published") }}';
                    } else {
                        Swal.fire('{{ __("admin.error") }}', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('{{ __("admin.error") }}', '{{ __("admin.error_occurred") }}', 'error');
                });
            }
        });
    }
</script>
@endsection