@extends('admin.layout.master')
@section('title') {{ __('admin.add_course') }} @endsection
{{-- extra css files --}}
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    @include('admin.partials.media-picker-css')
@endsection

@section('content')
<!-- Basic multiple Column Form section start -->
<form method="POST" action="{{route('admin.courses.store')}}" class="store form-horizontal" novalidate enctype="multipart/form-data">
<section id="multiple-column-form">
    <div class="row">
        <div class="col-md-3">
            <div class="col-12 card card-body">
                <div class="imgMontg col-12 text-center">
                    <div class="dropBox">
                        <div class="textCenter">
                            <div class="imagesUploadBlock">
                                <label class="uploadImg">
                                    <span><i class="feather icon-image"></i></span>
                                    <input type="file" accept="image/*" name="image" class="imageUploader" required>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-9">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        @csrf
                        <div class="form-body">
                            <div class="row">
                                <!-- Course Name Arabic -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="name_ar">{{ __('admin.course_name') }} ({{ __('admin.ar') }}) <span class="text-danger">*</span></label>
                                        <input type="text" id="name_ar" class="form-control" name="name[ar]" required minlength="5">
                                        <div class="error-message" id="name_ar_error"></div>
                                    </div>
                                </div>

                                <!-- Course Name English -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="name_en">{{ __('admin.course_name') }} ({{ __('admin.en') }}) <span class="text-danger">*</span></label>
                                        <input type="text" id="name_en" class="form-control" name="name[en]" required minlength="5">
                                        <div class="error-message" id="name_en_error"></div>
                                    </div>
                                </div>

                                <!-- Instructor Name Arabic -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="instructor_name_ar">{{ __('admin.instructor_name') }} ({{ __('admin.ar') }}) <span class="text-danger">*</span></label>
                                        <input type="text" id="instructor_name_ar" class="form-control" name="instructor_name[ar]" required minlength="5">
                                        <div class="error-message" id="instructor_name_ar_error"></div>
                                    </div>
                                </div>

                                <!-- Instructor Name English -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="instructor_name_en">{{ __('admin.instructor_name') }} ({{ __('admin.en') }}) <span class="text-danger">*</span></label>
                                        <input type="text" id="instructor_name_en" class="form-control" name="instructor_name[en]" required minlength="5">
                                        <div class="error-message" id="instructor_name_en_error"></div>
                                    </div>
                                </div>

                                <!-- Duration -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="duration">{{ __('admin.duration_hours') }} <span class="text-danger">*</span></label>
                                        <input type="number" id="duration" class="form-control" name="duration" required min="1" step="0.5">
                                        <div class="error-message" id="duration_error"></div>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="price">{{ __('admin.price') }} <span class="text-danger">*</span></label>
                                        <input type="number" id="price" class="form-control" name="price" required min="0" step="0.01">
                                        <div class="error-message" id="price_error"></div>
                                    </div>
                                </div>

                                <!-- Description Arabic -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="description_ar">{{ __('admin.description') }} ({{ __('admin.ar') }}) <span class="text-danger">*</span></label>
                                        <textarea id="description_ar" class="form-control" name="description[ar]" rows="4" required minlength="20"></textarea>
                                        <div class="error-message" id="description_ar_error"></div>
                                    </div>
                                </div>

                                <!-- Description English -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="description_en">{{ __('admin.description') }} ({{ __('admin.en') }}) <span class="text-danger">*</span></label>
                                        <textarea id="description_en" class="form-control" name="description[en]" rows="4" required minlength="20"></textarea>
                                        <div class="error-message" id="description_en_error"></div>
                                    </div>
                                </div>

                             
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Stages Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('admin.course_stages') }}</h4>
                    <button type="button" class="btn btn-primary" id="add-stage">{{ __('admin.add_stage') }}</button>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div id="stages-container">
                            <!-- Stages will be added here dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{ __('admin.save') }}</button>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-light-secondary mr-1 mb-1">{{ __('admin.cancel') }}</a>
        </div>
    </div>
</section>
</form>
@endsection

@section('js')
<script src="{{asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
<script src="{{asset('admin/app-assets/js/scripts/forms/validation/form-validation.js')}}"></script>
<script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
<script src="{{asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js')}}"></script>
@include('admin.partials.media-picker-js')

