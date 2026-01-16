@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    @include('admin.partials.media-picker-css')
@endsection

@section('content')
<!-- Basic multiple Column Form section start -->
<form method="POST" action="{{route('admin.courses.update', $course->id)}}" class="store form-horizontal" novalidate enctype="multipart/form-data">
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
                                    <input type="file" accept="image/*" name="image" class="imageUploader">
                                </label>
                                <div class="uploadedBlock">
                                    <img src="{{ $course->getfirstMediaUrl('courses') }}">
                                    <button class="close"><i class="la la-times"></i></button>
                                </div>
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
                        @method('PUT')
                        <div class="form-body">
                            <div class="row">
                                <!-- Course Name Arabic -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="name_ar">اسم الدورة (عربي) <span class="text-danger">*</span></label>
                                        <input type="text" id="name_ar" class="form-control" name="name[ar]" value="{{ $course->getTranslation('name', 'ar') }}" required minlength="5">
                                        <div class="error-message" id="name_ar_error"></div>
                                    </div>
                                </div>

                                <!-- Course Name English -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="name_en">اسم الدورة (إنجليزي) <span class="text-danger">*</span></label>
                                        <input type="text" id="name_en" class="form-control" name="name[en]" value="{{ $course->getTranslation('name', 'en') }}" required minlength="5">
                                        <div class="error-message" id="name_en_error"></div>
                                    </div>
                                </div>

                                <!-- Instructor Name Arabic -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="instructor_name_ar">اسم المدرب (عربي) <span class="text-danger">*</span></label>
                                        <input type="text" id="instructor_name_ar" class="form-control" name="instructor_name[ar]" value="{{ $course->getTranslation('instructor_name', 'ar') }}" required minlength="5">
                                        <div class="error-message" id="instructor_name_ar_error"></div>
                                    </div>
                                </div>

                                <!-- Instructor Name English -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="instructor_name_en">اسم المدرب (إنجليزي) <span class="text-danger">*</span></label>
                                        <input type="text" id="instructor_name_en" class="form-control" name="instructor_name[en]" value="{{ $course->getTranslation('instructor_name', 'en') }}" required minlength="5">
                                        <div class="error-message" id="instructor_name_en_error"></div>
                                    </div>
                                </div>

                                <!-- Duration -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="duration">مدة الدورة (بالساعات) <span class="text-danger">*</span></label>
                                        <input type="number" id="duration" class="form-control" name="duration" value="{{ $course->duration }}" required min="1" step="0.5">
                                        <div class="error-message" id="duration_error"></div>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="price">سعر الدورة <span class="text-danger">*</span></label>
                                        <input type="number" id="price" class="form-control" name="price" value="{{ $course->price }}" required min="0" step="0.01">
                                        <div class="error-message" id="price_error"></div>
                                    </div>
                                </div>

                                <!-- Description Arabic -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="description_ar">وصف الدورة (عربي) <span class="text-danger">*</span></label>
                                        <textarea id="description_ar" class="form-control" name="description[ar]" rows="4" required minlength="20">{{ $course->getTranslation('description', 'ar') }}</textarea>
                                        <div class="error-message" id="description_ar_error"></div>
                                    </div>
                                </div>

                                <!-- Description English -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="description_en">وصف الدورة (إنجليزي) <span class="text-danger">*</span></label>
                                        <textarea id="description_en" class="form-control" name="description[en]" rows="4" required minlength="20">{{ $course->getTranslation('description', 'en') }}</textarea>
                                        <div class="error-message" id="description_en_error"></div>
                                    </div>
                                </div>

                                <!-- Active Status -->
                                
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
                    <h4 class="card-title">مراحل الدورة</h4>
                    <button type="button" class="btn btn-primary" id="add-stage">إضافة مرحلة</button>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div id="stages-container">
                            <!-- Existing stages will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary mr-1 mb-1">تحديث</button>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-light-secondary mr-1 mb-1">إلغاء</a>
        </div>
    </div>
</section>
</form>
@endsection

