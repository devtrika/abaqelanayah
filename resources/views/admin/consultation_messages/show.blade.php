@extends('admin.layout.master')
@section('css')
<style>
    .chat-input-wrapper {
        background: #fff;
        border-radius: 24px;
        border: 1px solid #e0e0e0;
        padding: 6px 12px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .chat-textarea {
        border: none;
        outline: none;
        box-shadow: none;
        min-height: 38px;
        max-height: 120px;
        resize: none;
        flex: 1 1 auto;
        background: transparent;
    }
    .file-upload-label {
        cursor: pointer;
        color: #888;
        font-size: 18px;
        transition: color 0.2s;
    }
    .file-upload-label:hover {
        color: #007bff;
    }
    .chat-send-btn {
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    </style> 
@endsection
@section('content')
<div class="container">
    <h2>Conversation with {{ $client->name }}</h2>
    <div class="card mb-3">
        <div class="card-body" id="chat-messages" style="max-height: 400px; overflow-y: auto;">
            @foreach($messages as $message)
                <div class="mb-2 d-flex {{ $message->sender_type === 'admin' ? 'justify-content-end' : 'justify-content-start' }}">
                    @if($message->sender_type !== 'admin')
                        <img src="{{ $client->getFirstMediaUrl('profile') ?: asset('images/default-avatar.png') }}" alt="{{ $client->name }}" class="me-2 rounded-circle" style="width:32px;height:32px;object-fit:cover;">
                    @endif
                    <div class="p-2 rounded {{ $message->sender_type === 'admin' ? 'bg-primary text-white text-end' : 'bg-light text-dark text-start' }}" style="max-width: 70%;">
                        <strong>{{ $message->sender_type === 'admin' ? 'Admin' : $client->name }}:</strong>
                        <span>{{ $message->message }}</span>
                        @php $media = $message->getFirstMedia('chat-attachments'); @endphp
                        @if($media)
                            <div class="mt-2">
                                @if(Str::startsWith($media->mime_type, 'image/'))
                                    <img src="{{ $media->getUrl() }}" alt="attachment" style="max-width: 150px; max-height: 150px; border-radius: 8px;">
                                @else
                                    <a href="{{ $media->getUrl() }}" target="_blank">Download Attachment</a>
                                @endif
                            </div>
                        @endif
                        <div>
                            <small class="{{ $message->sender_type === 'admin' ? 'text-dark' : 'text-muted' }}">{{ $message->created_at->format('Y-m-d H:i') }}</small>
                        </div>
                    </div>
                    @if($message->sender_type === 'admin')
                        <img src="{{ $message->admin->avatar }}" alt="Admin" class="ms-2 rounded-circle" style="width:32px;height:32px;object-fit:cover;">
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    <form id="reply-form" method="POST" action="{{ route('admin.consultations.reply', $client->id) }}" enctype="multipart/form-data" class="chat-input-form">
        @csrf
        <div class="chat-input-wrapper d-flex align-items-center">
            <label for="file-upload" class="file-upload-label mb-0 me-2" title="Attach file">
                <i class="fa fa-paperclip"></i>
                <input id="file-upload" type="file" name="file" class="d-none" />
            </label>
            <span id="file-preview" class="me-2"></span>
            <textarea name="message" class="form-control chat-textarea" rows="1" placeholder="Type your reply..." style="resize: none;"></textarea>
            <button type="submit" class="btn btn-success ms-2 chat-send-btn">
                <i class="fa fa-paper-plane"></i>
            </button>
        </div>
        <div id="reply-error" class="text-danger mb-2" style="display:none;"></div>
    </form>
</div>
@endsection

@section('js')
<script>
$(function() {
    $('.chat-textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    $('#file-upload').on('change', function(e) {
        var file = this.files[0];
        var $preview = $('#file-preview');
        $preview.empty();
        if (file) {
            if (file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $preview.html('<img src="' + e.target.result + '" alt="preview" style="max-width:40px; max-height:40px; border-radius:6px;">');
                };
                reader.readAsDataURL(file);
            } else {
                $preview.text(file.name);
            }
        }
    });
    $('#reply-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('button[type=submit]');
        var $error = $('#reply-error');
        $btn.prop('disabled', true);
        $error.hide().text('');
        var formData = new FormData(this);
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.message) {
                    var html = `<div class=\"mb-2 d-flex justify-content-end\"><div class=\"p-2 rounded bg-primary text-white text-end\" style=\"max-width: 70%;\"><strong>Admin:</strong> <span>${res.message.message ?? ''}</span>`;
                    if (res.message.file_url) {
                        if (res.message.file_type && res.message.file_type.startsWith('image/')) {
                            html += `<div class=\"mt-2\"><img src=\"${res.message.file_url}\" alt=\"attachment\" style=\"max-width: 150px; max-height: 150px; border-radius: 8px;\"></div>`;
                        } else {
                            html += `<div class=\"mt-2\"><a href=\"${res.message.file_url}\" target=\"_blank\">Download Attachment</a></div>`;
                        }
                    }
                    html += `<div><small class=\"text-muted\">${new Date(res.message.created_at).toLocaleString()}</small></div></div></div>`;
                    $('#chat-messages').append(html);
                    $form[0].reset();
                    $('#file-preview').empty();
                    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error) {
                    $error.text(xhr.responseJSON.error).show();
                } else {
                    $error.text('An error occurred. Please try again.').show();
                }
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

