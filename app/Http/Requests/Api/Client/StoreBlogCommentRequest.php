<?php

namespace App\Http\Requests\Api\Client;

use App\Http\Requests\Api\BaseApiRequest;

class StoreBlogCommentRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'blog_id' => 'required|exists:blogs,id',
            'comment' => 'required|string|max:1000',
            'rate' => 'nullable|integer|min:1|max:5',
        ];
    }
}