@section('js')
    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    @include('admin.partials.media-picker-js')
    <script>
        let stageIndex = 0;
        let currentMediaPicker = null;

        // Add stage functionality
        $('#add-stage').click(function() {
            addStage();
        });

        // Load existing stages
        $(document).ready(function() {
            @foreach($course->stages as $index => $stage)
                loadExistingStage(
                    {{ $index }},
                    '{{ $stage->getTranslation('title', 'ar') }}',
                    '{{ $stage->getTranslation('title', 'en') }}',
                    '{{ $stage->video_name }}',
                    '{{ $stage->video }}'
                );
            @endforeach

            stageIndex = {{ count($course->stages) }};
            initializeMediaPicker();
        });

        function loadExistingStage(index, titleAr, titleEn, videoName, videoUrl) {
            const stageHtml = `
                <div class="stage-item" data-index="${index}">
                    <div class="stage-header">
                        <h5>المرحلة ${index + 1}</h5>
                        <button type="button" class="remove-stage" onclick="removeStage(this)">حذف المرحلة</button>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label>عنوان المرحلة (عربي) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="stages[${index}][title][ar]" value="${titleAr}" required minlength="5">
                                <div class="error-message" id="stage_${index}_title_ar_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label>عنوان المرحلة (إنجليزي) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="stages[${index}][title][en]" value="${titleEn}" required minlength="5">
                                <div class="error-message" id="stage_${index}_title_en_error"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>فيديو المرحلة</label>
                                <div class="media-picker-container">
                                    <button type="button" class="media-picker-button" onclick="openMediaPicker(${index})">
                                        <i class="feather icon-video"></i> اختيار فيديو جديد
                                    </button>
                                    <input type="file" name="stages[${index}][video]" class="video-file-input" accept="video/*" style="display: none;">
                                    <input type="hidden" name="stages[${index}][video_url]" class="video-url-input">
                                    ${videoUrl ? `
                                        <div class="current-video mt-3" id="current-video-${index}">
                                            <div class="video-preview-container">
                                                <video width="300" height="200" controls style="border-radius: 5px;">
                                                    <source src="${videoUrl}" type="video/mp4">
                                                    متصفحك لا يدعم عرض الفيديو
                                                </video>
                                                <div class="mt-2">
                                                    <strong>الفيديو الحالي:</strong> ${videoName}
                                                    <button type="button" class="btn btn-sm btn-danger ml-2" onclick="removeCurrentVideo(${index})">
                                                        <i class="feather icon-trash"></i> حذف الفيديو
                                                    </button>
                                                </div>
                                                <input type="hidden" name="stages[${index}][remove_video]" value="0" id="remove-video-${index}">
                                            </div>
                                        </div>
                                    ` : ''}
                                    <div class="video-preview mt-3" id="video-preview-${index}" style="display: none;">
                                        <video width="300" height="200" controls style="border-radius: 5px;">
                                            متصفحك لا يدعم عرض الفيديو
                                        </video>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeVideoPreview(${index})">
                                                <i class="feather icon-trash"></i> إزالة الفيديو الجديد
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#stages-container').append(stageHtml);
        }

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
                                <label>عنوان المرحلة (عربي) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="stages[${stageIndex}][title][ar]" required minlength="5">
                                <div class="error-message" id="stage_${stageIndex}_title_ar_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label>عنوان المرحلة (إنجليزي) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="stages[${stageIndex}][title][en]" required minlength="5">
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

        // Remove current video (existing videos)
        function removeCurrentVideo(stageIndex) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم حذف الفيديو الحالي نهائياً',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.value) {
                    $('#current-video-' + stageIndex).hide();
                    $('#remove-video-' + stageIndex).val('1');

                    Swal.fire(
                        'تم الحذف!',
                        'سيتم حذف الفيديو عند حفظ التغييرات',
                        'success'
                    );
                }
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
                        <div class="media-picker-tabs">
                            <button type="button" class="media-picker-tab active" onclick="switchTab('upload')">رفع ملف</button>
                            <button type="button" class="media-picker-tab" onclick="switchTab('url')">رابط URL</button>
                        </div>
                        <div id="upload-tab" class="media-picker-tab-content active">
                            <div class="media-picker-upload-area" id="upload-area">
                                <i class="feather icon-upload" style="font-size: 48px; color: #666; margin-bottom: 15px;"></i>
                                <h5>اسحب وأفلت الفيديو هنا</h5>
                                <p>أو انقر لاختيار ملف</p>
                                <input type="file" id="video-file-input" accept="video/*" style="display: none;">
                            </div>
                        </div>
                        <div id="url-tab" class="media-picker-tab-content">
                            <input type="url" class="media-picker-url-input" id="video-url-input" placeholder="أدخل رابط الفيديو (YouTube, Vimeo, إلخ)">
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
            
            // Initialize URL input
            $('#video-url-input').on('input', handleUrlInput);
        }

        function openMediaPicker(stageIndex) {
            currentMediaPicker = stageIndex;
            $('#media-picker-modal').show();
        }

        function closeMediaPicker() {
            $('#media-picker-modal').hide();
            currentMediaPicker = null;
            resetMediaPicker();
        }

        function switchTab(tabName) {
            $('.media-picker-tab').removeClass('active');
            $('.media-picker-tab-content').removeClass('active');
            
            $(`[onclick="switchTab('${tabName}')"]`).addClass('active');
            $(`#${tabName}-tab`).addClass('active');
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
            const file = document.getElementById('video-file-input').files[0];
            if (file) {
                // Check file size (10MB limit to match server configuration)
                const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                if (file.size > maxSize) {
                    Swal.fire('خطأ!', `حجم الملف (${(file.size / (1024 * 1024)).toFixed(1)} MB) أكبر من الحد المسموح (10 MB)`, 'error');
                    // Clear the file input
                    document.getElementById('video-file-input').value = '';
                    return;
                }
                
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

        function handleUrlInput() {
            const url = document.getElementById('video-url-input').value.trim();
            if (url) {
                showVideoPreview(url);
            } else {
                hideVideoPreview();
            }
        }

        function showVideoPreview(url) {
            const preview = document.getElementById('media-preview');
            const video = document.getElementById('preview-video');
            
            video.src = url;
            preview.style.display = 'block';
        }

        function hideVideoPreview() {
            document.getElementById('media-preview').style.display = 'none';
        }

        function resetMediaPicker() {
            const fileInput = document.getElementById('video-file-input');
            const urlInput = document.getElementById('video-url-input');
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
            
            fileInput.value = '';
            urlInput.value = '';
            video.src = '';
            preview.style.display = 'none';
        }

        function selectMedia() {
            if (currentMediaPicker === null) return;

            const fileInput = document.getElementById('video-file-input');
            const urlInput = document.getElementById('video-url-input');
            
            let mediaType = '';

            if (fileInput.files.length > 0) {
                // File upload
                mediaType = 'file';
            } else if (urlInput.value.trim()) {
                // URL
                mediaType = 'url';
            } else {
                Swal.fire('خطأ!', 'يرجى اختيار فيديو أولاً', 'error');
                return;
            }

            // Update the stage
            const stageItem = $(`.stage-item[data-index="${currentMediaPicker}"]`);
            const stageFileInput = stageItem.find('.video-file-input');
            const stageUrlInput = stageItem.find('.video-url-input');
            const videoPreview = stageItem.find('.video-preview');
            const previewVideo = videoPreview.find('video')[0];

            if (mediaType === 'file') {
                // For file uploads, set the actual file
                const file = fileInput.files[0];
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
            } else {
                // For URLs, clear file input and set URL
                const url = urlInput.value.trim();
                stageFileInput.val('');
                stageUrlInput.val(url);
                previewVideo.src = url;
            }

            videoPreview.show();
            closeMediaPicker();
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
            let isValid = true;
            const errorMessages = [];

            // Clear all previous errors
            clearAllErrors();

            // Validate course name Arabic
            const nameAr = $('#name_ar').val().trim();
            if (!nameAr) {
                showError('name_ar', 'اسم الدورة مطلوب');
                isValid = false;
            } else if (nameAr.length < 5) {
                showError('name_ar', 'اسم الدورة يجب أن يكون 5 أحرف على الأقل');
                isValid = false;
            }

            // Validate course name English
            const nameEn = $('#name_en').val().trim();
            if (!nameEn) {
                showError('name_en', 'Course name is required');
                isValid = false;
            } else if (nameEn.length < 5) {
                showError('name_en', 'Course name must be at least 5 characters');
                isValid = false;
            }

            // Validate instructor name Arabic
            const instructorAr = $('#instructor_name_ar').val().trim();
            if (!instructorAr) {
                showError('instructor_name_ar', 'اسم المدرب مطلوب');
                isValid = false;
            } else if (instructorAr.length < 5) {
                showError('instructor_name_ar', 'اسم المدرب يجب أن يكون 5 أحرف على الأقل');
                isValid = false;
            }

            // Validate instructor name English
            const instructorEn = $('#instructor_name_en').val().trim();
            if (!instructorEn) {
                showError('instructor_name_en', 'Instructor name is required');
                isValid = false;
            } else if (instructorEn.length < 5) {
                showError('instructor_name_en', 'Instructor name must be at least 5 characters');
                isValid = false;
            }

            // Validate duration
            const duration = $('#duration').val();
            if (!duration) {
                showError('duration', 'مدة الدورة مطلوبة');
                isValid = false;
            } else if (duration < 1) {
                showError('duration', 'مدة الدورة يجب أن تكون ساعة واحدة على الأقل');
                isValid = false;
            }

            // Validate price
            const price = $('#price').val();
            if (!price) {
                showError('price', 'سعر الدورة مطلوب');
                isValid = false;
            } else if (price < 0) {
                showError('price', 'سعر الدورة يجب أن يكون صفر أو أكثر');
                isValid = false;
            }

            // Validate description Arabic
            const descAr = $('#description_ar').val().trim();
            if (!descAr) {
                showError('description_ar', 'وصف الدورة مطلوب');
                isValid = false;
            } else if (descAr.length < 20) {
                showError('description_ar', 'وصف الدورة يجب أن يكون 20 حرف على الأقل');
                isValid = false;
            }

            // Validate description English
            const descEn = $('#description_en').val().trim();
            if (!descEn) {
                showError('description_en', 'Course description is required');
                isValid = false;
            } else if (descEn.length < 20) {
                showError('description_en', 'Course description must be at least 20 characters');
                isValid = false;
            }

            // Check each stage for video and title validation (only for new stages)
            $('.stage-item').each(function(index) {
                const stageItem = $(this);
                const fileInput = stageItem.find('.video-file-input');
                const urlInput = stageItem.find('.video-url-input');
                const currentVideo = stageItem.find('.current-video');
                const removeVideo = stageItem.find('input[name*="[remove_video]"]');
                const titleAr = stageItem.find('input[name*="[title][ar]"]').val().trim();
                const titleEn = stageItem.find('input[name*="[title][en]"]').val().trim();
                
                // Skip validation if this is an existing stage with current video that's not being removed
                if (currentVideo.length > 0 && removeVideo.val() === '0') {
                    // Still validate titles for existing stages
                    if (!titleAr) {
                        showError(`stage_${index}_title_ar`, 'عنوان المرحلة مطلوب');
                        isValid = false;
                    } else if (titleAr.length < 5) {
                        showError(`stage_${index}_title_ar`, 'عنوان المرحلة يجب أن يكون 5 أحرف على الأقل');
                        isValid = false;
                    }

                    if (!titleEn) {
                        showError(`stage_${index}_title_en`, 'Stage title is required');
                        isValid = false;
                    } else if (titleEn.length < 5) {
                        showError(`stage_${index}_title_en`, 'Stage title must be at least 5 characters');
                        isValid = false;
                    }
                    return;
                }
                
                const hasFile = fileInput[0].files.length > 0;
                const hasUrl = urlInput.val().trim() !== '';
                
                // Validate stage title Arabic
                if (!titleAr) {
                    showError(`stage_${index}_title_ar`, 'عنوان المرحلة مطلوب');
                    isValid = false;
                } else if (titleAr.length < 5) {
                    showError(`stage_${index}_title_ar`, 'عنوان المرحلة يجب أن يكون 5 أحرف على الأقل');
                    isValid = false;
                }

                // Validate stage title English
                if (!titleEn) {
                    showError(`stage_${index}_title_en`, 'Stage title is required');
                    isValid = false;
                } else if (titleEn.length < 5) {
                    showError(`stage_${index}_title_en`, 'Stage title must be at least 5 characters');
                    isValid = false;
                }
                
                if (!hasFile && !hasUrl) {
                    isValid = false;
                    errorMessages.push(`المرحلة ${index + 1}: يجب اختيار فيديو أو إدخال رابط`);
                }
            });

            if (!isValid) {
                e.preventDefault();
                if (errorMessages.length > 0) {
                    Swal.fire('خطأ في التحقق!', errorMessages.join('<br>'), 'error');
                }
            }
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
@include('admin.shared.submitAddForm')

@endsection