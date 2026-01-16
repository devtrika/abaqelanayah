<?php

namespace App\Http\Requests\Admin\delivery;

use Illuminate\Foundation\Http\FormRequest;

class NotifyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        // Get supported languages
        $languages = languages();

        if ($this->notify == 'notify') {
            // Validation for notification
            foreach ($languages as $lang) {
                if ($lang == 'ar') {
                    // Arabic is required
                    $rules["title.{$lang}"] = 'required|string|min:3|max:255';
                    $rules["body.{$lang}"] = 'required|string|min:10|max:1000';
                } else {
                    // Other languages are optional but if provided, must meet criteria
                    $rules["title.{$lang}"] = 'required|string|min:3|max:255';
                    $rules["body.{$lang}"] = 'required|string|min:10|max:1000';
                }
            }
        } elseif ($this->notify == 'email') {
            // Validation for email
            $rules['message'] = 'required|string|min:10|max:1000';
        } elseif ($this->notify == 'sms') {
            // Validation for SMS
            $rules['body'] = 'required|string|min:10|max:160';
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = [];
        $languages = languages();

        foreach ($languages as $lang) {
            if ($lang == 'ar') {
                $attributes["title.{$lang}"] = __('admin.the_title') . ' ' . strtoupper($lang) . ' (' . __('admin.required') . ')';
                $attributes["body.{$lang}"] = __('admin.the_message') . ' ' . strtoupper($lang) . ' (' . __('admin.required') . ')';
            } else {
                $attributes["title.{$lang}"] = __('admin.the_title') . ' ' . strtoupper($lang) . ' (' . __('admin.optional') . ')';
                $attributes["body.{$lang}"] = __('admin.the_message') . ' ' . strtoupper($lang) . ' (' . __('admin.optional') . ')';
            }
        }

        $attributes['message'] = __('admin.the_written_text_of_the_email');
        $attributes['body'] = __('admin.the_written_text_of_the_sms');

        return $attributes;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        $messages = [];
        $languages = languages();

        // Create field-specific messages based on current locale
        if (app()->getLocale() == 'ar') {
            // Arabic messages with field names
            foreach ($languages as $lang) {
                $langName = $lang == 'ar' ? 'العربية' : 'الإنجليزية';

                $messages["title.{$lang}.required"] = "حقل العنوان باللغة {$langName} مطلوب";
                $messages["body.{$lang}.required"] = "حقل الرسالة باللغة {$langName} مطلوب";
                $messages["title.{$lang}.min"] = "حقل العنوان يجب أن يحتوي على 3 أحرف على الأقل";
                $messages["body.{$lang}.min"] = "حقل الرسالة يجب أن يحتوي على 10 أحرف على الأقل";
                $messages["title.{$lang}.max"] = "حقل العنوان يجب أن لا يتجاوز 255 حرف";
                $messages["body.{$lang}.max"] = "حقل الرسالة يجب أن لا يتجاوز 1000 حرف";
            }

            // General messages in Arabic
            $messages['message.required'] = 'حقل الرسالة مطلوب';
            $messages['body.required'] = 'حقل الرسالة مطلوب';
            $messages['message.min'] = 'حقل الرسالة يجب أن يحتوي على 10 أحرف على الأقل';
            $messages['body.min'] = 'حقل الرسالة يجب أن يحتوي على 10 أحرف على الأقل';
            $messages['body.max'] = 'رسالة SMS يجب أن لا تتجاوز 160 حرف';

        } else {
            // English messages with field names
            foreach ($languages as $lang) {
                $langName = $lang == 'ar' ? 'Arabic' : 'English';

                $messages["title.{$lang}.required"] = "Title field in {$langName} is required";
                $messages["body.{$lang}.required"] = "Message field in {$langName} is required";
                $messages["title.{$lang}.min"] = "Title field must be at least 3 characters";
                $messages["body.{$lang}.min"] = "Message field must be at least 10 characters";
                $messages["title.{$lang}.max"] = "Title field must not exceed 255 characters";
                $messages["body.{$lang}.max"] = "Message field must not exceed 1000 characters";
            }

            // General messages in English
            $messages['message.required'] = 'Message field is required';
            $messages['body.required'] = 'Message field is required';
            $messages['message.min'] = 'Message field must be at least 10 characters';
            $messages['body.min'] = 'Message field must be at least 10 characters';
            $messages['body.max'] = 'SMS message cannot exceed 160 characters';
        }

        return $messages;
    }
}
