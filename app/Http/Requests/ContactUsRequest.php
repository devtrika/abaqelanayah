<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest
{
    public function authorize()
    {
    return true;
    }

    public function rules()
    {
        $rules = [
            'type' => 'required|in:suggestion,complaint',
            'title' => 'required|string',
            'body' => 'required|string|max:1000',
        ];

        // Require phone, name, and email if user is guest (not authenticated)
        if (is_null(auth()->user())) {
            $rules['phone'] = 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:20';
            $rules['name'] = 'required|string|min:3|max:255';
            $rules['email'] = 'required|email|max:255';
        }

        return $rules;
    }
}
