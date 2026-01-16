@extends('admin.layout.master')

@section('content')
<section id="blog-show">
    <div class="row">
        <!-- Blog Details Card -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{__('admin.view') . ' ' . __('admin.blog')}}</h4>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                        <i class="feather icon-arrow-left"></i> {{__('admin.back')}}
                    </a>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <!-- Blog Title -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h3 class="text-primary">{{ $blog->title }}</h3>
                            </div>
                        </div>

                        <!-- Blog Image -->
                        @if($blog->getFirstMediaUrl('blogs'))
                        <div class="row mb-3">
                            <div class="col-12">
                                <img src="{{ $blog->getFirstMediaUrl('blogs') }}"
                                     alt="{{ $blog->title }}"
                                     class="img-fluid rounded"
                                     style="max-height: 400px; object-fit: cover;">
                            </div>
                        </div>
                        @endif

                        <!-- Blog Content -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>{{__('admin.content')}}</h5>
                                <div class="border p-3 rounded bg-light">
                                    {!! $blog->content !!}
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <i class="feather icon-thumbs-up font-large-2"></i>
                                        <h3 class="mt-1">{{ $blog->reactions->where('reaction', 'like')->count() }}</h3>
                                        <p class="mb-0">{{__('admin.likes')}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <i class="feather icon-thumbs-down font-large-2"></i>
                                        <h3 class="mt-1">{{ $blog->reactions->where('reaction', 'dislike')->count() }}</h3>
                                        <p class="mb-0">{{__('admin.dislikes')}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <i class="feather icon-message-circle font-large-2"></i>
                                        <h3 class="mt-1">{{ $blog->comments->count() }}</h3>
                                        <p class="mb-0">{{__('admin.comments')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Likes and Dislikes Users Section -->
                        <div class="row">
                            <!-- Users who liked the blog -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0"><i class="feather icon-thumbs-up"></i> {{ __('admin.likes') }} - {{ $blog->reactions->where('reaction', 'like')->count() }}</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($blog->reactions->where('reaction', 'like')->count() > 0)
                                            <ul class="list-unstyled"></ul>
                                                @foreach($blog->reactions->where('reaction', 'like') as $reaction)
                                                    <li class="media mb-2 align-items-center">
                                                        <img src="{{ $reaction->user->getFirstMediaUrl('profile') }}" alt="{{ $reaction->user->name }}" class="mr-2 rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                                                        <div class="media-body">
                                                            <span>{{ $reaction->user->name }}</span>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted">{{ __('admin.no_data') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- Users who disliked the blog -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0"><i class="feather icon-thumbs-down"></i> {{ __('admin.dislikes') }} - {{ $blog->reactions->where('reaction', 'dislike')->count() }}</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($blog->reactions->where('reaction', 'dislike')->count() > 0)
                                            <ul class="list-unstyled">
                                                @foreach($blog->reactions->where('reaction', 'dislike') as $reaction)
                                                    <li class="media mb-2 align-items-center">
                                                        <img src="{{ $reaction->user->get() }}" alt="{{ $reaction->user->name }}" class="mr-2 rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                                                        <div class="media-body">
                                                            <span>{{ $reaction->user->name }}</span>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted">{{ __('admin.no_data') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="feather icon-message-circle"></i>
                        {{__('admin.comments')}} ({{ $blog->comments()->count() }})
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        @if($comments->count() > 0)
                            <div class="row" id="comments-container">
                                @include('admin.blogs.partials.comments', ['comments' => $comments])
                            </div>

                            <!-- Load More Button -->
                            @if($comments->hasMorePages())
                            <div class="text-center mt-3">
                                <button type="button"
                                        class="btn btn-outline-primary"
                                        id="load-more-btn"
                                        data-blog-id="{{ $blog->id }}"
                                        data-next-page="{{ $comments->currentPage() + 1 }}">
                                    <i class="feather icon-plus-circle"></i>
                                    {{ __('admin.load_more') }}
                                </button>
                            </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="feather icon-message-circle font-large-2 text-muted"></i>
                                <p class="text-muted mt-2">{{__('admin.no_comments_yet')}}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Handle approval toggle (using event delegation for dynamically loaded content)
    $(document).on('change', '.approval-toggle', function() {
        const commentId = $(this).data('comment-id');
        const isChecked = $(this).is(':checked');
        const toggleElement = $(this);
        const textElement = toggleElement.closest('.ml-3').find('.approval-text');
        const cardElement = toggleElement.closest('.comment-card');

        // Disable toggle during request
        toggleElement.prop('disabled', true);

        $.ajax({
            url: `{{ url('admin/blogs/comments') }}/${commentId}/toggle-approval`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Update text and card styling
                    if (response.is_approved) {
                        textElement.text('{{ __("admin.approved") }}');
                        cardElement.removeClass('border-warning').addClass('border-success');

                        // Show success message
                        toastr.success('{{ __("admin.comment_approved_successfully") }}');
                    } else {
                        textElement.text('{{ __("admin.pending") }}');
                        cardElement.removeClass('border-success').addClass('border-warning');

                        // Show info message
                        toastr.info('{{ __("admin.comment_unapproved_successfully") }}');
                    }
                } else {
                    // Revert toggle state on error
                    toggleElement.prop('checked', !isChecked);
                    toastr.error(response.message || '{{ __("admin.error_occurred") }}');
                }
            },
            error: function(xhr) {
                // Revert toggle state on error
                toggleElement.prop('checked', !isChecked);
                toastr.error('{{ __("admin.error_occurred") }}');
            },
            complete: function() {
                // Re-enable toggle
                toggleElement.prop('disabled', false);
            }
        });
    });

    // Handle load more comments
    $('#load-more-btn').on('click', function() {
        const button = $(this);
        const blogId = button.data('blog-id');
        const nextPage = button.data('next-page');
        const originalText = button.html();

        // Show loading state
        button.prop('disabled', true);
        button.html('<i class="feather icon-loader"></i> {{ __("admin.loading") }}...');

        $.ajax({
            url: `{{ url('admin/blogs') }}/${blogId}/comments/load-more`,
            type: 'GET',
            data: {
                page: nextPage
            },
            success: function(response) {
                if (response.success) {
                    // Append new comments to container
                    $('#comments-container').append(response.html);

                    if (response.has_more) {
                        // Update next page number
                        button.data('next-page', response.next_page);
                        button.prop('disabled', false);
                        button.html(originalText);
                    } else {
                        // No more comments, hide button
                        button.closest('.text-center').remove();
                    }
                } else {
                    // No more comments
                    button.closest('.text-center').remove();
                    toastr.info(response.message || '{{ __("admin.no_more_comments") }}');
                }
            },
            error: function(xhr) {
                button.prop('disabled', false);
                button.html(originalText);
                toastr.error('{{ __("admin.error_occurred") }}');
            }
        });
    });
});
</script>

<style>
.comment-card {
    transition: all 0.3s ease;
}

.comment-card.border-success {
    border-left: 4px solid #28a745 !important;
}

.comment-card.border-warning {
    border-left: 4px solid #ffc107 !important;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}

.approval-toggle:disabled {
    opacity: 0.6;
}

.custom-control-label .approval-text {
    font-weight: 500;
    margin-left: 5px;
}
</style>
@endsection