<script>
    let stageIndex = 0;
    let currentMediaPicker = null;
    let validationRules = {
        video_size_limit_kb: 1200, // Default fallback
        video_allowed_types: ['video/mp4', 'video/avi', 'video/mov', 'video/wmv']
    };

    // Fetch validation rules from backend
    function fetchValidationRules() {
        console.log('Fetching validation rules from backend...');
        $.ajax({
            url: '{{ route("admin.courses.validationRules") }}',
            method: 'GET',
            cache: false, // Disable caching
            success: function(response) {
                validationRules = response;
                console.log('Validation rules loaded from backend:', validationRules);
                console.log('Video size limit:', validationRules.video_size_limit_kb, 'KB');
            },
            error: function(xhr) {
                console.warn('Failed to load validation rules from backend, using defaults');
                console.error('Error details:', xhr.responseText);
            }
        });
    }

    // Media picker validation functions - moved to top
    function validateVideoFile(file) {
        console.log('Validating file:', file.name, file.size, file.type);
        console.log('Using validation rules:', validationRules);
        
        const maxSize = validationRules.video_size_limit_kb * 1024; // Convert KB to bytes
        const allowedTypes = validationRules.video_allowed_types;
        
        // Check file size
        if (file.size > maxSize) {
            const errorMsg = `حجم الملف (${(file.size / 1024).toFixed(1)} KB) أكبر من الحد المسموح (${validationRules.video_size_limit_kb} KB)`;
            console.log('File size error:', errorMsg);
            return errorMsg;
        }
        
        // Check file type
        if (!allowedTypes.includes(file.type)) {
            const errorMsg = 'نوع الملف غير مدعوم. الأنواع المدعومة: MP4, AVI, MOV, WMV';
            console.log('File type error:', errorMsg, 'File type:', file.type);
            return errorMsg;
        }
        
        console.log('File validation passed');
        return null; // No error
    }

    function showMediaPickerError(message) {
        console.log('Showing media picker error:', message);
        // Remove any existing error message
        $('.media-picker-error').remove();
        
        // Add error message to media picker
        const errorHtml = `<div class="media-picker-error text-danger mt-2">${message}</div>`;
        $('#media-picker-modal .media-picker-actions').before(errorHtml);
    }

    function clearMediaPickerError() {
        console.log('Clearing media picker error');
        $('.media-picker-error').remove();
    }

    // Add stage functionality
    $('#add-stage').click(function() {
        addStage();
    });

    // Add initial stage
    $(document).ready(function() {
        fetchValidationRules(); // Load validation rules from backend
        addStage();
        initializeMediaPicker();
        
        // Add manual refresh button for testing (remove in production)
        $('body').append('<button id="refresh-validation-rules" style="position: fixed; top: 10px; right: 10px; z-index: 9999; background: #007bff; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">Refresh Validation Rules</button>');
        $('#refresh-validation-rules').click(function() {
            console.log('Manual refresh of validation rules...');
            fetchValidationRules();
        });
    });

    function addStage() {
        const stageHtml = `
            <div class="stage-item" data-index="${stageIndex}">
                <div class="stage-header">
                    <h5>المرحلة ${stageIndex + 1}</h5>
                    <button type="button" class="remove-stage" onclick="removeStage(this)">حذف المرحلة</button>
                </div>
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="stage_${stageIndex}_title_ar">عنوان المرحلة (عربي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="stage_${stageIndex}_title_ar" name="stages[${stageIndex}][title][ar]" required minlength="5">
                            <div class="error-message" id="stage_${stageIndex}_title_ar_error"></div>
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="stage_${stageIndex}_title_en">عنوان المرحلة (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="stage_${stageIndex}_title_en" name="stages[${stageIndex}][title][en]" required minlength="5">
                            <div class="error-message" id="stage_${stageIndex}_title_en_error"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>فيديو المرحلة <span class="text-danger">*</span></label>
                            <div class="media-picker-container">
                                <button type="button" class="media-picker-button" onclick="openMediaPicker(${stageIndex})">
                                    <i class="feather icon-video"></i> اختيار الفيديو
                                </button>
                                <input type="file" name="stages[${stageIndex}][video]" class="video-file-input" accept="video/*" style="display: none;" required>
                                <input type="hidden" name="stages[${stageIndex}][video_url]" class="video-url-input">
                                <div class="video-preview mt-3" id="video-preview-${stageIndex}" style="display: none;">
                                    <video width="300" height="200" controls style="border-radius: 5px;">
                                        متصفحك لا يدعم عرض الفيديو
                                    </video>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeVideoPreview(${stageIndex})">
                                            <i class="feather icon-trash"></i> إزالة الفيديو
                                        </button>
                                    </div>
                                </div>
                                <div class="error-message" id="stage_${stageIndex}_video_error"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#stages-container').append(stageHtml);
        stageIndex++;
        updateStageNumbers();
    }

    function removeStage(button) {
        if ($('.stage-item').length > 1) {
            $(button).closest('.stage-item').remove();
            updateStageNumbers();
        } else {
            Swal.fire('تنبيه!', 'يجب أن تحتوي الدورة على مرحلة واحدة على الأقل', 'warning');
        }
    }

    function updateStageNumbers() {
        $('.stage-item').each(function(index) {
            $(this).find('.stage-header h5').text('المرحلة ' + (index + 1));
        });
    }

    // Media Picker Functions
    function initializeMediaPicker() {
        // Create modal HTML
        const modalHtml = `
            <div id="media-picker-modal" class="media-picker-modal">
                <div class="media-picker-content">
                    <div class="media-picker-header">
                        <h4>اختيار الفيديو</h4>
                        <button type="button" class="media-picker-close" onclick="closeMediaPicker()">&times;</button>
                    </div>
                    <div id="upload-tab" class="media-picker-tab-content active">
                        <div class="media-picker-upload-area" id="upload-area">
                            <i class="feather icon-upload" style="font-size: 48px; color: #666; margin-bottom: 15px;"></i>
                            <h5>اسحب وأفلت الفيديو هنا</h5>
                            <p>أو انقر لاختيار ملف</p>
                            <input type="file" id="video-file-input" accept="video/*" style="display: none;">
                        </div>
                    </div>
                    <div class="media-picker-preview" id="media-preview" style="display: none;">
                        <video id="preview-video" width="400" height="300" controls>
                            متصفحك لا يدعم عرض الفيديو
                        </video>
                    </div>
                    <div class="media-picker-actions">
                        <button type="button" class="media-picker-cancel" onclick="closeMediaPicker()">إلغاء</button>
                        <button type="button" class="media-picker-select" onclick="selectMedia()">اختيار</button>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
        
        // Initialize drag and drop
        initializeDragAndDrop();
        
        // Initialize file input
        $('#video-file-input').on('change', handleFileSelect);
    }

    function openMediaPicker(stageIndex) {
        console.log('Opening media picker for stage:', stageIndex);
        currentMediaPicker = stageIndex;
        $('#media-picker-modal').show();
        // Clear any previous errors when opening modal
        clearMediaPickerError();
    }

    function closeMediaPicker() {
        console.log('Closing media picker');
        $('#media-picker-modal').hide();
        currentMediaPicker = null;
        resetMediaPicker();
    }

    function initializeDragAndDrop() {
        const uploadArea = document.getElementById('upload-area');
        const fileInput = document.getElementById('video-file-input');

        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect();
            }
        });
    }

    function handleFileSelect() {
        console.log('File selected');
        const file = document.getElementById('video-file-input').files[0];
        if (file) {
            console.log('Processing file:', file.name);
            // Validate file immediately when selected
            const error = validateVideoFile(file);
            if (error) {
                showMediaPickerError(error);
                // Clear the file input
                document.getElementById('video-file-input').value = '';
                return;
            }
            
            // Clear any previous errors
            clearMediaPickerError();
            
            // Show file info instead of video preview
            showFilePreview(file);
        }
    }

    function showFilePreview(file) {
        const preview = document.getElementById('media-preview');
        const video = document.getElementById('preview-video');
        
        // Show file information
        const fileSize = (file.size / (1024 * 1024)).toFixed(2);
        const fileInfo = `
            <div class="file-info mb-3">
                <h6>معلومات الملف:</h6>
                <p><strong>اسم الملف:</strong> ${file.name}</p>
                <p><strong>حجم الملف:</strong> ${fileSize} MB</p>
                <p><strong>نوع الملف:</strong> ${file.type}</p>
            </div>
        `;
        
        // Create a temporary blob URL for preview only
        const url = URL.createObjectURL(file);
        video.src = url;
        preview.style.display = 'block';
        
        // Add file info before the video
        if (!preview.querySelector('.file-info')) {
            video.insertAdjacentHTML('beforebegin', fileInfo);
        }
        
        // Clean up the blob URL when video loads
        video.onload = function() {
            URL.revokeObjectURL(url);
        };
    }

    function resetMediaPicker() {
        const fileInput = document.getElementById('video-file-input');
        const preview = document.getElementById('media-preview');
        const video = document.getElementById('preview-video');
        
        // Clean up any existing blob URL
        if (video.src && video.src.startsWith('blob:')) {
            URL.revokeObjectURL(video.src);
        }
        
        // Remove file info if exists
        const fileInfo = preview.querySelector('.file-info');
        if (fileInfo) {
            fileInfo.remove();
        }
        
        // Clear any error messages
        clearMediaPickerError();
        
        fileInput.value = '';
        video.src = '';
        preview.style.display = 'none';
    }

    function selectMedia() {
        console.log('Selecting media for stage:', currentMediaPicker);
        if (currentMediaPicker === null) return;

        const fileInput = document.getElementById('video-file-input');
        
        if (fileInput.files.length === 0) {
            Swal.fire('خطأ!', 'يرجى اختيار فيديو أولاً', 'error');
            return;
        }

        // Validate file again before selecting
        const file = fileInput.files[0];
        const error = validateVideoFile(file);
        if (error) {
            showMediaPickerError(error);
            return;
        }

        // Clear any errors
        clearMediaPickerError();

        // Update the stage
        const stageItem = $(`.stage-item[data-index="${currentMediaPicker}"]`);
        const stageFileInput = stageItem.find('.video-file-input');
        const stageUrlInput = stageItem.find('.video-url-input');
        const videoPreview = stageItem.find('.video-preview');
        const previewVideo = videoPreview.find('video')[0];

        // Set the actual file
        const dt = new DataTransfer();
        dt.items.add(file);
        stageFileInput[0].files = dt.files;
        stageUrlInput.val(''); // Clear URL input
        
        // Create a temporary blob URL for preview only
        const previewUrl = URL.createObjectURL(file);
        previewVideo.src = previewUrl;
        
        // Clean up the blob URL when video loads
        previewVideo.onload = function() {
            URL.revokeObjectURL(previewUrl);
        };

        videoPreview.show();
        closeMediaPicker();
    }

    // Remove video preview
    function removeVideoPreview(stageIndex) {
        const stageItem = $(`.stage-item[data-index="${stageIndex}"]`);
        const stageFileInput = stageItem.find('.video-file-input');
        const stageUrlInput = stageItem.find('.video-url-input');
        const videoPreview = stageItem.find('.video-preview');
        const previewVideo = videoPreview.find('video')[0];

        // Clean up any existing blob URL
        if (previewVideo.src && previewVideo.src.startsWith('blob:')) {
            URL.revokeObjectURL(previewVideo.src);
        }

        stageFileInput.val('');
        stageUrlInput.val('');
        previewVideo.src = '';
        videoPreview.hide();
    }

    // Close modal when clicking outside
    $(window).click(function(event) {
        const modal = document.getElementById('media-picker-modal');
        if (event.target === modal) {
            closeMediaPicker();
        }
    });

    // Form validation before submission
    $('.store').on('submit', function(e) {
        e.preventDefault();
        clearAllErrors();
        var form = this;
        var formData = new FormData(form);
        
        // Show loading state
        $(".submit_button").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').attr('disabled', true);
        
        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Reset button state
                $(".submit_button").html("{{ __('admin.save') }}").attr('disabled', false);
                
                // Show success message
                Swal.fire({
                    position: 'top-start',
                    type: 'success',
                    title: '{{ __('admin.added_successfully') }}',
                    showConfirmButton: false,
                    timer: 1500,
                    confirmButtonClass: 'btn btn-primary',
                    buttonsStyling: false,
                });

                // Redirect after success message
                setTimeout(function() {
                    window.location.href = response.url;
                }, 1000);
            },
            error: function(xhr) {
                // Reset button state
                $(".submit_button").html("{{ __('admin.save') }}").attr('disabled', false);
                
                clearAllErrors();
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    // Track which stage errors have been shown to avoid duplicate messages
                    const shownStageErrors = {};
                    Object.keys(errors).forEach(function(key) {
                        // Handle stage title errors
                        var match = key.match(/^stages\.(\d+)\.title\.(ar|en)$/);
                        if (match) {
                            var stageIdx = match[1];
                            var lang = match[2];
                            showError(`stage_${stageIdx}_title_${lang}`, errors[key][0]);
                            return;
                        }
                        // Handle stage video errors
                        var matchVideo = key.match(/^stages\.(\d+)\.(video|video_url)$/);
                        if (matchVideo) {
                            var stageIdx = matchVideo[1];
                            // Only show the first error for this stage video
                            if (!shownStageErrors[stageIdx]) {
                                showError(`stage_${stageIdx}_video`, errors[key][0]);
                                shownStageErrors[stageIdx] = true;
                            }
                            return;
                        }
                        // Handle other errors (course fields)
                        var inputId = key.replace(/\./g, '_');
                        showError(inputId, errors[key][0]);
                    });
                } else {
                    Swal.fire('خطأ!', xhr.responseJSON?.error || 'حدث خطأ غير متوقع', 'error');
                }
            }
        });
    });

    // Function to show error under input
    function showError(inputId, message) {
        const errorElement = $(`#${inputId}_error`);
        const inputElement = $(`#${inputId}`);
        
        errorElement.text(message).addClass('show');
        inputElement.addClass('input-error');
    }

    // Function to clear all errors
    function clearAllErrors() {
        $('.error-message').removeClass('show').text('');
        $('.form-control').removeClass('input-error');
    }

    // Real-time validation on input change
    $('input, textarea').on('input blur', function() {
        const inputId = $(this).attr('id');
        const value = $(this).val().trim();
        
        // Clear error when user starts typing
        if (value) {
            $(`#${inputId}_error`).removeClass('show').text('');
            $(this).removeClass('input-error');
        }
    });
</script>

@include('admin.shared.addImage')
{{-- show selected image script --}}

{{-- submit add form script --}}
{{-- @include('admin.shared.submitAddForm') --}}
@endsection