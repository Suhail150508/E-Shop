<?php

namespace Modules\LiveChat\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReplyMessageRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Admin is already authorized by middleware
    }

    public function rules()
    {
        return [
            'message' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx|max:2048',
        ];
    }
}
