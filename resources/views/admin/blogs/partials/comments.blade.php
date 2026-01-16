@foreach($comments as $comment)
<div class="col-12 mb-3">
    <div class="card comment-card {{ $comment->is_approved ? 'border-success' : 'border-warning' }}">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <!-- User Info -->
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar bg-primary mr-1">
                            <span class="avatar-content">
                                {{ substr($comment->user->name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $comment->user->name }}</h6>
                            <small class="text-muted">{{ $comment->created_at->format('Y-m-d H:i') }}</small>
                        </div>
                    </div>

                    <!-- Comment Body -->
                    <p class="mb-2">{{ $comment->comment }}</p>

                    <!-- Rating -->
                    @if($comment->rate)
                    <div class="mb-2">
                        <span class="badge badge-warning">
                            <i class="feather icon-star"></i> 
                            {{ $comment->rate }}/5
                        </span>
                    </div>
                    @endif
                </div>

                <!-- Approval Toggle -->
                <div class="ml-3 d-flex align-items-center">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" 
                               class="custom-control-input approval-toggle" 
                               id="approval-{{ $comment->id }}"
                               data-comment-id="{{ $comment->id }}"
                               {{ $comment->is_approved ? 'checked' : '' }}>
                        <label class="custom-control-label" for="approval-{{ $comment->id }}"></label>
                    </div>
                    <span class="approval-text ml-2">
                        {{ $comment->is_approved ? __('admin.approved') : __('admin.pending') }}
                    </span>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endforeach
