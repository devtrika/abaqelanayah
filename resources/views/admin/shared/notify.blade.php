<script>
    $(document).on('click' , '.mail' , function (e) {
        $('.notify_id').val($(this).data('id'))
    })
</script>
<script>
    $(document).on('click' , '.notify' , function (e) {
        $('.notify_id').val($(this).data('id'))
    })
</script>
<script>
    $(document).on('click' , '.sms' , function (e) {
        $('.notify_id').val($(this).data('id'))
    })
</script>
<script>
    // Translation object for validation messages
    var validationTranslations = {
        'ar': {
            'This field is required': 'هذا الحقل مطلوب',
            'required': 'هذا الحقل مطلوب',
            'min': 'يجب أن يكون الحد الأدنى :min أحرف',
            'max': 'يجب أن لا يتجاوز :max أحرف',
            'email': 'يجب أن يكون بريد إلكتروني صحيح',
            'phone': 'يجب أن يكون رقم هاتف صحيح',
            'numeric': 'يجب أن يكون رقم',
            'unique': 'هذا الحقل مستخدم من قبل',
            'body.required': 'نص الرسالة مطلوب',
            'message.required': 'نص الرسالة مطلوب',
            'title.required': 'العنوان مطلوب',
            'body[ar].required': 'الرسالة بالعربية مطلوبة',
            'body[en].required': 'الرسالة بالإنجليزية مطلوبة',
            'title[ar].required': 'العنوان بالعربية مطلوب',
            'title[en].required': 'العنوان بالإنجليزية مطلوب'
        },
        'en': {
            'This field is required': 'This field is required',
            'required': 'This field is required',
            'min': 'Must be at least :min characters',
            'max': 'Must not exceed :max characters',
            'email': 'Must be a valid email address',
            'phone': 'Must be a valid phone number',
            'numeric': 'Must be a number',
            'unique': 'This field has already been taken',
            'body.required': 'Message text is required',
            'message.required': 'Message text is required',
            'title.required': 'Title is required',
            'body[ar].required': 'Arabic message is required',
            'body[en].required': 'English message is required',
            'title[ar].required': 'Arabic title is required',
            'title[en].required': 'English title is required'
        }
    };

    var currentLocale = '{{ app()->getLocale() }}';
    console.log(currentLocale);
    function translateValidationMessage(key, message) {
        // If current locale is English, return original message
        if (currentLocale === 'en') {
            return message;
        }

        // Check if we have a direct translation for this specific key
        if (validationTranslations[currentLocale] && validationTranslations[currentLocale][key]) {
            return validationTranslations[currentLocale][key];
        }

        // Check if we have a direct translation for the exact message
        if (validationTranslations[currentLocale] && validationTranslations[currentLocale][message]) {
            return validationTranslations[currentLocale][message];
        }

        // // Check for specific field patterns
        // if (key.includes('body') && message.toLowerCase().includes('required')) {
        //     return 'نص الرسالة مطلوب';
        // }
        // if (key.includes('title') && message.toLowerCase().includes('required')) {
        //     return 'العنوان مطلوب';
        // }

        // Check for common validation patterns
        if (message.toLowerCase().includes('required') || message.toLowerCase().includes('is required')) {
            return validationTranslations[currentLocale]['required'];
        }
        if (message.toLowerCase().includes('must be at least')) {
            return 'يجب أن يكون الحد الأدنى 3 أحرف';
        }
        if (message.toLowerCase().includes('may not be greater than')) {
            return 'يجب أن لا يتجاوز الحد الأقصى المسموح';
        }

        // Check for partial matches in the message
        for (var rule in validationTranslations[currentLocale]) {
            if (message.toLowerCase().includes(rule.toLowerCase()) ||
                key.toLowerCase().includes(rule.toLowerCase())) {
                return validationTranslations[currentLocale][rule];
            }
        }

        // Return original message if no translation found
        return message;
    }

    $(document).ready(function(){
        $(document).on('submit','.notify-form',function(e){
            e.preventDefault();
            var $form = $(this);
            var url = $form.attr('action');

            $.ajax({
                url: url,
                method: 'post',
                data: new FormData($form[0]),
                dataType:'json',
                processData: false,
                contentType: false,
                beforeSend: function(){
                    $(".send-notify-button").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').attr('disable',true)
                    // Clear previous error messages and styling for this specific form
                    $form.find(".text-danger").remove()
                    $form.find(".validation-error").remove()
                    $form.find('input').removeClass('border-danger')
                    $form.find('textarea').removeClass('border-danger')
                    // Also clear any general error messages in modal body
                    $form.closest('.modal-body').find('.validation-error').remove()
                },
                success: function(response){
                    $(".send-notify-button").html("{{__('admin.send')}}").attr('disable',false)
                    Swal.fire({
                                position: 'top-start',
                                type: 'success',
                                title: '{{__('admin.send_successfully')}}',
                                showConfirmButton: false,
                                timer: 1500,
                                confirmButtonClass: 'btn btn-primary',
                                buttonsStyling: false,
                            })
                    setTimeout(function(){
                        window.location.reload()
                    }, 1000);
                },
                error: function (xhr) {
                    $(".send-notify-button").html("{{__('admin.send')}}").attr('disable',false)

                    // Clear previous error messages and styling for this specific form
                    $form.find(".text-danger").remove()
                    $form.find(".validation-error").remove()
                    $form.find('input').removeClass('border-danger')
                    $form.find('textarea').removeClass('border-danger')
                    // Also clear any general error messages in modal body
                    $form.closest('.modal-body').find('.validation-error').remove()

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            // Create field selector - handle array notation like body[ar], body[en]
                            var fieldSelector = '[name="' + key + '"]';

                            // Also try with escaped brackets for jQuery selector
                            var escapedKey = key.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
                            var escapedFieldSelector = '[name="' + escapedKey + '"]';

                            // Find the input/textarea field in this specific form
                            var field = $form.find(fieldSelector);
                            if (field.length === 0) {
                                field = $form.find(escapedFieldSelector);
                            }

                            if (field.length > 0) {
                                // Add error styling
                                field.addClass('border-danger');

                                // Add error message after the field
                                var originalMessage = Array.isArray(value) ? value[0] : value;
                                var translatedMessage = translateValidationMessage(key, originalMessage);

                                // Find the controls div or create one
                                var controlsDiv = field.closest('.form-group').find('.controls');
                                if (controlsDiv.length === 0) {
                                    controlsDiv = field.parent();
                                }
                                controlsDiv.append(`<span class="text-danger d-block mt-1 validation-error">${translatedMessage}</span>`);
                            } else {
                                // If field not found in form, show general error in modal body
                                var modalBody = $form.closest('.modal-body');
                                if (modalBody.length > 0) {
                                    var originalMessage = Array.isArray(value) ? value[0] : value;
                                    var translatedMessage = translateValidationMessage(key, originalMessage);
                                    modalBody.append(`<div class="alert alert-danger validation-error mt-2">${translatedMessage}</div>`);
                                }
                            }
                        });
                    }
                },
            });

        });

        // Handle submit button clicks for forms with external buttons
        $(document).on('click', 'button[type="submit"][form]', function(e) {
            e.preventDefault();
            var formId = $(this).attr('form');
            var $form = $('#' + formId);
            if ($form.length > 0) {
                $form.trigger('submit');
            }
        });
    });
</script>