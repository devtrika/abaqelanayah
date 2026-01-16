<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
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
        return [
            'type' => 'required|in:notify',
            'body.*' => 'required|string|min:10|max:1000',
            'title.*' => 'required|string|min:3|max:255',
            'user_type' => 'required|in:all,clients,provider,admins'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $messages = [];

        // Create custom messages for each language field
        foreach (['ar', 'en'] as $lang) {
            if (app()->getLocale() == 'ar') {
                $langName = $lang == 'ar' ? 'العربية' : 'الإنجليزية';
                $messages["body.{$lang}.required"] = "حقل الرسالة باللغة {$langName} مطلوب";
                $messages["body.{$lang}.min"] = "حقل الرسالة يجب أن يحتوي على 10 أحرف على الأقل";
                $messages["body.{$lang}.max"] = "حقل الرسالة يجب أن لا يتجاوز 1000 حرف";
                $messages["title.{$lang}.min"] = "حقل العنوان يجب أن يحتوي على 3 أحرف على الأقل";
                $messages["title.{$lang}.max"] = "حقل العنوان يجب أن لا يتجاوز 255 حرف";
                $messages["title.{$lang}.required"] = "حقل العنوان باللغة {$langName} مطلوب";
            } else {
                $langName = $lang == 'ar' ? 'Arabic' : 'English';
                $messages["body.{$lang}.required"] = "Message field in {$langName} is required";
                $messages["body.{$lang}.min"] = "Message field must be at least 10 characters";
                $messages["body.{$lang}.max"] = "Message field cannot exceed 1000 characters";
                $messages["title.{$lang}.min"] = "Title field must be at least 3 characters";
                $messages["title.{$lang}.max"] = "Title field cannot exceed 255 characters";
                $messages["title.{$lang}.required"] = "Title field in {$langName} is required";
            }
        }

        // Add other validation messages
        $messages['user_type.required'] = app()->getLocale() == 'ar'
            ? 'يجب اختيار فئة المرسل إليهم'
            : 'Please select recipient category';

        $messages['user_type.in'] = app()->getLocale() == 'ar'
            ? 'فئة المرسل إليهم غير صحيحة'
            : 'Invalid recipient category';

        $messages['type.required'] = app()->getLocale() == 'ar'
            ? 'نوع الإشعار مطلوب'
            : 'Notification type is required';

        $messages['type.in'] = app()->getLocale() == 'ar'
            ? 'نوع الإشعار غير صحيح'
            : 'Invalid notification type';

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'type' => app()->getLocale() == 'ar' ? 'نوع الإشعار' : 'Notification Type',
            'user_type' => app()->getLocale() == 'ar' ? 'فئة المرسل إليهم' : 'Recipient Category',
            'title.ar' => app()->getLocale() == 'ar' ? 'العنوان بالعربية' : 'Title in Arabic',
            'title.en' => app()->getLocale() == 'ar' ? 'العنوان بالإنجليزية' : 'Title in English',
            'body.ar' => app()->getLocale() == 'ar' ? 'الرسالة بالعربية' : 'Message in Arabic',
            'body.en' => app()->getLocale() == 'ar' ? 'الرسالة بالإنجليزية' : 'Message in English',
        ];
    }
}
