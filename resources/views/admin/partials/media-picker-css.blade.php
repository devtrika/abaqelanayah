<!-- MediaElement.js CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mediaelement/4.2.16/mediaelementplayer.min.css">
<style>
    .stage-item {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f9f9f9;
    }
    .stage-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .remove-stage {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 3px;
        padding: 5px 10px;
        cursor: pointer;
    }
    .video-preview {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        background-color: #f8f9fa;
    }
    .video-preview-container {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        background-color: #f8f9fa;
    }
    .current-video {
        border: 1px solid #28a745;
        border-radius: 5px;
        padding: 15px;
        background-color: #f8fff9;
    }
    .media-picker-container {
        position: relative;
        margin-bottom: 15px;
    }
    .media-picker-button {
        background: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        cursor: pointer;
        font-size: 14px;
    }
    .media-picker-button:hover {
        background: #0056b3;
    }
    .media-picker-modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }
    .media-picker-content {
        background-color: white;
        margin: 5% auto;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 600px;
        max-height: 80vh;
        overflow-y: auto;
    }
    .media-picker-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
    }
    .media-picker-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
    }
    .media-picker-close:hover {
        color: #000;
    }
    .media-picker-tabs {
        display: flex;
        margin-bottom: 20px;
        border-bottom: 1px solid #ddd;
    }
    .media-picker-tab {
        padding: 10px 20px;
        cursor: pointer;
        border: none;
        background: none;
        border-bottom: 2px solid transparent;
    }
    .media-picker-tab.active {
        border-bottom-color: #007bff;
        color: #007bff;
    }
    .media-picker-tab-content {
        display: none;
    }
    .media-picker-tab-content.active {
        display: block;
    }
    .media-picker-upload-area {
        border: 2px dashed #ddd;
        border-radius: 10px;
        padding: 40px;
        text-align: center;
        margin-bottom: 20px;
        cursor: pointer;
        transition: border-color 0.3s;
    }
    .media-picker-upload-area:hover {
        border-color: #007bff;
    }
    .media-picker-upload-area.dragover {
        border-color: #007bff;
        background-color: #f8f9fa;
    }
    .media-picker-url-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .media-picker-preview {
        margin-top: 15px;
        text-align: center;
    }
    .media-picker-preview video {
        max-width: 100%;
        border-radius: 5px;
    }
    .media-picker-actions {
        margin-top: 20px;
        text-align: right;
    }
    .media-picker-select {
        background: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        cursor: pointer;
        margin-left: 10px;
    }
    .media-picker-select:hover {
        background: #218838;
    }
    .media-picker-cancel {
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        cursor: pointer;
    }
    .media-picker-cancel:hover {
        background: #5a6268;
    }
    .file-info {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .file-info h6 {
        color: #495057;
        margin-bottom: 10px;
        font-weight: 600;
    }
    .file-info p {
        margin-bottom: 5px;
        color: #6c757d;
    }
    .file-info strong {
        color: #495057;
    }
    .error-message {
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
        display: none;
    }
    .error-message.show {
        display: block;
    }
    .input-error {
        border-color: #dc3545 !important;
    }
    .input-error:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    /* Media picker error styling */
    .media-picker-error {
        color: #dc3545;
        font-size: 14px;
        margin-top: 10px;
        padding: 10px;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
        text-align: center;
    }
</style> 